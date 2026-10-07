<?php
/**
 * Recommended companion plugins.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ASOSYOLOJI_WEEKLY_PLUGIN_FILE', 'asosyoloji-weekly/asosyoloji-weekly.php' );
define( 'ASOSYOLOJI_WEEKLY_PLUGIN_REPO', 'm4v3r4/asosyoloji-weekly' );
define( 'ASOSYOLOJI_WEEKLY_PLUGIN_ASSET', 'asosyoloji-weekly.zip' );

function asosyoloji_weekly_plugin_is_active() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	return is_plugin_active( ASOSYOLOJI_WEEKLY_PLUGIN_FILE );
}

function asosyoloji_weekly_plugin_is_installed() {
	return file_exists( WP_PLUGIN_DIR . '/asosyoloji-weekly/asosyoloji-weekly.php' );
}

function asosyoloji_weekly_recommended_release() {
	$cache_key = 'asosyoloji_weekly_recommended_release';
	$cached    = get_site_transient( $cache_key );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	$response = wp_remote_get(
		'https://api.github.com/repos/' . ASOSYOLOJI_WEEKLY_PLUGIN_REPO . '/releases/latest',
		array(
			'headers' => array(
				'Accept'               => 'application/vnd.github+json',
				'X-GitHub-Api-Version' => '2022-11-28',
				'User-Agent'           => 'Asosyoloji-Theme/' . ASOSYOLOJI_VERSION,
			),
			'timeout' => 8,
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return new WP_Error( 'asosyoloji_weekly_release_unavailable' );
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $data ) ) {
		return new WP_Error( 'asosyoloji_weekly_release_invalid' );
	}

	$download_url = '';
	foreach ( $data['assets'] ?? array() as $asset ) {
		if ( ASOSYOLOJI_WEEKLY_PLUGIN_ASSET === ( $asset['name'] ?? '' ) ) {
			$download_url = esc_url_raw( $asset['browser_download_url'] ?? '' );
			break;
		}
	}

	$release = array(
		'version'      => ltrim( (string) ( $data['tag_name'] ?? '' ), 'vV' ),
		'download_url' => $download_url,
		'html_url'     => esc_url_raw( $data['html_url'] ?? 'https://github.com/' . ASOSYOLOJI_WEEKLY_PLUGIN_REPO ),
	);

	set_site_transient( $cache_key, $release, HOUR_IN_SECONDS );

	return $release;
}

function asosyoloji_weekly_activate_url() {
	return wp_nonce_url(
		admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( ASOSYOLOJI_WEEKLY_PLUGIN_FILE ) ),
		'activate-plugin_' . ASOSYOLOJI_WEEKLY_PLUGIN_FILE
	);
}

function asosyoloji_weekly_install_url() {
	return wp_nonce_url(
		admin_url( 'admin-post.php?action=asosyoloji_install_weekly' ),
		'asosyoloji_install_weekly'
	);
}

function asosyoloji_weekly_recommendation_notice() {
	if ( ! current_user_can( 'install_plugins' ) || asosyoloji_weekly_plugin_is_active() ) {
		return;
	}

	$installed = asosyoloji_weekly_plugin_is_installed();
	$release   = asosyoloji_weekly_recommended_release();
	?>
	<div class="notice notice-info">
		<p>
			<strong><?php esc_html_e( 'Asosyoloji Haftalık öneriliyor.', 'asosyoloji' ); ?></strong>
			<?php esc_html_e( 'Etkinlikleri, haftalık listeyi ve takvimi temadan bağımsız yönetmek için companion eklentiyi kullanabilirsiniz.', 'asosyoloji' ); ?>
		</p>
		<p>
			<?php if ( $installed ) : ?>
				<a class="button button-primary" href="<?php echo esc_url( asosyoloji_weekly_activate_url() ); ?>">
					<?php esc_html_e( 'Eklentiyi Etkinleştir', 'asosyoloji' ); ?>
				</a>
			<?php elseif ( ! is_wp_error( $release ) && ! empty( $release['download_url'] ) ) : ?>
				<a class="button button-primary" href="<?php echo esc_url( asosyoloji_weekly_install_url() ); ?>">
					<?php esc_html_e( 'GitHub’dan Kur ve Etkinleştir', 'asosyoloji' ); ?>
				</a>
			<?php else : ?>
				<a class="button" href="https://github.com/m4v3r4/asosyoloji-weekly" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'GitHub Deposunu Aç', 'asosyoloji' ); ?>
				</a>
				<span><?php esc_html_e( 'İlk GitHub Release yayınlandığında tek tık kurulum otomatik açılır.', 'asosyoloji' ); ?></span>
			<?php endif; ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'asosyoloji_weekly_recommendation_notice' );

function asosyoloji_install_weekly_plugin() {
	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_die( esc_html__( 'Bu eklentiyi kurma yetkiniz yok.', 'asosyoloji' ) );
	}

	check_admin_referer( 'asosyoloji_install_weekly' );

	$release = asosyoloji_weekly_recommended_release();
	if ( is_wp_error( $release ) || empty( $release['download_url'] ) ) {
		wp_safe_redirect( admin_url( 'themes.php?asosyoloji-weekly=release-missing' ) );
		exit;
	}

	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';

	$skin     = new Automatic_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );
	$result   = $upgrader->install( $release['download_url'] );

	if ( is_wp_error( $result ) || ! $result ) {
		wp_safe_redirect( admin_url( 'themes.php?asosyoloji-weekly=install-failed' ) );
		exit;
	}

	$activated = activate_plugin( ASOSYOLOJI_WEEKLY_PLUGIN_FILE );

	if ( is_wp_error( $activated ) ) {
		wp_safe_redirect( admin_url( 'plugins.php?asosyoloji-weekly=activate-failed' ) );
		exit;
	}

	delete_site_transient( 'update_plugins' );
	wp_safe_redirect( admin_url( 'plugins.php?asosyoloji-weekly=installed' ) );
	exit;
}
add_action( 'admin_post_asosyoloji_install_weekly', 'asosyoloji_install_weekly_plugin' );
