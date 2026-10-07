<?php
/**
 * GEO-oriented editorial content helpers.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_meta_lines( $post_id, $key ) {
	$value = (string) get_post_meta( $post_id, $key, true );

	if ( '' === trim( $value ) ) {
		return array();
	}

	return array_values(
		array_filter(
			array_map(
				'trim',
				preg_split( '/\r\n|\r|\n/', $value )
			)
		)
	);
}

function asosyoloji_get_geo_summary( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	return trim( (string) get_post_meta( $post_id, '_asosyoloji_geo_summary', true ) );
}

function asosyoloji_get_key_points( $post_id = 0 ) {
	return asosyoloji_meta_lines( $post_id ? $post_id : get_the_ID(), '_asosyoloji_key_points' );
}

function asosyoloji_get_about_entities( $post_id = 0 ) {
	return asosyoloji_meta_lines( $post_id ? $post_id : get_the_ID(), '_asosyoloji_about' );
}

function asosyoloji_get_citations( $post_id = 0 ) {
	$urls = asosyoloji_meta_lines( $post_id ? $post_id : get_the_ID(), '_asosyoloji_citations' );

	return array_values(
		array_filter(
			array_map( 'esc_url_raw', $urls )
		)
	);
}

function asosyoloji_geo_summary_markup( $post_id = 0 ) {
	$post_id    = $post_id ? $post_id : get_the_ID();
	$summary    = asosyoloji_get_geo_summary( $post_id );
	$key_points = asosyoloji_get_key_points( $post_id );

	if ( ! get_theme_mod( 'aso_show_geo_summary', true ) || ( ! $summary && ! $key_points ) ) {
		return '';
	}

	ob_start();
	?>
	<aside class="entry-geo-summary" aria-label="<?php esc_attr_e( 'Yazı özeti', 'asosyoloji' ); ?>">
		<?php if ( $summary ) : ?>
			<div class="entry-geo-summary__summary">
				<div class="section-kicker"><?php esc_html_e( 'Kısaca', 'asosyoloji' ); ?></div>
				<p><?php echo esc_html( $summary ); ?></p>
			</div>
		<?php endif; ?>

		<?php if ( $key_points ) : ?>
			<div class="entry-geo-summary__points">
				<h2><?php esc_html_e( 'Bu yazıda', 'asosyoloji' ); ?></h2>
				<ul>
					<?php foreach ( $key_points as $point ) : ?>
						<li><?php echo esc_html( $point ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</aside>
	<?php
	return (string) ob_get_clean();
}

function asosyoloji_citations_markup( $post_id = 0 ) {
	$post_id   = $post_id ? $post_id : get_the_ID();
	$citations = asosyoloji_get_citations( $post_id );

	if ( ! get_theme_mod( 'aso_show_citations', true ) || ! $citations ) {
		return '';
	}

	ob_start();
	?>
	<section class="entry-citations" aria-labelledby="entry-citations-title">
		<div class="section-kicker"><?php esc_html_e( 'Referans', 'asosyoloji' ); ?></div>
		<h2 id="entry-citations-title"><?php esc_html_e( 'Kaynaklar', 'asosyoloji' ); ?></h2>
		<ol>
			<?php foreach ( $citations as $url ) : ?>
				<?php $host = wp_parse_url( $url, PHP_URL_HOST ); ?>
				<li>
					<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html( $host ? $host : $url ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ol>
	</section>
	<?php
	return (string) ob_get_clean();
}

function asosyoloji_auto_toc_content( $content ) {
	if (
		! is_single() ||
		! in_the_loop() ||
		! is_main_query() ||
		! get_theme_mod( 'aso_show_auto_toc', true )
	) {
		return $content;
	}

	$headings = array();
	$used_ids = array();

	$updated = preg_replace_callback(
		'/<h([23])([^>]*)>(.*?)<\/h\1>/is',
		static function ( $matches ) use ( &$headings, &$used_ids ) {
			$level = absint( $matches[1] );
			$attrs = $matches[2];
			$inner = $matches[3];
			$text  = trim( wp_strip_all_tags( $inner ) );

			if ( '' === $text ) {
				return $matches[0];
			}

			if ( preg_match( '/\sid=["\']([^"\']+)["\']/i', $attrs, $id_match ) ) {
				$id = sanitize_title( $id_match[1] );
			} else {
				$base = sanitize_title( $text );
				$id   = $base ? $base : 'bolum';
				$try  = $id;
				$i    = 2;

				while ( in_array( $try, $used_ids, true ) ) {
					$try = $id . '-' . $i;
					++$i;
				}

				$id    = $try;
				$attrs = rtrim( $attrs ) . ' id="' . esc_attr( $id ) . '"';
			}

			$used_ids[] = $id;
			$headings[] = array(
				'level' => $level,
				'id'    => $id,
				'text'  => $text,
			);

			return sprintf(
				'<h%d%s>%s</h%d>',
				$level,
				$attrs,
				$inner,
				$level
			);
		},
		$content
	);

	if ( count( $headings ) < 3 ) {
		return $updated;
	}

	ob_start();
	?>
	<nav class="entry-toc" aria-labelledby="entry-toc-title">
		<div class="section-kicker"><?php esc_html_e( 'İçerik', 'asosyoloji' ); ?></div>
		<h2 id="entry-toc-title"><?php esc_html_e( 'İçindekiler', 'asosyoloji' ); ?></h2>
		<ol>
			<?php foreach ( $headings as $heading ) : ?>
				<li class="entry-toc__level-<?php echo esc_attr( $heading['level'] ); ?>">
					<a href="#<?php echo esc_attr( $heading['id'] ); ?>"><?php echo esc_html( $heading['text'] ); ?></a>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
	$toc = ob_get_clean();

	return $toc . $updated;
}
add_filter( 'the_content', 'asosyoloji_auto_toc_content', 12 );
