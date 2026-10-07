<?php
if ( ! get_theme_mod( 'aso_home_show_archive', true ) ) {
	return;
}
$page_id = absint( get_theme_mod( 'aso_home_archive_page', 0 ) );
$url = $page_id ? get_permalink( $page_id ) : '';
?>
<section class="home-section home-archive">
	<div class="aso-container">
		<div class="section-kicker"><?php echo esc_html( get_theme_mod( 'aso_home_archive_kicker', __( 'Geçmişten Bugüne', 'asosyoloji' ) ) ); ?></div>
		<div class="home-archive__grid">
			<div>
				<h2 class="section-title"><?php echo esc_html( get_theme_mod( 'aso_home_archive_title', __( 'Asosyoloji Arşivi', 'asosyoloji' ) ) ); ?></h2>
				<p class="home-archive__text"><?php echo esc_html( get_theme_mod( 'aso_home_archive_text', __( 'Basılı dergi sayılarına ve PDF arşivine ulaşın.', 'asosyoloji' ) ) ); ?></p>
			</div>
			<?php if ( $url ) : ?><a class="aso-button" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( get_theme_mod( 'aso_home_archive_button', __( 'Arşivi Gör', 'asosyoloji' ) ) ); ?></a><?php endif; ?>
		</div>
	</div>
</section>
