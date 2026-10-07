<?php
/**
 * Homepage editorial slider.
 *
 * @package Asosyoloji
 */

$slider_source   = get_theme_mod( 'aso_home_slider_source', 'latest' );
$slider_category = absint( get_theme_mod( 'aso_home_slider_category', 0 ) );
$slider_count    = min( 10, max( 2, absint( get_theme_mod( 'aso_home_slider_count', 5 ) ) ) );
$slider_layout   = get_theme_mod( 'aso_home_slider_layout', 'split' );
$slider_autoplay = get_theme_mod( 'aso_home_slider_autoplay', true );
$slider_interval = min( 15000, max( 3000, absint( get_theme_mod( 'aso_home_slider_interval', 6000 ) ) ) );
$slider_arrows   = get_theme_mod( 'aso_home_slider_arrows', true );
$slider_dots     = get_theme_mod( 'aso_home_slider_dots', true );
$slider_excerpt  = get_theme_mod( 'aso_home_slider_excerpt', true );

$args = array(
	'posts_per_page'      => $slider_count,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => false,
);

if ( 'category' === $slider_source && $slider_category ) {
	$args['cat'] = $slider_category;
}

$slider_query = new WP_Query( $args );

if ( ! $slider_query->have_posts() ) {
	return;
}

$slider_id = wp_unique_id( 'aso-slider-' );
?>

<section
	id="<?php echo esc_attr( $slider_id ); ?>"
	class="aso-slider aso-slider--<?php echo esc_attr( in_array( $slider_layout, array( 'split', 'overlay' ), true ) ? $slider_layout : 'split' ); ?>"
	data-slider
	data-autoplay="<?php echo $slider_autoplay ? 'true' : 'false'; ?>"
	data-interval="<?php echo esc_attr( $slider_interval ); ?>"
	aria-roledescription="<?php esc_attr_e( 'carousel', 'asosyoloji' ); ?>"
	aria-label="<?php esc_attr_e( 'Öne çıkan yazılar', 'asosyoloji' ); ?>"
>
	<div class="aso-slider__viewport">
		<div class="aso-slider__track" data-slider-track>
			<?php
			$slide_index = 0;
			while ( $slider_query->have_posts() ) :
				$slider_query->the_post();
				$slide_index++;
				$image      = asosyoloji_get_post_image( get_the_ID(), 'large', array( 'class' => 'aso-slider__img' ) );
				$categories = get_the_category();
				?>
				<article
					class="aso-slider__slide<?php echo 1 === $slide_index ? ' is-active' : ''; ?>"
					data-slider-slide
					aria-hidden="<?php echo 1 === $slide_index ? 'false' : 'true'; ?>"
					aria-label="<?php echo esc_attr( sprintf( __( '%1$d / %2$d', 'asosyoloji' ), $slide_index, $slider_query->post_count ) ); ?>"
				>
					<?php if ( $image ) : ?>
						<a class="aso-slider__media" href="<?php the_permalink(); ?>" tabindex="<?php echo 1 === $slide_index ? '0' : '-1'; ?>">
							<?php echo wp_kses_post( $image ); ?>
						</a>
					<?php endif; ?>

					<div class="aso-slider__content">
						<?php if ( ! empty( $categories ) ) : ?>
							<div class="aso-slider__kicker"><?php echo esc_html( $categories[0]->name ); ?></div>
						<?php endif; ?>

						<h2 class="aso-slider__title">
							<a href="<?php the_permalink(); ?>" tabindex="<?php echo 1 === $slide_index ? '0' : '-1'; ?>">
								<?php the_title(); ?>
							</a>
						</h2>

						<div class="aso-slider__meta">
							<span><?php echo esc_html( get_the_author() ); ?></span>
							<span aria-hidden="true"> · </span>
							<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
								<?php echo esc_html( get_the_date() ); ?>
							</time>
						</div>

						<?php if ( $slider_excerpt ) : ?>
							<div class="aso-slider__excerpt"><?php the_excerpt(); ?></div>
						<?php endif; ?>

						<a class="aso-slider__read-more" href="<?php the_permalink(); ?>" tabindex="<?php echo 1 === $slide_index ? '0' : '-1'; ?>">
							<?php esc_html_e( 'Devamını oku', 'asosyoloji' ); ?>
						</a>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>

	<?php if ( $slider_arrows ) : ?>
		<div class="aso-slider__arrows">
			<button class="aso-slider__arrow aso-slider__arrow--prev" type="button" data-slider-prev aria-label="<?php esc_attr_e( 'Önceki slayt', 'asosyoloji' ); ?>">
				<span aria-hidden="true">←</span>
			</button>
			<button class="aso-slider__arrow aso-slider__arrow--next" type="button" data-slider-next aria-label="<?php esc_attr_e( 'Sonraki slayt', 'asosyoloji' ); ?>">
				<span aria-hidden="true">→</span>
			</button>
		</div>
	<?php endif; ?>

	<?php if ( $slider_dots ) : ?>
		<div class="aso-slider__dots" data-slider-dots aria-label="<?php esc_attr_e( 'Slayt seçimi', 'asosyoloji' ); ?>">
			<?php for ( $dot_index = 0; $dot_index < $slider_query->post_count; $dot_index++ ) : ?>
				<button
					class="aso-slider__dot<?php echo 0 === $dot_index ? ' is-active' : ''; ?>"
					type="button"
					data-slider-dot="<?php echo esc_attr( $dot_index ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( '%d. slayta git', 'asosyoloji' ), $dot_index + 1 ) ); ?>"
					aria-current="<?php echo 0 === $dot_index ? 'true' : 'false'; ?>"
				></button>
			<?php endfor; ?>
		</div>
	<?php endif; ?>
</section>
