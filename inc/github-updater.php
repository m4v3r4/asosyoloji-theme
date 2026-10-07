<?php
/**
 * GitHub release updater for the Asosyoloji theme.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ASOSYOLOJI_GITHUB_REPOSITORY', 'm4v3r4/asosyoloji-theme' );
define( 'ASOSYOLOJI_GITHUB_RELEASE_ASSET', 'asosyoloji-theme.zip' );

/**
 * Return the optional private-repository token.
 *
 * Define ASOSYOLOJI_GITHUB_TOKEN in wp-config.php. Never store it in the theme.
 *
 * @return string
 */
function asosyoloji_github_token() {
	return defined( 'ASOSYOLOJI_GITHUB_TOKEN' ) ? trim( (string) ASOSYOLOJI_GITHUB_TOKEN ) : '';
}

/**
 * Build GitHub API request headers.
 *
 * @param bool $binary Whether a binary release asset is being downloaded.
 * @return array
 */
function asosyoloji_github_headers( $binary = false ) {
	$headers = array(
		'Accept'               => $binary ? 'application/octet-stream' : 'application/vnd.github+json',
		'X-GitHub-Api-Version' => '2022-11-28',
		'User-Agent'           => 'Asosyoloji-WordPress-Theme/' . ASOSYOLOJI_VERSION,
	);

	$token = asosyoloji_github_token();
	if ( $token ) {
		$headers['Authorization'] = 'Bearer ' . $token;
	}

	return $headers;
}

/**
 * Fetch and cache the latest GitHub release metadata.
 *
 * @param bool $force Force a fresh API request.
 * @return array|WP_Error
 */
