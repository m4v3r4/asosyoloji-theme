<?php
/**
 * Homepage section order settings.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_load_sortable_control() {
	require_once get_template_directory() . '/inc/class-asosyoloji-sortable-control.php';
}
add_action( 'customize_register', 'asosyoloji_load_sortable_control', 5 );

function asosyoloji_sanitize_section_order( $value ) {
	$allowed = array( 'slider', 'hero', 'latest', 'featured', 'archive' );
	$items   = array_filter( array_map( 'sanitize_key', explode( ',', (string) $value ) ) );
	$items   = array_values( array_intersect( $items, $allowed ) );

	foreach ( $allowed as $key ) {
		if ( ! in_array( $key, $items, true ) ) {
			$items[] = $key;
		}
	}

	return implode( ',', $items );
}

function asosyoloji_section_order_customize_register( $wp_customize ) {
	if ( ! class_exists( 'Asosyoloji_Sortable_Control' ) ) {
		return;
	}

	$wp_customize->add_section(
		'aso_home_order',
		array(
			'title' => __( 'Ana Sayfa: Bölüm Sırası', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_home_section_order',
		array(
			'default'           => 'slider,hero,latest,featured,archive',
			'sanitize_callback' => 'asosyoloji_sanitize_section_order',
		)
	);

	$wp_customize->add_control(
		new Asosyoloji_Sortable_Control(
			$wp_customize,
			'aso_home_section_order',
			array(
				'label'       => __( 'Ana sayfa bölüm sırası', 'asosyoloji' ),
				'description' => __( 'Bölümleri sürükleyip bırakarak sıralayın.', 'asosyoloji' ),
				'section'     => 'aso_home_order',
				'choices'     => array(
					'slider'   => __( 'Slider', 'asosyoloji' ),
					'hero'     => __( 'Öne Çıkan', 'asosyoloji' ),
					'latest'   => __( 'Son Yazılar', 'asosyoloji' ),
					'featured' => __( 'Kategori Bölümü', 'asosyoloji' ),
					'archive'  => __( 'Arşiv', 'asosyoloji' ),
				),
			)
		)
	);
}
add_action( 'customize_register', 'asosyoloji_section_order_customize_register', 60 );
