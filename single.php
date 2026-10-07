<?php
/**
 * Single post template.
 *
 * @package Asosyoloji
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
		<header class="entry-header">
			<?php $categories = get_the_category(); ?>
			<?php if ( ! empty( $categories ) ) : ?>
				<div class="entry-kicker"><?php echo esc_html( $categories[0]->name ); ?></div>
			<?php endif; ?>

			<h1 class="entry-title"><?php the_title(); ?></h1>

			<div class="entry-meta">
				<?php echo esc_html( get_the_author() ); ?> ·
				<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="entry-featured-image">
				<?php the_post_thumbnail( 'full' ); ?>
			</figure>
		<?php endif; ?>

		<div class="entry-content">
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

	<div class="aso-container">
		<?php the_post_navigation(); ?>
	</div>
	<?php
endwhile;

get_footer();
