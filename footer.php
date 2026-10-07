<?php
/**
 * Theme footer.
 *
 * @package Asosyoloji
 */

$footer_columns = (int) get_theme_mod( 'aso_footer_columns', '3' );
$footer_columns = max( 1, min( 4, $footer_columns ) );
$repo_url       = get_theme_mod( 'aso_footer_repo_url', 'https://github.com/m4v3r4/asosyoloji-theme' );
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

		<div class="site-footer__bottom">
			<div class="site-footer__copyright">
				&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
			</div>

			<?php if ( get_theme_mod( 'aso_footer_show_license', true ) ) : ?>
				<div class="site-footer__license">
					<span>
						<?php
						echo esc_html(
							get_theme_mod(
								'aso_footer_license_text',
								__( 'Asosyoloji teması özgür yazılımdır. Tema kodu GPL-3.0-or-later; dokümantasyon ve ayrı görsel materyaller CC BY-SA 4.0 lisansıyla paylaşılır.', 'asosyoloji' )
							)
						);
						?>
					</span>

					<?php if ( $repo_url ) : ?>
						<a href="<?php echo esc_url( $repo_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Kaynak kodu', 'asosyoloji' ); ?>
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
