<?php
/**
 * Theme activation and one-time migrations.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run lightweight activation tasks.
 */
function asosyoloji_after_switch_theme() {
	update_option( 'asosyoloji_theme_version', ASOSYOLOJI_VERSION, false );
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'asosyoloji_after_switch_theme' );

/**
 * Settings that make up the shared app/web design scheme.
 *
 * @return string[]
 */
function asosyoloji_scheme_theme_mods() {
	return array(
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
		'aso_heading_font',
		'aso_body_font',
		'aso_body_size',
		'aso_home_slider_layout',
		'aso_logo_width',
		'aso_logo_width_mobile',
		'aso_menu_separators',
	);
}

/**
 * One-time switch to the Asosyoloji app scheme (0.9.0).
 *
 * Earlier saved colours, fonts and layout choices would otherwise keep the
 * old look. They are backed up to the `asosyoloji_scheme_backup` option first,
 * so nothing is lost; everything stays editable in the Customizer afterwards.
 */
function asosyoloji_migrate_to_app_scheme() {
	if ( 'app-1' === get_option( 'asosyoloji_scheme' ) ) {
		return;
	}

	$backup = array();
	foreach ( asosyoloji_scheme_theme_mods() as $mod ) {
		$value = get_theme_mod( $mod, null );
		if ( null !== $value ) {
			$backup[ $mod ] = $value;
			remove_theme_mod( $mod );
		}
	}

	if ( $backup ) {
		update_option( 'asosyoloji_scheme_backup', $backup, false );
	}

	update_option( 'asosyoloji_scheme', 'app-1' );
}
add_action( 'after_setup_theme', 'asosyoloji_migrate_to_app_scheme', 20 );
