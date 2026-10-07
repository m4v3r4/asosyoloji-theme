<?php
/**
 * Posts page template.
 *
 * @package Asosyoloji
 */

get_header();
?>
<header class="archive-header">
	<div class="aso-container archive-header__inner">
		<div>
			<div class="section-kicker"><?php esc_html_e( 'Asosyoloji', 'asosyoloji' ); ?></div>
			<h1 class="archive-title"><?php single_post_title(); ?></h1>
		</div>
	</div>
</header>

<div class="aso-container archive-layout">
	<?php if ( have_posts() ) : ?>
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
