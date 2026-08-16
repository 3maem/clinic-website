<?php
/**
 * Plugin Name: إعداد المستوصف على الاستضافة (تشغيل لمرة واحدة)
 * Description: يطبّق الإعدادات التي لا ينقلها ملف استيراد المحتوى: ألوان وخطوط Astra، الصفحة الرئيسية وصفحة الأخبار، موقع القائمة الرئيسية، الروابط الدائمة، بيانات التواصل، ونموذج حجز المواعيد. فعّلها مرة واحدة بعد استيراد المحتوى ثم احذفها.
 * Version: 1.0.0
 * Author: clinic-website
 */

defined( 'ABSPATH' ) || exit;

register_activation_hook( __FILE__, 'clinic_server_setup_run' );

/**
 * يُنفَّذ مرة واحدة عند تفعيل الإضافة.
 */
function clinic_server_setup_run() {
	$log = array();

	// ---------- 1) ألوان وخطوط Astra ----------
	$astra = get_option( 'astra-settings', array() );
	if ( ! is_array( $astra ) ) {
		$astra = array();
	}
	$astra = array_merge(
		$astra,
		array(
			'theme-color'          => '#159EEC',
			'link-color'           => '#159EEC',
			'text-color'           => '#212124',
			'heading-base-color'   => '#1F2B6C',
			'button-bg-color'      => '#159EEC',
			'button-bg-h-color'    => '#1F2B6C',
			'button-color'         => '#ffffff',
			'button-radius'        => 6,
			'body-font-family'     => "'Cairo', sans-serif",
			'headings-font-family' => "'Cairo', sans-serif",
			'headings-font-weight' => '700',
			'font-size-body'       => array(
				'desktop'      => 16,
				'tablet'       => '',
				'mobile'       => '',
				'desktop-unit' => 'px',
				'tablet-unit'  => 'px',
				'mobile-unit'  => 'px',
			),
		)
	);
	update_option( 'astra-settings', $astra );
	$log[] = '✅ طُبّقت ألوان وخطوط Astra (كحلي #1F2B6C + أزرق #159EEC + خط Cairo).';

	// ---------- 2) بيانات التواصل (بيانات تجريبية — تُستبدل بالحقيقية) ----------
	$existing_settings = get_option( 'clinic_settings', array() );
	if ( empty( $existing_settings ) || ! is_array( $existing_settings ) ) {
		update_option(
			'clinic_settings',
			array(
				'phone'     => '011 123 4567',
				'whatsapp'  => '966501234567',
				'email'     => 'info@clinic.example.com',
				'address'   => 'الرياض — حي النسيم، طريق الأمير محمد بن سلمان',
				'map_embed' => 'https://maps.google.com/maps?q=Riyadh&z=13&output=embed',
				'hours'     => array(
					'sat' => array( 'closed' => false, 'from' => '09:00', 'to' => '21:00' ),
					'sun' => array( 'closed' => false, 'from' => '09:00', 'to' => '21:00' ),
					'mon' => array( 'closed' => false, 'from' => '09:00', 'to' => '21:00' ),
					'tue' => array( 'closed' => false, 'from' => '09:00', 'to' => '21:00' ),
					'wed' => array( 'closed' => false, 'from' => '09:00', 'to' => '21:00' ),
					'thu' => array( 'closed' => false, 'from' => '09:00', 'to' => '21:00' ),
					'fri' => array( 'closed' => true,  'from' => '',      'to' => '' ),
				),
			)
		);
		$log[] = '✅ أُضيفت بيانات تواصل تجريبية — غيّرها من «إعدادات المستوصف».';
	} else {
		$log[] = 'ℹ️ بيانات التواصل موجودة مسبقًا — لم تُلمس.';
	}

	// ---------- 3) الصفحة الرئيسية وصفحة الأخبار ----------
	$home = get_page_by_path( 'home' );
	$news = get_page_by_path( 'news' );

	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home->ID );
		$log[] = '✅ ضُبطت الصفحة الرئيسية على «' . $home->post_title . '».';
	} else {
		$log[] = '⚠️ لم يُعثر على صفحة الرابط «home» — هل استوردت المحتوى قبل التفعيل؟';
	}

	if ( $news ) {
		update_option( 'page_for_posts', $news->ID );
		$log[] = '✅ ضُبطت صفحة الأخبار على «' . $news->post_title . '».';
	} else {
		$log[] = '⚠️ لم يُعثر على صفحة الرابط «news».';
	}

	// ---------- 4) موقع القائمة الرئيسية ----------
	$menu = wp_get_nav_menu_object( 'القائمة الرئيسية' );
	if ( $menu ) {
		$locations             = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary']  = (int) $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations );
		$log[] = '✅ رُبطت «القائمة الرئيسية» بموقع القائمة الأساسي.';
	} else {
		$log[] = '⚠️ لم يُعثر على «القائمة الرئيسية» — اربطها يدويًا من المظهر ← القوائم.';
	}

	// ---------- 5) نموذج حجز المواعيد (Fluent Forms) ----------
	if ( class_exists( '\FluentForm\App\Modules\Form\Form' ) || defined( 'FLUENTFORM' ) ) {
		if ( ! function_exists( 'clinic_ff_text' ) ) {
			ob_start();
			include __DIR__ . '/seed-form.php';
			ob_end_clean();
		}
		$form_id = (int) get_option( 'clinic_booking_form_id', 0 );
		if ( $form_id ) {
			$log[] = '✅ نموذج «طلب حجز موعد» جاهز (رقم ' . $form_id . ').';
		} else {
			$log[] = '⚠️ تعذّر إنشاء نموذج الحجز — أنشئه يدويًا من Fluent Forms.';
		}
	} else {
		$log[] = '⚠️ إضافة Fluent Forms غير مفعّلة — فعّلها ثم أعد تفعيل هذه الإضافة.';
	}

	// ---------- 6) الروابط الدائمة ----------
	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules( false );
	$log[] = '✅ ضُبطت الروابط الدائمة على «اسم المقالة» وأُعيد بناء قواعد التوجيه.';

	update_option( 'clinic_setup_log', $log );
}

/**
 * يعرض نتيجة التشغيل مرة واحدة في لوحة التحكم.
 */
add_action(
	'admin_notices',
	function () {
		$log = get_option( 'clinic_setup_log' );
		if ( empty( $log ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		echo '<div class="notice notice-success"><p><strong>نتيجة إعداد المستوصف:</strong></p><ul style="margin-right:1.5em;list-style:disc;">';
		foreach ( (array) $log as $item ) {
			echo '<li>' . esc_html( $item ) . '</li>';
		}
		echo '</ul><p>انتهى العمل — يمكنك الآن <strong>تعطيل هذه الإضافة وحذفها</strong>.</p></div>';
		delete_option( 'clinic_setup_log' );
	}
);
