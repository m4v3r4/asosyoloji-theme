<?php
/**
 * Theme header.
 *
 * @package Asosyoloji
 */
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

	<div class="site-menu-bar">
		<div class="aso-container site-menu-bar__inner">
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
