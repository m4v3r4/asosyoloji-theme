<?php
/**
 * Search results template.
 *
 * @package Asosyoloji
 */

get_header();
?>

<header class="archive-header">
	<div class="aso-container">
		<div class="section-kicker"><?php esc_html_e( 'Arama', 'asosyoloji' ); ?></div>
		<h1 class="archive-title">
			<?php
			/* translators: %s: Search query. */
			printf( esc_html__( '“%s” için sonuçlar', 'asosyoloji' ), esc_html( get_search_query() ) );
			?>
		</h1>
	</div>
</header>

<div class="aso-container posts-list">
	<?php if ( have_posts() ) : ?>
		<div class="article-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'card' );
			endwhile;
			?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</div>

<?php
get_footer();
