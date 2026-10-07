<?php
/**
 * Front page template.
 *
 * @package Asosyoloji
 */

get_header();

$hero_query = new WP_Query(
	array(
		'posts_per_page'      => 1,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => false,
	)
);

$hero_post_id = 0;
?>

<?php if ( $hero_query->have_posts() ) : ?>
	<section class="home-hero">
		<div class="aso-container">
			<?php
			while ( $hero_query->have_posts() ) :
				$hero_query->the_post();
				$hero_post_id = get_the_ID();
				?>
				<div class="home-hero__grid">
					<div>
						<div class="home-hero__label"><?php esc_html_e( 'Öne Çıkan', 'asosyoloji' ); ?></div>
						<h1 class="home-hero__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h1>
						<div class="home-hero__meta">
							<?php echo esc_html( get_the_author() ); ?> · <?php echo esc_html( get_the_date() ); ?>
						</div>
					</div>

					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" class="home-hero__image" aria-hidden="true" tabindex="-1">
							<?php the_post_thumbnail( 'large' ); ?>
						</a>
					<?php else : ?>
						<div><?php the_excerpt(); ?></div>
					<?php endif; ?>
				</div>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</section>
<?php endif; ?>

<section class="home-section">
	<div class="aso-container">
		<div class="section-heading">
			<div>
				<div class="section-kicker"><?php esc_html_e( 'Güncel', 'asosyoloji' ); ?></div>
				<h2 class="section-title"><?php esc_html_e( 'Son Yazılar', 'asosyoloji' ); ?></h2>
			</div>
		</div>

		<?php
		$latest_count = min( 12, max( 3, absint( get_theme_mod( 'aso_home_latest_count', 6 ) ) ) );
		$latest_query = new WP_Query(
			array(
				'posts_per_page' => $latest_count,
				'post__not_in'   => $hero_post_id ? array( $hero_post_id ) : array(),
				'post_status'    => 'publish',
			)
		);
		?>

		<?php if ( $latest_query->have_posts() ) : ?>
			<div class="article-grid">
				<?php
				while ( $latest_query->have_posts() ) :
					$latest_query->the_post();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php if ( get_theme_mod( 'aso_home_show_archive', true ) ) : ?>
	<section class="home-section">
		<div class="aso-container">
			<div class="section-kicker"><?php esc_html_e( 'Geçmişten Bugüne', 'asosyoloji' ); ?></div>
			<h2 class="section-title"><?php esc_html_e( 'Asosyoloji Arşivi', 'asosyoloji' ); ?></h2>
			<p><?php esc_html_e( 'Basılı dergi sayıları ve mevcut PDF arşivi korunarak yeni tema içerisinde sunulacaktır.', 'asosyoloji' ); ?></p>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
