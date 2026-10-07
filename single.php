<?php
/**
 * Single post template.
 *
 * @package Asosyoloji
 */

get_header();

while ( have_posts() ) :
	the_post();

	$share_links = asosyoloji_share_links( get_the_ID() );
	$categories  = get_the_category();
	?>
	<?php if ( get_theme_mod( 'aso_show_reading_progress', true ) ) : ?>
		<div class="reading-progress" aria-hidden="true"><span data-reading-progress></span></div>
	<?php endif; ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?> data-reading-article>
		<header class="entry-header">
			<?php if ( ! empty( $categories ) ) : ?>
				<div class="entry-kicker"><?php echo esc_html( $categories[0]->name ); ?></div>
			<?php endif; ?>

			<h1 class="entry-title"><?php the_title(); ?></h1>

			<div class="entry-meta">
				<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
					<?php echo esc_html( get_the_author() ); ?>
				</a>
				<span aria-hidden="true"> · </span>
				<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>

				<?php if ( get_theme_mod( 'aso_show_reading_time', true ) ) : ?>
					<span aria-hidden="true"> · </span>
					<span><?php echo esc_html( asosyoloji_reading_time() ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( has_excerpt() ) : ?>
				<div class="entry-deck"><?php echo esc_html( get_the_excerpt() ); ?></div>
			<?php endif; ?>

			<?php if ( get_theme_mod( 'aso_show_reading_tools', true ) || get_theme_mod( 'aso_show_share', true ) ) : ?>
				<div class="entry-tools">
					<?php if ( get_theme_mod( 'aso_show_reading_tools', true ) ) : ?>
						<div class="reading-tools" aria-label="<?php esc_attr_e( 'Okuma araçları', 'asosyoloji' ); ?>">
							<button type="button" data-font-decrease aria-label="<?php esc_attr_e( 'Yazıyı küçült', 'asosyoloji' ); ?>">A−</button>
							<button type="button" data-font-reset aria-label="<?php esc_attr_e( 'Yazı boyutunu sıfırla', 'asosyoloji' ); ?>">A</button>
							<button type="button" data-font-increase aria-label="<?php esc_attr_e( 'Yazıyı büyüt', 'asosyoloji' ); ?>">A+</button>
						</div>
					<?php endif; ?>

					<?php if ( get_theme_mod( 'aso_show_share', true ) ) : ?>
						<div class="share-tools" aria-label="<?php esc_attr_e( 'Paylaş', 'asosyoloji' ); ?>">
							<?php foreach ( $share_links as $network => $share_url ) : ?>
								<a href="<?php echo esc_url( $share_url ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( ucfirst( $network ) ); ?>
								</a>
							<?php endforeach; ?>
							<button type="button" data-copy-link data-url="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Bağlantıyı kopyala', 'asosyoloji' ); ?></button>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</header>

		<?php $entry_image = asosyoloji_get_post_image( get_the_ID(), 'full', array( 'class' => 'entry-featured-image__img' ) ); ?>
		<?php if ( $entry_image ) : ?>
			<figure class="entry-featured-image"><?php echo wp_kses_post( $entry_image ); ?></figure>
		<?php endif; ?>

		<div class="entry-content" data-reading-content>
			<?php
			the_content();
			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Sayfalar:', 'asosyoloji' ),
					'after'  => '</div>',
				)
			);
			?>
		</div>

		<footer class="entry-footer">
			<?php the_tags( '<p>', ' · ', '</p>' ); ?>
		</footer>
	</article>

	<?php if ( get_theme_mod( 'aso_show_author_box', true ) ) : ?>
		<section class="author-box aso-container">
			<div class="author-box__avatar"><?php echo wp_kses_post( get_avatar( get_the_author_meta( 'ID' ), 120 ) ); ?></div>
			<div class="author-box__content">
				<div class="section-kicker"><?php esc_html_e( 'Yazar', 'asosyoloji' ); ?></div>
				<h2><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo esc_html( get_the_author() ); ?></a></h2>
				<?php if ( get_the_author_meta( 'description' ) ) : ?>
					<p><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<div class="aso-container post-navigation-wrap">
		<?php the_post_navigation(); ?>
	</div>

	<?php if ( comments_open() || get_comments_number() ) : ?>
		<?php comments_template(); ?>
	<?php endif; ?>

	<?php if ( get_theme_mod( 'aso_show_related_posts', true ) ) : ?>
		<?php
		$category_ids = wp_get_post_categories( get_the_ID() );
		$related = new WP_Query(
			array(
				'posts_per_page' => min( 6, max( 2, absint( get_theme_mod( 'aso_related_count', 3 ) ) ) ),
				'post__not_in'   => array( get_the_ID() ),
				'category__in'   => $category_ids,
				'post_status'    => 'publish',
			)
		);
		?>
		<?php if ( $related->have_posts() ) : ?>
			<section class="related-posts home-section">
				<div class="aso-container">
					<div class="section-heading"><div>
						<div class="section-kicker"><?php esc_html_e( 'Devam Et', 'asosyoloji' ); ?></div>
						<h2 class="section-title"><?php esc_html_e( 'Benzer Yazılar', 'asosyoloji' ); ?></h2>
					</div></div>
					<div class="article-grid">
						<?php while ( $related->have_posts() ) : $related->the_post(); get_template_part( 'template-parts/content', 'card' ); endwhile; ?>
					</div>
					<?php wp_reset_postdata(); ?>
				</div>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
