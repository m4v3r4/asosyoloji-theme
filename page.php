<?php
/**
 * Page template.
 *
 * @package Asosyoloji
 */

get_header();

while ( have_posts() ) :
	the_post();

	$archive_page_id = absint( get_theme_mod( 'aso_home_archive_page', 0 ) );
	$is_archive_page = $archive_page_id && get_the_ID() === $archive_page_id;
	$archive_like    = $is_archive_page || has_block( 'gallery' ) || has_block( 'file' ) || has_block( 'buttons' );
	?>
	<?php asosyoloji_breadcrumbs(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( $archive_like ? 'entry page-entry page-entry--collection' : 'entry page-entry' ); ?>>
		<header class="entry-header">
			<div class="section-kicker"><?php esc_html_e( 'Asosyoloji', 'asosyoloji' ); ?></div>
			<h1 class="entry-title"><?php the_title(); ?></h1>

			<?php if ( has_excerpt() ) : ?>
				<div class="entry-deck"><?php echo esc_html( get_the_excerpt() ); ?></div>
			<?php endif; ?>
		</header>

		<?php if ( $is_archive_page && ( get_theme_mod( 'aso_archive_show_search', true ) || get_theme_mod( 'aso_archive_show_years', true ) ) ) : ?>
			<div class="archive-tools aso-container" data-archive-tools>
				<?php if ( get_theme_mod( 'aso_archive_show_search', true ) ) : ?>
					<label class="archive-tools__search">
						<span class="screen-reader-text"><?php esc_html_e( 'Arşivde ara', 'asosyoloji' ); ?></span>
						<input
							type="search"
							data-archive-search
							placeholder="<?php echo esc_attr( get_theme_mod( 'aso_archive_search_placeholder', __( 'Arşivde ara…', 'asosyoloji' ) ) ); ?>"
						>
					</label>
				<?php endif; ?>

				<?php if ( get_theme_mod( 'aso_archive_show_years', true ) ) : ?>
					<div class="archive-tools__years" data-archive-years aria-label="<?php esc_attr_e( 'Yıla göre filtrele', 'asosyoloji' ); ?>"></div>
				<?php endif; ?>

				<div class="archive-tools__status" data-archive-status aria-live="polite"></div>
			</div>
		<?php endif; ?>

		<div class="entry-content"<?php echo $is_archive_page ? ' data-magazine-archive' : ''; ?>>
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
