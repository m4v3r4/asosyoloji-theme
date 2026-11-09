<?php
/**
 * Asosyoloji theme functions.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ASOSYOLOJI_VERSION', '1.0.0' );

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
require_once get_template_directory() . '/inc/forms.php';
require_once get_template_directory() . '/inc/mobile-app.php';

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

function asosyoloji_public_author_name( $name ) {
	if ( in_array( strtolower( trim( (string) $name ) ), array( 'admin', 'administrator' ), true ) ) {
		return (string) get_theme_mod( 'aso_editorial_author_name', __( 'Asosyoloji Editoryası', 'asosyoloji' ) );
	}

	return $name;
}
add_filter( 'the_author', 'asosyoloji_public_author_name' );

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

/**
 * Preload the two faces used above the fold (headings and body text).
 */
function asosyoloji_preload_fonts() {
	foreach ( array( 'ibmplexserif-700', 'roboto-400' ) as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_template_directory_uri() . '/assets/fonts/' . $font . '.woff2' )
		);
	}
}
add_action( 'wp_head', 'asosyoloji_preload_fonts', 2 );

/**
 * Logo image sources: the Customizer logo (and dark-theme logo) or the bundled Asosyoloji logos.
 *
 * @return array{light: array, dark: array|null} Each entry is [ url, width, height ].
 */
function asosyoloji_logo_sources() {
	static $sources = null;
	if ( null !== $sources ) {
		return $sources;
	}

	$bundled = get_template_directory_uri() . '/assets/images/';
	$source  = static function ( $attachment_id ) {
		$image = $attachment_id ? wp_get_attachment_image_src( $attachment_id, 'full' ) : false;
		return $image ? array( $image[0], (int) $image[1], (int) $image[2] ) : null;
	};

	$light = $source( absint( get_theme_mod( 'custom_logo', 0 ) ) );
	$dark  = $source( absint( get_theme_mod( 'aso_dark_logo', 0 ) ) );

	$light = $light ? $light : array( $bundled . 'logo.png', 1186, 242 );
	// Without a dedicated dark logo, the bundled light-on-dark Asosyoloji logo is used.
	$dark = $dark ? $dark : array( $bundled . 'logo-dark.png', 1186, 242 );

	$sources = array(
		'light'    => $light,
		'dark'     => $dark,
		'on_brand' => $dark,
	);

	return $sources;
}

/**
 * Logo <img> tags (light + optional dark variant), toggled by the colour scheme in CSS.
 *
 * @param string $class Extra class for both images.
 * @param string $alt   Alternative text ('' for decorative use).
 * @return string
 */
function asosyoloji_logo_images( $class = '', $alt = null ) {
	$sources = asosyoloji_logo_sources();
	$alt     = null === $alt ? get_bloginfo( 'name' ) : $alt;
	$img     = static function ( $image, $variant ) use ( $class, $alt ) {
		return sprintf(
			'<img class="%1$s aso-logo-%2$s" src="%3$s" alt="%4$s" width="%5$d" height="%6$d" loading="lazy" decoding="async">',
			esc_attr( trim( $class ) ),
			esc_attr( $variant ),
			esc_url( $image[0] ),
			esc_attr( $alt ),
			$image[1],
			$image[2]
		);
	};

	$html = $img( $sources['light'], 'light' );
	if ( $sources['dark'] ) {
		$html .= $img( $sources['dark'], 'dark' );
	}

	return $html;
}

/**
 * Site logo with a separate dark-theme variant, matching the app.
 *
 * @return string
 */
function asosyoloji_site_logo() {
	$sources = asosyoloji_logo_sources();
	$html    = '<a class="custom-logo-link' . ( $sources['dark'] ? ' has-dark-logo' : '' ) . '" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
	$html   .= str_replace( ' loading="lazy"', '', asosyoloji_logo_images( 'custom-logo' ) );
	$html   .= '</a>';

	return $html;
}

