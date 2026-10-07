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

function asosyoloji_sanitize_post_id( $value ) {
	$value = absint( $value );
	return ( 0 === $value || 'post' === get_post_type( $value ) ) ? $value : 0;
}

function asosyoloji_sanitize_page_id( $value ) {
	$value = absint( $value );
	return ( 0 === $value || 'page' === get_post_type( $value ) ) ? $value : 0;
}

function asosyoloji_post_choices() {
	$choices = array(
		0 => __( 'Otomatik: en güncel yazı', 'asosyoloji' ),
	);

	$posts = get_posts(
		array(
			'numberposts' => 100,
			'post_status' => 'publish',
			'orderby'     => 'date',
			'order'       => 'DESC',
		)
	);

	foreach ( $posts as $post ) {
		$choices[ $post->ID ] = sprintf(
			'%s — %s',
			wp_strip_all_tags( $post->post_title ),
			get_the_date( 'd.m.Y', $post )
		);
	}

	return $choices;
}

function asosyoloji_category_choices() {
	$choices = array(
		0 => __( 'Tüm kategoriler', 'asosyoloji' ),
	);

	$categories = get_categories(
		array(
			'hide_empty' => false,
		)
	);

	foreach ( $categories as $category ) {
		$choices[ $category->term_id ] = $category->name;
	}

	return $choices;
}

