<?php
/**
 * Archive page enhancement settings.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_archive_tools_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'aso_archive_tools',
		array(
			'title' => __( 'Arşiv / PDF Araçları', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	foreach (
		array(
			'aso_archive_show_search' => __( 'Arşiv içi aramayı göster', 'asosyoloji' ),
			'aso_archive_show_years'  => __( 'Otomatik yıl filtrelerini göster', 'asosyoloji' ),
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => true,
				'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'aso_archive_tools',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'aso_archive_search_placeholder',
		array(
			'default'           => __( 'Arşivde ara…', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'aso_archive_search_placeholder',
		array(
			'label'   => __( 'Arama alanı metni', 'asosyoloji' ),
			'section' => 'aso_archive_tools',
			'type'    => 'text',
		)
	);
}
add_action( 'customize_register', 'asosyoloji_archive_tools_customize_register', 80 );
