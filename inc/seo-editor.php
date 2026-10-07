<?php
/**
 * SEO/GEO editor fields.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_seo_meta_box() {
	add_meta_box(
		'asosyoloji-seo-geo',
		__( 'Asosyoloji SEO / GEO', 'asosyoloji' ),
		'asosyoloji_seo_meta_box_render',
		'post',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'asosyoloji_seo_meta_box' );

function asosyoloji_seo_meta_box_render( $post ) {
	wp_nonce_field( 'asosyoloji_save_seo_meta', 'asosyoloji_seo_meta_nonce' );

	$description = get_post_meta( $post->ID, '_asosyoloji_seo_description', true );
	$summary     = get_post_meta( $post->ID, '_asosyoloji_geo_summary', true );
	$key_points  = get_post_meta( $post->ID, '_asosyoloji_key_points', true );
	$citations   = get_post_meta( $post->ID, '_asosyoloji_citations', true );
	$about       = get_post_meta( $post->ID, '_asosyoloji_about', true );
	?>
	<p>
		<label for="asosyoloji-seo-description"><strong><?php esc_html_e( 'SEO açıklaması', 'asosyoloji' ); ?></strong></label>
		<textarea class="widefat" rows="3" id="asosyoloji-seo-description" name="asosyoloji_seo_description"><?php echo esc_textarea( $description ); ?></textarea>
		<small><?php esc_html_e( 'Boşsa excerpt, o da yoksa yazı metninden otomatik üretilir.', 'asosyoloji' ); ?></small>
	</p>

	<p>
		<label for="asosyoloji-geo-summary"><strong><?php esc_html_e( 'Kısa özet', 'asosyoloji' ); ?></strong></label>
		<textarea class="widefat" rows="4" id="asosyoloji-geo-summary" name="asosyoloji_geo_summary"><?php echo esc_textarea( $summary ); ?></textarea>
		<small><?php esc_html_e( 'Yazının ana tezini 2–4 cümlede açık ve doğrudan özetleyin.', 'asosyoloji' ); ?></small>
	</p>

	<p>
		<label for="asosyoloji-key-points"><strong><?php esc_html_e( 'Bu yazıda', 'asosyoloji' ); ?></strong></label>
		<textarea class="widefat" rows="5" id="asosyoloji-key-points" name="asosyoloji_key_points"><?php echo esc_textarea( $key_points ); ?></textarea>
		<small><?php esc_html_e( 'Her satıra bir ana başlık / çıkarım yazın.', 'asosyoloji' ); ?></small>
	</p>

	<p>
		<label for="asosyoloji-about"><strong><?php esc_html_e( 'Ana konular / entity’ler', 'asosyoloji' ); ?></strong></label>
		<textarea class="widefat" rows="4" id="asosyoloji-about" name="asosyoloji_about"><?php echo esc_textarea( $about ); ?></textarea>
		<small><?php esc_html_e( 'Her satıra bir kişi, kurum, kavram, kitap veya temel konu yazın.', 'asosyoloji' ); ?></small>
	</p>

	<p>
		<label for="asosyoloji-citations"><strong><?php esc_html_e( 'Kaynak URL’leri', 'asosyoloji' ); ?></strong></label>
		<textarea class="widefat" rows="6" id="asosyoloji-citations" name="asosyoloji_citations"><?php echo esc_textarea( $citations ); ?></textarea>
		<small><?php esc_html_e( 'Her satıra bir kaynak URL’si yazın. Yazı altında Kaynaklar olarak gösterilebilir ve schema citation alanına eklenir.', 'asosyoloji' ); ?></small>
	</p>
	<?php
}

function asosyoloji_save_seo_meta( $post_id ) {
	if (
		! isset( $_POST['asosyoloji_seo_meta_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['asosyoloji_seo_meta_nonce'] ) ), 'asosyoloji_save_seo_meta' ) ||
		! current_user_can( 'edit_post', $post_id ) ||
		wp_is_post_revision( $post_id )
	) {
		return;
	}

	$fields = array(
		'asosyoloji_seo_description' => array( '_asosyoloji_seo_description', 'sanitize_textarea_field' ),
		'asosyoloji_geo_summary'     => array( '_asosyoloji_geo_summary', 'sanitize_textarea_field' ),
		'asosyoloji_key_points'      => array( '_asosyoloji_key_points', 'sanitize_textarea_field' ),
		'asosyoloji_about'           => array( '_asosyoloji_about', 'sanitize_textarea_field' ),
		'asosyoloji_citations'       => array( '_asosyoloji_citations', 'sanitize_textarea_field' ),
	);

	foreach ( $fields as $input => $config ) {
		$value = isset( $_POST[ $input ] ) ? call_user_func( $config[1], wp_unslash( $_POST[ $input ] ) ) : '';

		if ( '' === trim( $value ) ) {
			delete_post_meta( $post_id, $config[0] );
		} else {
			update_post_meta( $post_id, $config[0], $value );
		}
	}
}
add_action( 'save_post_post', 'asosyoloji_save_seo_meta' );
