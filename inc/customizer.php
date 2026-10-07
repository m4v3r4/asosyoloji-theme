<?php
/**
 * Theme Customizer settings.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_load_multi_select_control() {
	if ( class_exists( 'WP_Customize_Control' ) ) {
		require_once get_template_directory() . '/inc/class-asosyoloji-multi-select-control.php';
	}
}
add_action( 'customize_register', 'asosyoloji_load_multi_select_control', 1 );

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

function asosyoloji_sanitize_category_ids( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}

	return array_values( array_filter( array_map( 'absint', $value ) ) );
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

	$wp_customize->add_setting(
		'aso_page_content_width',
		array(
			'default'           => 'wide',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);

	$wp_customize->add_control(
		'aso_page_content_width',
		array(
			'label'       => __( 'Sayfa içerik genişliği', 'asosyoloji' ),
			'description' => __( 'Dar: makale genişliği, Geniş: site container genişliği, Tam: ekran genişliği.', 'asosyoloji' ),
			'section'     => 'aso_layout',
			'type'        => 'select',
			'choices'     => array(
				'narrow' => __( 'Dar', 'asosyoloji' ),
				'wide'   => __( 'Geniş', 'asosyoloji' ),
				'full'   => __( 'Tam', 'asosyoloji' ),
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
		'aso_navigation',
		array(
			'title' => __( 'Ana Menü ve Sosyal', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting( 'aso_menu_background', array( 'default' => 'accent', 'sanitize_callback' => 'asosyoloji_sanitize_select' ) );
	$wp_customize->add_control(
		'aso_menu_background',
		array(
			'label' => __( 'Menü bar rengi', 'asosyoloji' ),
			'section' => 'aso_navigation',
			'type' => 'select',
			'choices' => array(
				'accent' => __( 'Vurgu rengi', 'asosyoloji' ),
				'light'  => __( 'Açık', 'asosyoloji' ),
				'dark'   => __( 'Koyu', 'asosyoloji' ),
				'custom' => __( 'Özel renk', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting( 'aso_menu_custom_color', array( 'default' => '#8f1d2c', 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'aso_menu_custom_color',
			array(
				'label' => __( 'Özel menü bar rengi', 'asosyoloji' ),
				'section' => 'aso_navigation',
			)
		)
	);

	$wp_customize->add_setting( 'aso_menu_alignment', array( 'default' => 'center', 'sanitize_callback' => 'asosyoloji_sanitize_select' ) );
	$wp_customize->add_control(
		'aso_menu_alignment',
		array(
			'label' => __( 'Menü hizası', 'asosyoloji' ),
			'section' => 'aso_navigation',
			'type' => 'select',
			'choices' => array(
				'left'   => __( 'Sol', 'asosyoloji' ),
				'center' => __( 'Orta', 'asosyoloji' ),
				'right'  => __( 'Sağ', 'asosyoloji' ),
				'spread' => __( 'Yayılmış', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting( 'aso_menu_width', array( 'default' => 'container', 'sanitize_callback' => 'asosyoloji_sanitize_select' ) );
	$wp_customize->add_control(
		'aso_menu_width',
		array(
			'label' => __( 'Menü iç genişliği', 'asosyoloji' ),
			'section' => 'aso_navigation',
			'type' => 'select',
			'choices' => array(
				'container' => __( 'Site genişliği', 'asosyoloji' ),
				'full' => __( 'Tam ekran', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting( 'aso_menu_density', array( 'default' => 'normal', 'sanitize_callback' => 'asosyoloji_sanitize_select' ) );
	$wp_customize->add_control(
		'aso_menu_density',
		array(
			'label' => __( 'Menü yüksekliği', 'asosyoloji' ),
			'section' => 'aso_navigation',
			'type' => 'select',
			'choices' => array(
				'compact' => __( 'Kompakt', 'asosyoloji' ),
				'normal'  => __( 'Normal', 'asosyoloji' ),
				'large'   => __( 'Geniş', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting( 'aso_menu_separators', array( 'default' => true, 'sanitize_callback' => 'asosyoloji_sanitize_checkbox' ) );
	$wp_customize->add_control( 'aso_menu_separators', array( 'label' => __( 'Menü öğeleri arasında ayırıcı göster', 'asosyoloji' ), 'section' => 'aso_navigation', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'aso_menu_uppercase', array( 'default' => true, 'sanitize_callback' => 'asosyoloji_sanitize_checkbox' ) );
	$wp_customize->add_control( 'aso_menu_uppercase', array( 'label' => __( 'Menü metnini büyük harfle göster', 'asosyoloji' ), 'section' => 'aso_navigation', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'aso_menu_active_style', array( 'default' => 'underline', 'sanitize_callback' => 'asosyoloji_sanitize_select' ) );
	$wp_customize->add_control(
		'aso_menu_active_style',
		array(
			'label' => __( 'Aktif menü stili', 'asosyoloji' ),
			'section' => 'aso_navigation',
			'type' => 'select',
			'choices' => array(
				'underline' => __( 'Alt çizgi', 'asosyoloji' ),
				'fill' => __( 'Dolgu', 'asosyoloji' ),
				'none' => __( 'Vurgu yok', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting( 'aso_show_social_bar', array( 'default' => true, 'sanitize_callback' => 'asosyoloji_sanitize_checkbox' ) );
	$wp_customize->add_control(
		'aso_show_social_bar',
		array(
			'label' => __( 'Üst alanda sosyal medya bağlantılarını göster', 'asosyoloji' ),
			'description' => __( 'Bağlantısı boş bırakılan ağ gösterilmez.', 'asosyoloji' ),
			'section' => 'aso_navigation',
			'type' => 'checkbox',
		)
	);

	$wp_customize->add_setting( 'aso_social_alignment', array( 'default' => 'right', 'sanitize_callback' => 'asosyoloji_sanitize_select' ) );
	$wp_customize->add_control(
		'aso_social_alignment',
		array(
			'label' => __( 'Sosyal ikon hizası', 'asosyoloji' ),
			'section' => 'aso_navigation',
			'type' => 'select',
			'choices' => array(
				'left' => __( 'Sol', 'asosyoloji' ),
				'center' => __( 'Orta', 'asosyoloji' ),
				'right' => __( 'Sağ', 'asosyoloji' ),
			),
		)
	);

	$social_networks = array(
		'instagram' => 'Instagram',
		'x' => 'X / Twitter',
		'facebook' => 'Facebook',
		'youtube' => 'YouTube',
		'linkedin' => 'LinkedIn',
		'mastodon' => 'Mastodon',
		'telegram' => 'Telegram',
	);
	foreach ( $social_networks as $network => $label ) {
		$setting_id = 'aso_social_' . $network;
		$wp_customize->add_setting( $setting_id, array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control(
			$setting_id,
			array(
				'label' => sprintf( __( '%s bağlantısı', 'asosyoloji' ), $label ),
				'section' => 'aso_navigation',
				'type' => 'url',
				'input_attrs' => array( 'placeholder' => 'https://' ),
			)
		);
	}

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
		'aso_home_general',
		array(
			'title' => __( 'Ana Sayfa: Genel', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_home_global_exclude_categories',
		array(
			'default'           => array(),
			'sanitize_callback' => 'asosyoloji_sanitize_category_ids',
		)
	);

	$wp_customize->add_control(
		new Asosyoloji_Multi_Select_Control(
			$wp_customize,
			'aso_home_global_exclude_categories',
			array(
				'label'       => __( 'Ana sayfada genel olarak hariç tutulacak kategoriler', 'asosyoloji' ),
				'description' => __( 'Duyurular gibi ana sayfadaki yazı akışlarında görünmesini istemediğiniz kategorileri seçin. Bölüm bazlı hariç tutmalar ayrıca uygulanır.', 'asosyoloji' ),
				'section'     => 'aso_home_general',
				'choices'     => array_filter( asosyoloji_category_choices(), 'is_int', ARRAY_FILTER_USE_KEY ),
			)
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
		'aso_home_latest_exclude_categories',
		array(
			'default'           => array(),
			'sanitize_callback' => 'asosyoloji_sanitize_category_ids',
		)
	);

	$wp_customize->add_control(
		new Asosyoloji_Multi_Select_Control(
			$wp_customize,
			'aso_home_latest_exclude_categories',
			array(
				'label'       => __( 'Hariç tutulacak kategoriler', 'asosyoloji' ),
				'description' => __( 'Duyurular gibi ana yazı akışında görünmesini istemediğiniz kategorileri seçin. Ctrl/Cmd ile birden fazla seçim yapabilirsiniz.', 'asosyoloji' ),
				'section'     => 'aso_home_latest',
				'choices'     => array_filter( asosyoloji_category_choices(), 'is_int', ARRAY_FILTER_USE_KEY ),
			)
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
		'aso_home_latest_load_mode',
		array(
			'default'           => 'button',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);

	$wp_customize->add_control(
		'aso_home_latest_load_mode',
		array(
			'label'       => __( 'Daha fazla içerik yükleme', 'asosyoloji' ),
			'description' => __( 'Son Yazılar bölümünde ilk grup sonrasında yeni içeriklerin nasıl yükleneceğini belirler.', 'asosyoloji' ),
			'section'     => 'aso_home_latest',
			'type'        => 'select',
			'choices'     => array(
				'none'     => __( 'Kapalı', 'asosyoloji' ),
				'button'   => __( 'Daha fazla yükle butonu', 'asosyoloji' ),
				'infinite' => __( 'Aşağı indikçe otomatik yükle', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting(
		'aso_home_latest_load_count',
		array(
			'default'           => 6,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'aso_home_latest_load_count',
		array(
			'label'       => __( 'Her yüklemede getirilecek yazı sayısı', 'asosyoloji' ),
			'section'     => 'aso_home_latest',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 3,
				'max'  => 12,
				'step' => 1,
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


}
add_action( 'customize_register', 'asosyoloji_customize_register' );
