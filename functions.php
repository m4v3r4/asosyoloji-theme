<?php
/**
 * Asosyoloji theme functions.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ASOSYOLOJI_VERSION', '0.1.0' );

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/dynamic-css.php';
require_once get_template_directory() . '/inc/section-customizer.php';

function asosyoloji_setup() {
	load_theme_textdomain( 'asosyoloji', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 160,
			'width'       => 520,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Ana Menü', 'asosyoloji' ),
			'footer'  => __( 'Alt Menü', 'asosyoloji' ),
		)
	);
}
add_action( 'after_setup_theme', 'asosyoloji_setup' );

function asosyoloji_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'asosyoloji_content_width', 760 );
}
add_action( 'after_setup_theme', 'asosyoloji_content_width', 0 );

function asosyoloji_enqueue_assets() {
	wp_enqueue_style(
		'asosyoloji-style',
		get_stylesheet_uri(),
		array(),
		ASOSYOLOJI_VERSION
	);

	wp_add_inline_style( 'asosyoloji-style', asosyoloji_dynamic_css() );

	wp_enqueue_script(
		'asosyoloji-theme',
		get_template_directory_uri() . '/assets/js/theme.js',
		array(),
		ASOSYOLOJI_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'asosyoloji_enqueue_assets' );

function asosyoloji_excerpt_length( $length ) {
	return 28;
}
add_filter( 'excerpt_length', 'asosyoloji_excerpt_length', 999 );

function asosyoloji_body_classes( $classes ) {
	if ( get_theme_mod( 'aso_sticky_header', true ) ) {
		$classes[] = 'has-sticky-header';
	}
	return $classes;
}
add_filter( 'body_class', 'asosyoloji_body_classes' );
