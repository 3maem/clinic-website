<?php
/**
 * إنشاء نموذج «طلب حجز موعد» في Fluent Forms وحفظ رقمه في خيار clinic_booking_form_id.
 */

global $wpdb;
$table = $wpdb->prefix . 'fluentform_forms';

$existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE title = %s", 'طلب حجز موعد' ) );
if ( $existing ) {
	update_option( 'clinic_booking_form_id', (int) $existing );
	echo "form already exists: $existing\n";
	return;
}

function clinic_ff_text( $index, $name, $label, $placeholder, $required = true, $type = 'text' ) {
	return array(
		'index'          => $index,
		'element'        => 'input_text',
		'attributes'     => array(
			'type'        => $type,
			'name'        => $name,
			'value'       => '',
			'id'          => '',
			'class'       => '',
			'placeholder' => $placeholder,
			'maxlength'   => '',
		),
		'settings'       => array(
			'container_class'   => '',
			'label'             => $label,
			'label_placement'   => '',
			'admin_field_label' => '',
			'help_message'      => '',
			'validation_rules'  => array(
				'required' => array(
					'value'          => $required,
					'message'        => 'هذا الحقل مطلوب',
					'global_message' => 'هذا الحقل مطلوب',
					'global'         => true,
				),
			),
			'conditional_logics' => array(),
		),
		'editor_options' => array(
			'title'      => $label,
			'icon_class' => 'ff-edit-text',
			'template'   => 'inputText',
		),
		'uniqElKey'      => 'el_' . $index . '_' . $name,
	);
}

$department_options = array();
foreach ( array( 'عيادة الأسرة والطب العام', 'الأسنان', 'النساء والولادة', 'الأطفال', 'الباطنية', 'الجلدية', 'المختبر والأشعة' ) as $i => $dep ) {
	$department_options[] = array(
		'label'      => $dep,
		'value'      => $dep,
		'calc_value' => '',
		'id'         => $i + 1,
	);
}

$form_fields = array(
	'fields'       => array(
		clinic_ff_text( 1, 'full_name', 'الاسم الكامل', 'اكتب اسمك الثلاثي' ),
		clinic_ff_text( 2, 'phone_number', 'رقم الجوال', '05XXXXXXXX' ),
		array(
			'index'          => 3,
			'element'        => 'select',
			'attributes'     => array(
				'name'  => 'department',
				'value' => '',
				'id'    => '',
				'class' => '',
			),
			'settings'       => array(
				'container_class'    => '',
				'label'              => 'العيادة / التخصص',
				'label_placement'    => '',
				'admin_field_label'  => '',
				'help_message'       => '',
				'placeholder'        => '- اختر العيادة -',
				'advanced_options'   => $department_options,
				'calc_value_status'  => false,
				'enable_image_input' => false,
				'randomize_options'  => 'no',
				'enable_select_2'    => 'no',
				'validation_rules'   => array(
					'required' => array(
						'value'          => true,
						'message'        => 'اختر العيادة',
						'global_message' => 'هذا الحقل مطلوب',
						'global'         => true,
					),
				),
				'conditional_logics' => array(),
			),
			'editor_options' => array(
				'title'      => 'العيادة / التخصص',
				'icon_class' => 'ff-edit-dropdown',
				'template'   => 'select',
			),
			'uniqElKey'      => 'el_3_department',
		),
		clinic_ff_text( 4, 'preferred_time', 'اليوم والوقت المفضل', 'مثال: الثلاثاء بعد المغرب', false ),
		array(
			'index'          => 5,
			'element'        => 'textarea',
			'attributes'     => array(
				'name'        => 'notes',
				'value'       => '',
				'id'          => '',
				'class'       => '',
				'placeholder' => 'أي تفاصيل إضافية (اختياري)',
				'rows'        => 4,
				'cols'        => 2,
				'maxlength'   => '',
			),
			'settings'       => array(
				'container_class'    => '',
				'label'              => 'ملاحظات',
				'label_placement'    => '',
				'admin_field_label'  => '',
				'help_message'       => '',
				'validation_rules'   => array(
					'required' => array(
						'value'          => false,
						'message'        => '',
						'global_message' => 'هذا الحقل مطلوب',
						'global'         => true,
					),
				),
				'conditional_logics' => array(),
			),
			'editor_options' => array(
				'title'      => 'ملاحظات',
				'icon_class' => 'ff-edit-textarea',
				'template'   => 'inputTextarea',
			),
			'uniqElKey'      => 'el_5_notes',
		),
	),
	'submitButton' => array(
		'uniqElKey'      => 'el_submit',
		'element'        => 'button',
		'attributes'     => array(
			'type'  => 'submit',
			'class' => '',
		),
		'settings'       => array(
			'container_class' => '',
			'align'           => 'left',
			'button_style'    => 'default',
			'button_size'     => 'md',
			'color'           => '#ffffff',
			'background_color' => '#159EEC',
			'button_ui'       => array(
				'type'    => 'default',
				'text'    => 'إرسال طلب الحجز',
				'img_url' => '',
			),
			'normal_styles'   => array(),
			'hover_styles'    => array(),
			'current_state'   => 'normal_styles',
		),
		'editor_options'  => array(
			'title' => 'Submit Button',
		),
	),
);

$now = current_time( 'mysql' );
$wpdb->insert( $table, array(
	'title'       => 'طلب حجز موعد',
	'status'      => 'published',
	'form_fields' => wp_json_encode( $form_fields, JSON_UNESCAPED_UNICODE ),
	'type'        => 'form',
	'has_payment' => 0,
	'created_by'  => 1,
	'created_at'  => $now,
	'updated_at'  => $now,
) );
$form_id = (int) $wpdb->insert_id;

$meta_table    = $wpdb->prefix . 'fluentform_form_meta';
$form_settings = array(
	'confirmation' => array(
		'redirectTo'           => 'samePage',
		'messageToShow'        => 'تم استلام طلبك بنجاح ✅ سيتواصل معك فريق الاستقبال قريبًا لتأكيد الموعد.',
		'customPage'           => null,
		'samePageFormBehavior' => 'hide_form',
		'customUrl'            => null,
	),
	'restrictions' => array(
		'limitNumberOfEntries'   => array( 'enabled' => false ),
		'scheduleForm'           => array( 'enabled' => false ),
		'requireLogin'           => array( 'enabled' => false ),
		'denyEmptySubmission'    => array( 'enabled' => true, 'message' => 'النموذج فارغ' ),
		'restrictForm'           => array( 'enabled' => false ),
	),
	'layout'       => array(
		'labelPlacement'          => 'top',
		'helpMessagePlacement'    => 'with_label',
		'errorMessagePlacement'   => 'inline',
		'cssClassName'            => '',
		'asteriskPlacement'       => 'asterisk-right',
	),
);
$wpdb->insert( $meta_table, array(
	'form_id'   => $form_id,
	'meta_key'  => 'formSettings',
	'value'     => wp_json_encode( $form_settings, JSON_UNESCAPED_UNICODE ),
) );

update_option( 'clinic_booking_form_id', $form_id );
echo "form created: $form_id\n";
