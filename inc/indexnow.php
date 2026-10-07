<?php
/**
 * Optional IndexNow integration.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_indexnow_key() {
	return asosyoloji_sanitize_indexnow_key( get_theme_mod( 'aso_indexnow_key', '' ) );
}

function asosyoloji_indexnow_verification() {
	if ( ! get_theme_mod( 'aso_indexnow_enabled', false ) ) {
		return;
	}

	$key = asosyoloji_indexnow_key();
	if ( ! $key ) {
		return;
	}

	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$request_path = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );
	if ( $request_path !== $key . '.txt' ) {
		return;
	}

	header( 'Content-Type: text/plain; charset=utf-8' );
	echo esc_html( $key );
	exit;
}
add_action( 'template_redirect', 'asosyoloji_indexnow_verification', 1 );

function asosyoloji_indexnow_submit( $post_id, $post, $update ) {
	if (
		! get_theme_mod( 'aso_indexnow_enabled', false ) ||
		wp_is_post_revision( $post_id ) ||
		! in_array( $post->post_type, array( 'post', 'page' ), true ) ||
		'publish' !== $post->post_status
	) {
		return;
	}

	$key = asosyoloji_indexnow_key();
	if ( strlen( $key ) < 8 ) {
		return;
	}

	$url          = get_permalink( $post_id );
	$key_location = home_url( '/' . $key . '.txt' );

	wp_remote_get(
		add_query_arg(
			array(
				'url'         => $url,
				'key'         => $key,
				'keyLocation' => $key_location,
			),
			'https://api.indexnow.org/indexnow'
		),
		array(
			'timeout'     => 4,
			'redirection' => 2,
			'blocking'    => false,
		)
	);
}
add_action( 'save_post', 'asosyoloji_indexnow_submit', 20, 3 );
