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

function asosyoloji_dynamic_css() {
	$primary   = sanitize_hex_color( get_theme_mod( 'aso_primary_color', '#b3212b' ) ) ?: '#b3212b';
	$text      = sanitize_hex_color( get_theme_mod( 'aso_text_color', '#171717' ) ) ?: '#171717';
	$muted     = sanitize_hex_color( get_theme_mod( 'aso_muted_color', '#6d6d6d' ) ) ?: '#6d6d6d';
	$border    = sanitize_hex_color( get_theme_mod( 'aso_border_color', '#dedede' ) ) ?: '#dedede';
	$bg        = sanitize_hex_color( get_theme_mod( 'aso_background_color', '#ffffff' ) ) ?: '#ffffff';
	$surface   = sanitize_hex_color( get_theme_mod( 'aso_surface_color', '#f6f4f1' ) ) ?: '#f6f4f1';
	$container = min( 1600, max( 960, absint( get_theme_mod( 'aso_container_width', 1280 ) ) ) );
	$article   = min( 980, max( 580, absint( get_theme_mod( 'aso_article_width', 760 ) ) ) );
	$body_size = min( 24, max( 15, absint( get_theme_mod( 'aso_body_size', 18 ) ) ) );

	$heading_font = asosyoloji_font_stack( get_theme_mod( 'aso_heading_font', 'sans' ) );
	$body_font    = asosyoloji_font_stack( get_theme_mod( 'aso_body_font', 'serif' ) );

	return sprintf(
		':root{--aso-primary:%1$s;--aso-text:%2$s;--aso-muted:%3$s;--aso-border:%4$s;--aso-bg:%5$s;--aso-surface:%6$s;--aso-container:%7$dpx;--aso-article:%8$dpx;--aso-body-size:%9$dpx;--aso-heading-font:%10$s;--aso-body-font:%11$s;}',
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
		$body_font
	);
}
