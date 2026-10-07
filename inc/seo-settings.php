<?php
/**
 * SEO/GEO Customizer settings.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_seo_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'aso_seo_geo',
		array(
			'title' => __( 'SEO / GEO', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_seo_mode',
		array(
			'default'           => 'auto',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);

	$wp_customize->add_control(
		'aso_seo_mode',
		array(
			'label'       => __( 'Tema SEO çıktısı', 'asosyoloji' ),
			'description' => __( 'Otomatik mod, bilinen büyük SEO eklentileri aktifse tema meta/schema çıktısını devre dışı bırakır.', 'asosyoloji' ),
			'section'     => 'aso_seo_geo',
			'type'        => 'select',
			'choices'     => array(
				'auto'  => __( 'Otomatik', 'asosyoloji' ),
				'theme' => __( 'Her zaman tema', 'asosyoloji' ),
				'off'   => __( 'Kapalı', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting(
		'aso_publisher_name',
		array(
			'default'           => get_bloginfo( 'name' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'aso_publisher_name',
		array(
			'label'   => __( 'Yayıncı adı', 'asosyoloji' ),
			'section' => 'aso_seo_geo',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aso_publisher_description',
		array(
			'default'           => get_bloginfo( 'description' ),
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);

	$wp_customize->add_control(
		'aso_publisher_description',
		array(
			'label'   => __( 'Yayıncı açıklaması', 'asosyoloji' ),
			'section' => 'aso_seo_geo',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'aso_publisher_same_as',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);

	$wp_customize->add_control(
		'aso_publisher_same_as',
		array(
			'label'       => __( 'Yayıncı sosyal / profil URL’leri', 'asosyoloji' ),
			'description' => __( 'Her satıra bir tam URL girin.', 'asosyoloji' ),
			'section'     => 'aso_seo_geo',
			'type'        => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'aso_default_social_image',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'aso_default_social_image',
			array(
				'label'       => __( 'Varsayılan sosyal paylaşım görseli', 'asosyoloji' ),
				'description' => __( 'Yazıda öne çıkan veya içerik görseli yoksa kullanılır.', 'asosyoloji' ),
				'section'     => 'aso_seo_geo',
				'mime_type'   => 'image',
			)
		)
	);

	foreach (
		array(
			'aso_show_auto_toc'    => __( 'Uzun yazılarda otomatik İçindekiler oluştur', 'asosyoloji' ),
			'aso_show_geo_summary' => __( 'SEO/GEO özet ve “Bu yazıda” kutusunu göster', 'asosyoloji' ),
			'aso_show_citations'   => __( 'Kaynaklar bölümünü yazı sonunda göster', 'asosyoloji' ),
			'aso_indexnow_enabled' => __( 'IndexNow bildirimlerini etkinleştir', 'asosyoloji' ),
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => 'aso_indexnow_enabled' === $id ? false : true,
				'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'aso_seo_geo',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'aso_indexnow_key',
		array(
			'default'           => '',
			'sanitize_callback' => 'asosyoloji_sanitize_indexnow_key',
		)
	);

	$wp_customize->add_control(
		'aso_indexnow_key',
		array(
			'label'       => __( 'IndexNow anahtarı', 'asosyoloji' ),
			'description' => __( 'IndexNow etkinse kullanılır. Tema anahtar doğrulama dosyasını sanal olarak sunar.', 'asosyoloji' ),
			'section'     => 'aso_seo_geo',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'asosyoloji_seo_customize_register', 95 );

function asosyoloji_sanitize_indexnow_key( $value ) {
	return substr( preg_replace( '/[^A-Za-z0-9\-]/', '', (string) $value ), 0, 128 );
}
