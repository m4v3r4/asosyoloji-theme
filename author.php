<?php
/**
 * Author archive template.
 *
 * @package Asosyoloji
 */

get_header();

$author = get_queried_object();
?>

<header class="archive-header">
	<div class="aso-container">
		<div class="section-kicker"><?php esc_html_e( 'Yazar', 'asosyoloji' ); ?></div>
		<h1 class="archive-title"><?php echo esc_html( $author->display_name ); ?></h1>
		<?php if ( get_the_author_meta( 'description', $author->ID ) ) : ?>
			<div class="archive-description">
				<?php echo wp_kses_post( wpautop( get_the_author_meta( 'description', $author->ID ) ) ); ?>
			</div>
		<?php endif; ?>
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
	<?php endif; ?>
</div>

<?php
get_footer();
