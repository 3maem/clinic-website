<?php
/**
 * يعيد بناء شجرة فرع النشر في C:\projects\clinic-deploy من site-mysql.
 *
 * فرع deploy محتواه = ما يوضع في public_html على الاستضافة. هذا السكربت
 * يزامنه مع النسخة المحلية بعد أي تعديل، ولا يدفع شيئًا — الدفع قرار يدوي.
 *
 * التشغيل: php build/make-deploy-branch.php
 */

$root   = dirname( __DIR__ );
$src    = $root . '/site-mysql';
$dest   = dirname( $root ) . '/clinic-deploy';
$db_dir = $dest . '/deploy-db';

// ملفات يملكها فرع النشر نفسه ولا تأتي من site-mysql.
$keep = array( '.git', '.gitignore', '.gitattributes', '.htaccess', 'DEPLOY.md', 'deploy-db' );

// لا تُنسخ إلى الاستضافة إطلاقًا.
$exclude = array( 'wp-config.php', 'wp-content/upgrade', 'wp-content/db.php', 'wp-content/plugins/sqlite-database-integration', 'wp-content/database' );

if ( ! is_dir( $src ) ) {
	fwrite( STDERR, "المصدر غير موجود: $src\n" );
	exit( 1 );
}

// فحص وقائي: لا نحذف إلا داخل شجرة فرع النشر فعلًا.
if ( ! is_dir( $dest . '/.git' ) || ! file_exists( $dest . '/DEPLOY.md' ) ) {
	fwrite( STDERR, "الوجهة لا تبدو شجرة فرع النشر (ينقصها .git أو DEPLOY.md): $dest\n" );
	exit( 1 );
}

/**
 * @param string $path مسار الملف أو المجلد.
 */
function clinic_rm( $path ) {
	if ( is_dir( $path ) && ! is_link( $path ) ) {
		foreach ( scandir( $path ) as $e ) {
			if ( '.' !== $e && '..' !== $e ) {
				clinic_rm( $path . '/' . $e );
			}
		}
		rmdir( $path );
	} elseif ( file_exists( $path ) ) {
		unlink( $path );
	}
}

// 1) تفريغ الوجهة مما لا يملكه الفرع.
$removed = 0;
foreach ( scandir( $dest ) as $entry ) {
	if ( '.' === $entry || '..' === $entry || in_array( $entry, $keep, true ) ) {
		continue;
	}
	clinic_rm( $dest . '/' . $entry );
	$removed++;
}

// 2) نسخ الشجرة من المصدر.
$copied = 0;
$files  = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $src, FilesystemIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::SELF_FIRST
);

foreach ( $files as $file ) {
	$rel = str_replace( DIRECTORY_SEPARATOR, '/', substr( $file->getPathname(), strlen( $src ) + 1 ) );

	foreach ( $exclude as $x ) {
		if ( $rel === $x || 0 === strpos( $rel, $x . '/' ) ) {
			continue 2;
		}
	}

	$target = $dest . '/' . $rel;
	if ( $file->isDir() ) {
		if ( ! is_dir( $target ) ) {
			mkdir( $target, 0755, true );
		}
	} else {
		copy( $file->getPathname(), $target );
		$copied++;
	}
}

// 3) تحديث نسخة قاعدة البيانات.
$db_ok = true;
foreach ( array( 'clinic-db.sql', 'clinic-db-compat.sql' ) as $f ) {
	$from = $root . '/dist/' . $f;
	if ( ! file_exists( $from ) ) {
		fwrite( STDERR, "ناقص: dist/$f — شغّل build/make-db.php أولًا\n" );
		$db_ok = false;
		continue;
	}
	copy( $from, $db_dir . '/' . $f );
}

printf( "حُذف %d عنصر  |  نُسخ %d ملف\n", $removed, $copied );

// 4) فحوصات ما بعد البناء.
$checks = array(
	'wp-config.php غير موجود'                    => ! file_exists( $dest . '/wp-config.php' ),
	'wp-content/db.php غير موجود'                => ! file_exists( $dest . '/wp-content/db.php' ),
	'إضافة sqlite غير موجودة'                    => ! is_dir( $dest . '/wp-content/plugins/sqlite-database-integration' ),
	'القالب الابن astra-clinic موجود'            => is_dir( $dest . '/wp-content/themes/astra-clinic' ),
	'إضافة clinic-core موجودة'                   => is_dir( $dest . '/wp-content/plugins/clinic-core' ),
	'الصور موجودة'                               => is_dir( $dest . '/wp-content/uploads' ),
	'قاعدة البيانات موجودة'                      => $db_ok && file_exists( $db_dir . '/clinic-db.sql' ),
	'deploy-db محمي بـ .htaccess'                => file_exists( $db_dir . '/.htaccess' ),
);

$failed = 0;
foreach ( $checks as $label => $ok ) {
	printf( "%s %s\n", $ok ? '✅' : '❌', $label );
	if ( ! $ok ) {
		$failed++;
	}
}

if ( $failed ) {
	fwrite( STDERR, "\n$failed فحص فشل — لا تدفع قبل إصلاحه.\n" );
	exit( 1 );
}

echo "\nللدفع:\n";
echo "  cd " . str_replace( '/', '\\', $dest ) . "\n";
echo "  git add -A && git commit -m \"تحديث نسخة النشر\" && git push\n";
