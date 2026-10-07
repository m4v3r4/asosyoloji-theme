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
	<?php if ( $post_image ) : ?>
		<a class="article-card__image" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
			<?php echo wp_kses_post( $post_image ); ?>
			<?php if ( $category ) : ?>
				<span class="article-card__category"><?php echo esc_html( $category->name ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<div class="article-card__body">
		<?php if ( ! $post_image && $category ) : ?>
			<div class="article-card__category article-card__category--no-image"><?php echo esc_html( $category->name ); ?></div>
		<?php endif; ?>

		<h2 class="article-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<div class="article-card__excerpt">
			<?php the_excerpt(); ?>
		</div>

		<div class="card-meta">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
				<?php echo esc_html( get_the_author() ); ?>
			</a>
			<span aria-hidden="true"> · </span>
			<?php echo esc_html( get_the_date() ); ?>
		</div>

		<a class="article-card__read-more" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Devamını oku', 'asosyoloji' ); ?>
		</a>
	</div>
</article>