function asosyoloji_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'aso_theme_options',
		array(
			'title'       => __( 'Asosyoloji Tema Ayarları', 'asosyoloji' ),
			'description' => __( 'Tema görünümünü ve ana sayfa yayın akışını kod değiştirmeden yönetin.', 'asosyoloji' ),
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
			'default' => '#8f1d2c',
		),
		'aso_text_color' => array(
			'label'   => __( 'Metin rengi', 'asosyoloji' ),
			'default' => '#111111',
		),
		'aso_muted_color' => array(
			'label'   => __( 'İkincil metin rengi', 'asosyoloji' ),
			'default' => '#666666',
		),
		'aso_border_color' => array(
			'label'   => __( 'Çizgi ve kenarlık rengi', 'asosyoloji' ),
			'default' => '#d8d8d8',
		),
		'aso_background_color' => array(
			'label'   => __( 'Arka plan rengi', 'asosyoloji' ),
			'default' => '#ffffff',
		),
		'aso_surface_color' => array(
			'label'   => __( 'İkincil arka plan rengi', 'asosyoloji' ),
			'default' => '#f5f3ef',
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

	$wp_customize->add_setting(
		'aso_show_search',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'aso_show_search',
		array(
			'label'   => __( 'Üst menüde arama bağlantısını göster', 'asosyoloji' ),
			'section' => 'aso_header',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'aso_logo_width',
		array(
			'default'           => 620,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'aso_logo_width',
		array(
			'label'       => __( 'Logo maksimum genişliği (masaüstü)', 'asosyoloji' ),
			'section'     => 'aso_header',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 180,
				'max'  => 900,
				'step' => 10,
			),
		)
	);

	$wp_customize->add_setting(
		'aso_logo_width_mobile',
		array(
			'default'           => 280,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'aso_logo_width_mobile',
		array(
			'label'       => __( 'Logo maksimum genişliği (mobil)', 'asosyoloji' ),
			'section'     => 'aso_header',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 120,
				'max'  => 420,
				'step' => 10,
			),
		)
	);


	$wp_customize->add_section(
		'aso_footer',
		array(
			'title' => __( 'Alt Alan', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_footer_note',
		array(
			'default'           => __( 'Bağımsız düşünce, kültür ve toplum dergisi.', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'aso_footer_note',
		array(
			'label'   => __( 'Footer kısa notu', 'asosyoloji' ),
			'section' => 'aso_footer',
			'type'    => 'text',
		)
	);

	$wp_customize->add_section(
		'aso_home_hero',
		array(
			'title' => __( 'Ana Sayfa: Öne Çıkan', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_home_show_hero',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'aso_home_show_hero',
		array(
			'label'   => __( 'Öne çıkan alanı göster', 'asosyoloji' ),
			'section' => 'aso_home_hero',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'aso_home_hero_post',
		array(
			'default'           => 0,
			'sanitize_callback' => 'asosyoloji_sanitize_post_id',
		)
	);

	$wp_customize->add_control(
		'aso_home_hero_post',
		array(
			'label'       => __( 'Öne çıkan yazı', 'asosyoloji' ),
			'description' => __( 'Bir yazı seçmezseniz en güncel yazı otomatik gösterilir.', 'asosyoloji' ),
			'section'     => 'aso_home_hero',
			'type'        => 'select',
			'choices'     => asosyoloji_post_choices(),
		)
	);

	$wp_customize->add_setting(
		'aso_home_hero_label',
		array(
			'default'           => __( 'Öne Çıkan', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'aso_home_hero_label',
		array(
			'label'   => __( 'Üst etiket', 'asosyoloji' ),
			'section' => 'aso_home_hero',
			'type'    => 'text',
		)
	);

	$wp_customize->add_section(
		'aso_home_latest',
		array(
			'title' => __( 'Ana Sayfa: Yazı Akışı', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_home_show_latest',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'aso_home_show_latest',
		array(
			'label'   => __( 'Yazı akışı bölümünü göster', 'asosyoloji' ),
			'section' => 'aso_home_latest',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'aso_home_latest_kicker',
		array(
			'default'           => __( 'Güncel', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'aso_home_latest_kicker',
		array(
			'label'   => __( 'Bölüm üst etiketi', 'asosyoloji' ),
			'section' => 'aso_home_latest',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aso_home_latest_title',
		array(
			'default'           => __( 'Son Yazılar', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'aso_home_latest_title',
		array(
			'label'   => __( 'Bölüm başlığı', 'asosyoloji' ),
			'section' => 'aso_home_latest',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aso_home_latest_category',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'aso_home_latest_category',
		array(
			'label'       => __( 'Kategori filtresi', 'asosyoloji' ),
			'description' => __( 'Sadece seçilen kategorideki içerikleri gösterir.', 'asosyoloji' ),
			'section'     => 'aso_home_latest',
			'type'        => 'select',
			'choices'     => asosyoloji_category_choices(),
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
			'label'       => __( 'Gösterilecek içerik sayısı', 'asosyoloji' ),
			'section'     => 'aso_home_latest',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 3,
				'max' => 12,
			),
		)
	);

	$wp_customize->add_setting(
		'aso_home_latest_orderby',
		array(
			'default'           => 'date',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);

	$wp_customize->add_control(
		'aso_home_latest_orderby',
		array(
			'label'   => __( 'Sıralama ölçütü', 'asosyoloji' ),
			'section' => 'aso_home_latest',
			'type'    => 'select',
			'choices' => array(
				'date'       => __( 'Yayın tarihi', 'asosyoloji' ),
				'modified'   => __( 'Son güncellenme', 'asosyoloji' ),
				'title'      => __( 'Başlık', 'asosyoloji' ),
				'rand'       => __( 'Rastgele', 'asosyoloji' ),
				'menu_order' => __( 'Menü sırası', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting(
		'aso_home_latest_order',
		array(
			'default'           => 'DESC',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);

	$wp_customize->add_control(
		'aso_home_latest_order',
		array(
			'label'   => __( 'Sıralama yönü', 'asosyoloji' ),
			'section' => 'aso_home_latest',
			'type'    => 'select',
			'choices' => array(
				'DESC' => __( 'Azalan / en yeni önce', 'asosyoloji' ),
				'ASC'  => __( 'Artan / en eski önce', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_section(
		'aso_home_archive',
		array(
			'title' => __( 'Ana Sayfa: Arşiv Alanı', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
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
			'label'   => __( 'Arşiv alanını göster', 'asosyoloji' ),
			'section' => 'aso_home_archive',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'aso_home_archive_kicker',
		array(
			'default'           => __( 'Geçmişten Bugüne', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'aso_home_archive_kicker',
		array(
			'label'   => __( 'Arşiv üst etiketi', 'asosyoloji' ),
			'section' => 'aso_home_archive',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aso_home_archive_title',
		array(
			'default'           => __( 'Asosyoloji Arşivi', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'aso_home_archive_title',
		array(
			'label'   => __( 'Arşiv başlığı', 'asosyoloji' ),
			'section' => 'aso_home_archive',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aso_home_archive_text',
		array(
			'default'           => __( 'Basılı dergi sayılarına ve PDF arşivine ulaşın.', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);

	$wp_customize->add_control(
		'aso_home_archive_text',
		array(
			'label'   => __( 'Arşiv açıklaması', 'asosyoloji' ),
			'section' => 'aso_home_archive',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'aso_home_archive_page',
		array(
			'default'           => 0,
			'sanitize_callback' => 'asosyoloji_sanitize_page_id',
		)
	);

	$wp_customize->add_control(
		'aso_home_archive_page',
		array(
			'label'       => __( 'Arşiv sayfası', 'asosyoloji' ),
			'description' => __( 'Mevcut basılı PDF arşiv sayfanızı seçin. İçeriğine dokunulmaz.', 'asosyoloji' ),
			'section'     => 'aso_home_archive',
			'type'        => 'dropdown-pages',
		)
	);

	$wp_customize->add_setting(
		'aso_home_archive_button',
		array(
			'default'           => __( 'Arşivi Gör', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'aso_home_archive_button',
		array(
			'label'   => __( 'Buton metni', 'asosyoloji' ),
			'section' => 'aso_home_archive',
			'type'    => 'text',
		)
	);
}
add_action( 'customize_register', 'asosyoloji_customize_register' );
