<?php
/**
 * Front page template.
 *
 * @package Asosyoloji
 */

get_header();

$GLOBALS['asosyoloji_home_hero_post_id'] = 0;
$GLOBALS['asosyoloji_home_used_post_ids'] = array();

$section_order = get_theme_mod( 'aso_home_section_order', 'slider,hero,latest,featured,archive' );
$sections = array_filter( array_map( 'sanitize_key', explode( ',', $section_order ) ) );

foreach ( $sections as $section ) {
	switch ( $section ) {
		case 'slider':
			if ( get_theme_mod( 'aso_home_slider_show', true ) ) {
				get_template_part( 'template-parts/home', 'slider' );
			}
			break;

		case 'hero':
			get_template_part( 'template-parts/home', 'hero' );
			break;

		case 'latest':
			get_template_part( 'template-parts/home', 'latest' );
			break;

		case 'featured':
			get_template_part( 'template-parts/home', 'featured' );
			break;

		case 'archive':
			get_template_part( 'template-parts/home', 'archive' );
			break;
	}
}

get_footer();