/**
 * The logo drawn for a light-on-brand surface (footer, menu drawer).
 *
 * @param string $class Image class.
 * @return string
 */
function asosyoloji_brand_logo( $class = 'aso-brand-logo' ) {
	$image = asosyoloji_logo_sources()['on_brand'];
	return sprintf(
		'<img class="%1$s" src="%2$s" alt="%3$s" width="%4$d" height="%5$d" loading="lazy" decoding="async">',
		esc_attr( $class ),
		esc_url( $image[0] ),
		esc_attr( get_bloginfo( 'name' ) ),
		$image[1],
		$image[2]
	);
}

function asosyoloji_security_headers() {
	if ( headers_sent() ) {
		return;
	}

	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
	if ( is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=15552000' );
	}
}
add_action( 'send_headers', 'asosyoloji_security_headers' );


function asosyoloji_home_excluded_categories( $specific = array() ) {
	$global   = asosyoloji_sanitize_category_ids( get_theme_mod( 'aso_home_global_exclude_categories', array() ) );
	$specific = asosyoloji_sanitize_category_ids( $specific );

	return array_values( array_unique( array_merge( $global, $specific ) ) );
}

function asosyoloji_normalize_internal_media_url( $url ) {
	$url  = esc_url_raw( $url );
	$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
	$path = (string) wp_parse_url( $url, PHP_URL_PATH );

	if ( $path && in_array( $host, array( 'asosyoloji.com', 'www.asosyoloji.com' ), true ) ) {
		return esc_url_raw( set_url_scheme( $url, 'https' ) );
	}

	return $url;
}

