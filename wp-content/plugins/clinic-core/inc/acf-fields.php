<?php
defined( 'ABSPATH' ) || exit;

/**
 * حقول ACF مسجلة برمجيًا (لا تعتمد على إدخال يدوي في لوحة التحكم).
 */
add_action( 'acf/init', function () {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// حقول الطبيب.
	acf_add_local_field_group( array(
		'key'      => 'group_clinic_doctor',
		'title'    => 'بيانات الطبيب',
		'fields'   => array(
			array(
				'key'   => 'field_doctor_position',
				'label' => 'المسمى الوظيفي',
				'name'  => 'position',
				'type'  => 'text',
				'instructions' => 'مثال: استشاري طب الأسرة',
			),
			array(
				'key'   => 'field_doctor_qualifications',
				'label' => 'المؤهلات والشهادات',
				'name'  => 'qualifications',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_doctor_schedule',
				'label' => 'أوقات الدوام',
				'name'  => 'schedule',
				'type'  => 'text',
				'instructions' => 'مثال: السبت – الأربعاء، 4م – 9م',
			),
			array(
				'key'   => 'field_doctor_languages',
				'label' => 'اللغات',
				'name'  => 'languages',
				'type'  => 'text',
				'instructions' => 'مثال: العربية، الإنجليزية',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'clinic_doctor',
				),
			),
		),
	) );

	// حقول الخدمة.
	acf_add_local_field_group( array(
		'key'      => 'group_clinic_service',
		'title'    => 'بيانات الخدمة',
		'fields'   => array(
			array(
				'key'   => 'field_service_price',
				'label' => 'السعر (اختياري)',
				'name'  => 'price',
				'type'  => 'text',
				'instructions' => 'اتركه فارغًا إذا لا تريدون عرض الأسعار',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'clinic_service',
				),
			),
		),
	) );

	// حقول شركة التأمين.
	acf_add_local_field_group( array(
		'key'      => 'group_clinic_insurance',
		'title'    => 'بيانات شركة التأمين',
		'fields'   => array(
			array(
				'key'   => 'field_insurance_notes',
				'label' => 'ملاحظات التغطية',
				'name'  => 'notes',
				'type'  => 'textarea',
				'rows'  => 2,
				'instructions' => 'مثال: جميع الفئات ما عدا البلاتينية',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'clinic_insurance',
				),
			),
		),
	) );

	// حقول التنبيه.
	acf_add_local_field_group( array(
		'key'      => 'group_clinic_announcement',
		'title'    => 'إعدادات التنبيه',
		'fields'   => array(
			array(
				'key'           => 'field_announcement_active',
				'label'         => 'مفعّل؟',
				'name'          => 'active',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 1,
				'instructions'  => 'يظهر أعلى الموقع عند التفعيل. آخر تنبيه مفعّل هو الذي يُعرض.',
			),
			array(
				'key'          => 'field_announcement_link',
				'label'        => 'رابط (اختياري)',
				'name'         => 'link',
				'type'         => 'url',
				'instructions' => 'إذا أُضيف رابط يصبح التنبيه قابلًا للنقر',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'clinic_announcement',
				),
			),
		),
	) );
} );
