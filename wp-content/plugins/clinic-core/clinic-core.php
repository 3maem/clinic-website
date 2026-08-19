<?php
/**
 * Plugin Name: إدارة المستوصف
 * Description: أقسام لوحة التحكم الخاصة بالمستوصف — الأطباء، التخصصات، الخدمات، شركات التأمين، التنبيهات، أوقات العمل وبيانات التواصل.
 * Version: 1.0.0
 * Author: Clinic
 * Text Domain: clinic-core
 */

defined( 'ABSPATH' ) || exit;

define( 'CLINIC_CORE_DIR', plugin_dir_path( __FILE__ ) );

require_once CLINIC_CORE_DIR . 'inc/post-types.php';
require_once CLINIC_CORE_DIR . 'inc/settings.php';
require_once CLINIC_CORE_DIR . 'inc/acf-fields.php';
require_once CLINIC_CORE_DIR . 'inc/shortcodes.php';
require_once CLINIC_CORE_DIR . 'inc/frontend.php';

register_activation_hook( __FILE__, function () {
	clinic_register_post_types();
	flush_rewrite_rules();
} );
