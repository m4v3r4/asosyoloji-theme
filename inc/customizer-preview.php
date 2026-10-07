<?php
/**
 * Customizer live preview support.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_customize_live_transport( $wp_customize ) {
	$settings = array(
		'aso_primary_color',
		'aso_text_color',
		'aso_muted_color',
		'aso_border_color',
		'aso_background_color',
		'aso_surface_color',
		'aso_dark_background',
		'aso_dark_surface',
		'aso_dark_text',
		'aso_dark_muted',
		'aso_dark_border',
		'aso_dark_link',
		'aso_dark_footer',
		'aso_logo_width',
		'aso_logo_width_mobile',
		'aso_container_width',
		'aso_article_width',
		'aso_body_size',
	);

	foreach ( $settings as $setting_id ) {
		$setting = $wp_customize->get_setting( $setting_id );
		if ( $setting ) {
			$setting->transport = 'postMessage';
		}
	}
}
add_action( 'customize_register', 'asosyoloji_customize_live_transport', 100 );

function asosyoloji_customize_preview_assets() {
	wp_enqueue_script(
		'asosyoloji-customizer-preview',
		get_template_directory_uri() . '/assets/js/customizer-preview.js',
		array( 'customize-preview' ),
		ASOSYOLOJI_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'asosyoloji_customize_preview_assets' );
