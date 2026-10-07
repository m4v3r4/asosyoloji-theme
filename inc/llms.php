<?php
/**
 * Optional llms.txt endpoint.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_llms_txt() {
	if ( ! get_theme_mod( 'aso_llms_enabled', true ) ) {
		return;
	}

	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$request_path = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );

	if ( 'llms.txt' !== $request_path ) {
		return;
	}

	$publisher = trim( (string) get_theme_mod( 'aso_publisher_name', get_bloginfo( 'name' ) ) );
	$desc      = trim( (string) get_theme_mod( 'aso_publisher_description', get_bloginfo( 'description' ) ) );
	$posts     = get_posts(
		array(
			'numberposts' => 20,
			'post_status' => 'publish',
			'orderby'     => 'date',
			'order'       => 'DESC',
		)
	);

	$lines   = array();
	$lines[] = '# ' . sanitize_text_field( $publisher ? $publisher : get_bloginfo( 'name' ) );
	$lines[] = '';

	if ( $desc ) {
		$lines[] = sanitize_textarea_field( $desc );
		$lines[] = '';
	}

	$lines[] = '## Site';
	$lines[] = '- ' . esc_url_raw( home_url( '/' ) );
	$lines[] = '';
	$lines[] = '## Temel Sayfalar';

	foreach ( get_pages( array( 'number' => 12, 'sort_column' => 'menu_order,post_title' ) ) as $page ) {
		$lines[] = '- ' . sanitize_text_field( get_the_title( $page ) ) . ': ' . esc_url_raw( get_permalink( $page ) );
	}

	$lines[] = '';
	$lines[] = '## Son Yazılar';

	foreach ( $posts as $post ) {
		$lines[] = '- ' . sanitize_text_field( get_the_title( $post ) ) . ': ' . esc_url_raw( get_permalink( $post ) );
	}

	header( 'Content-Type: text/plain; charset=utf-8' );
	$output = implode( PHP_EOL, $lines ) . PHP_EOL;

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Plain-text response assembled from sanitized values.
	echo $output;
	exit;
}
add_action( 'template_redirect', 'asosyoloji_llms_txt', 2 );
