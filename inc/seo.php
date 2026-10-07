<?php
/**
 * Lightweight SEO and author metadata helpers.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_user_contact_methods( $methods ) {
	$methods['mastodon'] = __( 'Mastodon URL', 'asosyoloji' );
	$methods['bluesky']  = __( 'Bluesky URL', 'asosyoloji' );
	$methods['linkedin'] = __( 'LinkedIn URL', 'asosyoloji' );
	$methods['website2'] = __( 'İkinci web sitesi', 'asosyoloji' );
	return $methods;
}
add_filter( 'user_contactmethods', 'asosyoloji_user_contact_methods' );

function asosyoloji_article_schema() {
	if ( ! is_single() ) {
		return;
	}

	$post_id = get_queried_object_id();
	$author_id = (int) get_post_field( 'post_author', $post_id );

	$schema = array(
		'@context'      => 'https://schema.org',
		'@type'         => 'Article',
		'headline'      => get_the_title( $post_id ),
		'datePublished' => get_the_date( DATE_W3C, $post_id ),
		'dateModified'  => get_the_modified_date( DATE_W3C, $post_id ),
		'mainEntityOfPage' => get_permalink( $post_id ),
		'author'        => array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $author_id ),
			'url'   => get_author_posts_url( $author_id ),
		),
		'publisher'     => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( has_post_thumbnail( $post_id ) ) {
		$schema['image'] = wp_get_attachment_image_url( get_post_thumbnail_id( $post_id ), 'full' );
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}
add_action( 'wp_head', 'asosyoloji_article_schema', 30 );
