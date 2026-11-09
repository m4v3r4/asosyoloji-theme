<?php
/**
 * Article card.
 *
 * @package Asosyoloji
 */

$post_image = asosyoloji_get_post_image( get_the_ID(), 'medium_large', array( 'class' => 'article-card__img' ) );
$categories = get_the_category();
$category   = ! empty( $categories ) ? $categories[0] : null;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'article-card' ); ?>>
	<a class="article-card__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php echo wp_kses_post( $post_image ); ?>
	</a>

	<div class="article-card__body">
		<?php if ( $category ) : ?>
			<a class="article-card__category" href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
		<?php endif; ?>

		<h2 class="article-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<div class="article-card__excerpt">
			<?php the_excerpt(); ?>
		</div>

		<div class="card-meta">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo esc_html( get_the_author() ); ?></a>
			<span aria-hidden="true"> · </span>
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</div>
	</div>
</article>
