<?php
/**
 * Reading experience controls and helpers.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function asosyoloji_reading_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'aso_reading',
		array(
			'title' => __( 'Yazı Okuma Deneyimi', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$settings = array(
		'aso_show_reading_time'   => __( 'Tahmini okuma süresini göster', 'asosyoloji' ),
		'aso_show_reading_progress' => __( 'Okuma ilerleme çubuğunu göster', 'asosyoloji' ),
		'aso_show_reading_tools'  => __( 'Yazı boyutu araçlarını göster', 'asosyoloji' ),
		'aso_show_share'          => __( 'Paylaşım bağlantılarını göster', 'asosyoloji' ),
		'aso_show_author_box'     => __( 'Yazar kutusunu göster', 'asosyoloji' ),
		'aso_show_related_posts'  => __( 'Benzer yazıları göster', 'asosyoloji' ),
	);

	foreach ( $settings as $id => $label ) {
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
				'section' => 'aso_reading',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'aso_related_count',
		array(
			'default'           => 3,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'aso_related_count',
		array(
			'label'       => __( 'Benzer yazı sayısı', 'asosyoloji' ),
			'section'     => 'aso_reading',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 2,
				'max' => 6,
			),
		)
	);
}
add_action( 'customize_register', 'asosyoloji_reading_customize_register', 70 );

function asosyoloji_reading_time( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$content = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
	$words   = str_word_count( wp_strip_all_tags( $content ) );
	$minutes = max( 1, (int) ceil( $words / 220 ) );

	/* translators: %s: Estimated reading time in minutes. */
	return sprintf(
		_n( '%s dk okuma', '%s dk okuma', $minutes, 'asosyoloji' ),
		number_format_i18n( $minutes )
	);
}

function asosyoloji_share_links( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$url     = rawurlencode( get_permalink( $post_id ) );
	$title   = rawurlencode( get_the_title( $post_id ) );

	return array(
		'x'        => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
		'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
		'telegram' => 'https://t.me/share/url?url=' . $url . '&text=' . $title,
		'whatsapp' => 'https://wa.me/?text=' . $title . '%20' . $url,
		'bluesky'  => 'https://bsky.app/intent/compose?text=' . $title . '%20' . $url,
	);
}
