<?php
/**
 * Theme header.
 *
 * @package Asosyoloji
 */

$menu_alignment = get_theme_mod( 'aso_menu_alignment', 'center' );
$menu_width     = get_theme_mod( 'aso_menu_width', 'container' );
$menu_density   = get_theme_mod( 'aso_menu_density', 'normal' );
$menu_active    = get_theme_mod( 'aso_menu_active_style', 'underline' );
$menu_classes   = array(
	'site-menu-bar',
	'aso-menu-align-' . sanitize_html_class( $menu_alignment ),
	'aso-menu-density-' . sanitize_html_class( $menu_density ),
	'aso-menu-active-' . sanitize_html_class( $menu_active ),
);
if ( get_theme_mod( 'aso_menu_separators', false ) ) {
	$menu_classes[] = 'has-separators';
}
if ( get_theme_mod( 'aso_menu_uppercase', true ) ) {
	$menu_classes[] = 'is-uppercase';
}
$has_primary_menu = has_nav_menu( 'primary' );

$social_links     = asosyoloji_social_links_from_settings();
$social_alignment = get_theme_mod( 'aso_social_alignment', 'right' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php if ( ! has_site_icon() ) : ?>
		<link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/favicon.svg' ); ?>" type="image/svg+xml">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'İçeriğe geç', 'asosyoloji' ); ?></a>

<header class="site-header<?php echo get_theme_mod( 'aso_sticky_header', true ) ? ' is-sticky' : ''; ?>">
	<?php if ( get_theme_mod( 'aso_show_social_bar', false ) && $social_links ) : ?>
		<div class="site-social-bar aso-social-align-<?php echo esc_attr( $social_alignment ); ?>">
			<div class="aso-container site-social-bar__inner">
				<nav class="site-social-links" aria-label="<?php esc_attr_e( 'Sosyal medya', 'asosyoloji' ); ?>">
					<?php foreach ( $social_links as $network => $social_link ) : ?>
						<a class="site-social-link site-social-link--<?php echo esc_attr( $network ); ?>" href="<?php echo esc_url( $social_link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
							<span class="screen-reader-text"><?php echo esc_html( $social_link['label'] ); ?></span>
							<?php echo asosyoloji_social_icon( $network ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endforeach; ?>
				</nav>
			</div>
		</div>
	<?php endif; ?>

	<div class="site-masthead">
		<div class="aso-container site-masthead__inner">
			<div class="site-branding">
				<?php echo asosyoloji_site_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in the helper. ?>

				<?php if ( get_theme_mod( 'aso_show_tagline', true ) && get_bloginfo( 'description' ) ) : ?>
					<p class="site-description"><?php bloginfo( 'description' ); ?></p>
				<?php endif; ?>
			</div>

			<div class="site-masthead__actions">
				<?php if ( get_theme_mod( 'aso_show_theme_toggle', true ) ) : ?>
					<button class="header-action theme-toggle" type="button" aria-label="<?php esc_attr_e( 'Karanlık temayı aç/kapat', 'asosyoloji' ); ?>" aria-pressed="false">
						<svg class="theme-toggle__icon theme-toggle__icon--moon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z"/></svg>
						<svg class="theme-toggle__icon theme-toggle__icon--sun" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="4"/><path d="M12 2.5v2.2M12 19.3v2.2M4.6 4.6l1.6 1.6M17.8 17.8l1.6 1.6M2.5 12h2.2M19.3 12h2.2M4.6 19.4l1.6-1.6M17.8 6.2l1.6-1.6"/></svg>
					</button>
				<?php endif; ?>

				<?php if ( get_theme_mod( 'aso_show_search', true ) ) : ?>
					<details class="header-search">
						<summary class="header-action" aria-label="<?php esc_attr_e( 'Ara', 'asosyoloji' ); ?>">
							<svg class="header-search__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
								<circle cx="11" cy="11" r="6.5"></circle>
								<path d="m16 16 4.25 4.25"></path>
							</svg>
						</summary>
						<div class="header-search__panel">
							<?php get_search_form(); ?>
						</div>
					</details>
				<?php endif; ?>

				<?php if ( $has_primary_menu ) : ?>
					<button class="header-action menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu" aria-label="<?php esc_attr_e( 'Menü', 'asosyoloji' ); ?>">
						<svg class="menu-toggle__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
					</button>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php if ( $has_primary_menu ) : ?>
	<div class="<?php echo esc_attr( implode( ' ', $menu_classes ) ); ?>">
		<div class="<?php echo 'full' === $menu_width ? 'site-menu-bar__inner site-menu-bar__inner--full' : 'aso-container site-menu-bar__inner'; ?>">
			<nav class="site-navigation" aria-label="<?php esc_attr_e( 'Ana menü', 'asosyoloji' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'menu_class'     => 'primary-menu',
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		</div>
	</div>
	<?php endif; ?>
</header>

<main id="primary" class="site-main">
