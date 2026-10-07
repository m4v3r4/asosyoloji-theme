<?php
/**
 * SEO, GEO, breadcrumb and structured data helpers.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_theme_seo_enabled() {
	$mode = get_theme_mod( 'aso_seo_mode', 'auto' );

	if ( 'off' === $mode ) {
		return false;
	}

	if ( 'theme' === $mode ) {
		return true;
	}

	$known_plugin = defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'AIOSEO_VERSION' );

	return ! $known_plugin;
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
	$links   = array();
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

function asosyoloji_author_same_as( $user_id ) {
	return array_values(
		array_filter(
			array_map(
				static function ( $item ) {
					return esc_url_raw( $item['url'] ?? '' );
				},
				asosyoloji_author_social_links( $user_id )
			)
		)
	);
}

function asosyoloji_publisher_same_as() {
	$value = (string) get_theme_mod( 'aso_publisher_same_as', '' );
	$urls  = preg_split( '/\r\n|\r|\n/', $value );

	return array_values(
		array_filter(
			array_map( 'esc_url_raw', array_map( 'trim', $urls ) )
		)
	);
}

function asosyoloji_publisher_logo_url() {
	$custom_logo_id = absint( get_theme_mod( 'custom_logo', 0 ) );

	if ( $custom_logo_id ) {
		$url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
		if ( $url ) {
			return $url;
		}
	}

	return '';
}

function asosyoloji_default_social_image_url() {
	$attachment_id = absint( get_theme_mod( 'aso_default_social_image', 0 ) );

	if ( $attachment_id ) {
		$url = wp_get_attachment_image_url( $attachment_id, 'full' );
		if ( $url ) {
			return $url;
		}
	}

	$publisher_logo = asosyoloji_publisher_logo_url();
	return $publisher_logo ? $publisher_logo : '';
}

function asosyoloji_get_post_image_url( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( has_post_thumbnail( $post_id ) ) {
		$url = wp_get_attachment_image_url( get_post_thumbnail_id( $post_id ), 'full' );
		if ( $url ) {
			return (string) $url;
		}
	}

	$fallback = asosyoloji_get_fallback_image_url( $post_id );
	return $fallback ? $fallback : asosyoloji_default_social_image_url();
}

function asosyoloji_get_seo_description( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$manual  = trim( (string) get_post_meta( $post_id, '_asosyoloji_seo_description', true ) );

	if ( $manual ) {
		return $manual;
	}

	$summary = function_exists( 'asosyoloji_get_geo_summary' ) ? asosyoloji_get_geo_summary( $post_id ) : '';
	if ( $summary ) {
		return $summary;
	}

	if ( has_excerpt( $post_id ) ) {
		return wp_strip_all_tags( get_the_excerpt( $post_id ) );
	}

	return wp_trim_words(
		preg_replace( '/\s+/', ' ', wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) ),
		32,
		'…'
	);
}

function asosyoloji_unicode_word_count( $text ) {
	preg_match_all( '/[\p{L}\p{N}\'’]+/u', wp_strip_all_tags( (string) $text ), $matches );
	return count( $matches[0] );
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
		$items[]  = array(
			'label' => $category->name,
			'url'   => '',
		);
	} elseif ( is_author() ) {
		$author  = get_queried_object();
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

function asosyoloji_current_url() {
	if ( is_singular() ) {
		return get_permalink();
	}

	if ( is_author() ) {
		return get_author_posts_url( get_queried_object_id() );
	}

	if ( is_category() ) {
		return get_category_link( get_queried_object_id() );
	}

	global $wp;
	return home_url( trailingslashit( $wp->request ?? '' ) );
}

function asosyoloji_social_meta() {
	if ( ! asosyoloji_theme_seo_enabled() || is_search() || is_404() ) {
		return;
	}

	$title = wp_get_document_title();
	$url   = asosyoloji_current_url();
	$desc  = get_bloginfo( 'description' );
	$image = asosyoloji_default_social_image_url();
	$type  = 'website';

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$desc    = asosyoloji_get_seo_description( $post_id );
		$image   = asosyoloji_get_post_image_url( $post_id );
		$type    = is_single() ? 'article' : 'website';
	} elseif ( is_author() ) {
		$desc = get_the_author_meta( 'description', get_queried_object_id() );
	} elseif ( is_category() ) {
		$desc = wp_strip_all_tags( category_description( get_queried_object_id() ) );
	}

	if ( ! $desc ) {
		$desc = get_bloginfo( 'description' );
	}
	?>
	<meta name="description" content="<?php echo esc_attr( $desc ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $desc ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( $type ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta property="og:locale" content="tr_TR">
	<meta name="twitter:card" content="<?php echo $image ? 'summary_large_image' : 'summary'; ?>">
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $desc ); ?>">
	<?php if ( $image ) : ?>
		<meta property="og:image" content="<?php echo esc_url( $image ); ?>">
		<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>">
	<?php endif; ?>
	<?php
}
add_action( 'wp_head', 'asosyoloji_social_meta', 20 );

function asosyoloji_schema_graph() {
	if ( ! asosyoloji_theme_seo_enabled() || is_search() || is_404() ) {
		return;
	}

	$home_url      = home_url( '/' );
	$current_url   = asosyoloji_current_url();
	$publisher_id  = $home_url . '#organization';
	$website_id    = $home_url . '#website';
	$webpage_id    = $current_url . '#webpage';
	$publisher     = trim( (string) get_theme_mod( 'aso_publisher_name', get_bloginfo( 'name' ) ) );
	$publisher_desc = trim( (string) get_theme_mod( 'aso_publisher_description', get_bloginfo( 'description' ) ) );
	$logo_url      = asosyoloji_publisher_logo_url();
	$graph         = array();

	$organization = array(
		'@type'       => 'Organization',
		'@id'         => $publisher_id,
		'name'        => $publisher ? $publisher : get_bloginfo( 'name' ),
		'url'         => $home_url,
		'description' => $publisher_desc,
	);

	$same_as = asosyoloji_publisher_same_as();
	if ( $same_as ) {
		$organization['sameAs'] = $same_as;
	}
	if ( $logo_url ) {
		$organization['logo'] = array(
			'@type' => 'ImageObject',
			'url'   => $logo_url,
		);
	}

	$publishing_principles = esc_url_raw( get_theme_mod( 'aso_publishing_principles_url', '' ) );
	if ( $publishing_principles ) {
		$organization['publishingPrinciples'] = $publishing_principles;
	}

	$graph[] = $organization;

	$graph[] = array(
		'@type'      => 'WebSite',
		'@id'        => $website_id,
		'url'        => $home_url,
		'name'       => get_bloginfo( 'name' ),
		'description' => get_bloginfo( 'description' ),
		'inLanguage' => 'tr-TR',
		'publisher'  => array( '@id' => $publisher_id ),
	);

	$page_node = array(
		'@type'      => is_author() ? 'ProfilePage' : 'WebPage',
		'@id'        => $webpage_id,
		'url'        => $current_url,
		'name'       => wp_get_document_title(),
		'isPartOf'   => array( '@id' => $website_id ),
		'inLanguage' => 'tr-TR',
	);

	$breadcrumb_items = asosyoloji_breadcrumb_items();
	if ( count( $breadcrumb_items ) > 1 ) {
		$breadcrumb_id = $current_url . '#breadcrumb';
		$page_node['breadcrumb'] = array( '@id' => $breadcrumb_id );
		$list_items = array();

		foreach ( $breadcrumb_items as $index => $item ) {
			$list_items[] = array(
				'@type'    => 'ListItem',
				'position' => $index + 1,
				'name'     => $item['label'],
				'item'     => $item['url'] ? $item['url'] : $current_url,
			);
		}

		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $breadcrumb_id,
			'itemListElement' => $list_items,
		);
	}

	if ( is_singular() ) {
		$image = asosyoloji_get_post_image_url( get_queried_object_id() );
		if ( $image ) {
			$page_node['primaryImageOfPage'] = array(
				'@type' => 'ImageObject',
				'url'   => $image,
			);
		}
	}
	$page_index = count( $graph );
	$graph[]     = $page_node;

	if ( is_author() ) {
		$author_id  = get_queried_object_id();
		$author_url = get_author_posts_url( $author_id );
		$person_id  = $author_url . '#person';
		$person     = array(
			'@type'       => 'Person',
			'@id'         => $person_id,
			'name'        => get_the_author_meta( 'display_name', $author_id ),
			'url'         => $author_url,
			'description' => get_the_author_meta( 'description', $author_id ),
		);
		$author_same_as = asosyoloji_author_same_as( $author_id );
		if ( $author_same_as ) {
			$person['sameAs'] = $author_same_as;
		}
		$graph[] = $person;
		$graph[ $page_index ]['mainEntity'] = array( '@id' => $person_id );
	}

	if ( is_single() ) {
		$post_id    = get_queried_object_id();
		$author_id  = (int) get_post_field( 'post_author', $post_id );
		$author_url = get_author_posts_url( $author_id );
		$person_id  = $author_url . '#person';
		$categories = wp_get_post_categories( $post_id, array( 'fields' => 'names' ) );
		$tags       = wp_get_post_tags( $post_id, array( 'fields' => 'names' ) );
		$about      = function_exists( 'asosyoloji_get_about_entities' ) ? asosyoloji_get_about_entities( $post_id ) : array();
		$citations  = function_exists( 'asosyoloji_get_citations' ) ? asosyoloji_get_citations( $post_id ) : array();
		$content    = (string) get_post_field( 'post_content', $post_id );
		$image      = asosyoloji_get_post_image_url( $post_id );

		$person = array(
			'@type' => 'Person',
			'@id'   => $person_id,
			'name'  => get_the_author_meta( 'display_name', $author_id ),
			'url'   => $author_url,
		);
		$author_same_as = asosyoloji_author_same_as( $author_id );
		if ( $author_same_as ) {
			$person['sameAs'] = $author_same_as;
		}
		$graph[] = $person;

		$article = array(
			'@type'            => 'Article',
			'@id'              => get_permalink( $post_id ) . '#article',
			'headline'         => get_the_title( $post_id ),
			'description'      => asosyoloji_get_seo_description( $post_id ),
			'datePublished'    => get_the_date( DATE_W3C, $post_id ),
			'dateModified'     => get_the_modified_date( DATE_W3C, $post_id ),
			'mainEntityOfPage' => array( '@id' => $webpage_id ),
			'isPartOf'         => array( '@id' => $website_id ),
			'author'           => array( '@id' => $person_id ),
			'publisher'        => array( '@id' => $publisher_id ),
			'inLanguage'       => 'tr-TR',
			'wordCount'        => asosyoloji_unicode_word_count( $content ),
			'commentCount'     => get_comments_number( $post_id ),
		);

		if ( $image ) {
			$article['image'] = array( $image );
		}
		if ( $categories ) {
			$article['articleSection'] = $categories;
		}
		if ( $tags ) {
			$article['keywords'] = $tags;
		}
		if ( $about ) {
			$article['about'] = array_map(
				static function ( $name ) {
					return array(
						'@type' => 'Thing',
						'name'  => $name,
					);
				},
				$about
			);
		}
		if ( $citations ) {
			$article['citation'] = array_map(
				static function ( $url ) {
					return array(
						'@type' => 'CreativeWork',
						'url'   => $url,
					);
				},
				$citations
			);
		}

		$graph[] = $article;
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}
add_action( 'wp_head', 'asosyoloji_schema_graph', 30 );


function asosyoloji_archive_canonical() {
	if ( ! asosyoloji_theme_seo_enabled() || is_singular() || is_search() || is_404() ) {
		return;
	}

	echo '<link rel="canonical" href="' . esc_url( asosyoloji_current_url() ) . '">' . PHP_EOL;
}
add_action( 'wp_head', 'asosyoloji_archive_canonical', 9 );

function asosyoloji_sitemap_link() {
	if ( ! asosyoloji_theme_seo_enabled() ) {
		return;
	}

	echo '<link rel="sitemap" type="application/xml" href="' . esc_url( home_url( '/wp-sitemap.xml' ) ) . '">' . PHP_EOL;
}
add_action( 'wp_head', 'asosyoloji_sitemap_link', 8 );
