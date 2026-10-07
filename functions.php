<?php
/**
 * Asosyoloji theme functions.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ASOSYOLOJI_VERSION', '0.4.0' );

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/dynamic-css.php';
require_once get_template_directory() . '/inc/section-customizer.php';
require_once get_template_directory() . '/inc/appearance.php';
require_once get_template_directory() . '/inc/footer-customizer.php';
require_once get_template_directory() . '/inc/slider-customizer.php';
require_once get_template_directory() . '/inc/section-order.php';
require_once get_template_directory() . '/inc/reading.php';
require_once get_template_directory() . '/inc/archive-tools.php';
require_once get_template_directory() . '/inc/seo.php';

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


function asosyoloji_get_post_image( $post_id = 0, $size = 'medium_large', $attr = array() ) {
	$post_id = $post_id ?: get_the_ID();

	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail( $post_id, $size, $attr );
	}

	$content = get_post_field( 'post_content', $post_id );
	if ( ! $content ) {
		return '';
	}

	if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches ) ) {
		$src = esc_url( $matches[1] );
		if ( $src ) {
			$alt = get_the_title( $post_id );
			return sprintf(
				'<img src="%1$s" alt="%2$s" loading="lazy" decoding="async">',
				$src,
				esc_attr( $alt )
			);
		}
	}

	return '';
}

function asosyoloji_has_post_image( $post_id = 0 ) {
	return '' !== asosyoloji_get_post_image( $post_id ?: get_the_ID() );
}

function asosyoloji_register_widget_areas() {
	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar(
			array(
				'name'          => sprintf( __( 'Footer Sütun %d', 'asosyoloji' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => __( 'Footer sütununa blok veya bileşen ekleyin.', 'asosyoloji' ),
				'before_widget' => '<section id="%1$s" class="footer-widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="footer-widget__title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'asosyoloji_register_widget_areas' );

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
