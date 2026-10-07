<?php
/**
 * Article card.
 *
 * @package Asosyoloji
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'article-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="article-card__image" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php the_post_thumbnail( 'medium_large' ); ?>
		</a>
	<?php endif; ?>

	<?php $categories = get_the_category(); ?>
	<?php if ( ! empty( $categories ) ) : ?>
		<div class="entry-kicker"><?php echo esc_html( $categories[0]->name ); ?></div>
	<?php endif; ?>

	<h2 class="article-card__title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h2>

	<div class="card-meta">
		<?php echo esc_html( get_the_author() ); ?> · <?php echo esc_html( get_the_date() ); ?>
	</div>

	<div class="article-card__excerpt">
		<?php the_excerpt(); ?>
	</div>
</article>
