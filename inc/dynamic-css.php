<?php
/**
 * Generates CSS variables from Customizer values.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_font_stack( $choice ) {
	// Same typefaces as the Asosyoloji app: IBM Plex Serif and Roboto, bundled in assets/fonts.
	$stacks = array(
		'sans'   => 'Roboto, "Helvetica Neue", Arial, sans-serif',
		'system' => '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
		'serif'  => '"IBM Plex Serif", Georgia, "Times New Roman", serif',
	);

	return isset( $stacks[ $choice ] ) ? $stacks[ $choice ] : $stacks['sans'];
}

function asosyoloji_theme_color( $setting_id, $default ) {
	$value = sanitize_hex_color( get_theme_mod( $setting_id, $default ) );

	return $value ? $value : $default;
}

function asosyoloji_dynamic_css() {
	$primary     = asosyoloji_theme_color( 'aso_primary_color', '#731c3f' );
	$text        = asosyoloji_theme_color( 'aso_text_color', '#1e1a1c' );
	$muted       = asosyoloji_theme_color( 'aso_muted_color', '#5f5559' );
	$border      = asosyoloji_theme_color( 'aso_border_color', '#e5dcd7' );
	$bg          = asosyoloji_theme_color( 'aso_background_color', '#ffffff' );
	$surface     = asosyoloji_theme_color( 'aso_surface_color', '#f6f2ef' );
	$dark_bg     = asosyoloji_theme_color( 'aso_dark_background', '#141113' );
	$dark_surf   = asosyoloji_theme_color( 'aso_dark_surface', '#1d191b' );
	$dark_text   = asosyoloji_theme_color( 'aso_dark_text', '#f2ecee' );
	$dark_muted  = asosyoloji_theme_color( 'aso_dark_muted', '#bbafb4' );
	$dark_border = asosyoloji_theme_color( 'aso_dark_border', '#3b3337' );
	$dark_link   = asosyoloji_theme_color( 'aso_dark_link', '#e58cae' );
	$container   = min( 1600, max( 960, absint( get_theme_mod( 'aso_container_width', 1280 ) ) ) );
	$article     = min( 980, max( 580, absint( get_theme_mod( 'aso_article_width', 760 ) ) ) );
	$body_size   = min( 24, max( 15, absint( get_theme_mod( 'aso_body_size', 17 ) ) ) );
	$logo_width  = min( 900, max( 180, absint( get_theme_mod( 'aso_logo_width', 240 ) ) ) );
	$logo_mobile = min( 420, max( 120, absint( get_theme_mod( 'aso_logo_width_mobile', 160 ) ) ) );
	$slider_height = min( 760, max( 460, absint( get_theme_mod( 'aso_home_slider_height', 520 ) ) ) );
	$slider_mobile_raw = absint( get_theme_mod( 'aso_home_slider_height_mobile', 0 ) );
	$slider_height_mobile = $slider_mobile_raw ? min( 800, max( 440, $slider_mobile_raw ) ) . 'px' : 'auto';

	$heading_font = asosyoloji_font_stack( get_theme_mod( 'aso_heading_font', 'serif' ) );
	$body_font    = asosyoloji_font_stack( get_theme_mod( 'aso_body_font', 'sans' ) );
	$ui_font      = asosyoloji_font_stack( 'sans' );

	return sprintf(
		':root{--aso-primary:%1$s;--aso-text:%2$s;--aso-muted:%3$s;--aso-border:%4$s;--aso-bg:%5$s;--aso-surface:%6$s;--aso-container:%7$dpx;--aso-article:%8$dpx;--aso-body-size:%9$dpx;--aso-heading-font:%10$s;--aso-body-font:%11$s;--aso-dark-bg:%12$s;--aso-dark-surface:%13$s;--aso-dark-text:%14$s;--aso-dark-muted:%15$s;--aso-dark-border:%16$s;--aso-dark-link:%17$s;--aso-logo-width:%18$dpx;--aso-logo-width-mobile:%19$dpx;--aso-slider-height:%20$dpx;--aso-slider-height-mobile:%21$s;--aso-ui-font:%22$s;--aso-brand-color:%1$s;}',
		$primary,
		$text,
		$muted,
		$border,
		$bg,
		$surface,
		$container,
		$article,
		$body_size,
		$heading_font,
		$body_font,
		$dark_bg,
		$dark_surf,
		$dark_text,
		$dark_muted,
		$dark_border,
		$dark_link,
		$logo_width,
		$logo_mobile,
		$slider_height,
		$slider_height_mobile,
		$ui_font
	);
}
