<?php
/**
 * Recommended companion plugins.
 *
 * One generic implementation serves every companion plugin; add a new entry to
 * asosyoloji_companion_plugins() to support another plugin.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Companion plugin definitions.
 *
 * @return array<string, array<string, string>>
 */
function asosyoloji_companion_plugins() {
	return array(
		'weekly' => array(
			'name'        => __( 'Asosyoloji Haftalık', 'asosyoloji' ),
			'description' => __( 'Etkinlik içerik tipi, haftalık etkinlik listesi, aylık takvim, Event schema, Google Calendar ve ICS desteği sağlar.', 'asosyoloji' ),
			'file'        => 'asosyoloji-weekly/asosyoloji-weekly.php',
			'repo'        => 'm4v3r4/asosyoloji-weekly',
			'asset'       => 'asosyoloji-weekly.zip',
			'manage_url'  => 'edit.php?post_type=em_event',
			'manage'      => __( 'Etkinlikleri Yönet', 'asosyoloji' ),
		),
		'print'  => array(
			'name'        => __( 'Asosyoloji Basılı Yayın Arşivi', 'asosyoloji' ),
			'description' => __( 'Dergi sayılarını başlık, sayı, yayın tarihi, kapak ve PDF belgesiyle yönetir; PDF okuyucu, arşiv sayfası, widget ve kısa kod sağlar.', 'asosyoloji' ),
			'file'        => 'asosyoloji-print-archive/asosyoloji-print-archive.php',
			'repo'        => 'm4v3r4/asosyoloji-print-archive',
			'asset'       => 'asosyoloji-print-archive.zip',
			'manage_url'  => 'edit.php?post_type=aso_print_issue',
			'manage'      => __( 'Basılı Yayınları Yönet', 'asosyoloji' ),
		),
	);
}

/**
 * Get one companion definition.
 *
 * @param string $key Companion key.
 * @return array|null
 */
function asosyoloji_companion( $key ) {
	$plugins = asosyoloji_companion_plugins();
	return $plugins[ $key ] ?? null;
}

/**
 * Whether a companion plugin is active.
 *
 * @param string $key Companion key.
 * @return bool
 */
function asosyoloji_companion_is_active( $key ) {
	$plugin = asosyoloji_companion( $key );
	if ( ! $plugin ) {
		return false;
	}

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	return is_plugin_active( $plugin['file'] );
}

/**
 * Whether a companion plugin is installed.
 *
 * @param string $key Companion key.
 * @return bool
 */
function asosyoloji_companion_is_installed( $key ) {
	$plugin = asosyoloji_companion( $key );
	return $plugin && file_exists( WP_PLUGIN_DIR . '/' . $plugin['file'] );
}

/**
 * Latest GitHub release of a companion plugin, cached.
 *
 * Failures are cached briefly as well, so an unreachable or rate-limited GitHub
 * API never adds a request to every admin page load.
 *
 * @param string $key Companion key.
 * @return array|WP_Error
 */
function asosyoloji_companion_release( $key ) {
	$plugin = asosyoloji_companion( $key );
	if ( ! $plugin ) {
		return new WP_Error( 'asosyoloji_companion_unknown' );
	}

	$cache_key = 'asosyoloji_companion_release_' . $key;
	$cached    = get_site_transient( $cache_key );

	if ( is_array( $cached ) ) {
		return $cached;
	}
	if ( 'error' === $cached ) {
		return new WP_Error( 'asosyoloji_companion_release_unavailable' );
	}

	$response = wp_remote_get(
		'https://api.github.com/repos/' . $plugin['repo'] . '/releases/latest',
		array(
			'headers' => array(
				'Accept'               => 'application/vnd.github+json',
				'X-GitHub-Api-Version' => '2022-11-28',
				'User-Agent'           => 'Asosyoloji-Theme/' . ASOSYOLOJI_VERSION,
			),
			'timeout' => 6,
		)
	);

	$data = is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response )
		? null
		: json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $data ) ) {
		set_site_transient( $cache_key, 'error', 15 * MINUTE_IN_SECONDS );
		return new WP_Error( 'asosyoloji_companion_release_unavailable' );
	}

	$download_url = '';
	foreach ( $data['assets'] ?? array() as $asset ) {
		if ( $plugin['asset'] === ( $asset['name'] ?? '' ) ) {
			$download_url = esc_url_raw( $asset['browser_download_url'] ?? '' );
			break;
		}
	}

	$release = array(
		'version'      => ltrim( (string) ( $data['tag_name'] ?? '' ), 'vV' ),
		'download_url' => $download_url,
		'html_url'     => esc_url_raw( $data['html_url'] ?? 'https://github.com/' . $plugin['repo'] ),
	);

	set_site_transient( $cache_key, $release, HOUR_IN_SECONDS );

	return $release;
}

