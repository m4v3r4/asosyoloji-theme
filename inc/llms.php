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

	$request_path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
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

	header( 'Content-Type: text/plain; charset=utf-8' );

	echo '# ' . esc_html( $publisher ? $publisher : get_bloginfo( 'name' ) ) . "

";

	if ( $desc ) {
		echo esc_html( $desc ) . "

";
	}

	echo '## Site' . "
";
	echo '- ' . esc_url( home_url( '/' ) ) . "

";

	echo '## Temel Sayfalar' . "
";
	foreach ( get_pages( array( 'number' => 12, 'sort_column' => 'menu_order,post_title' ) ) as $page ) {
		echo '- ' . esc_html( get_the_title( $page ) ) . ': ' . esc_url( get_permalink( $page ) ) . "
";
	}

	echo "
## Son Yazılar
";
	foreach ( $posts as $post ) {
		echo '- ' . esc_html( get_the_title( $post ) ) . ': ' . esc_url( get_permalink( $post ) ) . "
";
	}

	exit;
}
add_action( 'template_redirect', 'asosyoloji_llms_txt', 2 );
