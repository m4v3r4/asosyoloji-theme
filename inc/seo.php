<?php
/**
 * SEO, breadcrumb and author metadata helpers.
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

function asosyoloji_author_social_links( $user_id ) {
	$links = array();

	$website = get_the_author_meta( 'user_url', $user_id );
	if ( $website ) {
		$links['website'] = array(
			'label' => __( 'Web sitesi', 'asosyoloji' ),
			'url'   => $website,
		);
	}

	foreach (
		array(
			'mastodon' => __( 'Mastodon', 'asosyoloji' ),
			'bluesky'  => __( 'Bluesky', 'asosyoloji' ),
			'linkedin' => __( 'LinkedIn', 'asosyoloji' ),
			'website2' => __( 'Web sitesi 2', 'asosyoloji' ),
		) as $key => $label
	) {
		$url = get_the_author_meta( $key, $user_id );
		if ( $url ) {
			$links[ $key ] = array(
				'label' => $label,
				'url'   => $url,
			);
		}
	}

	return $links;
}

function asosyoloji_get_post_image_url( $post_id = 0 ) {
	$post_id = $post_id ?: get_the_ID();

	if ( has_post_thumbnail( $post_id ) ) {
		return (string) wp_get_attachment_image_url( get_post_thumbnail_id( $post_id ), 'full' );
	}

	return asosyoloji_get_fallback_image_url( $post_id );
}

function asosyoloji_breadcrumb_items() {
	$items = array(
		array(
			'label' => __( 'Ana Sayfa', 'asosyoloji' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( is_single() ) {
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			$items[] = array(
				'label' => $categories[0]->name,
				'url'   => get_category_link( $categories[0] ),
			);
		}
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_category() ) {
		$category = get_queried_object();
		$items[] = array(
			'label' => $category->name,
			'url'   => '',
		);
	} elseif ( is_author() ) {
		$author = get_queried_object();
		$items[] = array(
			'label' => $author->display_name,
			'url'   => '',
		);
	} elseif ( is_page() ) {
		$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
		foreach ( $ancestors as $ancestor_id ) {
			$items[] = array(
				'label' => get_the_title( $ancestor_id ),
				'url'   => get_permalink( $ancestor_id ),
			);
		}
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_archive() ) {
		$items[] = array(
			'label' => wp_strip_all_tags( get_the_archive_title() ),
			'url'   => '',
		);
	}

	return $items;
}

function asosyoloji_breadcrumbs() {
	$items = asosyoloji_breadcrumb_items();

	if ( count( $items ) < 2 ) {
		return;
	}
	?>
	<nav class="breadcrumbs aso-container" aria-label="<?php esc_attr_e( 'İçerik yolu', 'asosyoloji' ); ?>">
		<ol>
			<?php foreach ( $items as $index => $item ) : ?>
				<li>
					<?php if ( $item['url'] && $index < count( $items ) - 1 ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
					<?php else : ?>
						<span aria-current="page"><?php echo esc_html( $item['label'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

function asosyoloji_breadcrumb_schema() {
	if ( is_front_page() ) {
		return;
	}

	$items = asosyoloji_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}

	$schema_items = array();
	foreach ( $items as $index => $item ) {
		$schema_items[] = array(
			'@type'    => 'ListItem',
			'position' => $index + 1,
			'name'     => $item['label'],
			'item'     => $item['url'] ? $item['url'] : ( is_singular() ? get_permalink() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) ) ),
		);
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $schema_items,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}
add_action( 'wp_head', 'asosyoloji_breadcrumb_schema', 29 );

function asosyoloji_social_meta() {
	if ( ! is_singular() ) {
		return;
	}

	$post_id = get_queried_object_id();
	$title   = wp_strip_all_tags( get_the_title( $post_id ) );
	$url     = get_permalink( $post_id );
	$desc    = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 32 );
	$image   = asosyoloji_get_post_image_url( $post_id );
	$type    = is_single() ? 'article' : 'website';
	?>
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $desc ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( $type ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta name="twitter:card" content="<?php echo $image ? 'summary_large_image' : 'summary'; ?>">
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $desc ); ?>">
	<?php if ( $image ) : ?>
		<meta property="og:image" content="<?php echo esc_url( $image ); ?>">
		<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>">
	<?php endif; ?>
	<?php
}
add_action( 'wp_head', 'asosyoloji_social_meta', 28 );

function asosyoloji_article_schema() {
	if ( ! is_single() ) {
		return;
	}

	$post_id   = get_queried_object_id();
	$author_id = (int) get_post_field( 'post_author', $post_id );

	$schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'Article',
		'headline'         => get_the_title( $post_id ),
		'datePublished'    => get_the_date( DATE_W3C, $post_id ),
		'dateModified'     => get_the_modified_date( DATE_W3C, $post_id ),
		'mainEntityOfPage' => get_permalink( $post_id ),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $author_id ),
			'url'   => get_author_posts_url( $author_id ),
		),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
	);

	$image = asosyoloji_get_post_image_url( $post_id );
	if ( $image ) {
		$schema['image'] = $image;
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}
add_action( 'wp_head', 'asosyoloji_article_schema', 30 );
