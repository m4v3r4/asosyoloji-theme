<?php
/**
 * Footer builder controls.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_footer_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'aso_footer_builder',
		array(
			'title' => __( 'Footer Builder', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_footer_columns',
		array(
			'default'           => '3',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);

	$wp_customize->add_control(
		'aso_footer_columns',
		array(
			'label'       => __( 'Footer sütun sayısı', 'asosyoloji' ),
			'description' => __( 'Sütun içeriklerini Görünüm → Bileşenler ekranından yönetebilirsiniz.', 'asosyoloji' ),
			'section'     => 'aso_footer_builder',
			'type'        => 'select',
			'choices'     => array(
				'1' => __( '1 sütun', 'asosyoloji' ),
				'2' => __( '2 sütun', 'asosyoloji' ),
				'3' => __( '3 sütun', 'asosyoloji' ),
				'4' => __( '4 sütun', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting(
		'aso_footer_show_brand',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'aso_footer_show_brand',
		array(
			'label'   => __( 'Footer marka alanını göster', 'asosyoloji' ),
			'section' => 'aso_footer_builder',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'aso_footer_show_license',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'aso_footer_show_license',
		array(
			'label'   => __( 'Özgür yazılım/lisans bilgisini göster', 'asosyoloji' ),
			'section' => 'aso_footer_builder',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'aso_footer_repo_url',
		array(
			'default'           => 'https://github.com/m4v3r4/asosyoloji-theme',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'aso_footer_repo_url',
		array(
			'label'       => __( 'Tema kaynak kodu / GitHub adresi', 'asosyoloji' ),
			'description' => __( 'Repo herkese açıldığında footer bağlantısı doğrudan buraya gider.', 'asosyoloji' ),
			'section'     => 'aso_footer_builder',
			'type'        => 'url',
		)
	);

	$wp_customize->add_setting(
		'aso_footer_license_text',
		array(
			'default'           => __( 'Asosyoloji teması özgür yazılımdır. Tema kodu GPL-3.0-or-later; dokümantasyon ve ayrı görsel materyaller CC BY-SA 4.0 lisansıyla paylaşılır.', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);

	$wp_customize->add_control(
		'aso_footer_license_text',
		array(
			'label'   => __( 'Lisans açıklaması', 'asosyoloji' ),
			'section' => 'aso_footer_builder',
			'type'    => 'textarea',
		)
	);
}
add_action( 'customize_register', 'asosyoloji_footer_customize_register', 50 );
