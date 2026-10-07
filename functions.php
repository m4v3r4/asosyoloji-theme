<?php
/**
 * Asosyoloji theme functions.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ASOSYOLOJI_VERSION', '0.7.6' );

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/dynamic-css.php';
require_once get_template_directory() . '/inc/section-customizer.php';
require_once get_template_directory() . '/inc/appearance.php';
require_once get_template_directory() . '/inc/footer-customizer.php';
require_once get_template_directory() . '/inc/slider-customizer.php';
require_once get_template_directory() . '/inc/section-order.php';
require_once get_template_directory() . '/inc/reading.php';
require_once get_template_directory() . '/inc/archive-tools.php';
require_once get_template_directory() . '/inc/seo-settings.php';
require_once get_template_directory() . '/inc/seo-editor.php';
require_once get_template_directory() . '/inc/geo-content.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/indexnow.php';
require_once get_template_directory() . '/inc/llms.php';
require_once get_template_directory() . '/inc/customizer-preview.php';
require_once get_template_directory() . '/inc/widgets.php';
require_once get_template_directory() . '/inc/blocks.php';
require_once get_template_directory() . '/inc/activation.php';
require_once get_template_directory() . '/inc/github-updater.php';
require_once get_template_directory() . '/inc/recommended-plugins.php';

function asosyoloji_setup() {
	load_theme_textdomain( 'asosyoloji', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_editor_style( 'editor-style.css' );

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

	wp_localize_script(
		'asosyoloji-theme',
		'asosyolojiTheme',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'asosyoloji_home_latest' ),
			'strings' => array(
				'loading' => __( 'Yazılar yükleniyor…', 'asosyoloji' ),
				'more'    => __( 'Daha fazla yükle', 'asosyoloji' ),
				'done'    => __( 'Tüm yazılar yüklendi.', 'asosyoloji' ),
				'error'   => __( 'Yazılar yüklenemedi. Tekrar deneyin.', 'asosyoloji' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'asosyoloji_enqueue_assets' );


function asosyoloji_home_excluded_categories( $specific = array() ) {
	$global   = asosyoloji_sanitize_category_ids( get_theme_mod( 'aso_home_global_exclude_categories', array() ) );
	$specific = asosyoloji_sanitize_category_ids( $specific );

	return array_values( array_unique( array_merge( $global, $specific ) ) );
}

function asosyoloji_get_fallback_image_url( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$cache_key = '_asosyoloji_fallback_image_url';
	$cached = get_post_meta( $post_id, $cache_key, true );

	if ( '__none__' === $cached ) {
		return '';
	}

	if ( is_string( $cached ) && '' !== $cached ) {
		return esc_url_raw( $cached );
	}

	$content = get_post_field( 'post_content', $post_id );
	$url     = '';

	if ( $content && preg_match( '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches ) ) {
		$url = esc_url_raw( $matches[1] );
	}

	update_post_meta( $post_id, $cache_key, $url ? $url : '__none__' );

	return $url;
}

function asosyoloji_clear_fallback_image_cache( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || 'post' !== get_post_type( $post_id ) ) {
		return;
	}

	delete_post_meta( $post_id, '_asosyoloji_fallback_image_url' );
}
add_action( 'save_post', 'asosyoloji_clear_fallback_image_cache' );

function asosyoloji_get_default_card_image_url() {
	$custom_logo_id = absint( get_theme_mod( 'custom_logo', 0 ) );

	if ( $custom_logo_id ) {
		$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'large' );
		if ( $logo_url ) {
			return $logo_url;
		}
	}

	return get_template_directory_uri() . '/assets/images/card-fallback.svg';
}

function asosyoloji_get_post_image( $post_id = 0, $size = 'medium_large', $attr = array() ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail( $post_id, $size, $attr );
	}

	$src         = asosyoloji_get_fallback_image_url( $post_id );
	$is_fallback = false;

	if ( ! $src ) {
		$src         = asosyoloji_get_default_card_image_url();
		$is_fallback = true;
	}

	$alt     = get_the_title( $post_id );
	$classes = isset( $attr['class'] ) ? sanitize_html_class( $attr['class'] ) : '';
	if ( $is_fallback ) {
		$classes = trim( $classes . ' is-fallback-image' );
	}

	return sprintf(
		'<img src="%1$s" alt="%2$s" class="%3$s" loading="lazy" decoding="async">',
		esc_url( $src ),
		esc_attr( $alt ),
		esc_attr( $classes )
	);
}

function asosyoloji_has_post_image( $post_id = 0 ) {
	return '' !== asosyoloji_get_post_image( $post_id ? $post_id : get_the_ID() );
}

function asosyoloji_register_widget_areas() {
	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar(
			array(
				/* translators: %d: Footer column number. */
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

function asosyoloji_excerpt_length( $_length ) {
	return 28;
}
add_filter( 'excerpt_length', 'asosyoloji_excerpt_length', 999 );

function asosyoloji_body_classes( $classes ) {
	$page_width = get_theme_mod( 'aso_page_content_width', 'wide' );
	if ( ! in_array( $page_width, array( 'narrow', 'wide', 'full' ), true ) ) {
		$page_width = 'wide';
	}
	$classes[] = 'aso-page-width-' . $page_width;

	if ( get_theme_mod( 'aso_sticky_header', true ) ) {
		$classes[] = 'has-sticky-header';
	}

	if ( get_theme_mod( 'aso_enable_motion', true ) ) {
		$classes[] = 'aso-motion-enabled';
		$level     = get_theme_mod( 'aso_motion_level', 'subtle' );
		$classes[] = 'aso-motion-' . ( 'normal' === $level ? 'normal' : 'subtle' );
	}

	return $classes;
}
add_filter( 'body_class', 'asosyoloji_body_classes' );


