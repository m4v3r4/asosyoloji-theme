<?php
/**
 * Theme footer.
 *
 * @package Asosyoloji
 */
?>
</main>

<footer class="site-footer">
	<div class="aso-container site-footer__inner">
		<div>
			<strong><?php bloginfo( 'name' ); ?></strong>
			<p><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
		</div>

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

		<small>
			&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
		</small>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
