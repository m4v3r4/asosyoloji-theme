<?php
/**
 * Theme activation helpers.
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
