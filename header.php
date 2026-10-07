<?php
/**
 * Theme header.
 *
 * @package Asosyoloji
 */

$menu_alignment = get_theme_mod( 'aso_menu_alignment', 'center' );
$menu_width     = get_theme_mod( 'aso_menu_width', 'container' );
$menu_density   = get_theme_mod( 'aso_menu_density', 'normal' );
$menu_bg        = get_theme_mod( 'aso_menu_background', 'accent' );
$menu_active    = get_theme_mod( 'aso_menu_active_style', 'underline' );
$menu_classes   = array(
	'site-menu-bar',
	'aso-menu-align-' . sanitize_html_class( $menu_alignment ),
	'aso-menu-width-' . sanitize_html_class( $menu_width ),
	'aso-menu-density-' . sanitize_html_class( $menu_density ),
	'aso-menu-bg-' . sanitize_html_class( $menu_bg ),
	'aso-menu-active-' . sanitize_html_class( $menu_active ),
);
if ( get_theme_mod( 'aso_menu_separators', true ) ) {
	$menu_classes[] = 'has-separators';
}
if ( get_theme_mod( 'aso_menu_uppercase', true ) ) {
	$menu_classes[] = 'is-uppercase';
}

$social_links = array();
foreach ( array( 'instagram', 'x', 'facebook', 'youtube', 'linkedin', 'mastodon', 'telegram' ) as $network ) {
	$url = esc_url( get_theme_mod( 'aso_social_' . $network, '' ) );
	if ( $url ) {
		$social_links[ $network ] = $url;
	}
}
$social_alignment = get_theme_mod( 'aso_social_alignment', 'right' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'İçeriğe geç', 'asosyoloji' ); ?></a>

<header class="site-header<?php echo get_theme_mod( 'aso_sticky_header', true ) ? ' is-sticky' : ''; ?>">
	<?php if ( get_theme_mod( 'aso_show_social_bar', true ) && $social_links ) : ?>
		<div class="site-social-bar aso-social-align-<?php echo esc_attr( $social_alignment ); ?>">
			<div class="aso-container site-social-bar__inner">
				<div class="site-social-links" aria-label="<?php esc_attr_e( 'Sosyal medya', 'asosyoloji' ); ?>">
					<?php foreach ( $social_links as $network => $url ) : ?>
						<a class="site-social-link site-social-link--<?php echo esc_attr( $network ); ?>" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
							<span class="screen-reader-text"><?php echo esc_html( ucfirst( $network ) ); ?></span>
							<?php echo asosyoloji_social_icon( $network ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="site-masthead">
		<div class="aso-container site-masthead__inner">
			<div class="site-branding">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<p class="site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
					</p>
				<?php endif; ?>

				<?php if ( get_theme_mod( 'aso_show_tagline', true ) && get_bloginfo( 'description' ) ) : ?>
					<p class="site-description"><?php bloginfo( 'description' ); ?></p>
				<?php endif; ?>
			</div>

			<div class="site-masthead__actions">
				<?php if ( get_theme_mod( 'aso_show_theme_toggle', true ) ) : ?>
					<button class="theme-toggle" type="button" aria-label="<?php esc_attr_e( 'Tema değiştir', 'asosyoloji' ); ?>" title="<?php esc_attr_e( 'Aydınlık / karanlık tema', 'asosyoloji' ); ?>">
						<span class="theme-toggle__icon" aria-hidden="true">◐</span>
					</button>
				<?php endif; ?>

				<?php if ( get_theme_mod( 'aso_show_search', true ) ) : ?>
					<details class="header-search">
						<summary><?php esc_html_e( 'Ara', 'asosyoloji' ); ?></summary>
						<div class="header-search__panel">
							<?php get_search_form(); ?>
						</div>
					</details>
				<?php endif; ?>

				<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu">
					<?php esc_html_e( 'Menü', 'asosyoloji' ); ?>
				</button>
			</div>
		</div>
	</div>

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
</header>

<main id="primary" class="site-main">
