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
		'aso_footer_show_social',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'aso_footer_show_social',
		array(
			'label'       => __( 'Footer sosyal medya alanını göster', 'asosyoloji' ),
			'description' => __( 'Footer bağlantısı boşsa Ana Menü ve Sosyal bölümündeki bağlantı kullanılır.', 'asosyoloji' ),
			'section'     => 'aso_footer_builder',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'aso_footer_social_title',
		array(
			'default'           => __( 'Bizi takip edin', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'aso_footer_social_title',
		array(
			'label'   => __( 'Sosyal medya alanı başlığı', 'asosyoloji' ),
			'section' => 'aso_footer_builder',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aso_footer_social_alignment',
		array(
			'default'           => 'right',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'aso_footer_social_alignment',
		array(
			'label'   => __( 'Footer sosyal ikon hizası', 'asosyoloji' ),
			'section' => 'aso_footer_builder',
			'type'    => 'select',
			'choices' => array(
				'left'   => __( 'Sol', 'asosyoloji' ),
				'center' => __( 'Orta', 'asosyoloji' ),
				'right'  => __( 'Sağ', 'asosyoloji' ),
			),
		)
	);

	foreach ( asosyoloji_social_network_labels() as $network => $label ) {
		$setting_id = 'aso_footer_social_' . $network;
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				/* translators: %s: Social network name. */
				'label'       => sprintf( __( 'Footer %s bağlantısı', 'asosyoloji' ), $label ),
				'description' => __( 'Boş bırakılırsa üst alandaki bağlantı kullanılır.', 'asosyoloji' ),
				'section'     => 'aso_footer_builder',
				'type'        => 'url',
				'input_attrs' => array( 'placeholder' => 'https://' ),
			)
		);
	}

	$wp_customize->add_setting(
		'aso_footer_copyright_text',
		array(
			'default'           => '© {year} {site_name}',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'aso_footer_copyright_text',
		array(
			'label'       => __( 'Telif metni', 'asosyoloji' ),
			'description' => __( '{year} ve {site_name} otomatik olarak değiştirilir.', 'asosyoloji' ),
			'section'     => 'aso_footer_builder',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'aso_footer_license_text',
		array(
			'default'           => 'GPL-3.0-or-later',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'aso_footer_license_text',
		array(
			'label'   => __( 'Lisans metni', 'asosyoloji' ),
			'section' => 'aso_footer_builder',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aso_footer_license_url',
		array(
			'default'           => 'https://www.gnu.org/licenses/gpl-3.0.html',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'aso_footer_license_url',
		array(
			'label'   => __( 'Lisans bağlantısı', 'asosyoloji' ),
			'section' => 'aso_footer_builder',
			'type'    => 'url',
		)
	);

	$wp_customize->add_setting(
		'aso_footer_source_label',
		array(
			'default'           => __( 'Tema kaynak kodu', 'asosyoloji' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'aso_footer_source_label',
		array(
			'label'   => __( 'Kaynak bağlantısı metni', 'asosyoloji' ),
			'section' => 'aso_footer_builder',
			'type'    => 'text',
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
}
add_action( 'customize_register', 'asosyoloji_footer_customize_register', 50 );
