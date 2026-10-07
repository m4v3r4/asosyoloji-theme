<?php
/**
 * Theme Customizer settings.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_sanitize_checkbox( $checked ) {
	return (bool) $checked;
}

function asosyoloji_sanitize_select( $value, $setting ) {
	$control = $setting->manager->get_control( $setting->id );

	if ( ! $control ) {
		return $setting->default;
	}

	return array_key_exists( $value, $control->choices ) ? $value : $setting->default;
}

function asosyoloji_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'aso_theme_options',
		array(
			'title'       => __( 'Asosyoloji Tema Ayarları', 'asosyoloji' ),
			'description' => __( 'Tema görünümünü kod değiştirmeden özelleştirin.', 'asosyoloji' ),
			'priority'    => 30,
		)
	);

	$wp_customize->add_section(
		'aso_colors',
		array(
			'title' => __( 'Renkler', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$colors = array(
		'aso_primary_color' => array(
			'label'   => __( 'Vurgu rengi', 'asosyoloji' ),
			'default' => '#b3212b',
		),
		'aso_text_color' => array(
			'label'   => __( 'Metin rengi', 'asosyoloji' ),
			'default' => '#171717',
		),
		'aso_muted_color' => array(
			'label'   => __( 'İkincil metin rengi', 'asosyoloji' ),
			'default' => '#6d6d6d',
		),
		'aso_border_color' => array(
			'label'   => __( 'Çizgi ve kenarlık rengi', 'asosyoloji' ),
			'default' => '#dedede',
		),
		'aso_background_color' => array(
			'label'   => __( 'Arka plan rengi', 'asosyoloji' ),
			'default' => '#ffffff',
		),
		'aso_surface_color' => array(
			'label'   => __( 'İkincil arka plan rengi', 'asosyoloji' ),
			'default' => '#f6f4f1',
		),
	);

	foreach ( $colors as $setting_id => $args ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'   => $args['label'],
					'section' => 'aso_colors',
				)
			)
		);
	}

	$wp_customize->add_section(
		'aso_typography',
		array(
			'title' => __( 'Tipografi', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_heading_font',
		array(
			'default'           => 'sans',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);

	$wp_customize->add_control(
		'aso_heading_font',
		array(
			'label'   => __( 'Başlık yazı tipi', 'asosyoloji' ),
			'section' => 'aso_typography',
			'type'    => 'select',
			'choices' => array(
				'sans'   => __( 'Modern Sans Serif', 'asosyoloji' ),
				'system' => __( 'Sistem Sans Serif', 'asosyoloji' ),
				'serif'  => __( 'Serif', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting(
		'aso_body_font',
		array(
			'default'           => 'serif',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);

	$wp_customize->add_control(
		'aso_body_font',
		array(
			'label'   => __( 'Yazı gövdesi yazı tipi', 'asosyoloji' ),
			'section' => 'aso_typography',
			'type'    => 'select',
			'choices' => array(
				'serif'  => __( 'Serif', 'asosyoloji' ),
				'sans'   => __( 'Modern Sans Serif', 'asosyoloji' ),
				'system' => __( 'Sistem Sans Serif', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting(
		'aso_body_size',
		array(
			'default'           => 18,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'aso_body_size',
		array(
			'label'       => __( 'Yazı boyutu', 'asosyoloji' ),
			'description' => __( 'Piksel cinsinden genel metin boyutu.', 'asosyoloji' ),
			'section'     => 'aso_typography',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 15,
				'max'  => 24,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_section(
		'aso_layout',
		array(
			'title' => __( 'Yerleşim', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_container_width',
		array(
			'default'           => 1280,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'aso_container_width',
		array(
			'label'       => __( 'Site maksimum genişliği', 'asosyoloji' ),
			'section'     => 'aso_layout',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 960,
				'max'  => 1600,
				'step' => 10,
			),
		)
	);

	$wp_customize->add_setting(
		'aso_article_width',
		array(
			'default'           => 760,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'aso_article_width',
		array(
			'label'       => __( 'Makale metin genişliği', 'asosyoloji' ),
			'section'     => 'aso_layout',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 580,
				'max'  => 980,
				'step' => 10,
			),
		)
	);

	$wp_customize->add_section(
		'aso_header',
		array(
			'title' => __( 'Üst Alan', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_sticky_header',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'aso_sticky_header',
		array(
			'label'   => __( 'Menüyü sayfa kaydırılırken sabit tut', 'asosyoloji' ),
			'section' => 'aso_header',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'aso_show_tagline',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'aso_show_tagline',
		array(
			'label'   => __( 'Site açıklamasını göster', 'asosyoloji' ),
			'section' => 'aso_header',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_section(
		'aso_homepage',
		array(
			'title' => __( 'Ana Sayfa', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_home_latest_count',
		array(
			'default'           => 6,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'aso_home_latest_count',
		array(
			'label'       => __( 'Son yazılar bölümündeki içerik sayısı', 'asosyoloji' ),
			'section'     => 'aso_homepage',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 3,
				'max' => 12,
			),
		)
	);

	$wp_customize->add_setting(
		'aso_home_show_archive',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'aso_home_show_archive',
		array(
			'label'   => __( 'Ana sayfada arşiv bağlantısını göster', 'asosyoloji' ),
			'section' => 'aso_homepage',
			'type'    => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'asosyoloji_customize_register' );
