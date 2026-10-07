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
	$stacks = array(
		'sans'   => 'Arial, Helvetica, sans-serif',
		'system' => '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
		'serif'  => 'Georgia, "Times New Roman", serif',
	);

	return isset( $stacks[ $choice ] ) ? $stacks[ $choice ] : $stacks['sans'];
}

function asosyoloji_theme_color( $setting_id, $default ) {
	$value = sanitize_hex_color( get_theme_mod( $setting_id, $default ) );

	return $value ? $value : $default;
}

function asosyoloji_dynamic_css() {
	$primary     = asosyoloji_theme_color( 'aso_primary_color', '#8f1d2c' );
	$text        = asosyoloji_theme_color( 'aso_text_color', '#111111' );
	$muted       = asosyoloji_theme_color( 'aso_muted_color', '#666666' );
	$border      = asosyoloji_theme_color( 'aso_border_color', '#d8d8d8' );
	$bg          = asosyoloji_theme_color( 'aso_background_color', '#ffffff' );
	$surface     = asosyoloji_theme_color( 'aso_surface_color', '#f5f3ef' );
	$dark_bg     = asosyoloji_theme_color( 'aso_dark_background', '#111111' );
	$dark_surf   = asosyoloji_theme_color( 'aso_dark_surface', '#1a1a1a' );
	$dark_text   = asosyoloji_theme_color( 'aso_dark_text', '#f2f2f2' );
	$dark_muted  = asosyoloji_theme_color( 'aso_dark_muted', '#b8b8b8' );
	$dark_border = asosyoloji_theme_color( 'aso_dark_border', '#343434' );
	$dark_link   = asosyoloji_theme_color( 'aso_dark_link', '#d56a78' );
	$dark_footer = asosyoloji_theme_color( 'aso_dark_footer', '#080808' );
	$container   = min( 1600, max( 960, absint( get_theme_mod( 'aso_container_width', 1280 ) ) ) );
	$article     = min( 980, max( 580, absint( get_theme_mod( 'aso_article_width', 760 ) ) ) );
	$body_size   = min( 24, max( 15, absint( get_theme_mod( 'aso_body_size', 18 ) ) ) );
	$logo_width  = min( 900, max( 180, absint( get_theme_mod( 'aso_logo_width', 620 ) ) ) );
	$logo_mobile = min( 420, max( 120, absint( get_theme_mod( 'aso_logo_width_mobile', 280 ) ) ) );

	$heading_font = asosyoloji_font_stack( get_theme_mod( 'aso_heading_font', 'sans' ) );
	$body_font    = asosyoloji_font_stack( get_theme_mod( 'aso_body_font', 'serif' ) );

	return sprintf(
		':root{--aso-primary:%1$s;--aso-text:%2$s;--aso-muted:%3$s;--aso-border:%4$s;--aso-bg:%5$s;--aso-surface:%6$s;--aso-container:%7$dpx;--aso-article:%8$dpx;--aso-body-size:%9$dpx;--aso-heading-font:%10$s;--aso-body-font:%11$s;--aso-dark-bg:%12$s;--aso-dark-surface:%13$s;--aso-dark-text:%14$s;--aso-dark-muted:%15$s;--aso-dark-border:%16$s;--aso-dark-link:%17$s;--aso-dark-footer:%18$s;--aso-logo-width:%19$dpx;--aso-logo-width-mobile:%20$dpx;}',
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
		$dark_footer,
		$logo_width,
		$logo_mobile
	);
}
