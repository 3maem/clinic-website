<?php
/**
 * يبني حزمة النشر الكاملة: wp-content بكل ما يحتاجه الموقع على الاستضافة.
 * تُفك داخل public_html مباشرة.
 * التشغيل: php build/make-deploy.php
 */

$root = dirname( __DIR__ );
$src  = $root . '/site-mysql/wp-content';
$out  = $root . '/dist/clinic-wp-content.zip';

// قوالب ووردبريس الافتراضية موجودة على الاستضافة أصلًا — لا داعي لرفعها.
$skip_dirs = array(
	'themes/twentytwentytwo',
	'themes/twentytwentythree',
	'themes/twentytwentyfour',
	'themes/twentytwentyfive',
	'upgrade',
	'database',
);

if ( ! is_dir( $src ) ) {
	fwrite( STDERR, "المصدر غير موجود: $src\n" );
	exit( 1 );
}

if ( file_exists( $out ) ) {
	unlink( $out );
}

$zip = new ZipArchive();
if ( true !== $zip->open( $out, ZipArchive::CREATE ) ) {
	fwrite( STDERR, "تعذّر إنشاء $out\n" );
	exit( 1 );
}

$files = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $src, FilesystemIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::SELF_FIRST
);

$count   = 0;
$skipped = 0;
foreach ( $files as $file ) {
	$rel = str_replace( DIRECTORY_SEPARATOR, '/', substr( $file->getPathname(), strlen( $src ) + 1 ) );

	foreach ( $skip_dirs as $s ) {
		if ( $rel === $s || 0 === strpos( $rel, $s . '/' ) ) {
			$skipped++;
			continue 2;
		}
	}

	// ملف قاعدة SQLite يجب ألا يصل للاستضافة إطلاقًا.
	if ( 'db.php' === $rel || false !== strpos( $rel, 'sqlite' ) ) {
		$skipped++;
		continue;
	}

	$local = 'wp-content/' . $rel;
	if ( $file->isDir() ) {
		$zip->addEmptyDir( $local );
	} else {
		$zip->addFile( $file->getPathname(), $local );
		$count++;
	}
}

$zip->close();

printf(
	"%s\n  %d ملف  |  %s ميجابايت  |  %d عنصر مستثنى\n",
	basename( $out ),
	$count,
	number_format( filesize( $out ) / 1048576, 1 ),
	$skipped
);
