<?php
/**
 * Search results template: matching authors first, then posts.
 *
 * @package Asosyoloji
 */

get_header();

$author_results = asosyoloji_search_authors_markup( get_search_query( false ) );
?>

<header class="archive-header">
	<div class="aso-container archive-header__inner">
		<div>
			<h1 class="archive-title">
				<?php
				/* translators: %s: Search query. */
				printf( esc_html__( '“%s” için sonuçlar', 'asosyoloji' ), esc_html( get_search_query() ) );
				?>
			</h1>
		</div>
		<div class="search-page-form"><?php get_search_form(); ?></div>
	</div>
</header>

<div class="aso-container posts-list">
	<?php echo $author_results; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in the helper. ?>

	<?php if ( have_posts() ) : ?>
		<?php if ( $author_results ) : ?>
			<h2 class="search-authors__title"><?php esc_html_e( 'Yazılar', 'asosyoloji' ); ?></h2>
		<?php endif; ?>
		<div class="article-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'card' );
			endwhile;
			?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php elseif ( ! $author_results ) : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</div>

<?php
get_footer();
