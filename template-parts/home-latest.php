<?php
if ( ! get_theme_mod( 'aso_home_show_latest', true ) ) {
	return;
}
$count    = min( 12, max( 3, absint( get_theme_mod( 'aso_home_latest_count', 6 ) ) ) );
$category = absint( get_theme_mod( 'aso_home_latest_category', 0 ) );
$orderby  = get_theme_mod( 'aso_home_latest_orderby', 'date' );
$order    = get_theme_mod( 'aso_home_latest_order', 'DESC' );
if ( ! in_array( $orderby, array( 'date', 'modified', 'title', 'rand' ), true ) ) $orderby = 'date';
if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) $order = 'DESC';
$args = array(
	'posts_per_page' => $count,
	'post_status'    => 'publish',
	'orderby'        => $orderby,
	'order'          => $order,
	'post__not_in'   => ! empty( $GLOBALS['asosyoloji_home_hero_post_id'] ) ? array( (int) $GLOBALS['asosyoloji_home_hero_post_id'] ) : array(),
);
if ( $category ) $args['cat'] = $category;
$query = new WP_Query( $args );
?>
<section class="home-section">
	<div class="aso-container">
		<div class="section-heading"><div>
			<div class="section-kicker"><?php echo esc_html( get_theme_mod( 'aso_home_latest_kicker', __( 'Güncel', 'asosyoloji' ) ) ); ?></div>
			<h2 class="section-title"><?php echo esc_html( get_theme_mod( 'aso_home_latest_title', __( 'Son Yazılar', 'asosyoloji' ) ) ); ?></h2>
		</div></div>
		<?php if ( $query->have_posts() ) : ?>
			<div class="article-grid">
				<?php while ( $query->have_posts() ) : $query->the_post(); get_template_part( 'template-parts/content', 'card' ); endwhile; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p class="home-section__empty"><?php esc_html_e( 'Bu bölüm için henüz içerik bulunmuyor.', 'asosyoloji' ); ?></p>
		<?php endif; ?>
	</div>
</section>
