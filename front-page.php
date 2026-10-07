<?php
/**
 * Front page template.
 *
 * @package Asosyoloji
 */

get_header();

$hero_post_id = 0;

if ( get_theme_mod( 'aso_home_show_hero', true ) ) :
	$selected_hero = absint( get_theme_mod( 'aso_home_hero_post', 0 ) );

	$hero_args = array(
		'posts_per_page'      => 1,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => false,
	);

	if ( $selected_hero ) {
		$hero_args['p'] = $selected_hero;
	}

	$hero_query = new WP_Query( $hero_args );
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
							<div class="home-hero__label">
								<?php echo esc_html( get_theme_mod( 'aso_home_hero_label', __( 'Öne Çıkan', 'asosyoloji' ) ) ); ?>
							</div>
							<h1 class="home-hero__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h1>
							<div class="home-hero__meta">
								<?php echo esc_html( get_the_author() ); ?> · <?php echo esc_html( get_the_date() ); ?>
							</div>
						</div>

						<?php $hero_image = asosyoloji_get_post_image( get_the_ID(), 'large', array( 'class' => 'home-hero__img' ) ); ?>
						<?php if ( $hero_image ) : ?>
							<a href="<?php the_permalink(); ?>" class="home-hero__image" aria-hidden="true" tabindex="-1">
								<?php echo wp_kses_post( $hero_image ); ?>
							</a>
						<?php else : ?>
							<div class="home-hero__excerpt"><?php the_excerpt(); ?></div>
						<?php endif; ?>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>
<?php endif; ?>

