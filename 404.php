<?php
/**
 * 404 template.
 *
 * @package Asosyoloji
 */

get_header();
?>

<section class="entry">
	<div class="entry-header">
		<div class="entry-kicker">404</div>
		<h1 class="entry-title"><?php esc_html_e( 'Sayfa bulunamadı.', 'asosyoloji' ); ?></h1>
		<p><?php esc_html_e( 'Aradığınız içerik taşınmış veya kaldırılmış olabilir.', 'asosyoloji' ); ?></p>
		<?php get_search_form(); ?>
	</div>
</section>

<?php
get_footer();