function asosyoloji_github_latest_release( $force = false ) {
	$cache_key = 'asosyoloji_github_latest_release';

	if ( ! $force ) {
		$cached = get_site_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$url = sprintf(
		'https://api.github.com/repos/%s/releases/latest',
		ASOSYOLOJI_GITHUB_REPOSITORY
	);

	$response = wp_remote_get(
		$url,
		array(
			'headers' => asosyoloji_github_headers(),
			'timeout' => 8,
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$status = wp_remote_retrieve_response_code( $response );
	if ( 200 !== $status ) {
		return new WP_Error(
			'asosyoloji_github_release_http',
			sprintf(
				/* translators: %d: GitHub API HTTP status code. */
				__( 'GitHub release bilgisi alınamadı. HTTP %d.', 'asosyoloji' ),
				$status
			)
		);
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $data ) || empty( $data['tag_name'] ) ) {
		return new WP_Error( 'asosyoloji_github_release_invalid', __( 'GitHub release yanıtı geçersiz.', 'asosyoloji' ) );
	}

	$asset = array();
	foreach ( $data['assets'] ?? array() as $candidate ) {
		if ( ASOSYOLOJI_GITHUB_RELEASE_ASSET === ( $candidate['name'] ?? '' ) ) {
			$asset = $candidate;
			break;
		}
	}

	$release = array(
		'version'     => ltrim( (string) $data['tag_name'], 'vV' ),
		'tag'         => (string) $data['tag_name'],
		'name'        => sanitize_text_field( $data['name'] ?? $data['tag_name'] ),
		'html_url'    => esc_url_raw( $data['html_url'] ?? '' ),
		'published_at'=> sanitize_text_field( $data['published_at'] ?? '' ),
		'body'        => wp_kses_post( $data['body'] ?? '' ),
		'asset_url'   => esc_url_raw( $asset['url'] ?? '' ),
		'asset_name'  => sanitize_file_name( $asset['name'] ?? '' ),
	);

	set_site_transient( $cache_key, $release, HOUR_IN_SECONDS );

	return $release;
}

/**
 * Inject GitHub releases into the native WordPress theme update transient.
 *
 * @param stdClass $transient Theme update transient.
 * @return stdClass
 */
function asosyoloji_github_theme_update( $transient ) {
	if ( ! is_object( $transient ) ) {
		$transient = new stdClass();
	}

	$release = asosyoloji_github_latest_release();
	if ( is_wp_error( $release ) || empty( $release['version'] ) || empty( $release['asset_url'] ) ) {
		return $transient;
	}

	if ( version_compare( $release['version'], ASOSYOLOJI_VERSION, '<=' ) ) {
		return $transient;
	}

	$theme_slug = get_template();
	$transient->response[ $theme_slug ] = array(
		'theme'        => $theme_slug,
		'new_version'  => $release['version'],
		'url'          => $release['html_url'],
		'package'      => $release['asset_url'],
		'requires'     => '6.0',
		'requires_php' => '8.0',
	);

	return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'asosyoloji_github_theme_update' );

/**
 * Provide release notes in the native theme information modal.
 *
 * @param false|array|object $result Existing result.
 * @param string             $action API action.
 * @param object             $args API arguments.
 * @return false|array|object
 */
function asosyoloji_github_theme_information( $result, $action, $args ) {
	if ( 'theme_information' !== $action || empty( $args->slug ) || get_template() !== $args->slug ) {
		return $result;
	}

	$release = asosyoloji_github_latest_release();
	if ( is_wp_error( $release ) ) {
		return $result;
	}

	return (object) array(
		'name'          => wp_get_theme()->get( 'Name' ),
		'slug'          => get_template(),
		'version'       => $release['version'],
		'author'        => wp_get_theme()->get( 'Author' ),
		'homepage'      => $release['html_url'],
		'requires'      => '6.0',
		'requires_php'  => '8.0',
		'download_link' => $release['asset_url'],
		'sections'      => array(
			'description' => wpautop( esc_html( wp_get_theme()->get( 'Description' ) ) ),
			'changelog'   => wpautop( wp_kses_post( $release['body'] ) ),
		),
	);
}
add_filter( 'themes_api', 'asosyoloji_github_theme_information', 20, 3 );

/**
 * Download private GitHub release assets with authentication.
 *
 * WordPress' normal upgrader receives the GitHub asset API URL from the update
 * transient. For a private repository it must be downloaded with the token.
 *
 * @param bool|WP_Error $reply Existing pre-download result.
 * @param string        $package Package URL.
 * @param WP_Upgrader   $upgrader Upgrader instance.
 * @return bool|string|WP_Error
 */
function asosyoloji_github_pre_download( $reply, $package, $upgrader ) {
	unset( $upgrader );

	if ( false !== $reply ) {
		return $reply;
	}

	$asset_prefix = sprintf(
		'https://api.github.com/repos/%s/releases/assets/',
		ASOSYOLOJI_GITHUB_REPOSITORY
	);

	if ( 0 !== strpos( $package, $asset_prefix ) ) {
		return false;
	}

	$token = asosyoloji_github_token();
	if ( ! $token ) {
		return new WP_Error(
			'asosyoloji_github_token_missing',
			__( 'Private GitHub temasını güncellemek için ASOSYOLOJI_GITHUB_TOKEN wp-config.php içinde tanımlanmalıdır.', 'asosyoloji' )
		);
	}

	$temp_file = wp_tempnam( ASOSYOLOJI_GITHUB_RELEASE_ASSET );
	if ( ! $temp_file ) {
		return new WP_Error( 'asosyoloji_github_temp_file', __( 'Tema güncellemesi için geçici dosya oluşturulamadı.', 'asosyoloji' ) );
	}

	$response = wp_remote_get(
		$package,
		array(
			'headers'     => asosyoloji_github_headers( true ),
			'timeout'     => 30,
			'redirection' => 5,
			'stream'      => true,
			'filename'    => $temp_file,
		)
	);

	if ( is_wp_error( $response ) ) {
		wp_delete_file( $temp_file );
		return $response;
	}

	$status = wp_remote_retrieve_response_code( $response );
	if ( 200 !== $status ) {
		wp_delete_file( $temp_file );
		return new WP_Error(
			'asosyoloji_github_asset_http',
			sprintf(
				/* translators: %d: GitHub release asset HTTP status code. */
				__( 'GitHub tema paketi indirilemedi. HTTP %d.', 'asosyoloji' ),
				$status
			)
		);
	}

	return $temp_file;
}
add_filter( 'upgrader_pre_download', 'asosyoloji_github_pre_download', 10, 3 );

/**
 * Clear the release cache when WordPress explicitly checks for updates.
 */
function asosyoloji_github_clear_update_cache() {
	delete_site_transient( 'asosyoloji_github_latest_release' );
}
add_action( 'wp_update_themes', 'asosyoloji_github_clear_update_cache' );
