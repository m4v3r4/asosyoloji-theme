<?php
/**
 * Page template.
 *
 * @package Asosyoloji
 */

get_header();

while ( have_posts() ) :
	the_post();

	$archive_like = has_block( 'gallery' ) || has_block( 'file' ) || has_block( 'buttons' );
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( $archive_like ? 'entry page-entry page-entry--collection' : 'entry page-entry' ); ?>>
		<header class="entry-header">
			<div class="section-kicker"><?php esc_html_e( 'Asosyoloji', 'asosyoloji' ); ?></div>
			<h1 class="entry-title"><?php the_title(); ?></h1>

			<?php if ( has_excerpt() ) : ?>
				<div class="entry-deck"><?php echo esc_html( get_the_excerpt() ); ?></div>
			<?php endif; ?>
		</header>

		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
