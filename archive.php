<?php
/**
 * Archive template.
 *
 * @package Asosyoloji
 */

get_header();
?>

<header class="archive-header">
	<div class="aso-container">
		<h1 class="archive-title"><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
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
