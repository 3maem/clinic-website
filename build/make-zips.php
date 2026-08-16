<?php
/**
 * يبني حزم الرفع بفواصل مسار "/" حتى تُفك بشكل صحيح على خوادم لينكس.
 * التشغيل: php build/make-zips.php
 */

$root = dirname( __DIR__ );

/**
 * @param string $src    مجلد المصدر.
 * @param string $prefix البادئة داخل الأرشيف.
 * @param string $out    مسار ملف الـ ZIP الناتج.
 */
function clinic_zip_dir( $src, $prefix, $out ) {
	if ( file_exists( $out ) ) {
		unlink( $out );
	}

	$zip = new ZipArchive();
	if ( true !== $zip->open( $out, ZipArchive::CREATE ) ) {
		fwrite( STDERR, "cannot create $out\n" );
		exit( 1 );
	}

	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $src, FilesystemIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::SELF_FIRST
	);

	$count = 0;
	foreach ( $files as $file ) {
		$path  = $file->getPathname();
		$local = $prefix . '/' . str_replace( '\\', '/', substr( $path, strlen( $src ) + 1 ) );

		if ( $file->isDir() ) {
			$zip->addEmptyDir( $local );
		} else {
			$zip->addFile( $path, $local );
			$count++;
		}
	}

	$zip->close();
	printf( "%-28s %4d files  %7d bytes\n", basename( $out ), $count, filesize( $out ) );
}

clinic_zip_dir(
	$root . '/build/clinic-server-setup',
	'clinic-server-setup',
	$root . '/dist/clinic-server-setup.zip'
);

clinic_zip_dir(
	$root . '/site/wp-content/uploads',
	'uploads',
	$root . '/dist/clinic-uploads.zip'
);
