<?php
/**
 * Homepage slider Customizer controls.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_slider_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'aso_home_slider',
		array(
			'title' => __( 'Ana Sayfa: Slider', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_home_slider_show',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'aso_home_slider_show',
		array(
			'label'   => __( 'Sliderı göster', 'asosyoloji' ),
			'section' => 'aso_home_slider',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'aso_home_slider_source',
		array(
			'default'           => 'latest',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'aso_home_slider_source',
		array(
			'label'   => __( 'İçerik kaynağı', 'asosyoloji' ),
			'section' => 'aso_home_slider',
			'type'    => 'select',
			'choices' => array(
				'latest'   => __( 'Son yazılar', 'asosyoloji' ),
				'category' => __( 'Belirli kategori', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting(
		'aso_home_slider_category',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'aso_home_slider_category',
		array(
			'label'       => __( 'Slider kategorisi', 'asosyoloji' ),
			'description' => __( 'Kaynak “Belirli kategori” ise kullanılır.', 'asosyoloji' ),
			'section'     => 'aso_home_slider',
			'type'        => 'select',
			'choices'     => asosyoloji_category_choices(),
		)
	);

	$wp_customize->add_setting(
		'aso_home_slider_exclude_categories',
		array(
			'default'           => array(),
			'sanitize_callback' => 'asosyoloji_sanitize_category_ids',
		)
	);

	$wp_customize->add_control(
		new Asosyoloji_Multi_Select_Control(
			$wp_customize,
			'aso_home_slider_exclude_categories',
			array(
				'label'       => __( 'Hariç tutulacak kategoriler', 'asosyoloji' ),
				'description' => __( 'Bu bölümde görünmesini istemediğiniz kategorileri seçin. Ctrl/Cmd ile birden fazla seçim yapabilirsiniz.', 'asosyoloji' ),
				'section'     => 'aso_home_slider',
				'choices'     => array_filter( asosyoloji_category_choices(), 'is_int', ARRAY_FILTER_USE_KEY ),
			)
		)
	);


	$wp_customize->add_setting(
		'aso_home_slider_count',
		array(
			'default'           => 5,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'aso_home_slider_count',
		array(
			'label'       => __( 'Slayt sayısı', 'asosyoloji' ),
			'section'     => 'aso_home_slider',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 2,
				'max'  => 10,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_setting(
		'aso_home_slider_layout',
		array(
			'default'           => 'split',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'aso_home_slider_layout',
		array(
			'label'   => __( 'Slider görünümü', 'asosyoloji' ),
			'section' => 'aso_home_slider',
			'type'    => 'select',
			'choices' => array(
				'split'   => __( 'Metin + görsel bölünmüş', 'asosyoloji' ),
				'overlay' => __( 'Görsel üzerinde metin', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting(
		'aso_home_slider_autoplay',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'aso_home_slider_autoplay',
		array(
			'label'   => __( 'Otomatik geçiş', 'asosyoloji' ),
			'section' => 'aso_home_slider',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'aso_home_slider_interval',
		array(
			'default'           => 6000,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'aso_home_slider_interval',
		array(
			'label'       => __( 'Geçiş süresi (ms)', 'asosyoloji' ),
			'section'     => 'aso_home_slider',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 3000,
				'max'  => 15000,
				'step' => 500,
			),
		)
	);

	foreach (
		array(
			'aso_home_slider_arrows' => __( 'Önceki / sonraki oklarını göster', 'asosyoloji' ),
			'aso_home_slider_dots'   => __( 'Slayt göstergelerini göster', 'asosyoloji' ),
			'aso_home_slider_excerpt' => __( 'Özet metnini göster', 'asosyoloji' ),
		) as $setting_id => $label
	) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => true,
				'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => 'aso_home_slider',
				'type'    => 'checkbox',
			)
		);
	}
}
add_action( 'customize_register', 'asosyoloji_slider_customize_register', 35 );