function asosyoloji_get_fallback_image_url( $post_id = 0 ) {
	$post_id   = $post_id ? $post_id : get_the_ID();
	$cache_key = '_asosyoloji_fallback_image_url';
	$cached    = get_post_meta( $post_id, $cache_key, true );

	if ( is_string( $cached ) && '' !== $cached ) {
		return '__none__' === $cached ? '' : asosyoloji_normalize_internal_media_url( $cached );
	}

	$content = (string) get_post_field( 'post_content', $post_id );
	$url     = '';

	if ( $content && preg_match( '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches ) ) {
		$url = asosyoloji_normalize_internal_media_url( $matches[1] );
	}

	// Store a negative result as well, so the content is not re-scanned on every render.
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

function asosyoloji_get_post_image( $post_id = 0, $size = 'medium_large', $attr = array() ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( has_post_thumbnail( $post_id ) ) {
		$thumbnail = get_the_post_thumbnail( $post_id, $size, $attr );
		// A thumbnail ID can point to a deleted attachment; fall through to the fallbacks then.
		if ( $thumbnail ) {
			return $thumbnail;
		}
	}

	$src     = asosyoloji_get_fallback_image_url( $post_id );
	$classes = isset( $attr['class'] ) ? implode( ' ', array_map( 'sanitize_html_class', preg_split( '/\s+/', (string) $attr['class'] ) ) ) : '';

	// No image anywhere in the post: show the site logo, as the app does.
	if ( ! $src ) {
		return asosyoloji_logo_images( $classes . ' is-fallback-image is-logo-fallback', '' );
	}

	$loading  = isset( $attr['loading'] ) && 'eager' === $attr['loading'] ? 'eager' : 'lazy';
	$priority = isset( $attr['fetchpriority'] ) && 'high' === $attr['fetchpriority'] ? ' fetchpriority="high"' : '';

	return sprintf(
		'<img src="%1$s" alt="%2$s" class="%3$s" loading="%4$s" decoding="async"%5$s>',
		esc_url( $src ),
		esc_attr( get_the_title( $post_id ) ),
		esc_attr( $classes ),
		esc_attr( $loading ),
		$priority
	);
}

function asosyoloji_has_post_image( $post_id = 0 ) {
	return '' !== asosyoloji_get_post_image( $post_id ? $post_id : get_the_ID() );
}

/**
 * Whether a page is a listing page (post grid, announcements, calendar, printed issues).
 *
 * Such pages use the full site width like archives instead of the narrow reading column.
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function asosyoloji_is_collection_page( $post_id ) {
	$content = (string) get_post_field( 'post_content', $post_id );

	foreach ( array( 'asosyoloji_posts', 'asosyoloji_duyurular', 'asosyoloji_haftalik', 'asosyoloji_takvim', 'asosyoloji_basili_yayinlar' ) as $shortcode ) {
		if ( has_shortcode( $content, $shortcode ) ) {
			return true;
		}
	}

	foreach ( array( 'asosyoloji/post-list', 'asosyoloji/announcements', 'asosyoloji/print-archive', 'asosyoloji-weekly/list', 'asosyoloji-weekly/calendar' ) as $block ) {
		if ( has_block( $block, $content ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Horizontal category chips, as on the app's home screen.
 *
 * @param int $active_id Highlighted category ID (0 = "Tümü").
 * @return string
 */
function asosyoloji_category_chips( $active_id = 0 ) {
	$categories = get_categories(
		array(
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => 10,
			'hide_empty' => true,
			'exclude'    => asosyoloji_home_excluded_categories(),
		)
	);

	if ( count( $categories ) < 2 ) {
		return '';
	}

	$posts_page = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
	$items      = sprintf(
		'<li><a class="aso-chip%s" href="%s">%s</a></li>',
		$active_id ? '' : ' is-active',
		esc_url( $posts_page ),
		esc_html__( 'Tümü', 'asosyoloji' )
	);

	foreach ( $categories as $category ) {
		$is_active = (int) $category->term_id === (int) $active_id;
		$items    .= sprintf(
			'<li><a class="aso-chip%s" href="%s"%s>%s</a></li>',
			$is_active ? ' is-active' : '',
			esc_url( get_category_link( $category ) ),
			$is_active ? ' aria-current="page"' : '',
			esc_html( $category->name )
		);
	}

	return '<nav class="aso-chips-nav" aria-label="' . esc_attr__( 'Kategoriler', 'asosyoloji' ) . '"><ul class="aso-chips">' . $items . '</ul></nav>';
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

function asosyoloji_social_network_labels() {
	return array(
		'instagram' => 'Instagram',
		'x'         => 'X / Twitter',
		'facebook'  => 'Facebook',
		'youtube'   => 'YouTube',
		'linkedin'  => 'LinkedIn',
		'mastodon'  => 'Mastodon',
		'telegram'  => 'Telegram',
	);
}

function asosyoloji_social_links_from_settings( $prefix = 'aso_social_', $fallback_prefix = '' ) {
	$hosts = array(
		'instagram' => array( 'instagram.com' ),
		'x'         => array( 'x.com', 'twitter.com' ),
		'facebook'  => array( 'facebook.com', 'fb.com' ),
		'youtube'   => array( 'youtube.com', 'youtu.be' ),
		'linkedin'  => array( 'linkedin.com' ),
		'telegram'  => array( 't.me', 'telegram.me' ),
	);
	$links = array();

	foreach ( asosyoloji_social_network_labels() as $network => $label ) {
		$url = esc_url( get_theme_mod( $prefix . $network, '' ) );
		if ( ! $url && $fallback_prefix ) {
			$url = esc_url( get_theme_mod( $fallback_prefix . $network, '' ) );
		}

		$host    = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
		$path    = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
		$allowed = ! isset( $hosts[ $network ] ) || array_filter(
			$hosts[ $network ],
			static function ( $expected_host ) use ( $host ) {
				return $host === $expected_host || str_ends_with( $host, '.' . $expected_host );
			}
		);

		if ( $url && $path && $allowed ) {
			$links[ $network ] = array(
				'url'   => $url,
				'label' => $label,
			);
		}
	}

	return $links;
}