<?php if ( get_theme_mod( 'aso_home_show_latest', true ) ) : ?>
	<?php
	$latest_count    = min( 12, max( 3, absint( get_theme_mod( 'aso_home_latest_count', 6 ) ) ) );
	$latest_category = absint( get_theme_mod( 'aso_home_latest_category', 0 ) );
	$latest_orderby  = get_theme_mod( 'aso_home_latest_orderby', 'date' );
	$latest_order    = get_theme_mod( 'aso_home_latest_order', 'DESC' );

	$allowed_orderby = array( 'date', 'modified', 'title', 'rand' );
	if ( ! in_array( $latest_orderby, $allowed_orderby, true ) ) {
		$latest_orderby = 'date';
	}

	if ( ! in_array( $latest_order, array( 'ASC', 'DESC' ), true ) ) {
		$latest_order = 'DESC';
	}

	$latest_args = array(
		'posts_per_page' => $latest_count,
		'post__not_in'   => $hero_post_id ? array( $hero_post_id ) : array(),
		'post_status'    => 'publish',
		'orderby'        => $latest_orderby,
		'order'          => $latest_order,
	);

	if ( $latest_category ) {
		$latest_args['cat'] = $latest_category;
	}

	$latest_query = new WP_Query( $latest_args );
	?>
	<section class="home-section">
		<div class="aso-container">
			<div class="section-heading">
				<div>
					<div class="section-kicker">
						<?php echo esc_html( get_theme_mod( 'aso_home_latest_kicker', __( 'Güncel', 'asosyoloji' ) ) ); ?>
					</div>
					<h2 class="section-title">
						<?php echo esc_html( get_theme_mod( 'aso_home_latest_title', __( 'Son Yazılar', 'asosyoloji' ) ) ); ?>
					</h2>
				</div>
			</div>

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
			<?php else : ?>
				<p class="home-section__empty"><?php esc_html_e( 'Bu bölüm için henüz içerik bulunmuyor.', 'asosyoloji' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>


<?php if ( get_theme_mod( 'aso_home_featured_section_show', true ) ) : ?>
	<?php
	$feature_category = absint( get_theme_mod( 'aso_home_featured_section_category', 0 ) );
	$feature_count    = min( 8, max( 2, absint( get_theme_mod( 'aso_home_featured_section_count', 4 ) ) ) );
	$feature_layout   = get_theme_mod( 'aso_home_featured_section_layout', 'feature-list' );

	$feature_args = array(
		'posts_per_page' => $feature_count,
		'post_status'    => 'publish',
		'post__not_in'   => $hero_post_id ? array( $hero_post_id ) : array(),
	);

	if ( $feature_category ) {
		$feature_args['cat'] = $feature_category;
	}

	$feature_query = new WP_Query( $feature_args );
	?>
	<?php if ( $feature_query->have_posts() ) : ?>
		<section class="home-section home-featured-section">
			<div class="aso-container">
				<div class="section-heading">
					<div>
						<div class="section-kicker">
							<?php echo esc_html( get_theme_mod( 'aso_home_featured_section_kicker', __( 'Dosya', 'asosyoloji' ) ) ); ?>
						</div>
						<h2 class="section-title">
							<?php echo esc_html( get_theme_mod( 'aso_home_featured_section_title', __( 'Seçili Kategoriden', 'asosyoloji' ) ) ); ?>
						</h2>
					</div>
				</div>

				<?php if ( 'grid' === $feature_layout ) : ?>
					<div class="article-grid">
						<?php
						while ( $feature_query->have_posts() ) :
							$feature_query->the_post();
							get_template_part( 'template-parts/content', 'card' );
						endwhile;
						?>
					</div>
				<?php else : ?>
					<div class="feature-list">
						<?php
						$feature_index = 0;
						while ( $feature_query->have_posts() ) :
							$feature_query->the_post();
							$feature_index++;
							?>
							<article <?php post_class( 1 === $feature_index ? 'feature-list__lead' : 'feature-list__item' ); ?>>
								<?php $feature_image = asosyoloji_get_post_image( get_the_ID(), 1 === $feature_index ? 'large' : 'medium_large', array( 'class' => 'feature-list__img' ) ); ?>
								<?php if ( $feature_image ) : ?>
									<a class="feature-list__image" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
										<?php echo wp_kses_post( $feature_image ); ?>
									</a>
								<?php endif; ?>

								<div class="feature-list__content">
									<?php $feature_categories = get_the_category(); ?>
									<?php if ( ! empty( $feature_categories ) ) : ?>
										<div class="entry-kicker"><?php echo esc_html( $feature_categories[0]->name ); ?></div>
									<?php endif; ?>

									<h3 class="feature-list__title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h3>

									<div class="card-meta">
										<?php echo esc_html( get_the_author() ); ?> · <?php echo esc_html( get_the_date() ); ?>
									</div>

									<?php if ( 1 === $feature_index ) : ?>
										<div class="feature-list__excerpt"><?php the_excerpt(); ?></div>
									<?php endif; ?>
								</div>
							</article>
							<?php
						endwhile;
						?>
					</div>
				<?php endif; ?>

				<?php wp_reset_postdata(); ?>
			</div>
		</section>
	<?php endif; ?>
<?php endif; ?>

<?php if ( get_theme_mod( 'aso_home_show_archive', true ) ) : ?>
	<?php
	$archive_page_id = absint( get_theme_mod( 'aso_home_archive_page', 0 ) );
	$archive_url     = $archive_page_id ? get_permalink( $archive_page_id ) : '';
	?>
	<section class="home-section home-archive">
		<div class="aso-container">
			<div class="section-kicker">
				<?php echo esc_html( get_theme_mod( 'aso_home_archive_kicker', __( 'Geçmişten Bugüne', 'asosyoloji' ) ) ); ?>
			</div>
			<div class="home-archive__grid">
				<div>
					<h2 class="section-title">
						<?php echo esc_html( get_theme_mod( 'aso_home_archive_title', __( 'Asosyoloji Arşivi', 'asosyoloji' ) ) ); ?>
					</h2>
					<p class="home-archive__text">
						<?php echo esc_html( get_theme_mod( 'aso_home_archive_text', __( 'Basılı dergi sayılarına ve PDF arşivine ulaşın.', 'asosyoloji' ) ) ); ?>
					</p>
				</div>

				<?php if ( $archive_url ) : ?>
					<a class="aso-button" href="<?php echo esc_url( $archive_url ); ?>">
						<?php echo esc_html( get_theme_mod( 'aso_home_archive_button', __( 'Arşivi Gör', 'asosyoloji' ) ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
