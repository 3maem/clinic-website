<?php
/**
 * Astra Clinic child theme.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'astra-clinic-cairo', 'https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap', array(), null );
	wp_enqueue_style(
		'astra-clinic',
		get_stylesheet_uri(),
		array( 'astra-theme-css' ),
		wp_get_theme()->get( 'Version' )
	);
}, 15 );

add_action( 'admin_init', function () {
	wp_enqueue_style( 'astra-clinic-cairo-admin', 'https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap', array(), null );
} );
