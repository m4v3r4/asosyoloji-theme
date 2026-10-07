<?php
/**
 * Light/dark appearance controls.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_appearance_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'aso_appearance',
		array(
			'title' => __( 'Aydınlık / Karanlık Tema', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_color_scheme',
		array(
			'default'           => 'system',
			'sanitize_callback' => 'asosyoloji_sanitize_select',
		)
	);

	$wp_customize->add_control(
		'aso_color_scheme',
		array(
			'label'       => __( 'Varsayılan görünüm', 'asosyoloji' ),
			'description' => __( 'Sistem seçeneği ziyaretçinin cihaz tercihine uyar.', 'asosyoloji' ),
			'section'     => 'aso_appearance',
			'type'        => 'select',
			'choices'     => array(
				'system' => __( 'Sistem tercihi', 'asosyoloji' ),
				'light'  => __( 'Aydınlık', 'asosyoloji' ),
				'dark'   => __( 'Karanlık', 'asosyoloji' ),
			),
		)
	);

	$wp_customize->add_setting(
		'aso_show_theme_toggle',
		array(
			'default'           => true,
			'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'aso_show_theme_toggle',
		array(
			'label'   => __( 'Ziyaretçiye tema değiştirme düğmesi göster', 'asosyoloji' ),
			'section' => 'aso_appearance',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'aso_dark_background',
		array(
			'default'           => '#111111',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'aso_dark_background',
			array(
				'label'   => __( 'Karanlık tema arka planı', 'asosyoloji' ),
				'section' => 'aso_appearance',
			)
		)
	);

	$wp_customize->add_setting(
		'aso_dark_surface',
		array(
			'default'           => '#1a1a1a',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'aso_dark_surface',
			array(
				'label'   => __( 'Karanlık tema ikincil zemin', 'asosyoloji' ),
				'section' => 'aso_appearance',
			)
		)
	);

	$wp_customize->add_setting(
		'aso_dark_text',
		array(
			'default'           => '#f2f2f2',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'aso_dark_text',
			array(
				'label'   => __( 'Karanlık tema metin rengi', 'asosyoloji' ),
				'section' => 'aso_appearance',
			)
		)
	);

	$dark_colors = array(
		'aso_dark_muted' => array(
			'label'   => __( 'Karanlık tema ikincil metin', 'asosyoloji' ),
			'default' => '#b8b8b8',
		),
		'aso_dark_border' => array(
			'label'   => __( 'Karanlık tema çizgi/kenarlık', 'asosyoloji' ),
			'default' => '#343434',
		),
		'aso_dark_link' => array(
			'label'   => __( 'Karanlık tema bağlantı/vurgu', 'asosyoloji' ),
			'default' => '#d56a78',
		),
		'aso_dark_footer' => array(
			'label'   => __( 'Karanlık tema footer zemini', 'asosyoloji' ),
			'default' => '#080808',
		),
	);

	foreach ( $dark_colors as $setting_id => $args ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'   => $args['label'],
					'section' => 'aso_appearance',
				)
			)
		);
	}
}
add_action( 'customize_register', 'asosyoloji_appearance_customize_register', 40 );

function asosyoloji_theme_boot_script() {
	$default = get_theme_mod( 'aso_color_scheme', 'system' );
	?>
	<script>
	(function () {
		var configured = <?php echo wp_json_encode( $default ); ?>;
		var saved = null;
		try { saved = localStorage.getItem('asosyoloji-theme'); } catch (e) {}
		var theme = saved;
		if (!theme) {
			if (configured === 'light' || configured === 'dark') {
				theme = configured;
			} else {
				theme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
			}
		}
		document.documentElement.dataset.theme = theme;
		document.documentElement.style.colorScheme = theme;
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'asosyoloji_theme_boot_script', 1 );
