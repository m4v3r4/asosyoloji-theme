<?php
/**
 * Homepage editorial controls.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_home_sanitize_checkbox( $checked ) {
	return (bool) $checked;
}

function asosyoloji_home_sanitize_select( $value, $setting ) {
	$control = $setting->manager->get_control( $setting->id );
	if ( ! $control ) {
		return $setting->default;
	}
	return array_key_exists( $value, $control->choices ) ? $value : $setting->default;
}

function asosyoloji_home_post_choices() {
	$choices = array( 0 => __( 'Otomatik: en güncel yazı', 'asosyoloji' ) );

	foreach ( get_posts( array( 'numberposts' => 100, 'post_status' => 'publish' ) ) as $post ) {
		$choices[ $post->ID ] = $post->post_title;
	}

	return $choices;
}

function asosyoloji_home_category_choices() {
	$choices = array( 0 => __( 'Tüm kategoriler', 'asosyoloji' ) );

	foreach ( get_categories( array( 'hide_empty' => false ) ) as $category ) {
		$choices[ $category->term_id ] = $category->name;
	}

	return $choices;
}

function asosyoloji_home_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'aso_home_hero',
		array(
			'title' => __( 'Ana Sayfa: Öne Çıkan', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting( 'aso_home_show_hero', array(
		'default' => true,
		'sanitize_callback' => 'asosyoloji_home_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'aso_home_show_hero', array(
		'label' => __( 'Öne çıkan alanı göster', 'asosyoloji' ),
		'section' => 'aso_home_hero',
		'type' => 'checkbox',
	) );

	$wp_customize->add_setting( 'aso_home_hero_post', array(
		'default' => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'aso_home_hero_post', array(
		'label' => __( 'Öne çıkan yazı', 'asosyoloji' ),
		'description' => __( 'Seçilmezse en güncel yazı gösterilir.', 'asosyoloji' ),
		'section' => 'aso_home_hero',
		'type' => 'select',
		'choices' => asosyoloji_home_post_choices(),
	) );

	$wp_customize->add_setting( 'aso_home_hero_label', array(
		'default' => __( 'Öne Çıkan', 'asosyoloji' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'aso_home_hero_label', array(
		'label' => __( 'Üst etiket', 'asosyoloji' ),
		'section' => 'aso_home_hero',
		'type' => 'text',
	) );

	$wp_customize->add_section(
		'aso_home_latest',
		array(
			'title' => __( 'Ana Sayfa: Yazı Akışı', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting( 'aso_home_show_latest', array(
		'default' => true,
		'sanitize_callback' => 'asosyoloji_home_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'aso_home_show_latest', array(
		'label' => __( 'Yazı akışı bölümünü göster', 'asosyoloji' ),
		'section' => 'aso_home_latest',
		'type' => 'checkbox',
	) );

	$wp_customize->add_setting( 'aso_home_latest_kicker', array(
		'default' => __( 'Güncel', 'asosyoloji' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'aso_home_latest_kicker', array(
		'label' => __( 'Bölüm üst etiketi', 'asosyoloji' ),
		'section' => 'aso_home_latest',
		'type' => 'text',
	) );

	$wp_customize->add_setting( 'aso_home_latest_title', array(
		'default' => __( 'Son Yazılar', 'asosyoloji' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'aso_home_latest_title', array(
		'label' => __( 'Bölüm başlığı', 'asosyoloji' ),
		'section' => 'aso_home_latest',
		'type' => 'text',
	) );

	$wp_customize->add_setting( 'aso_home_latest_category', array(
		'default' => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'aso_home_latest_category', array(
		'label' => __( 'Kategori filtresi', 'asosyoloji' ),
		'section' => 'aso_home_latest',
		'type' => 'select',
		'choices' => asosyoloji_home_category_choices(),
	) );

	$wp_customize->add_setting( 'aso_home_latest_count', array(
		'default' => 6,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'aso_home_latest_count', array(
		'label' => __( 'Gösterilecek içerik sayısı', 'asosyoloji' ),
		'section' => 'aso_home_latest',
		'type' => 'number',
		'input_attrs' => array( 'min' => 3, 'max' => 12 ),
	) );

	$wp_customize->add_setting( 'aso_home_latest_orderby', array(
		'default' => 'date',
		'sanitize_callback' => 'asosyoloji_home_sanitize_select',
	) );
	$wp_customize->add_control( 'aso_home_latest_orderby', array(
		'label' => __( 'Sıralama ölçütü', 'asosyoloji' ),
		'section' => 'aso_home_latest',
		'type' => 'select',
		'choices' => array(
			'date' => __( 'Yayın tarihi', 'asosyoloji' ),
			'modified' => __( 'Son güncellenme', 'asosyoloji' ),
			'title' => __( 'Başlık', 'asosyoloji' ),
			'rand' => __( 'Rastgele', 'asosyoloji' ),
		),
	) );

	$wp_customize->add_setting( 'aso_home_latest_order', array(
		'default' => 'DESC',
		'sanitize_callback' => 'asosyoloji_home_sanitize_select',
	) );
	$wp_customize->add_control( 'aso_home_latest_order', array(
		'label' => __( 'Sıralama yönü', 'asosyoloji' ),
		'section' => 'aso_home_latest',
		'type' => 'select',
		'choices' => array(
			'DESC' => __( 'En yeni önce', 'asosyoloji' ),
			'ASC' => __( 'En eski önce', 'asosyoloji' ),
		),
	) );

	$wp_customize->add_section(
		'aso_home_archive',
		array(
			'title' => __( 'Ana Sayfa: Arşiv Alanı', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting( 'aso_home_archive_kicker', array(
		'default' => __( 'Geçmişten Bugüne', 'asosyoloji' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'aso_home_archive_kicker', array(
		'label' => __( 'Üst etiket', 'asosyoloji' ),
		'section' => 'aso_home_archive',
		'type' => 'text',
	) );

	$wp_customize->add_setting( 'aso_home_archive_title', array(
		'default' => __( 'Asosyoloji Arşivi', 'asosyoloji' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'aso_home_archive_title', array(
		'label' => __( 'Başlık', 'asosyoloji' ),
		'section' => 'aso_home_archive',
		'type' => 'text',
	) );

	$wp_customize->add_setting( 'aso_home_archive_text', array(
		'default' => __( 'Basılı dergi sayılarına ve PDF arşivine ulaşın.', 'asosyoloji' ),
		'sanitize_callback' => 'sanitize_textarea_field',
	) );
	$wp_customize->add_control( 'aso_home_archive_text', array(
		'label' => __( 'Açıklama', 'asosyoloji' ),
		'section' => 'aso_home_archive',
		'type' => 'textarea',
	) );

	$wp_customize->add_setting( 'aso_home_archive_page', array(
		'default' => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'aso_home_archive_page', array(
		'label' => __( 'Mevcut arşiv sayfası', 'asosyoloji' ),
		'description' => __( 'Basılı PDF sayılarını içeren mevcut sayfayı seçin.', 'asosyoloji' ),
		'section' => 'aso_home_archive',
		'type' => 'dropdown-pages',
	) );

	$wp_customize->add_setting( 'aso_home_archive_button', array(
		'default' => __( 'Arşivi Gör', 'asosyoloji' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'aso_home_archive_button', array(
		'label' => __( 'Buton metni', 'asosyoloji' ),
		'section' => 'aso_home_archive',
		'type' => 'text',
	) );
}
add_action( 'customize_register', 'asosyoloji_home_customize_register', 20 );
