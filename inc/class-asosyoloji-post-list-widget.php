<?php
/**
 * Post list widget class.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post list widget.
 */
class Asosyoloji_Post_List_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'asosyoloji_post_list',
			__( 'Asosyoloji: Yazı Listesi', 'asosyoloji' ),
			array(
				'description' => __( 'Son yazıları veya belirli bir kategoriyi temayla uyumlu liste, grid ya da manşet görünümünde gösterir.', 'asosyoloji' ),
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
			asosyoloji_render_post_collection(
				array(
					'title'              => isset( $instance['title'] ) ? $instance['title'] : '',
					'category'           => isset( $instance['category'] ) ? absint( $instance['category'] ) : 0,
					'exclude_categories' => isset( $instance['exclude_categories'] ) ? (array) $instance['exclude_categories'] : array(),
					'count'              => isset( $instance['count'] ) ? absint( $instance['count'] ) : 5,
					'layout'             => isset( $instance['layout'] ) ? $instance['layout'] : 'list',
					'show_image'         => ! empty( $instance['show_image'] ),
					'show_excerpt'       => ! empty( $instance['show_excerpt'] ),
					'show_meta'          => ! empty( $instance['show_meta'] ),
					'heading_level'      => 'h3',
					'author'             => isset( $instance['author'] ) ? absint( $instance['author'] ) : 0,
					'orderby'            => isset( $instance['orderby'] ) ? $instance['orderby'] : 'date',
					'order'              => isset( $instance['order'] ) ? $instance['order'] : 'DESC',
					'date_after'         => isset( $instance['date_after'] ) ? $instance['date_after'] : '',
					'date_before'        => isset( $instance['date_before'] ) ? $instance['date_before'] : '',
					'offset'             => isset( $instance['offset'] ) ? absint( $instance['offset'] ) : 0,
					'include_sticky'     => ! empty( $instance['include_sticky'] ),
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
			'title'              => sanitize_text_field( $new_instance['title'] ?? '' ),
			'category'           => absint( $new_instance['category'] ?? 0 ),
			'exclude_categories' => asosyoloji_sanitize_category_ids( $new_instance['exclude_categories'] ?? array() ),
			'count'              => min( 12, max( 1, absint( $new_instance['count'] ?? 5 ) ) ),
			'layout'             => in_array( $new_instance['layout'] ?? 'list', array( 'list', 'compact', 'grid', 'feature' ), true ) ? $new_instance['layout'] : 'list',
			'show_image'         => ! empty( $new_instance['show_image'] ) ? 1 : 0,
			'show_excerpt'       => ! empty( $new_instance['show_excerpt'] ) ? 1 : 0,
			'show_meta'          => ! empty( $new_instance['show_meta'] ) ? 1 : 0,
			'author'             => absint( $new_instance['author'] ?? 0 ),
			'orderby'            => in_array( $new_instance['orderby'] ?? 'date', array( 'date', 'modified', 'title', 'comment_count', 'rand' ), true ) ? $new_instance['orderby'] : 'date',
			'order'              => in_array( strtoupper( $new_instance['order'] ?? 'DESC' ), array( 'ASC', 'DESC' ), true ) ? strtoupper( $new_instance['order'] ) : 'DESC',
			'date_after'         => sanitize_text_field( $new_instance['date_after'] ?? '' ),
			'date_before'        => sanitize_text_field( $new_instance['date_before'] ?? '' ),
			'offset'             => min( 100, max( 0, absint( $new_instance['offset'] ?? 0 ) ) ),
			'include_sticky'     => ! empty( $new_instance['include_sticky'] ) ? 1 : 0,
		);
	}

	/**
	 * Admin form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title      = $instance['title'] ?? __( 'Son Yazılar', 'asosyoloji' );
		$category   = absint( $instance['category'] ?? 0 );
		$excluded   = asosyoloji_sanitize_category_ids( $instance['exclude_categories'] ?? array() );
		$count      = absint( $instance['count'] ?? 5 );
		$layout     = $instance['layout'] ?? 'list';
		$show_image = ! isset( $instance['show_image'] ) || ! empty( $instance['show_image'] );
		$show_meta  = ! isset( $instance['show_meta'] ) || ! empty( $instance['show_meta'] );
		$show_excerpt = ! empty( $instance['show_excerpt'] );
		$categories = get_categories( array( 'hide_empty' => false ) );
		$authors     = get_users(
			array(
				'who'     => 'authors',
				'orderby' => 'display_name',
			)
		);
		$author      = absint( $instance['author'] ?? 0 );
		$orderby     = $instance['orderby'] ?? 'date';
		$order       = $instance['order'] ?? 'DESC';
		$date_after  = $instance['date_after'] ?? '';
		$date_before = $instance['date_before'] ?? '';
		$offset      = absint( $instance['offset'] ?? 0 );
		$include_sticky = ! empty( $instance['include_sticky'] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Başlık', 'asosyoloji' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Kategori', 'asosyoloji' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>">
				<option value="0"><?php esc_html_e( 'Tüm kategoriler', 'asosyoloji' ); ?></option>
				<?php foreach ( $categories as $term ) : ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $category, $term->term_id ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'exclude_categories' ) ); ?>"><?php esc_html_e( 'Hariç kategoriler', 'asosyoloji' ); ?></label>
			<select class="widefat" multiple size="6" id="<?php echo esc_attr( $this->get_field_id( 'exclude_categories' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude_categories' ) ); ?>[]">
				<?php foreach ( $categories as $term ) : ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( in_array( (int) $term->term_id, $excluded, true ) ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
			<small><?php esc_html_e( 'Ctrl/Cmd ile birden fazla kategori seçebilirsiniz.', 'asosyoloji' ); ?></small>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Yazı sayısı', 'asosyoloji' ); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" min="1" max="12" value="<?php echo esc_attr( $count ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><?php esc_html_e( 'Görünüm', 'asosyoloji' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
				<option value="list" <?php selected( $layout, 'list' ); ?>><?php esc_html_e( 'Görselli liste', 'asosyoloji' ); ?></option>
				<option value="compact" <?php selected( $layout, 'compact' ); ?>><?php esc_html_e( 'Kompakt liste', 'asosyoloji' ); ?></option>
				<option value="grid" <?php selected( $layout, 'grid' ); ?>><?php esc_html_e( 'Kart grid', 'asosyoloji' ); ?></option>
				<option value="feature" <?php selected( $layout, 'feature' ); ?>><?php esc_html_e( 'Bir büyük + liste', 'asosyoloji' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'author' ) ); ?>"><?php esc_html_e( 'Yazar', 'asosyoloji' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'author' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'author' ) ); ?>">
				<option value="0"><?php esc_html_e( 'Tüm yazarlar', 'asosyoloji' ); ?></option>
				<?php foreach ( $authors as $user ) : ?>
					<option value="<?php echo esc_attr( $user->ID ); ?>" <?php selected( $author, $user->ID ); ?>><?php echo esc_html( $user->display_name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Sıralama', 'asosyoloji' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
				<option value="date" <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Yayın tarihi', 'asosyoloji' ); ?></option>
				<option value="modified" <?php selected( $orderby, 'modified' ); ?>><?php esc_html_e( 'Güncellenme tarihi', 'asosyoloji' ); ?></option>
				<option value="title" <?php selected( $orderby, 'title' ); ?>><?php esc_html_e( 'Başlık', 'asosyoloji' ); ?></option>
				<option value="comment_count" <?php selected( $orderby, 'comment_count' ); ?>><?php esc_html_e( 'Yorum sayısı', 'asosyoloji' ); ?></option>
				<option value="rand" <?php selected( $orderby, 'rand' ); ?>><?php esc_html_e( 'Rastgele', 'asosyoloji' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Sıralama yönü', 'asosyoloji' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
				<option value="DESC" <?php selected( $order, 'DESC' ); ?>><?php esc_html_e( 'Azalan', 'asosyoloji' ); ?></option>
				<option value="ASC" <?php selected( $order, 'ASC' ); ?>><?php esc_html_e( 'Artan', 'asosyoloji' ); ?></option>
			</select>
		</p>

		<p>
			<label><?php esc_html_e( 'Tarih aralığı', 'asosyoloji' ); ?></label><br>
			<input type="date" name="<?php echo esc_attr( $this->get_field_name( 'date_after' ) ); ?>" value="<?php echo esc_attr( $date_after ); ?>"> —
			<input type="date" name="<?php echo esc_attr( $this->get_field_name( 'date_before' ) ); ?>" value="<?php echo esc_attr( $date_before ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>"><?php esc_html_e( 'İlk N yazıyı atla (offset)', 'asosyoloji' ); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'offset' ) ); ?>" type="number" min="0" max="100" value="<?php echo esc_attr( $offset ); ?>">
		</p>

		<p>
			<label><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'include_sticky' ) ); ?>" value="1" <?php checked( $include_sticky ); ?>> <?php esc_html_e( 'Sabitlenmiş yazıları normal akışa dahil et', 'asosyoloji' ); ?></label>
		</p>

		<p>
			<label><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_image' ) ); ?>" value="1" <?php checked( $show_image ); ?>> <?php esc_html_e( 'Görselleri göster', 'asosyoloji' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_meta' ) ); ?>" value="1" <?php checked( $show_meta ); ?>> <?php esc_html_e( 'Yazar ve tarihi göster', 'asosyoloji' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_excerpt' ) ); ?>" value="1" <?php checked( $show_excerpt ); ?>> <?php esc_html_e( 'Özeti göster', 'asosyoloji' ); ?></label>
		</p>
		<?php
	}
}

