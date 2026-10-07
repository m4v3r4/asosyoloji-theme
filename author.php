<?php
/**
 * Author archive template.
 *
 * @package Asosyoloji
 */

get_header();

$author      = get_queried_object();
$avatar      = get_avatar( $author->ID, 240, '', $author->display_name, array( 'class' => 'author-hero__avatar' ) );
$description = get_the_author_meta( 'description', $author->ID );
?>

<?php asosyoloji_breadcrumbs(); ?>

<header class="author-hero">
	<div class="aso-container author-hero__grid">
		<div class="author-hero__media">
			<?php echo wp_kses_post( $avatar ); ?>
		</div>

		<div class="author-hero__content">
			<div class="section-kicker"><?php esc_html_e( 'Yazar', 'asosyoloji' ); ?></div>
			<h1 class="archive-title"><?php echo esc_html( $author->display_name ); ?></h1>

			<?php if ( $description ) : ?>
				<div class="author-hero__bio">
					<?php echo wp_kses_post( wpautop( $description ) ); ?>
				</div>
			<?php endif; ?>

			<?php $author_links = asosyoloji_author_social_links( $author->ID ); ?>
			<?php if ( $author_links ) : ?>
				<div class="author-social-links author-social-links--hero">
					<?php foreach ( $author_links as $author_link ) : ?>
						<a href="<?php echo esc_url( $author_link['url'] ); ?>" target="_blank" rel="me noopener noreferrer"><?php echo esc_html( $author_link['label'] ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="author-hero__count">
				<?php
				echo esc_html(
					sprintf(
						_n( '%s yazı', '%s yazı', count_user_posts( $author->ID ), 'asosyoloji' ),
						number_format_i18n( count_user_posts( $author->ID ) )
					)
				);
				?>
			</div>
		</div>
	</div>
</header>

<section class="aso-container author-posts">
	<div class="section-heading">
		<div>
			<div class="section-kicker"><?php esc_html_e( 'Arşiv', 'asosyoloji' ); ?></div>
			<h2 class="section-title"><?php esc_html_e( 'Yazıları', 'asosyoloji' ); ?></h2>
		</div>
	</div>

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
</section>

<?php get_footer(); ?>
