<?php
/**
 * Homepage latest posts section.
 *
 * @package Asosyoloji
 */

if ( ! get_theme_mod( 'aso_home_show_latest', true ) ) {
	return;
}

$count          = min( 12, max( 3, absint( get_theme_mod( 'aso_home_latest_count', 6 ) ) ) );
$category       = absint( get_theme_mod( 'aso_home_latest_category', 0 ) );
$excluded_categories = asosyoloji_home_excluded_categories( get_theme_mod( 'aso_home_latest_exclude_categories', array() ) );
$latest_orderby = get_theme_mod( 'aso_home_latest_orderby', 'date' );
$latest_order   = get_theme_mod( 'aso_home_latest_order', 'DESC' );
$load_mode      = get_theme_mod( 'aso_home_latest_load_mode', 'button' );
$load_count     = min( 12, max( 3, absint( get_theme_mod( 'aso_home_latest_load_count', 6 ) ) ) );

if ( ! in_array( $latest_orderby, array( 'date', 'modified', 'title', 'rand', 'menu_order' ), true ) ) {
	$latest_orderby = 'date';
}

if ( ! in_array( $latest_order, array( 'ASC', 'DESC' ), true ) ) {
	$latest_order = 'DESC';
}

$args = array(
	'posts_per_page' => $count,
	'post_status'    => 'publish',
	'orderby'        => $latest_orderby,
	'order'          => $latest_order,
	'post__not_in'      => ! empty( $GLOBALS['asosyoloji_home_used_post_ids'] ) ? array_map( 'absint', $GLOBALS['asosyoloji_home_used_post_ids'] ) : array(),
	'category__not_in'   => $excluded_categories,
);

if ( $category ) {
	$args['cat'] = $category;
}

$query = new WP_Query( $args );
?>
<section
	class="home-section"
	data-home-latest
	data-load-mode="<?php echo esc_attr( in_array( $load_mode, array( 'none', 'button', 'infinite' ), true ) ? $load_mode : 'button' ); ?>"
	data-load-count="<?php echo esc_attr( $load_count ); ?>"
	data-category="<?php echo esc_attr( $category ); ?>"
	data-orderby="<?php echo esc_attr( $latest_orderby ); ?>"
	data-order="<?php echo esc_attr( $latest_order ); ?>"
	data-excluded-categories="<?php echo esc_attr( implode( ',', $excluded_categories ) ); ?>"
>
	<div class="aso-container">
		<div class="section-heading">
			<div>
				<div class="section-kicker"><?php echo esc_html( get_theme_mod( 'aso_home_latest_kicker', __( 'Güncel', 'asosyoloji' ) ) ); ?></div>
				<h2 class="section-title"><?php echo esc_html( get_theme_mod( 'aso_home_latest_title', __( 'Son Yazılar', 'asosyoloji' ) ) ); ?></h2>
			</div>
		</div>

		<?php if ( $query->have_posts() ) : ?>
			<div class="article-grid" data-home-latest-grid>
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$GLOBALS['asosyoloji_home_used_post_ids'][] = get_the_ID();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
				?>
			</div>
			<?php wp_reset_postdata(); ?>
			<?php
			$initial_loaded = $query->post_count;
			$has_more       = $initial_loaded < (int) $query->found_posts;
			?>
			<?php if ( 'none' !== $load_mode && $has_more ) : ?>
				<div class="home-latest-loader" data-home-latest-controls>
					<button class="aso-button home-latest-loader__button" type="button" data-home-latest-button>
						<?php esc_html_e( 'Daha fazla yükle', 'asosyoloji' ); ?>
					</button>
					<div class="home-latest-loader__status" data-home-latest-status aria-live="polite"></div>
					<div class="home-latest-loader__sentinel" data-home-latest-sentinel aria-hidden="true"></div>
				</div>
			<?php endif; ?>

		<?php else : ?>
			<p class="home-section__empty"><?php esc_html_e( 'Bu bölüm için henüz içerik bulunmuyor.', 'asosyoloji' ); ?></p>
		<?php endif; ?>
	</div>
</section>
