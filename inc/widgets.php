<?php
/**
 * Theme post listing widget and shortcode.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve category IDs from a comma-separated list of IDs or slugs.
 *
 * @param string|array $value Category identifiers.
 * @return int[]
 */
function asosyoloji_resolve_category_ids( $value ) {
	$items = is_array( $value ) ? $value : explode( ',', (string) $value );
	$ids   = array();

	foreach ( $items as $item ) {
		$item = trim( (string) $item );

		if ( '' === $item ) {
			continue;
		}

		if ( ctype_digit( $item ) ) {
			$ids[] = absint( $item );
			continue;
		}

		$term = get_category_by_slug( sanitize_title( $item ) );
		if ( $term ) {
			$ids[] = (int) $term->term_id;
		}
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Render a themed post collection.
 *
 * @param array $args Collection options.
 * @return string
 */
function asosyoloji_render_post_collection( $args = array() ) {
	$defaults = array(
		'title'              => '',
		'category'           => 0,
		'exclude_categories' => array(),
		'count'              => 5,
		'layout'             => 'list',
		'show_image'         => true,
		'show_excerpt'       => false,
		'show_meta'          => true,
		'heading_level'      => 'h2',
		'author'             => 0,
		'orderby'            => 'date',
		'order'              => 'DESC',
		'date_after'         => '',
		'date_before'        => '',
		'offset'             => 0,
		'include_sticky'     => false,
	);

	$args = wp_parse_args( $args, $defaults );

	$category           = absint( $args['category'] );
	$exclude_categories = asosyoloji_resolve_category_ids( $args['exclude_categories'] );
	$count              = min( 12, max( 1, absint( $args['count'] ) ) );
	$layout             = in_array( $args['layout'], array( 'list', 'compact', 'grid', 'feature' ), true ) ? $args['layout'] : 'list';
	$heading_level      = in_array( $args['heading_level'], array( 'h2', 'h3', 'h4' ), true ) ? $args['heading_level'] : 'h2';
	$author             = absint( $args['author'] );
	$orderby            = in_array( $args['orderby'], array( 'date', 'modified', 'title', 'comment_count', 'rand' ), true ) ? $args['orderby'] : 'date';
	$order              = in_array( strtoupper( (string) $args['order'] ), array( 'ASC', 'DESC' ), true ) ? strtoupper( (string) $args['order'] ) : 'DESC';
	$offset             = min( 100, max( 0, absint( $args['offset'] ) ) );
	$date_after         = sanitize_text_field( $args['date_after'] );
	$date_before        = sanitize_text_field( $args['date_before'] );
	$include_sticky     = (bool) $args['include_sticky'];

	$query_args = array(
		'posts_per_page'    => $count,
		'post_status'       => 'publish',
		'category__not_in'  => $exclude_categories,
		'ignore_sticky_posts' => ! $include_sticky,
		'orderby'             => $orderby,
		'order'               => $order,
		'offset'              => $offset,
	);

	if ( $category ) {
		$query_args['cat'] = $category;
	}

	if ( $author ) {
		$query_args['author'] = $author;
	}

	if ( $date_after || $date_before ) {
		$date_query = array( 'inclusive' => true );
		if ( $date_after ) {
			$date_query['after'] = $date_after;
		}
		if ( $date_before ) {
			$date_query['before'] = $date_before;
		}
		$query_args['date_query'] = array( $date_query );
	}

	$query = new WP_Query( $query_args );

	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<section class="aso-post-widget aso-post-widget--<?php echo esc_attr( $layout ); ?>">
		<?php if ( $args['title'] ) : ?>
			<<?php echo tag_escape( $heading_level ); ?> class="aso-post-widget__heading">
				<?php echo esc_html( $args['title'] ); ?>
			</<?php echo tag_escape( $heading_level ); ?>>
		<?php endif; ?>

		<div class="aso-post-widget__items">
			<?php
			$index = 0;
			while ( $query->have_posts() ) :
				$query->the_post();
				++$index;

				$image = asosyoloji_get_post_image(
					get_the_ID(),
					'medium_large',
					array( 'class' => 'aso-post-widget__img' )
				);
				?>
				<article class="aso-post-widget__item<?php echo ( 'feature' === $layout && 1 === $index ) ? ' is-featured' : ''; ?>">
					<?php if ( $args['show_image'] && $image ) : ?>
						<a class="aso-post-widget__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php echo wp_kses_post( $image ); ?>
						</a>
					<?php endif; ?>

					<div class="aso-post-widget__content">
						<?php
						$categories = get_the_category();
						if ( $categories ) :
							?>
							<div class="entry-kicker"><?php echo esc_html( $categories[0]->name ); ?></div>
						<?php endif; ?>

						<h3 class="aso-post-widget__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>

						<?php if ( $args['show_meta'] ) : ?>
							<div class="card-meta"><?php echo esc_html( get_the_author() ); ?> · <?php echo esc_html( get_the_date() ); ?></div>
						<?php endif; ?>

						<?php if ( $args['show_excerpt'] && ( 'compact' !== $layout || 1 === $index ) ) : ?>
							<div class="aso-post-widget__excerpt"><?php the_excerpt(); ?></div>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
	</section>
	<?php
	wp_reset_postdata();

	return (string) ob_get_clean();
}


/**
 * Render announcement posts with a notice-board presentation.
 *
 * @param array $args Announcement options.
 * @return string
 */
function asosyoloji_render_announcements( $args = array() ) {
	$defaults = array(
		'title'        => __( 'Duyurular', 'asosyoloji' ),
		'category'     => 0,
		'count'        => 5,
		'show_excerpt' => true,
		'show_date'    => true,
		'show_button'  => true,
		'compact'      => false,
	);

	$args = wp_parse_args( $args, $defaults );

	$category = absint( $args['category'] );
	if ( ! $category ) {
		$default_term = get_category_by_slug( 'duyurular' );
		if ( $default_term ) {
			$category = (int) $default_term->term_id;
		}
	}

	$query_args = array(
		'posts_per_page'      => min( 12, max( 1, absint( $args['count'] ) ) ),
		'post_status'         => 'publish',
		'ignore_sticky_posts' => false,
		'orderby'             => 'date',
		'order'               => 'DESC',
	);

	if ( $category ) {
		$query_args['cat'] = $category;
	}

	$query = new WP_Query( $query_args );

	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<section class="aso-announcements<?php echo $args['compact'] ? ' aso-announcements--compact' : ''; ?>">
		<?php if ( $args['title'] ) : ?>
			<div class="aso-announcements__header">
				<span class="aso-announcements__marker" aria-hidden="true"></span>
				<h2 class="aso-announcements__heading"><?php echo esc_html( $args['title'] ); ?></h2>
			</div>
		<?php endif; ?>

		<div class="aso-announcements__list">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				?>
				<article class="aso-announcement">
					<?php if ( $args['show_date'] ) : ?>
						<time class="aso-announcement__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
							<span class="aso-announcement__day"><?php echo esc_html( get_the_date( 'd' ) ); ?></span>
							<span class="aso-announcement__month"><?php echo esc_html( get_the_date( 'M' ) ); ?></span>
						</time>
					<?php endif; ?>

					<div class="aso-announcement__content">
						<div class="aso-announcement__label"><?php esc_html_e( 'Duyuru', 'asosyoloji' ); ?></div>
						<h3 class="aso-announcement__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>

						<?php if ( $args['show_excerpt'] ) : ?>
							<div class="aso-announcement__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></div>
						<?php endif; ?>

						<?php if ( $args['show_button'] ) : ?>
							<a class="aso-announcement__link" href="<?php the_permalink(); ?>">
								<?php esc_html_e( 'Duyuru detayları', 'asosyoloji' ); ?>
							</a>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
	</section>
	<?php
	wp_reset_postdata();

	return (string) ob_get_clean();
}

/**
 * Render magazine archive items.
 *
 * @param array $args Archive options.
 * @return string
 */
function asosyoloji_render_magazine_archive( $args = array() ) {
	$defaults = array(
		'title'  => __( 'Basılı Sayılar', 'asosyoloji' ),
		'items'  => array(),
		'layout' => 'grid',
	);

	$args   = wp_parse_args( $args, $defaults );
	$layout = in_array( $args['layout'], array( 'grid', 'list' ), true ) ? $args['layout'] : 'grid';
	$items  = is_array( $args['items'] ) ? $args['items'] : array();

	$items = array_values(
		array_filter(
			$items,
			static function ( $item ) {
				return ! empty( $item['title'] ) && ! empty( $item['pdf_url'] );
			}
		)
	);

	if ( empty( $items ) ) {
		return '';
	}

	ob_start();
	?>
	<section class="aso-magazine-archive aso-magazine-archive--<?php echo esc_attr( $layout ); ?>">
		<?php if ( $args['title'] ) : ?>
			<div class="aso-magazine-archive__header">
				<div class="section-kicker"><?php esc_html_e( 'Basılı Sayılar', 'asosyoloji' ); ?></div>
				<h2 class="aso-magazine-archive__heading"><?php echo esc_html( $args['title'] ); ?></h2>
			</div>
		<?php endif; ?>

		<div class="aso-magazine-archive__items">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$title     = sanitize_text_field( $item['title'] ?? '' );
				$pdf_url   = esc_url( $item['pdf_url'] ?? '' );
				$cover_url = esc_url( $item['cover_url'] ?? '' );
				?>
				<article class="aso-magazine-card">
					<a class="aso-magazine-card__cover" href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php if ( $cover_url ) : ?>
							<img src="<?php echo esc_url( $cover_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" decoding="async">
						<?php else : ?>
							<span class="aso-magazine-card__placeholder" aria-hidden="true">PDF</span>
						<?php endif; ?>
					</a>

					<div class="aso-magazine-card__content">
						<div class="aso-magazine-card__type">PDF</div>
						<h3 class="aso-magazine-card__title">
							<a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo esc_html( $title ); ?>
							</a>
						</h3>
						<a class="aso-magazine-card__link" href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'PDF dosyasını aç', 'asosyoloji' ); ?>
						</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<?php
	if ( function_exists( 'asosyoloji_theme_seo_enabled' ) && asosyoloji_theme_seo_enabled() ) {
		if ( ! isset( $GLOBALS['asosyoloji_publication_issues'] ) || ! is_array( $GLOBALS['asosyoloji_publication_issues'] ) ) {
			$GLOBALS['asosyoloji_publication_issues'] = array();
		}

		foreach ( $items as $item ) {
			$GLOBALS['asosyoloji_publication_issues'][] = array(
				'title'     => sanitize_text_field( $item['title'] ?? '' ),
				'pdf_url'   => esc_url_raw( $item['pdf_url'] ?? '' ),
				'cover_url' => esc_url_raw( $item['cover_url'] ?? '' ),
			);
		}
	}
	?>

	<?php

	return (string) ob_get_clean();
}

/**
 * Register theme widgets.
 */
function asosyoloji_register_theme_widgets() {
	require_once get_template_directory() . '/inc/class-asosyoloji-post-list-widget.php';
	require_once get_template_directory() . '/inc/class-asosyoloji-announcements-widget.php';
	require_once get_template_directory() . '/inc/class-asosyoloji-magazine-archive-widget.php';
	register_widget( 'Asosyoloji_Post_List_Widget' );
	register_widget( 'Asosyoloji_Announcements_Widget' );
	register_widget( 'Asosyoloji_Magazine_Archive_Widget' );
}
add_action( 'widgets_init', 'asosyoloji_register_theme_widgets' );

/**
 * Page-friendly post list shortcode.
 *
 * Example:
 * [asosyoloji_posts title="Son Yazılar" category="yazilar" exclude="duyurular" count="6" layout="grid"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function asosyoloji_posts_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'title'   => '',
			'category' => '',
			'exclude' => '',
			'count'   => 6,
			'layout'  => 'grid',
			'image'   => '1',
			'excerpt' => '0',
			'meta'    => '1',
			'author'  => '0',
			'orderby' => 'date',
			'order'   => 'DESC',
			'after'   => '',
			'before'  => '',
			'offset'  => '0',
			'sticky'  => '0',
		),
		$atts,
		'asosyoloji_posts'
	);

	$category_ids = asosyoloji_resolve_category_ids( $atts['category'] );
	$category_id  = $category_ids ? $category_ids[0] : 0;

	return asosyoloji_render_post_collection(
		array(
			'title'              => sanitize_text_field( $atts['title'] ),
			'category'           => $category_id,
			'exclude_categories' => asosyoloji_resolve_category_ids( $atts['exclude'] ),
			'count'              => absint( $atts['count'] ),
			'layout'             => sanitize_key( $atts['layout'] ),
			'show_image'         => '1' === (string) $atts['image'],
			'show_excerpt'       => '1' === (string) $atts['excerpt'],
			'show_meta'          => '1' === (string) $atts['meta'],
			'author'             => absint( $atts['author'] ),
			'orderby'            => sanitize_key( $atts['orderby'] ),
			'order'              => sanitize_key( strtoupper( (string) $atts['order'] ) ),
			'date_after'         => sanitize_text_field( $atts['after'] ),
			'date_before'        => sanitize_text_field( $atts['before'] ),
			'offset'             => absint( $atts['offset'] ),
			'include_sticky'     => '1' === (string) $atts['sticky'],
		)
	);
}
add_shortcode( 'asosyoloji_posts', 'asosyoloji_posts_shortcode' );


/**
 * Announcement shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function asosyoloji_announcements_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'title'   => __( 'Duyurular', 'asosyoloji' ),
			'category' => 'duyurular',
			'count'   => 5,
			'excerpt' => '1',
			'date'    => '1',
			'button'  => '1',
			'compact' => '0',
		),
		$atts,
		'asosyoloji_duyurular'
	);

	$category_ids = asosyoloji_resolve_category_ids( $atts['category'] );

	return asosyoloji_render_announcements(
		array(
			'title'        => sanitize_text_field( $atts['title'] ),
			'category'     => $category_ids ? $category_ids[0] : 0,
			'count'        => absint( $atts['count'] ),
			'show_excerpt' => '1' === (string) $atts['excerpt'],
			'show_date'    => '1' === (string) $atts['date'],
			'show_button'  => '1' === (string) $atts['button'],
			'compact'      => '1' === (string) $atts['compact'],
		)
	);
}
add_shortcode( 'asosyoloji_duyurular', 'asosyoloji_announcements_shortcode' );


/**
 * Output PublicationIssue schema collected by magazine archive components.
 */
function asosyoloji_publication_issue_schema_output() {
	if (
		! function_exists( 'asosyoloji_theme_seo_enabled' ) ||
		! asosyoloji_theme_seo_enabled() ||
		empty( $GLOBALS['asosyoloji_publication_issues'] )
	) {
		return;
	}

	$list_items = array();
	foreach ( array_values( $GLOBALS['asosyoloji_publication_issues'] ) as $position => $item ) {
		$issue = array(
			'@type'      => 'PublicationIssue',
			'name'       => $item['title'],
			'url'        => $item['pdf_url'],
			'inLanguage' => 'tr-TR',
			'isPartOf'   => array(
				'@type' => 'Periodical',
				'name'  => get_bloginfo( 'name' ),
				'url'   => home_url( '/' ),
			),
			'publisher'  => array(
				'@id' => home_url( '/' ) . '#organization',
			),
		);

		if ( $item['cover_url'] ) {
			$issue['image'] = $item['cover_url'];
		}

		$list_items[] = array(
			'@type'    => 'ListItem',
			'position' => $position + 1,
			'item'     => $issue,
		);
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'ItemList',
		'name'            => __( 'Basılı Sayılar', 'asosyoloji' ),
		'itemListElement' => $list_items,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}
add_action( 'wp_footer', 'asosyoloji_publication_issue_schema_output', 99 );
