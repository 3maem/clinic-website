<?php
defined( 'ABSPATH' ) || exit;

function clinic_register_post_types() {

	// التخصصات / الأقسام الطبية — تصنيف مشترك بين الأطباء والخدمات.
	register_taxonomy( 'clinic_specialty', array( 'clinic_doctor', 'clinic_service' ), array(
		'labels'            => array(
			'name'          => 'التخصصات',
			'singular_name' => 'تخصص',
			'add_new_item'  => 'إضافة تخصص جديد',
			'edit_item'     => 'تعديل التخصص',
			'search_items'  => 'بحث في التخصصات',
			'menu_name'     => 'التخصصات',
		),
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'specialty' ),
	) );

	// الأطباء.
	register_post_type( 'clinic_doctor', array(
		'labels'        => array(
			'name'               => 'الأطباء',
			'singular_name'      => 'طبيب',
			'add_new'            => 'إضافة طبيب',
			'add_new_item'       => 'إضافة طبيب جديد',
			'edit_item'          => 'تعديل بيانات الطبيب',
			'new_item'           => 'طبيب جديد',
			'view_item'          => 'عرض الطبيب',
			'search_items'       => 'بحث في الأطباء',
			'not_found'          => 'لا يوجد أطباء',
			'featured_image'     => 'صورة الطبيب',
			'set_featured_image' => 'تحديد صورة الطبيب',
			'menu_name'          => 'الأطباء',
		),
		'public'        => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-id-alt',
		'menu_position' => 21,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'taxonomies'    => array( 'clinic_specialty' ),
		'rewrite'       => array( 'slug' => 'doctors' ),
		'has_archive'   => true,
	) );

	// الخدمات الطبية.
	register_post_type( 'clinic_service', array(
		'labels'        => array(
			'name'               => 'الخدمات الطبية',
			'singular_name'      => 'خدمة',
			'add_new'            => 'إضافة خدمة',
			'add_new_item'       => 'إضافة خدمة جديدة',
			'edit_item'          => 'تعديل الخدمة',
			'view_item'          => 'عرض الخدمة',
			'search_items'       => 'بحث في الخدمات',
			'not_found'          => 'لا توجد خدمات',
			'featured_image'     => 'صورة الخدمة',
			'set_featured_image' => 'تحديد صورة الخدمة',
			'menu_name'          => 'الخدمات الطبية',
		),
		'public'        => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-heart',
		'menu_position' => 22,
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		'taxonomies'    => array( 'clinic_specialty' ),
		'rewrite'       => array( 'slug' => 'services' ),
		'has_archive'   => true,
	) );

	// شركات التأمين.
	register_post_type( 'clinic_insurance', array(
		'labels'        => array(
			'name'               => 'شركات التأمين',
			'singular_name'      => 'شركة تأمين',
			'add_new'            => 'إضافة شركة',
			'add_new_item'       => 'إضافة شركة تأمين',
			'edit_item'          => 'تعديل شركة التأمين',
			'search_items'       => 'بحث في شركات التأمين',
			'not_found'          => 'لا توجد شركات تأمين',
			'featured_image'     => 'شعار الشركة',
			'set_featured_image' => 'تحديد شعار الشركة',
			'menu_name'          => 'شركات التأمين',
		),
		'public'             => false,
		'show_ui'            => true,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-shield',
		'menu_position'      => 23,
		'supports'           => array( 'title', 'thumbnail', 'page-attributes' ),
	) );

	// الإعلانات والتنبيهات.
	register_post_type( 'clinic_announcement', array(
		'labels'        => array(
			'name'          => 'الإعلانات والتنبيهات',
			'singular_name' => 'تنبيه',
			'add_new'       => 'إضافة تنبيه',
			'add_new_item'  => 'إضافة تنبيه جديد',
			'edit_item'     => 'تعديل التنبيه',
			'search_items'  => 'بحث في التنبيهات',
			'not_found'     => 'لا توجد تنبيهات',
			'menu_name'     => 'الإعلانات والتنبيهات',
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-megaphone',
		'menu_position' => 24,
		'supports'      => array( 'title' ),
	) );
}
add_action( 'init', 'clinic_register_post_types' );

// إعادة تسمية «مقالات» إلى «الأخبار والمقالات».
add_action( 'init', function () {
	global $wp_post_types;
	if ( isset( $wp_post_types['post'] ) ) {
		$labels                = $wp_post_types['post']->labels;
		$labels->name          = 'الأخبار والمقالات';
		$labels->singular_name = 'خبر';
		$labels->add_new       = 'إضافة خبر';
		$labels->add_new_item  = 'إضافة خبر جديد';
		$labels->edit_item     = 'تعديل الخبر';
		$labels->menu_name     = 'الأخبار والمقالات';
	}
} );
