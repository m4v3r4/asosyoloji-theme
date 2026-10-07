<?php
/**
 * Theme footer.
 *
 * @package Asosyoloji
 */
?>
</main>

<footer class="site-footer">
	<div class="aso-container">
		<div class="site-footer__top">
			<div class="site-footer__brand">
				<div class="site-footer__name"><?php bloginfo( 'name' ); ?></div>
				<?php if ( get_bloginfo( 'description' ) ) : ?>
					<p><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
				<?php endif; ?>
			</div>

			<div class="site-footer__nav">
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
			</div>

			<div class="site-footer__meta">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ?: home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Yazılar', 'asosyoloji' ); ?>
				</a>
				<a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>">
					<?php esc_html_e( 'Arama', 'asosyoloji' ); ?>
				</a>
			</div>
		</div>

		<div class="site-footer__bottom">
			<small>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></small>
			<small><?php echo esc_html( get_theme_mod( 'aso_footer_note', __( 'Bağımsız düşünce, kültür ve toplum dergisi.', 'asosyoloji' ) ) ); ?></small>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
