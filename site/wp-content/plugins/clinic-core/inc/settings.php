<?php
defined( 'ABSPATH' ) || exit;

/**
 * صفحة «إعدادات المستوصف»: بيانات التواصل وأوقات العمل.
 * تُخزَّن كلها في خيار واحد clinic_settings.
 */

function clinic_get_settings() {
	$defaults = array(
		'phone'     => '',
		'whatsapp'  => '',
		'email'     => '',
		'address'   => '',
		'map_embed' => '',
		'hours'     => array(),
	);
	return wp_parse_args( get_option( 'clinic_settings', array() ), $defaults );
}

function clinic_days() {
	return array(
		'sat' => 'السبت',
		'sun' => 'الأحد',
		'mon' => 'الاثنين',
		'tue' => 'الثلاثاء',
		'wed' => 'الأربعاء',
		'thu' => 'الخميس',
		'fri' => 'الجمعة',
	);
}

add_action( 'admin_menu', function () {
	add_menu_page(
		'إعدادات المستوصف',
		'إعدادات المستوصف',
		'manage_options',
		'clinic-settings',
		'clinic_render_settings_page',
		'dashicons-admin-generic',
		25
	);
} );

add_action( 'admin_init', function () {
	register_setting( 'clinic_settings_group', 'clinic_settings', array(
		'sanitize_callback' => 'clinic_sanitize_settings',
	) );
} );

function clinic_sanitize_settings( $input ) {
	$out              = array();
	$out['phone']     = sanitize_text_field( $input['phone'] ?? '' );
	$out['whatsapp']  = preg_replace( '/[^0-9+]/', '', $input['whatsapp'] ?? '' );
	$out['email']     = sanitize_email( $input['email'] ?? '' );
	$out['address']   = sanitize_text_field( $input['address'] ?? '' );
	$out['map_embed'] = esc_url_raw( $input['map_embed'] ?? '' );
	$out['hours']     = array();
	foreach ( clinic_days() as $key => $label ) {
		$out['hours'][ $key ] = array(
			'closed' => ! empty( $input['hours'][ $key ]['closed'] ),
			'from'   => sanitize_text_field( $input['hours'][ $key ]['from'] ?? '' ),
			'to'     => sanitize_text_field( $input['hours'][ $key ]['to'] ?? '' ),
		);
	}
	return $out;
}

function clinic_render_settings_page() {
	$s = clinic_get_settings();
	?>
	<div class="wrap">
		<h1>إعدادات المستوصف</h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'clinic_settings_group' ); ?>

			<h2>بيانات التواصل</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="clinic-phone">رقم الهاتف</label></th>
					<td><input id="clinic-phone" class="regular-text" type="text" name="clinic_settings[phone]" value="<?php echo esc_attr( $s['phone'] ); ?>" dir="ltr"></td>
				</tr>
				<tr>
					<th scope="row"><label for="clinic-whatsapp">رقم واتساب</label></th>
					<td>
						<input id="clinic-whatsapp" class="regular-text" type="text" name="clinic_settings[whatsapp]" value="<?php echo esc_attr( $s['whatsapp'] ); ?>" dir="ltr" placeholder="9665XXXXXXXX">
						<p class="description">بصيغة دولية بدون صفر البداية، مثال: 966501234567</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="clinic-email">البريد الإلكتروني</label></th>
					<td><input id="clinic-email" class="regular-text" type="email" name="clinic_settings[email]" value="<?php echo esc_attr( $s['email'] ); ?>" dir="ltr"></td>
				</tr>
				<tr>
					<th scope="row"><label for="clinic-address">العنوان</label></th>
					<td><input id="clinic-address" class="large-text" type="text" name="clinic_settings[address]" value="<?php echo esc_attr( $s['address'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="clinic-map">رابط خريطة قوقل (Embed)</label></th>
					<td>
						<input id="clinic-map" class="large-text" type="url" name="clinic_settings[map_embed]" value="<?php echo esc_attr( $s['map_embed'] ); ?>" dir="ltr">
						<p class="description">من خرائط قوقل: مشاركة ← تضمين خريطة ← انسخ الرابط داخل src فقط.</p>
					</td>
				</tr>
			</table>

			<h2>أوقات العمل</h2>
			<table class="form-table" role="presentation">
				<?php foreach ( clinic_days() as $key => $label ) :
					$day = $s['hours'][ $key ] ?? array( 'closed' => false, 'from' => '', 'to' => '' );
					?>
					<tr>
						<th scope="row"><?php echo esc_html( $label ); ?></th>
						<td>
							من <input type="time" name="clinic_settings[hours][<?php echo esc_attr( $key ); ?>][from]" value="<?php echo esc_attr( $day['from'] ); ?>">
							إلى <input type="time" name="clinic_settings[hours][<?php echo esc_attr( $key ); ?>][to]" value="<?php echo esc_attr( $day['to'] ); ?>">
							&nbsp;<label><input type="checkbox" name="clinic_settings[hours][<?php echo esc_attr( $key ); ?>][closed]" value="1" <?php checked( ! empty( $day['closed'] ) ); ?>> مغلق</label>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>

			<?php submit_button( 'حفظ الإعدادات' ); ?>
		</form>
	</div>
	<?php
}
