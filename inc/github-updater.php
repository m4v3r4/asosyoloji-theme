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
 * Fetch and cache the latest public GitHub release metadata.
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
			'headers' => array(
				'Accept'               => 'application/vnd.github+json',
				'X-GitHub-Api-Version' => '2022-11-28',
				'User-Agent'           => 'Asosyoloji-WordPress-Theme/' . ASOSYOLOJI_VERSION,
			),
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
		return new WP_Error(
			'asosyoloji_github_release_invalid',
			__( 'GitHub release yanıtı geçersiz.', 'asosyoloji' )
		);
	}

	$asset = array();
	foreach ( $data['assets'] ?? array() as $candidate ) {
		if ( ASOSYOLOJI_GITHUB_RELEASE_ASSET === ( $candidate['name'] ?? '' ) ) {
			$asset = $candidate;
			break;
		}
	}

	$release = array(
		'version'      => ltrim( (string) $data['tag_name'], 'vV' ),
		'name'         => sanitize_text_field( $data['name'] ?? $data['tag_name'] ),
		'html_url'     => esc_url_raw( $data['html_url'] ?? '' ),
		'body'         => wp_kses_post( $data['body'] ?? '' ),
		'download_url' => esc_url_raw( $asset['browser_download_url'] ?? '' ),
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

	if (
		is_wp_error( $release ) ||
		empty( $release['version'] ) ||
		empty( $release['download_url'] ) ||
		version_compare( $release['version'], ASOSYOLOJI_VERSION, '<=' )
	) {
		return $transient;
	}

	if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
		$transient->response = array();
	}

	$theme_slug = get_template();

	$transient->response[ $theme_slug ] = array(
		'theme'        => $theme_slug,
		'new_version'  => $release['version'],
		'url'          => $release['html_url'],
		'package'      => $release['download_url'],
		'requires'     => '6.0',
		'requires_php' => '8.0',
	);

	return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'asosyoloji_github_theme_update' );

/**
 * Provide GitHub release notes in the native WordPress theme information modal.
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
		'download_link' => $release['download_url'],
		'sections'      => array(
			'description' => wpautop( esc_html( wp_get_theme()->get( 'Description' ) ) ),
			'changelog'   => wpautop( wp_kses_post( $release['body'] ) ),
		),
	);
}
add_filter( 'themes_api', 'asosyoloji_github_theme_information', 20, 3 );


/**
 * Clear cached GitHub release data when WordPress refreshes theme updates.
 */
function asosyoloji_github_force_refresh() {
	delete_site_transient( 'asosyoloji_github_latest_release' );
}
add_action( 'wp_update_themes', 'asosyoloji_github_force_refresh', 1 );
