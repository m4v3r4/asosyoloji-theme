<?php
/**
 * Gutenberg blocks for the theme.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the post list block.
 */
function asosyoloji_register_post_list_block() {
	wp_register_script(
		'asosyoloji-post-list-block',
		get_template_directory_uri() . '/assets/js/post-list-block.js',
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n' ),
		ASOSYOLOJI_VERSION,
		true
	);

	$categories = array(
		array(
			'label' => __( 'Tüm kategoriler', 'asosyoloji' ),
			'value' => 0,
		),
	);

	foreach ( get_categories( array( 'hide_empty' => false ) ) as $category ) {
		$categories[] = array(
			'label' => $category->name,
			'value' => (int) $category->term_id,
		);
	}

	$authors = array(
		array(
			'label' => __( 'Tüm yazarlar', 'asosyoloji' ),
			'value' => 0,
		),
	);

	foreach ( get_users(
		array(
			'who'     => 'authors',
			'orderby' => 'display_name',
		)
	) as $user ) {
		$authors[] = array(
			'label' => $user->display_name,
			'value' => (int) $user->ID,
		);
	}

	wp_localize_script(
		'asosyoloji-post-list-block',
		'asosyolojiPostListBlock',
		array(
			'categories' => $categories,
			'authors'    => $authors,
		)
	);

	register_block_type(
		'asosyoloji/post-list',
		array(
			'api_version'     => 3,
			'editor_script'   => 'asosyoloji-post-list-block',
			'render_callback' => 'asosyoloji_render_post_list_block',
			'attributes'      => array(
				'title' => array(
					'type'    => 'string',
					'default' => '',
				),
				'category' => array(
					'type'    => 'number',
					'default' => 0,
				),
				'excludeCategories' => array(
					'type'    => 'array',
					'default' => array(),
					'items'   => array( 'type' => 'number' ),
				),
				'count' => array(
					'type'    => 'number',
					'default' => 6,
				),
				'layout' => array(
					'type'    => 'string',
					'default' => 'grid',
				),
				'showImage' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'showExcerpt' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'showMeta' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'author' => array(
					'type'    => 'number',
					'default' => 0,
				),
				'orderby' => array(
					'type'    => 'string',
					'default' => 'date',
				),
				'order' => array(
					'type'    => 'string',
					'default' => 'DESC',
				),
				'dateAfter' => array(
					'type'    => 'string',
					'default' => '',
				),
				'dateBefore' => array(
					'type'    => 'string',
					'default' => '',
				),
				'offset' => array(
					'type'    => 'number',
					'default' => 0,
				),
				'includeSticky' => array(
					'type'    => 'boolean',
					'default' => false,
				),
			),
		)
	);
}
add_action( 'init', 'asosyoloji_register_post_list_block' );

/**
 * Render post list block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function asosyoloji_render_post_list_block( $attributes ) {
	return asosyoloji_render_post_collection(
		array(
			'title'              => isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : '',
			'category'           => isset( $attributes['category'] ) ? absint( $attributes['category'] ) : 0,
			'exclude_categories' => isset( $attributes['excludeCategories'] ) ? asosyoloji_sanitize_category_ids( $attributes['excludeCategories'] ) : array(),
			'count'              => isset( $attributes['count'] ) ? absint( $attributes['count'] ) : 6,
			'layout'             => isset( $attributes['layout'] ) ? sanitize_key( $attributes['layout'] ) : 'grid',
			'show_image'         => ! isset( $attributes['showImage'] ) || (bool) $attributes['showImage'],
			'show_excerpt'       => ! empty( $attributes['showExcerpt'] ),
			'show_meta'          => ! isset( $attributes['showMeta'] ) || (bool) $attributes['showMeta'],
			'author'             => isset( $attributes['author'] ) ? absint( $attributes['author'] ) : 0,
			'orderby'            => isset( $attributes['orderby'] ) ? sanitize_key( $attributes['orderby'] ) : 'date',
			'order'              => isset( $attributes['order'] ) ? sanitize_key( strtoupper( (string) $attributes['order'] ) ) : 'DESC',
			'date_after'         => isset( $attributes['dateAfter'] ) ? sanitize_text_field( $attributes['dateAfter'] ) : '',
			'date_before'        => isset( $attributes['dateBefore'] ) ? sanitize_text_field( $attributes['dateBefore'] ) : '',
			'offset'             => isset( $attributes['offset'] ) ? absint( $attributes['offset'] ) : 0,
			'include_sticky'     => ! empty( $attributes['includeSticky'] ),
		)
	);
}
