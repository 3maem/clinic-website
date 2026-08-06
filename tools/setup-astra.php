<?php
$s = get_option( 'astra-settings', array() );
$s['theme-color']           = '#159EEC';
$s['link-color']            = '#159EEC';
$s['text-color']            = '#212124';
$s['heading-base-color']    = '#1F2B6C';
$s['button-bg-color']       = '#159EEC';
$s['button-bg-h-color']     = '#1F2B6C';
$s['button-color']          = '#ffffff';
$s['button-radius']         = 6;
$s['body-font-family']      = "'Cairo', sans-serif";
$s['headings-font-family']  = "'Cairo', sans-serif";
$s['headings-font-weight']  = '700';
$s['font-size-body']        = array(
	'desktop'      => 16,
	'tablet'       => '',
	'mobile'       => '',
	'desktop-unit' => 'px',
	'tablet-unit'  => 'px',
	'mobile-unit'  => 'px',
);
update_option( 'astra-settings', $s );
echo "astra settings updated\n";
