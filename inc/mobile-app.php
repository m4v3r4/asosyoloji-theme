<?php
/**
 * Asosyoloji mobile app: store links, iOS Smart App Banner and an
 * "open in the app" strip for other mobile browsers.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer settings for the mobile app.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function asosyoloji_mobile_app_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'aso_mobile_app',
		array(
			'title'       => __( 'Mobil Uygulama', 'asosyoloji' ),
			'description' => __( 'Asosyoloji uygulamasının mağaza bağlantıları. Kimlik boş bırakılırsa ilgili mağaza gösterilmez.', 'asosyoloji' ),
			'panel'       => 'aso_theme_options',
		)
	);

	$fields = array(
		'aso_app_ios_id'          => array(
			'label'    => __( 'App Store uygulama kimliği', 'asosyoloji' ),
			'desc'     => __( 'App Store adresindeki “id” sonrasındaki sayı.', 'asosyoloji' ),
			'default'  => '6823511120',
			'sanitize' => 'asosyoloji_sanitize_app_store_id',
		),
		'aso_app_android_package' => array(
			'label'    => __( 'Google Play paket adı', 'asosyoloji' ),
			'desc'     => __( 'Örnek: com.asosyoloji.app', 'asosyoloji' ),
			'default'  => 'com.asosyoloji.app',
			'sanitize' => 'asosyoloji_sanitize_android_package',
		),
	);

	foreach ( $fields as $id => $field ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $field['default'],
				'sanitize_callback' => $field['sanitize'],
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $field['label'],
				'description' => $field['desc'],
				'section'     => 'aso_mobile_app',
				'type'        => 'text',
			)
		);
	}

	foreach (
		array(
			'aso_app_banner' => __( 'Mobil tarayıcılarda “Uygulamada aç” afişini göster', 'asosyoloji' ),
			'aso_app_footer' => __( 'Alt alanda App Store ve Google Play düğmelerini göster', 'asosyoloji' ),
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => true,
				'sanitize_callback' => 'asosyoloji_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'aso_mobile_app',
				'type'    => 'checkbox',
			)
		);
	}
}
add_action( 'customize_register', 'asosyoloji_mobile_app_customize_register', 55 );

/**
 * Keep only digits of an App Store ID.
 *
 * @param string $value Raw value.
 * @return string
 */
function asosyoloji_sanitize_app_store_id( $value ) {
	return preg_replace( '/\D+/', '', (string) $value );
}

/**
 * Validate an Android package name.
 *
 * @param string $value Raw value.
 * @return string
 */
function asosyoloji_sanitize_android_package( $value ) {
	$value = trim( (string) $value );
	return preg_match( '/^[a-zA-Z][\w]*(\.[a-zA-Z][\w]*)+$/', $value ) ? $value : '';
}

/**
 * Store URLs for the app.
 *
 * @return array{ios: string, android: string}
 */
function asosyoloji_app_store_urls() {
	$ios_id  = asosyoloji_sanitize_app_store_id( get_theme_mod( 'aso_app_ios_id', '6823511120' ) );
	$package = asosyoloji_sanitize_android_package( get_theme_mod( 'aso_app_android_package', 'com.asosyoloji.app' ) );

	return array(
		'ios'     => $ios_id ? 'https://apps.apple.com/tr/app/id' . $ios_id : '',
		'android' => $package ? 'https://play.google.com/store/apps/details?id=' . rawurlencode( $package ) : '',
	);
}

/**
 * iOS Safari's native Smart App Banner.
 */
function asosyoloji_app_smart_banner_meta() {
	$ios_id = asosyoloji_sanitize_app_store_id( get_theme_mod( 'aso_app_ios_id', '6823511120' ) );
	if ( ! $ios_id || ! get_theme_mod( 'aso_app_banner', true ) || is_admin() ) {
		return;
	}

	$current = function_exists( 'asosyoloji_current_url' ) ? asosyoloji_current_url() : home_url( '/' );
	printf(
		'<meta name="apple-itunes-app" content="app-id=%1$s, app-argument=%2$s">' . "\n",
		esc_attr( $ios_id ),
		esc_attr( esc_url_raw( $current ) )
	);
}
add_action( 'wp_head', 'asosyoloji_app_smart_banner_meta', 3 );

/**
 * "Open in the app" strip for Android and for iOS browsers other than Safari
 * (Safari shows its own banner). Rendered hidden; theme.js reveals it on
 * matching devices and remembers a dismissal for 30 days.
 */
function asosyoloji_app_banner() {
	$urls = asosyoloji_app_store_urls();
	if ( ! get_theme_mod( 'aso_app_banner', true ) || ( ! $urls['ios'] && ! $urls['android'] ) ) {
		return;
	}
	?>
	<aside class="aso-app-banner" data-app-banner data-ios="<?php echo esc_url( $urls['ios'] ); ?>" data-android="<?php echo esc_url( $urls['android'] ); ?>" aria-label="<?php esc_attr_e( 'Asosyoloji uygulaması', 'asosyoloji' ); ?>" hidden>
		<button class="aso-app-banner__close" type="button" data-app-banner-close aria-label="<?php esc_attr_e( 'Kapat', 'asosyoloji' ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>
		<img class="aso-app-banner__icon" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/app-icon.png' ); ?>" alt="" width="44" height="44">
		<span class="aso-app-banner__text">
			<strong><?php esc_html_e( 'Asosyoloji uygulaması', 'asosyoloji' ); ?></strong>
			<span><?php esc_html_e( 'Yazıları, sesli okumayı ve takvimi uygulamada açın.', 'asosyoloji' ); ?></span>
		</span>
		<a class="aso-app-banner__open" href="#" data-app-banner-link rel="noopener"><?php esc_html_e( 'Uygulamada aç', 'asosyoloji' ); ?></a>
	</aside>
	<?php
}
add_action( 'wp_body_open', 'asosyoloji_app_banner', 5 );

/**
 * App Store / Google Play buttons for the footer.
 *
 * @return string
 */
function asosyoloji_app_store_buttons() {
	$urls = asosyoloji_app_store_urls();
	if ( ! get_theme_mod( 'aso_app_footer', true ) || ( ! $urls['ios'] && ! $urls['android'] ) ) {
		return '';
	}

	$button = static function ( $url, $small, $store ) {
		return sprintf(
			'<a class="aso-store-button" href="%1$s" target="_blank" rel="noopener noreferrer"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="6" y="2.5" width="12" height="19" rx="2.5"/><path d="M10.5 18.5h3"/></svg><span><small>%2$s</small>%3$s</span></a>',
			esc_url( $url ),
			esc_html( $small ),
			esc_html( $store )
		);
	};

	$html  = '<section class="site-footer__app" aria-labelledby="aso-app-title">';
	$html .= '<div><h2 id="aso-app-title">' . esc_html__( 'Asosyoloji cebinizde', 'asosyoloji' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'Yazılar, sesli okuma, etkinlik takvimi ve basılı sayılar uygulamada.', 'asosyoloji' ) . '</p></div>';
	$html .= '<div class="site-footer__app-buttons">';
	if ( $urls['ios'] ) {
		$html .= $button( $urls['ios'], __( 'İndirin', 'asosyoloji' ), 'App Store' );
	}
	if ( $urls['android'] ) {
		$html .= $button( $urls['android'], __( 'Edinin', 'asosyoloji' ), 'Google Play' );
	}
	$html .= '</div></section>';

	return $html;
}