/**
 * Add an Asosyoloji block category.
 *
 * @param array $categories Existing block categories.
 * @return array
 */
function asosyoloji_block_categories( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'asosyoloji',
			'title' => __( 'Asosyoloji', 'asosyoloji' ),
		)
	);

	return $categories;
}
add_filter( 'block_categories_all', 'asosyoloji_block_categories' );


/**
 * Load admin behavior for theme widgets.
 *
 * @param string $hook Current admin page.
 */
function asosyoloji_widgets_admin_assets( $hook ) {
	if ( 'widgets.php' !== $hook && 'customize.php' !== $hook ) {
		return;
	}

	wp_enqueue_media();

	wp_enqueue_script(
		'asosyoloji-widgets-admin',
		get_template_directory_uri() . '/assets/js/widgets-admin.js',
		array( 'jquery' ),
		ASOSYOLOJI_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'asosyoloji_widgets_admin_assets' );


/**
 * AJAX: load additional homepage latest posts.
 */
function asosyoloji_ajax_load_latest_posts() {
	check_ajax_referer( 'asosyoloji_home_latest', 'nonce' );

	$count    = min( 12, max( 3, absint( $_POST['count'] ?? 6 ) ) );
	$offset   = max( 0, absint( $_POST['offset'] ?? 0 ) );
	$category = absint( $_POST['category'] ?? 0 );
	$orderby  = sanitize_key( wp_unslash( $_POST['orderby'] ?? 'date' ) );
	$order    = strtoupper( sanitize_key( wp_unslash( $_POST['order'] ?? 'DESC' ) ) );

	if ( ! in_array( $orderby, array( 'date', 'modified', 'title', 'menu_order' ), true ) ) {
		$orderby = 'date';
	}

	if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
		$order = 'DESC';
	}

	$excluded_categories = isset( $_POST['excluded_categories'] )
		? array_values( array_filter( array_map( 'absint', (array) $_POST['excluded_categories'] ) ) )
		: array();

	$excluded_posts = isset( $_POST['excluded_posts'] )
		? array_values( array_filter( array_map( 'absint', (array) $_POST['excluded_posts'] ) ) )
		: array();

	$args = array(
		'posts_per_page'   => $count,
		'offset'           => $offset,
		'post_status'      => 'publish',
		'orderby'          => $orderby,
		'order'            => $order,
		'post__not_in'     => $excluded_posts,
		'category__not_in' => $excluded_categories,
	);

	if ( $category ) {
		$args['cat'] = $category;
	}

	$query = new WP_Query( $args );
	ob_start();

	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'template-parts/content', 'card' );
	}

	$html   = ob_get_clean();
	$loaded = $query->post_count;
	$total  = (int) $query->found_posts;
	wp_reset_postdata();

	wp_send_json_success(
		array(
			'html'    => $html,
			'loaded'  => $loaded,
			'hasMore' => ( $offset + $loaded ) < $total,
		)
	);
}
add_action( 'wp_ajax_asosyoloji_load_latest', 'asosyoloji_ajax_load_latest_posts' );
add_action( 'wp_ajax_nopriv_asosyoloji_load_latest', 'asosyoloji_ajax_load_latest_posts' );


function asosyoloji_social_icon( $network ) {
	$icons = array(
		'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg>',
		'x' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4l14 16M19 4L5 20"/></svg>',
		'facebook' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 21v-8h3l.5-3H14V8.3c0-.9.3-1.6 1.7-1.6H18V4.1c-.4-.1-1.6-.1-2.6-.1-2.6 0-4.4 1.6-4.4 4.6V10H8v3h3v8z"/></svg>',
		'youtube' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 8.3a3 3 0 0 0-2.1-2.1C17 5.7 12 5.7 12 5.7s-5 0-6.9.5A3 3 0 0 0 3 8.3 31 31 0 0 0 2.5 12a31 31 0 0 0 .5 3.7 3 3 0 0 0 2.1 2.1c1.9.5 6.9.5 6.9.5s5 0 6.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .5-3.7 31 31 0 0 0-.5-3.7z"/><path d="M10 9l5 3-5 3z" class="aso-social-icon__fill"/></svg>',
		'linkedin' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="9" width="3" height="11"/><circle cx="5.5" cy="5.5" r="1.7"/><path d="M10 20V9h3v1.7c.8-1.2 2-2 3.8-2 3 0 4.2 2 4.2 5V20h-3v-5.6c0-1.8-.6-3-2.3-3-1.8 0-2.7 1.2-2.7 3V20z"/></svg>',
		'mastodon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19.7 7.2c-.3-2.3-2.3-3-2.3-3C16.2 3.6 14.2 3.4 12 3.4s-4.2.2-5.4.8c0 0-2 .7-2.3 3-.2 1.5-.2 3.4.1 5.4.4 2.7 2.5 3.4 4.7 3.7 1 .1 2 .1 3-.1-.1 1-.7 1.7-1.7 2-1.2.4-2.7.2-3.7-.5l-.8 1.8c1.5.8 3.2 1.1 4.9.9 2.9-.3 5.4-1.8 5.8-5.3.2-2 .2-5.8-.9-7.9z"/><path d="M8 8.3v5.4M12 8.3v5.4M16 8.3v5.4"/></svg>',
		'telegram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11.5l17-7-3 15-5-4-3 3 .5-4.5z"/><path d="M9.5 14L17 8"/></svg>',
	);

	return isset( $icons[ $network ] ) ? $icons[ $network ] : '';
}
