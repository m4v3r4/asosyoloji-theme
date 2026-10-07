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
				<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
					<?php echo esc_html( get_the_author() ); ?>
				</a>
				<span aria-hidden="true"> · </span>
				<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
			</div>

			<?php if ( has_excerpt() ) : ?>
				<div class="entry-deck"><?php echo esc_html( get_the_excerpt() ); ?></div>
			<?php endif; ?>
		</header>

		<?php $entry_image = asosyoloji_get_post_image( get_the_ID(), 'full', array( 'class' => 'entry-featured-image__img' ) ); ?>
		<?php if ( $entry_image ) : ?>
			<figure class="entry-featured-image">
				<?php echo wp_kses_post( $entry_image ); ?>
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
