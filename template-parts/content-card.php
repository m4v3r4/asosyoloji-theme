<?php
/**
 * Article card.
 *
 * @package Asosyoloji
 */

$post_image = asosyoloji_get_post_image( get_the_ID(), 'medium_large', array( 'class' => 'article-card__img' ) );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'article-card' ); ?>>
	<?php if ( $post_image ) : ?>
		<a class="article-card__image" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php echo wp_kses_post( $post_image ); ?>
		</a>
	<?php endif; ?>

	<div class="article-card__body">
		<?php $categories = get_the_category(); ?>
		<?php if ( ! empty( $categories ) ) : ?>
			<div class="entry-kicker"><?php echo esc_html( $categories[0]->name ); ?></div>
		<?php endif; ?>

		<h2 class="article-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<div class="card-meta">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
				<?php echo esc_html( get_the_author() ); ?>
			</a>
			<span aria-hidden="true"> · </span>
			<?php echo esc_html( get_the_date() ); ?>
		</div>

		<div class="article-card__excerpt">
			<?php the_excerpt(); ?>
		</div>

		<a class="article-card__read-more" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Devamını oku', 'asosyoloji' ); ?>
		</a>
	</div>
</article>
