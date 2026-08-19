<?php
/**
 * يصدّر قاعدة بيانات النسخة المحلية (site-mysql) جاهزة للاستيراد على الاستضافة.
 *
 * يستبدل روابط localhost بعنوان الموقع الحقيقي داخل البيانات المتسلسلة أيضًا،
 * ويضيف ترويسة تجعل الاستيراد في phpMyAdmin يمرّ بدون أخطاء.
 *
 * التشغيل: php build/make-db.php
 */

$root       = dirname( __DIR__ );
$local_url  = 'http://localhost:8080';
$live_url   = 'https://alkhaleejy-group.com';
$php        = 'C:/PHP/php-8.4.21-nts/php.exe';
$wp_cli     = $root . '/tools/wp-cli/wp-cli.phar';
$out        = $root . '/dist/clinic-db.sql';
$out_compat = $root . '/dist/clinic-db-compat.sql';

foreach ( array( $php, $wp_cli ) as $needed ) {
	if ( ! file_exists( $needed ) ) {
		fwrite( STDERR, "غير موجود: $needed\n" );
		exit( 1 );
	}
}

// 1) التصدير مع استبدال الروابط.
$cmd = sprintf(
	'"%s" "%s" --path=%s search-replace %s %s --all-tables --export=%s',
	$php,
	$wp_cli,
	escapeshellarg( $root . '/site-mysql' ),
	escapeshellarg( $local_url ),
	escapeshellarg( $live_url ),
	escapeshellarg( $out )
);

exec( $cmd . ' 2>&1', $lines, $code );
if ( 0 !== $code ) {
	fwrite( STDERR, implode( "\n", $lines ) . "\n" );
	exit( 1 );
}

$sql = file_get_contents( $out );

// 2) إضافة Spectra تخزّن اسم النطاق منفصلًا عن بقية الروابط.
$host = parse_url( $live_url, PHP_URL_HOST );
$sql  = str_replace( "'uagb_site_url', 'localhost'", "'uagb_site_url', '$host'", $sql );

// 3) ترويسة تمنع فشل الاستيراد بسبب الترميز أو الوضع الصارم.
$header = "SET NAMES utf8mb4;\nSET SESSION sql_mode='';\nSET FOREIGN_KEY_CHECKS=0;\n";
$footer = "\nSET FOREIGN_KEY_CHECKS=1;\n";
$sql    = $header . $sql . $footer;

file_put_contents( $out, $sql );

// 4) نسخة بترميز أوسع توافقًا — تُستخدم إذا رفض السيرفر utf8mb4_unicode_520_ci.
file_put_contents( $out_compat, str_replace( 'utf8mb4_unicode_520_ci', 'utf8mb4_unicode_ci', $sql ) );

// 5) فحوصات ما بعد البناء.
$left = substr_count( $sql, 'localhost' );
$hits = substr_count( $sql, $host );

printf( "%-24s %s كيلوبايت\n", basename( $out ), number_format( filesize( $out ) / 1024 ) );
printf( "%-24s %s كيلوبايت\n", basename( $out_compat ), number_format( filesize( $out_compat ) / 1024 ) );
printf( "روابط الاستضافة: %d  |  بقايا localhost: %d%s\n", $hits, $left, $left ? '  ⚠️ راجعها' : '  ✅' );

exit( $left ? 1 : 0 );
