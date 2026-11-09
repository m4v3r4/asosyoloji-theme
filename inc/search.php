<?php
/**
 * Search helpers: author matches shown above post results.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fold Turkish and other accented letters so "sumer", "Sümer" and "SÜMER" match,
 * and "ı/i/İ/I" are treated alike (as the Asosyoloji app does).
 *
 * @param string $text Text.
 * @return string
 */
function asosyoloji_search_fold( $text ) {
	$text = strtr(
		(string) $text,
		array(
			'İ' => 'i',
			'I' => 'i',
			'ı' => 'i',
			'Ş' => 's',
			'ş' => 's',
			'Ğ' => 'g',
			'ğ' => 'g',
			'Ç' => 'c',
			'ç' => 'c',
			'Ö' => 'o',
			'ö' => 'o',
			'Ü' => 'u',
			'ü' => 'u',
		)
	);
	$text = remove_accents( $text );
	$text = function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );

	return trim( preg_replace( '/\s+/u', ' ', $text ) );
}

/**
 * Authors with published posts, cached; invalidated when posts or profiles change.
 *
 * @return array<int, array{id: int, name: string, folded: string, count: int}>
 */
function asosyoloji_search_author_index() {
	$cached = get_transient( 'asosyoloji_author_index' );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$users = get_users(
		array(
			'has_published_posts' => array( 'post' ),
			'fields'              => array( 'ID', 'display_name' ),
			'number'              => 2000,
		)
	);

	$index = array();
	foreach ( $users as $user ) {
		$name    = asosyoloji_public_author_name( $user->display_name );
		$index[] = array(
			'id'     => (int) $user->ID,
			'name'   => $name,
			'folded' => asosyoloji_search_fold( $name ),
			'count'  => (int) count_user_posts( $user->ID, 'post', true ),
		);
	}

	set_transient( 'asosyoloji_author_index', $index, DAY_IN_SECONDS );

	return $index;
}

/**
 * Drop the author index when it could be stale.
 */
function asosyoloji_flush_author_index() {
	delete_transient( 'asosyoloji_author_index' );
}
add_action( 'save_post_post', 'asosyoloji_flush_author_index' );
add_action( 'deleted_post', 'asosyoloji_flush_author_index' );
add_action( 'profile_update', 'asosyoloji_flush_author_index' );
add_action( 'user_register', 'asosyoloji_flush_author_index' );
add_action( 'deleted_user', 'asosyoloji_flush_author_index' );

/**
 * Authors whose public name matches the search query.
 *
 * Every word of the query must appear in the name; matches that start the
 * name or a word in it come first, then authors with more posts.
 *
 * @param string $query Search query.
 * @param int    $limit Maximum number of authors.
 * @return array
 */
function asosyoloji_search_authors( $query, $limit = 8 ) {
	$folded = asosyoloji_search_fold( $query );
	if ( function_exists( 'mb_strlen' ) ? mb_strlen( $folded ) < 2 : strlen( $folded ) < 2 ) {
		return array();
	}

	$words   = array_filter( explode( ' ', $folded ) );
	$matches = array();

	foreach ( asosyoloji_search_author_index() as $author ) {
		$all = true;
		foreach ( $words as $word ) {
			if ( false === strpos( $author['folded'], $word ) ) {
				$all = false;
				break;
			}
		}

		if ( $all ) {
			$author['rank'] = preg_match( '/(^|\s)' . preg_quote( $words ? reset( $words ) : $folded, '/' ) . '/u', $author['folded'] ) ? 0 : 1;
			$matches[]      = $author;
		}
	}

	usort(
		$matches,
		static function ( $a, $b ) {
			return array( $a['rank'], -$a['count'], $a['name'] ) <=> array( $b['rank'], -$b['count'], $b['name'] );
		}
	);

	return array_slice( $matches, 0, $limit );
}

/**
 * Author results block for the search page.
 *
 * @param string $query Search query.
 * @return string
 */
function asosyoloji_search_authors_markup( $query ) {
	$authors = asosyoloji_search_authors( $query );
	if ( ! $authors ) {
		return '';
	}

	ob_start();
	?>
	<section class="search-authors" aria-labelledby="search-authors-title">
		<h2 class="search-authors__title" id="search-authors-title"><?php esc_html_e( 'Yazarlar', 'asosyoloji' ); ?></h2>
		<ul class="search-authors__list">
			<?php foreach ( $authors as $author ) : ?>
				<li>
					<a class="search-author" href="<?php echo esc_url( get_author_posts_url( $author['id'] ) ); ?>">
						<?php echo get_avatar( $author['id'], 56, '', '', array( 'class' => 'search-author__avatar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="search-author__text">
							<span class="search-author__name"><?php echo esc_html( $author['name'] ); ?></span>
							<span class="search-author__count">
								<?php
								/* translators: %s: Number of posts. */
								echo esc_html( sprintf( _n( '%s yazı', '%s yazı', $author['count'], 'asosyoloji' ), number_format_i18n( $author['count'] ) ) );
								?>
							</span>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php
	return (string) ob_get_clean();
}
