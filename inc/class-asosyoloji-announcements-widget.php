<?php
/**
 * Announcement widget.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dedicated announcement widget.
 */
class Asosyoloji_Announcements_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'asosyoloji_announcements',
			__( 'Asosyoloji: Duyurular', 'asosyoloji' ),
			array(
				'description' => __( 'Duyuru kategorisindeki yazıları pano/ilan görünümünde gösterir.', 'asosyoloji' ),
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
			asosyoloji_render_announcements(
				array(
					'title'        => $instance['title'] ?? __( 'Duyurular', 'asosyoloji' ),
					'category'     => absint( $instance['category'] ?? 0 ),
					'count'        => absint( $instance['count'] ?? 5 ),
					'show_excerpt' => ! empty( $instance['show_excerpt'] ),
					'show_date'    => ! empty( $instance['show_date'] ),
					'show_button'  => ! empty( $instance['show_button'] ),
					'compact'      => ! empty( $instance['compact'] ),
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
		return array(
			'title'        => sanitize_text_field( $new_instance['title'] ?? '' ),
			'category'     => absint( $new_instance['category'] ?? 0 ),
			'count'        => min( 12, max( 1, absint( $new_instance['count'] ?? 5 ) ) ),
			'show_excerpt' => ! empty( $new_instance['show_excerpt'] ) ? 1 : 0,
			'show_date'    => ! empty( $new_instance['show_date'] ) ? 1 : 0,
			'show_button'  => ! empty( $new_instance['show_button'] ) ? 1 : 0,
			'compact'      => ! empty( $new_instance['compact'] ) ? 1 : 0,
		);
	}

	/**
	 * Admin form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title        = $instance['title'] ?? __( 'Duyurular', 'asosyoloji' );
		$category     = absint( $instance['category'] ?? 0 );
		$count        = absint( $instance['count'] ?? 5 );
		$show_excerpt = ! isset( $instance['show_excerpt'] ) || ! empty( $instance['show_excerpt'] );
		$show_date    = ! isset( $instance['show_date'] ) || ! empty( $instance['show_date'] );
		$show_button  = ! isset( $instance['show_button'] ) || ! empty( $instance['show_button'] );
		$compact      = ! empty( $instance['compact'] );
		$categories   = get_categories( array( 'hide_empty' => false ) );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Başlık', 'asosyoloji' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Duyuru kategorisi', 'asosyoloji' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>">
				<option value="0"><?php esc_html_e( 'Varsayılan: duyurular', 'asosyoloji' ); ?></option>
				<?php foreach ( $categories as $term ) : ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $category, $term->term_id ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Duyuru sayısı', 'asosyoloji' ); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" min="1" max="12" value="<?php echo esc_attr( $count ); ?>">
		</p>

		<p>
			<label><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>" value="1" <?php checked( $show_date ); ?>> <?php esc_html_e( 'Tarih rozetini göster', 'asosyoloji' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_excerpt' ) ); ?>" value="1" <?php checked( $show_excerpt ); ?>> <?php esc_html_e( 'Kısa açıklamayı göster', 'asosyoloji' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_button' ) ); ?>" value="1" <?php checked( $show_button ); ?>> <?php esc_html_e( 'Detay bağlantısını göster', 'asosyoloji' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'compact' ) ); ?>" value="1" <?php checked( $compact ); ?>> <?php esc_html_e( 'Kompakt görünüm', 'asosyoloji' ); ?></label>
		</p>
		<?php
	}
}
