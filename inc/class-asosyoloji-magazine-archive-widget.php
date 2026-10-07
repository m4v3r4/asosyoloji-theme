<?php
/**
 * Magazine archive widget.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Magazine archive widget.
 */
class Asosyoloji_Magazine_Archive_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'asosyoloji_magazine_archive',
			__( 'Asosyoloji: Basılı Sayılar (PDF)', 'asosyoloji' ),
			array(
				'description' => __( 'Basılı dergi sayılarını kapak görseli, sayı başlığı ve PDF dosyasıyla listeler. WordPress yazı arşiviyle ilişkili değildir.', 'asosyoloji' ),
			)
		);
	}

	/**
	 * Frontend output.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {
		echo wp_kses_post( $args['before_widget'] );

		echo wp_kses_post(
			asosyoloji_render_magazine_archive(
				array(
					'title'  => isset( $instance['title'] ) ? $instance['title'] : __( 'Basılı Sayılar', 'asosyoloji' ),
					'items'  => isset( $instance['items'] ) && is_array( $instance['items'] ) ? $instance['items'] : array(),
					'layout' => isset( $instance['layout'] ) ? $instance['layout'] : 'grid',
				)
			)
		);

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Save settings.
	 *
	 * @param array $new_instance New values.
	 * @param array $old_instance Old values.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$items = array();

		if ( ! empty( $new_instance['items'] ) && is_array( $new_instance['items'] ) ) {
			foreach ( $new_instance['items'] as $item ) {
				$title         = sanitize_text_field( $item['title'] ?? '' );
				$pdf_url       = esc_url_raw( $item['pdf_url'] ?? '' );
				$cover_url     = esc_url_raw( $item['cover_url'] ?? '' );
				$attachment_id = absint( $item['attachment_id'] ?? 0 );

				if ( $title && $pdf_url ) {
					$items[] = array(
						'title'         => $title,
						'pdf_url'       => $pdf_url,
						'cover_url'     => $cover_url,
						'attachment_id' => $attachment_id,
					);
				}
			}
		}

		return array(
			'title'  => sanitize_text_field( $new_instance['title'] ?? '' ),
			'layout' => in_array( $new_instance['layout'] ?? 'list', array( 'grid', 'list' ), true ) ? $new_instance['layout'] : 'list',
			'items'  => $items,
		);
	}

	/**
	 * Admin form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title  = $instance['title'] ?? __( 'Basılı Sayılar', 'asosyoloji' );
		$layout = $instance['layout'] ?? 'list';
		$items  = isset( $instance['items'] ) && is_array( $instance['items'] ) ? $instance['items'] : array();

		if ( empty( $items ) ) {
			$items[] = array(
				'title'     => '',
				'pdf_url'   => '',
				'cover_url'     => '',
				'attachment_id' => 0,
			);
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Başlık', 'asosyoloji' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><?php esc_html_e( 'Görünüm', 'asosyoloji' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
				<option value="list" <?php selected( $layout, 'list' ); ?>><?php esc_html_e( 'Liste', 'asosyoloji' ); ?></option>
				<option value="grid" <?php selected( $layout, 'grid' ); ?>><?php esc_html_e( 'Kapak grid', 'asosyoloji' ); ?></option>
			</select>
		</p>

		<div class="aso-magazine-widget-admin" data-magazine-widget>
			<?php foreach ( $items as $index => $item ) : ?>
				<div class="aso-magazine-widget-admin__row" data-magazine-row>
					<p>
						<label><?php esc_html_e( 'Sayı / başlık', 'asosyoloji' ); ?></label>
						<input class="widefat" data-magazine-title type="text" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $item['title'] ?? '' ); ?>">
					</p>

					<p>
						<label><?php esc_html_e( 'PDF dosyası URL', 'asosyoloji' ); ?></label>
						<input class="widefat" data-magazine-pdf-url type="url" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[<?php echo esc_attr( $index ); ?>][pdf_url]" value="<?php echo esc_url( $item['pdf_url'] ?? '' ); ?>">
						<input data-magazine-attachment-id type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[<?php echo esc_attr( $index ); ?>][attachment_id]" value="<?php echo esc_attr( absint( $item['attachment_id'] ?? 0 ) ); ?>">
						<button type="button" class="button" data-magazine-select-pdf><?php esc_html_e( 'Medya Kütüphanesinden PDF Seç', 'asosyoloji' ); ?></button>
						<small style="display:block;margin-top:6px;"><?php esc_html_e( 'PDF önizlemesi destekleniyorsa ilk sayfa otomatik kapak olarak kullanılır.', 'asosyoloji' ); ?></small>
					</p>

					<p>
						<label><?php esc_html_e( 'Manuel kapak (isteğe bağlı)', 'asosyoloji' ); ?></label>
						<input class="widefat" data-magazine-cover-url type="url" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[<?php echo esc_attr( $index ); ?>][cover_url]" value="<?php echo esc_url( $item['cover_url'] ?? '' ); ?>">
						<button type="button" class="button" data-magazine-select-cover><?php esc_html_e( 'Kapak seç', 'asosyoloji' ); ?></button>
					</p>

					<p>
						<button type="button" class="button-link-delete" data-magazine-remove><?php esc_html_e( 'Bu sayıyı kaldır', 'asosyoloji' ); ?></button>
					</p>
					<hr>
				</div>
			<?php endforeach; ?>

			<p>
				<button type="button" class="button" data-magazine-add><?php esc_html_e( 'Yeni sayı ekle', 'asosyoloji' ); ?></button>
			</p>

			<template data-magazine-template>
				<div class="aso-magazine-widget-admin__row" data-magazine-row>
					<p>
						<label><?php esc_html_e( 'Sayı / başlık', 'asosyoloji' ); ?></label>
						<input class="widefat" data-magazine-title type="text" name="__NAME__[__INDEX__][title]" value="">
					</p>
					<p>
						<label><?php esc_html_e( 'PDF dosyası URL', 'asosyoloji' ); ?></label>
						<input class="widefat" data-magazine-pdf-url type="url" name="__NAME__[__INDEX__][pdf_url]" value="">
						<input data-magazine-attachment-id type="hidden" name="__NAME__[__INDEX__][attachment_id]" value="0">
						<button type="button" class="button" data-magazine-select-pdf><?php esc_html_e( 'Medya Kütüphanesinden PDF Seç', 'asosyoloji' ); ?></button>
						<small style="display:block;margin-top:6px;"><?php esc_html_e( 'PDF önizlemesi destekleniyorsa ilk sayfa otomatik kapak olarak kullanılır.', 'asosyoloji' ); ?></small>
					</p>
					<p>
						<label><?php esc_html_e( 'Manuel kapak (isteğe bağlı)', 'asosyoloji' ); ?></label>
						<input class="widefat" data-magazine-cover-url type="url" name="__NAME__[__INDEX__][cover_url]" value="">
						<button type="button" class="button" data-magazine-select-cover><?php esc_html_e( 'Kapak seç', 'asosyoloji' ); ?></button>
					</p>
					<p>
						<button type="button" class="button-link-delete" data-magazine-remove><?php esc_html_e( 'Bu sayıyı kaldır', 'asosyoloji' ); ?></button>
					</p>
					<hr>
				</div>
			</template>

			<input type="hidden" data-magazine-name value="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>">
		</div>
		<?php
	}
}