/**
 * Nonce-protected activation URL.
 *
 * @param string $key Companion key.
 * @return string
 */
function asosyoloji_companion_activate_url( $key ) {
	$plugin = asosyoloji_companion( $key );

	return wp_nonce_url(
		admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $plugin['file'] ) ),
		'activate-plugin_' . $plugin['file']
	);
}

/**
 * Nonce-protected one-click install URL.
 *
 * @param string $key Companion key.
 * @return string
 */
function asosyoloji_companion_install_url( $key ) {
	return wp_nonce_url(
		admin_url( 'admin-post.php?action=asosyoloji_install_companion&plugin=' . rawurlencode( $key ) ),
		'asosyoloji_install_companion_' . $key
	);
}

/**
 * Primary action button for a companion plugin.
 *
 * @param string $key       Companion key.
 * @param bool   $with_manage Show a manage link when active.
 * @return string
 */
function asosyoloji_companion_action( $key, $with_manage = true ) {
	$plugin = asosyoloji_companion( $key );

	if ( asosyoloji_companion_is_active( $key ) ) {
		return $with_manage
			? sprintf( '<a class="button button-primary" href="%s">%s</a>', esc_url( admin_url( $plugin['manage_url'] ) ), esc_html( $plugin['manage'] ) )
			: '';
	}

	if ( asosyoloji_companion_is_installed( $key ) ) {
		return sprintf( '<a class="button button-primary" href="%s">%s</a>', esc_url( asosyoloji_companion_activate_url( $key ) ), esc_html__( 'Etkinleştir', 'asosyoloji' ) );
	}

	$release = asosyoloji_companion_release( $key );
	if ( ! is_wp_error( $release ) && ! empty( $release['download_url'] ) ) {
		return sprintf( '<a class="button button-primary" href="%s">%s</a>', esc_url( asosyoloji_companion_install_url( $key ) ), esc_html__( 'GitHub’dan Kur ve Etkinleştir', 'asosyoloji' ) );
	}

	return '';
}

/**
 * One compact notice for all missing companions, only on screens where it is relevant.
 */
function asosyoloji_companion_notice() {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes', 'plugins' ), true ) ) {
		return;
	}

	$missing = array_filter(
		array_keys( asosyoloji_companion_plugins() ),
		static function ( $key ) {
			return ! asosyoloji_companion_is_active( $key );
		}
	);

	if ( ! $missing ) {
		return;
	}

	$names = array_map(
		static function ( $key ) {
			return asosyoloji_companion( $key )['name'];
		},
		$missing
	);
	?>
	<div class="notice notice-info">
		<p>
			<?php
			printf(
				/* translators: %s: Comma-separated plugin names. */
				esc_html__( 'Asosyoloji teması şu eklentilerle birlikte çalışacak şekilde tasarlandı: %s.', 'asosyoloji' ),
				esc_html( implode( ', ', $names ) )
			);
			?>
			<a href="<?php echo esc_url( admin_url( 'themes.php?page=asosyoloji-plugins' ) ); ?>"><?php esc_html_e( 'Eklentileri yönet', 'asosyoloji' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'asosyoloji_companion_notice' );

/**
 * Install and activate a companion plugin from its GitHub release.
 */
