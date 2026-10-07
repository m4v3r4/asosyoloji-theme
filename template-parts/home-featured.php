<?php
/**
 * Homepage featured category section.
 *
 * @package Asosyoloji
 */

if ( ! get_theme_mod( 'aso_home_featured_section_show', true ) ) {
	return;
}

$category = absint( get_theme_mod( 'aso_home_featured_section_category', 0 ) );
$count    = min( 8, max( 2, absint( get_theme_mod( 'aso_home_featured_section_count', 4 ) ) ) );
$layout   = get_theme_mod( 'aso_home_featured_section_layout', 'feature-list' );
$args     = array(
	'posts_per_page' => $count,
	'post_status'    => 'publish',
	'post__not_in'   => ! empty( $GLOBALS['asosyoloji_home_used_post_ids'] ) ? array_map( 'absint', $GLOBALS['asosyoloji_home_used_post_ids'] ) : array(),
);

if ( $category ) {
	$args['cat'] = $category;
}

$query = new WP_Query( $args );

if ( ! $query->have_posts() ) {
	return;
}
?>
<section class="home-section home-featured-section">
	<div class="aso-container">
		<div class="section-heading">
			<div>
				<div class="section-kicker"><?php echo esc_html( get_theme_mod( 'aso_home_featured_section_kicker', __( 'Dosya', 'asosyoloji' ) ) ); ?></div>
				<h2 class="section-title"><?php echo esc_html( get_theme_mod( 'aso_home_featured_section_title', __( 'Seçili Kategoriden', 'asosyoloji' ) ) ); ?></h2>
			</div>
		</div>

		<?php if ( 'grid' === $layout ) : ?>
			<div class="article-grid">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$GLOBALS['asosyoloji_home_used_post_ids'][] = get_the_ID();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
				?>
			</div>
		<?php else : ?>
			<div class="feature-list">
				<?php
				$i = 0;

				while ( $query->have_posts() ) :
					$query->the_post();
					++$i;
					$GLOBALS['asosyoloji_home_used_post_ids'][] = get_the_ID();
					$image = asosyoloji_get_post_image(
						get_the_ID(),
						1 === $i ? 'large' : 'medium_large',
						array( 'class' => 'feature-list__img' )
					);
					?>
					<article <?php post_class( 1 === $i ? 'feature-list__lead' : 'feature-list__item' ); ?>>
						<?php if ( $image ) : ?>
							<a class="feature-list__image" href="<?php the_permalink(); ?>">
								<?php echo wp_kses_post( $image ); ?>
							</a>
						<?php endif; ?>

						<div class="feature-list__content">
							<?php
							$categories = get_the_category();
							if ( $categories ) :
								?>
								<div class="entry-kicker"><?php echo esc_html( $categories[0]->name ); ?></div>
							<?php endif; ?>

							<h3 class="feature-list__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>

							<div class="card-meta"><?php echo esc_html( get_the_author() ); ?> · <?php echo esc_html( get_the_date() ); ?></div>

							<?php if ( 1 === $i ) : ?>
								<div class="feature-list__excerpt"><?php the_excerpt(); ?></div>
							<?php endif; ?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>
	</div>
</section>
