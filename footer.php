<?php
/**
 * Theme footer.
 *
 * @package Asosyoloji
 */

$footer_columns = (int) get_theme_mod( 'aso_footer_columns', '3' );
$footer_columns = max( 1, min( 4, $footer_columns ) );
$repo_url       = get_theme_mod( 'aso_footer_repo_url', 'https://github.com/m4v3r4/asosyoloji-theme' );
$footer_social_links = asosyoloji_social_links_from_settings( 'aso_footer_social_', 'aso_social_' );
$footer_social_alignment = get_theme_mod( 'aso_footer_social_alignment', 'right' );
if ( ! in_array( $footer_social_alignment, array( 'left', 'center', 'right' ), true ) ) {
	$footer_social_alignment = 'right';
}
$footer_social_title = get_theme_mod( 'aso_footer_social_title', __( 'Bizi takip edin', 'asosyoloji' ) );
$copyright_template = get_theme_mod( 'aso_footer_copyright_text', '© {year} {site_name}' );
$copyright_text     = strtr(
	$copyright_template,
	array(
		'{year}'      => wp_date( 'Y' ),
		'{site_name}' => get_bloginfo( 'name' ),
	)
);
$license_text = get_theme_mod( 'aso_footer_license_text', 'GPL-3.0-or-later' );
$license_url  = get_theme_mod( 'aso_footer_license_url', 'https://www.gnu.org/licenses/gpl-3.0.html' );
$source_label = get_theme_mod( 'aso_footer_source_label', __( 'Tema kaynak kodu', 'asosyoloji' ) );
$has_footer_widgets = has_nav_menu( 'footer' );
for ( $column = 1; $column <= $footer_columns; $column++ ) {
	$has_footer_widgets = $has_footer_widgets || is_active_sidebar( 'footer-' . $column );
}
?>
</main>

<footer class="site-footer">
	<div class="aso-container">
		<?php if ( get_theme_mod( 'aso_footer_show_brand', true ) ) : ?>
			<div class="site-footer__brand-row">
				<div class="site-footer__brand">
					<div class="site-footer__name"><?php bloginfo( 'name' ); ?></div>

					<?php if ( get_bloginfo( 'description' ) ) : ?>
						<p><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
					<?php endif; ?>
				</div>

				<div class="site-footer__note">
					<?php echo esc_html( get_theme_mod( 'aso_footer_note', __( 'Bağımsız düşünce, kültür ve toplum dergisi.', 'asosyoloji' ) ) ); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( get_theme_mod( 'aso_footer_show_social', true ) && $footer_social_links ) : ?>
			<section class="site-footer__social aso-footer-social-align-<?php echo esc_attr( $footer_social_alignment ); ?>" aria-label="<?php esc_attr_e( 'Sosyal medya', 'asosyoloji' ); ?>">
				<?php if ( $footer_social_title ) : ?>
					<h2 class="site-footer__social-title"><?php echo esc_html( $footer_social_title ); ?></h2>
				<?php endif; ?>
				<div class="site-footer__social-links">
					<?php foreach ( $footer_social_links as $network => $social_link ) : ?>
						<a class="site-footer__social-link site-footer__social-link--<?php echo esc_attr( $network ); ?>" href="<?php echo esc_url( $social_link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
							<span class="screen-reader-text"><?php echo esc_html( $social_link['label'] ); ?></span>
							<?php echo asosyoloji_social_icon( $network ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $has_footer_widgets ) : ?>
		<div class="footer-widgets footer-widgets--<?php echo esc_attr( $footer_columns ); ?>">
			<?php for ( $column = 1; $column <= $footer_columns; $column++ ) : ?>
				<div class="footer-widgets__column footer-widgets__column--<?php echo esc_attr( $column ); ?>">
					<?php if ( is_active_sidebar( 'footer-' . $column ) ) : ?>
						<?php dynamic_sidebar( 'footer-' . $column ); ?>
					<?php elseif ( 1 === $column ) : ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'menu_class'     => 'footer-menu',
								'container'      => 'nav',
								'fallback_cb'    => false,
							)
						);
						?>
					<?php endif; ?>
				</div>
			<?php endfor; ?>
		</div>
		<?php endif; ?>

		<div class="site-footer__bottom">
			<div class="site-footer__copyright">
				<?php echo esc_html( $copyright_text ); ?>
			</div>

			<?php if ( get_theme_mod( 'aso_footer_show_license', true ) ) : ?>
				<div class="site-footer__license">
					<?php if ( $license_text ) : ?>
						<?php if ( $license_url ) : ?>
							<a class="site-footer__license-link" href="<?php echo esc_url( $license_url ); ?>" target="_blank" rel="license noopener noreferrer"><?php echo esc_html( $license_text ); ?></a>
						<?php else : ?>
							<span><?php echo esc_html( $license_text ); ?></span>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ( $repo_url ) : ?>
						<?php if ( $license_text ) : ?><span aria-hidden="true"> · </span><?php endif; ?>
						<a href="<?php echo esc_url( $repo_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( $source_label ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