function asosyoloji_install_companion_plugin() {
	$key    = isset( $_GET['plugin'] ) ? sanitize_key( wp_unslash( $_GET['plugin'] ) ) : '';
	$plugin = asosyoloji_companion( $key );
	$back   = admin_url( 'themes.php?page=asosyoloji-plugins' );

	if ( ! $plugin || ! current_user_can( 'install_plugins' ) ) {
		wp_die( esc_html__( 'Bu eklentiyi kurma yetkiniz yok.', 'asosyoloji' ) );
	}

	check_admin_referer( 'asosyoloji_install_companion_' . $key );

	delete_site_transient( 'asosyoloji_companion_release_' . $key );
	$release = asosyoloji_companion_release( $key );
	if ( is_wp_error( $release ) || empty( $release['download_url'] ) ) {
		wp_safe_redirect( add_query_arg( 'aso_companion', 'release-missing', $back ) );
		exit;
	}

	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';

	$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
	$result   = $upgrader->install( $release['download_url'] );

	if ( is_wp_error( $result ) || ! $result ) {
		wp_safe_redirect( add_query_arg( 'aso_companion', 'install-failed', $back ) );
		exit;
	}

	$activated = activate_plugin( $plugin['file'] );
	delete_site_transient( 'update_plugins' );

	wp_safe_redirect( add_query_arg( 'aso_companion', is_wp_error( $activated ) ? 'activate-failed' : 'installed', $back ) );
	exit;
}
add_action( 'admin_post_asosyoloji_install_companion', 'asosyoloji_install_companion_plugin' );

/**
 * Register the companion plugins screen.
 */
function asosyoloji_companion_plugins_menu() {
	add_theme_page(
		__( 'Asosyoloji Eklentileri', 'asosyoloji' ),
		__( 'Asosyoloji Eklentileri', 'asosyoloji' ),
		'install_plugins',
		'asosyoloji-plugins',
		'asosyoloji_companion_plugins_page'
	);
}
add_action( 'admin_menu', 'asosyoloji_companion_plugins_menu' );

/**
 * Render the companion plugins screen.
 */
function asosyoloji_companion_plugins_page() {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	$messages = array(
		'installed'       => array( 'success', __( 'Eklenti kuruldu ve etkinleştirildi.', 'asosyoloji' ) ),
		'release-missing' => array( 'error', __( 'GitHub Release paketi bulunamadı. Daha sonra tekrar deneyin.', 'asosyoloji' ) ),
		'install-failed'  => array( 'error', __( 'Eklenti kurulamadı. Dosya izinlerini kontrol edin.', 'asosyoloji' ) ),
		'activate-failed' => array( 'warning', __( 'Eklenti kuruldu ancak etkinleştirilemedi.', 'asosyoloji' ) ),
	);
	$status = isset( $_GET['aso_companion'] ) ? sanitize_key( wp_unslash( $_GET['aso_companion'] ) ) : '';
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Asosyoloji Eklentileri', 'asosyoloji' ); ?></h1>

		<?php if ( isset( $messages[ $status ] ) ) : ?>
			<div class="notice notice-<?php echo esc_attr( $messages[ $status ][0] ); ?> is-dismissible"><p><?php echo esc_html( $messages[ $status ][1] ); ?></p></div>
		<?php endif; ?>

		<p><?php esc_html_e( 'Temayla birlikte çalışması önerilen bağımsız Asosyoloji eklentileri.', 'asosyoloji' ); ?></p>

		<table class="widefat striped" style="max-width:900px">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Eklenti', 'asosyoloji' ); ?></th>
					<th><?php esc_html_e( 'Durum', 'asosyoloji' ); ?></th>
					<th><?php esc_html_e( 'GitHub sürümü', 'asosyoloji' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( asosyoloji_companion_plugins() as $key => $plugin ) : ?>
					<?php
					$release = asosyoloji_companion_release( $key );
					if ( asosyoloji_companion_is_active( $key ) ) {
						$state = __( 'Etkin', 'asosyoloji' );
					} elseif ( asosyoloji_companion_is_installed( $key ) ) {
						$state = __( 'Kurulu, etkin değil', 'asosyoloji' );
					} else {
						$state = __( 'Kurulu değil', 'asosyoloji' );
					}
					?>
					<tr>
						<td>
							<strong><?php echo esc_html( $plugin['name'] ); ?></strong>
							<p class="description"><?php echo esc_html( $plugin['description'] ); ?></p>
						</td>
						<td><?php echo esc_html( $state ); ?></td>
						<td><?php echo is_wp_error( $release ) || empty( $release['version'] ) ? '—' : esc_html( $release['version'] ); ?></td>
						<td>
							<?php echo wp_kses_post( asosyoloji_companion_action( $key ) ); ?>
							<a class="button" href="<?php echo esc_url( 'https://github.com/' . $plugin['repo'] ); ?>" target="_blank" rel="noopener noreferrer">GitHub</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}
