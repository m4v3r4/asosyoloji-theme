<?php
/**
 * Category archive template.
 *
 * @package Asosyoloji
 */

get_header();

$category = get_queried_object();
?>

<?php asosyoloji_breadcrumbs(); ?>

<header class="archive-header category-hero">
	<div class="aso-container archive-header__inner">
		<div>
			<div class="section-kicker"><?php esc_html_e( 'Kategori', 'asosyoloji' ); ?></div>
			<h1 class="archive-title"><?php single_cat_title(); ?></h1>
		</div>

		<?php if ( category_description() ) : ?>
			<div class="archive-description"><?php echo wp_kses_post( category_description() ); ?></div>
		<?php endif; ?>
	</div>
</header>

<div class="aso-container archive-layout">
	<?php if ( have_posts() ) : ?>
		<div class="archive-results-meta">
			<span>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: Number of archive items. */
						__( '%s içerik', 'asosyoloji' ),
						number_format_i18n( $GLOBALS['wp_query']->found_posts )
					)
				);
				?>
			</span>
		</div>

		<div class="article-grid archive-grid">
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

<?php get_footer(); ?>
