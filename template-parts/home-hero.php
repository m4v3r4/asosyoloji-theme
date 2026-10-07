<?php
/**
 * Homepage hero section.
 *
 * @package Asosyoloji
 */

if ( ! get_theme_mod( 'aso_home_show_hero', true ) ) {
	return;
}

$selected_hero       = absint( get_theme_mod( 'aso_home_hero_post', 0 ) );
$excluded_categories = asosyoloji_home_excluded_categories();
$used_ids      = ! empty( $GLOBALS['asosyoloji_home_used_post_ids'] ) ? array_map( 'absint', $GLOBALS['asosyoloji_home_used_post_ids'] ) : array();
$args          = array(
	'posts_per_page'      => 1,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => false,
	'post__not_in'        => $used_ids,
	'category__not_in'   => $excluded_categories,
);

if ( $selected_hero && ! in_array( $selected_hero, $used_ids, true ) && ! has_category( $excluded_categories, $selected_hero ) ) {
	$args['p'] = $selected_hero;
}

$query = new WP_Query( $args );

if ( ! $query->have_posts() ) {
	return;
}
?>
<section class="home-hero">
	<div class="aso-container">
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();

			$GLOBALS['asosyoloji_home_hero_post_id']     = get_the_ID();
			$GLOBALS['asosyoloji_home_used_post_ids'][] = get_the_ID();

			$image = asosyoloji_get_post_image(
				get_the_ID(),
				'large',
				array( 'class' => 'home-hero__img' )
			);
			?>
			<div class="home-hero__grid">
				<div>
					<div class="home-hero__label"><?php echo esc_html( get_theme_mod( 'aso_home_hero_label', __( 'Öne Çıkan', 'asosyoloji' ) ) ); ?></div>
					<h1 class="home-hero__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h1>
					<div class="home-hero__meta"><?php echo esc_html( get_the_author() ); ?> · <?php echo esc_html( get_the_date() ); ?></div>
				</div>

				<?php if ( $image ) : ?>
					<a href="<?php the_permalink(); ?>" class="home-hero__image" aria-hidden="true" tabindex="-1">
						<?php echo wp_kses_post( $image ); ?>
					</a>
				<?php else : ?>
					<div class="home-hero__excerpt"><?php the_excerpt(); ?></div>
				<?php endif; ?>
			</div>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</section>
