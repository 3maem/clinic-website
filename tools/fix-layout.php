<?php
// إخفاء عنوان الصفحة وجعل الرئيسية كاملة العرض + تنسيقات Astra لكل الصفحات.
$pages = get_posts( array( 'post_type' => 'page', 'posts_per_page' => -1 ) );
foreach ( $pages as $p ) {
	update_post_meta( $p->ID, 'site-post-title', 'disabled' );
	update_post_meta( $p->ID, 'site-sidebar-layout', 'no-sidebar' );
}
$home = get_page_by_path( 'home' );
if ( $home ) {
	update_post_meta( $home->ID, 'site-content-layout', 'page-builder' );
	update_post_meta( $home->ID, 'ast-site-content-layout', 'full-width' );
	update_post_meta( $home->ID, 'site-content-style', 'unboxed' );
	// لون الهيرو: كحلي صريح بدل الشفافية.
	$content = str_replace( 'has-background-dim-80', 'has-background-dim-100', $home->post_content );
	$content = str_replace( '"dimRatio":80', '"dimRatio":100', $content );
	wp_update_post( array( 'ID' => $home->ID, 'post_content' => $content ) );
}
echo "layout fixed\n";
