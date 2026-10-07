<?php
/**
 * Additional homepage section controls.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_section_category_choices() {
	$choices = array( 0 => __( 'Kategori seçin', 'asosyoloji' ) );

	foreach ( get_categories( array( 'hide_empty' => false ) ) as $category ) {
		$choices[ $category->term_id ] = $category->name;
	}

	return $choices;
}

function asosyoloji_section_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'aso_home_featured_section',
		array(
			'title' => __( 'Ana Sayfa: Kategori Bölümü', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_home_featured_section_show',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'aso_home_featured_section_show',
		array(
			'label'   => __( 'Kategori bölümünü göster', 'asosyoloji' ),
			'section' => 'aso_home_featured_section',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'aso_home_featured_section_category',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'aso_home_featured_section_category',
		array(
			'label'       => __( 'Gösterilecek kategori', 'asosyoloji' ),
			'description' => __( 'Söyleşi, Çeviri, Yazı gibi istediğiniz kategoriyi seçebilirsiniz.', 'asosyoloji' ),
			'section'     => 'aso_home_featured_section',
			'type'        => 'select',
			'choices'     => asosyoloji_section_category_choices(),
		)
	);

	$wp_customize->add_setting(
		'aso_home_featured_section_exclude_categories',
		array(
			'default'           => array(),
			'sanitize_callback' => 'asosyoloji_sanitize_category_ids',
		)
	);

	$wp_customize->add_control(
		new Asosyoloji_Multi_Select_Control(
			$wp_customize,
			'aso_home_featured_section_exclude_categories',
			array(
				'label'       => __( 'Hariç tutulacak kategoriler', 'asosyoloji' ),
				'description' => __( 'Bu bölümde görünmesini istemediğiniz kategorileri seçin. Ctrl/Cmd ile birden fazla seçim yapabilirsiniz.', 'asosyoloji' ),
				'section'     => 'aso_home_featured_section',
				'choices'     => array_filter( asosyoloji_category_choices(), 'is_int', ARRAY_FILTER_USE_KEY ),
			)
		)
	);

	$wp_customize->add_setting(
		'aso_home_featured_section_kicker',
		array(
			'default'           => __( 'Dosya', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'aso_home_featured_section_kicker',
		array(
			'label'   => __( 'Üst etiket', 'asosyoloji' ),
			'section' => 'aso_home_featured_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aso_home_featured_section_title',
		array(
			'default'           => __( 'Seçili Kategoriden', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'aso_home_featured_section_title',
		array(
			'label'   => __( 'Bölüm başlığı', 'asosyoloji' ),
			'section' => 'aso_home_featured_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aso_home_featured_section_count',
		array(
			'default'           => 4,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'aso_home_featured_section_count',
		array(
			'label'       => __( 'İçerik sayısı', 'asosyoloji' ),
			'section'     => 'aso_home_featured_section',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 2,
				'max' => 8,
			),
		)
	);

	$wp_customize->add_setting(
		'aso_home_featured_section_layout',
		array(
			'default'           => 'feature-list',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'aso_home_featured_section_layout',
		array(
			'label'   => __( 'Bölüm düzeni', 'asosyoloji' ),
			'section' => 'aso_home_featured_section',
			'type'    => 'select',
			'choices' => array(
				'feature-list' => __( 'Bir büyük + yan liste', 'asosyoloji' ),
				'grid'         => __( 'Eşit kartlar', 'asosyoloji' ),
			),
		)
	);
}
add_action( 'customize_register', 'asosyoloji_section_customize_register', 30 );
