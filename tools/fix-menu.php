<?php
$map = array(
	'الأقسام والتخصصات' => 'التخصصات',
	'الخدمات الطبية'    => 'الخدمات',
);
foreach ( wp_get_nav_menu_items( 'القائمة الرئيسية' ) as $item ) {
	if ( isset( $map[ $item->title ] ) ) {
		wp_update_post( array( 'ID' => $item->ID, 'post_title' => $map[ $item->title ] ) );
		echo $item->title . ' -> ' . $map[ $item->title ] . "\n";
	}
}
