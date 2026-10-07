<?php
/**
 * Theme post listing widget and shortcode.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve category IDs from a comma-separated list of IDs or slugs.
 *
 * @param string|array $value Category identifiers.
 * @return int[]
 */
function asosyoloji_resolve_category_ids( $value ) {
	$items = is_array( $value ) ? $value : explode( ',', (string) $value );
	$ids   = array();

	foreach ( $items as $item ) {
		$item = trim( (string) $item );

		if ( '' === $item ) {
			continue;
		}

		if ( ctype_digit( $item ) ) {
			$ids[] = absint( $item );
			continue;
		}

		$term = get_category_by_slug( sanitize_title( $item ) );
		if ( $term ) {
			$ids[] = (int) $term->term_id;
		}
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Render a themed post collection.
 *
 * @param array $args Collection options.
 * @return string
 */
function asosyoloji_render_post_collection( $args = array() ) {
	$defaults = array(
		'title'              => '',
		'category'           => 0,
		'exclude_categories' => array(),
		'count'              => 5,
		'layout'             => 'list',
		'show_image'         => true,
		'show_excerpt'       => false,
		'show_meta'          => true,
		'heading_level'      => 'h2',
		'author'             => 0,
		'orderby'            => 'date',
		'order'              => 'DESC',
		'date_after'         => '',
		'date_before'        => '',
		'offset'             => 0,
		'include_sticky'     => false,
	);

	$args = wp_parse_args( $args, $defaults );

	$category           = absint( $args['category'] );
	$exclude_categories = asosyoloji_resolve_category_ids( $args['exclude_categories'] );
	$count              = min( 12, max( 1, absint( $args['count'] ) ) );
	$layout             = in_array( $args['layout'], array( 'list', 'compact', 'grid', 'feature' ), true ) ? $args['layout'] : 'list';
	$heading_level      = in_array( $args['heading_level'], array( 'h2', 'h3', 'h4' ), true ) ? $args['heading_level'] : 'h2';
	$author             = absint( $args['author'] );
	$orderby            = in_array( $args['orderby'], array( 'date', 'modified', 'title', 'comment_count', 'rand' ), true ) ? $args['orderby'] : 'date';
	$order              = in_array( strtoupper( (string) $args['order'] ), array( 'ASC', 'DESC' ), true ) ? strtoupper( (string) $args['order'] ) : 'DESC';
	$offset             = min( 100, max( 0, absint( $args['offset'] ) ) );
	$date_after         = sanitize_text_field( $args['date_after'] );
	$date_before        = sanitize_text_field( $args['date_before'] );
	$include_sticky     = (bool) $args['include_sticky'];

	$query_args = array(
		'posts_per_page'    => $count,
		'post_status'       => 'publish',
		'category__not_in'  => $exclude_categories,
		'ignore_sticky_posts' => ! $include_sticky,
		'orderby'             => $orderby,
		'order'               => $order,
		'offset'              => $offset,
	);

	if ( $category ) {
		$query_args['cat'] = $category;
	}

	if ( $author ) {
		$query_args['author'] = $author;
	}

	if ( $date_after || $date_before ) {
		$date_query = array( 'inclusive' => true );
		if ( $date_after ) {
			$date_query['after'] = $date_after;
		}
		if ( $date_before ) {
			$date_query['before'] = $date_before;
		}
		$query_args['date_query'] = array( $date_query );
	}

	$query = new WP_Query( $query_args );

	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<section class="aso-post-widget aso-post-widget--<?php echo esc_attr( $layout ); ?>">
		<?php if ( $args['title'] ) : ?>
			<<?php echo tag_escape( $heading_level ); ?> class="aso-post-widget__heading">
				<?php echo esc_html( $args['title'] ); ?>
			</<?php echo tag_escape( $heading_level ); ?>>
		<?php endif; ?>

		<div class="aso-post-widget__items">
			<?php
			$index = 0;
			while ( $query->have_posts() ) :
				$query->the_post();
				++$index;

				$image = asosyoloji_get_post_image(
					get_the_ID(),
					'medium_large',
					array( 'class' => 'aso-post-widget__img' )
				);
				?>
				<article class="aso-post-widget__item<?php echo ( 'feature' === $layout && 1 === $index ) ? ' is-featured' : ''; ?>">
					<?php if ( $args['show_image'] && $image ) : ?>
						<a class="aso-post-widget__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php echo wp_kses_post( $image ); ?>
						</a>
					<?php endif; ?>

					<div class="aso-post-widget__content">
						<?php
						$categories = get_the_category();
						if ( $categories ) :
							?>
							<div class="entry-kicker"><?php echo esc_html( $categories[0]->name ); ?></div>
						<?php endif; ?>

						<h3 class="aso-post-widget__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>

						<?php if ( $args['show_meta'] ) : ?>
							<div class="card-meta"><?php echo esc_html( get_the_author() ); ?> · <?php echo esc_html( get_the_date() ); ?></div>
						<?php endif; ?>

						<?php if ( $args['show_excerpt'] && ( 'compact' !== $layout || 1 === $index ) ) : ?>
							<div class="aso-post-widget__excerpt"><?php the_excerpt(); ?></div>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
	</section>
	<?php
	wp_reset_postdata();

	return (string) ob_get_clean();
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
		$authors     = get_users( array( 'who' => 'authors', 'orderby' => 'display_name' ) );
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

/**
 * Register theme widgets.
 */
function asosyoloji_register_theme_widgets() {
	register_widget( 'Asosyoloji_Post_List_Widget' );
}
add_action( 'widgets_init', 'asosyoloji_register_theme_widgets' );

/**
 * Page-friendly post list shortcode.
 *
 * Example:
 * [asosyoloji_posts title="Son Yazılar" category="yazilar" exclude="duyurular" count="6" layout="grid"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function asosyoloji_posts_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'title'   => '',
			'category'=> '',
			'exclude' => '',
			'count'   => 6,
			'layout'  => 'grid',
			'image'   => '1',
			'excerpt' => '0',
			'meta'    => '1',
			'author'  => '0',
			'orderby' => 'date',
			'order'   => 'DESC',
			'after'   => '',
			'before'  => '',
			'offset'  => '0',
			'sticky'  => '0',
		),
		$atts,
		'asosyoloji_posts'
	);

	$category_ids = asosyoloji_resolve_category_ids( $atts['category'] );
	$category_id  = $category_ids ? $category_ids[0] : 0;

	return asosyoloji_render_post_collection(
		array(
			'title'              => sanitize_text_field( $atts['title'] ),
			'category'           => $category_id,
			'exclude_categories' => asosyoloji_resolve_category_ids( $atts['exclude'] ),
			'count'              => absint( $atts['count'] ),
			'layout'             => sanitize_key( $atts['layout'] ),
			'show_image'         => '1' === (string) $atts['image'],
			'show_excerpt'       => '1' === (string) $atts['excerpt'],
			'show_meta'          => '1' === (string) $atts['meta'],
			'author'             => absint( $atts['author'] ),
			'orderby'            => sanitize_key( $atts['orderby'] ),
			'order'              => sanitize_key( strtoupper( (string) $atts['order'] ) ),
			'date_after'         => sanitize_text_field( $atts['after'] ),
			'date_before'        => sanitize_text_field( $atts['before'] ),
			'offset'             => absint( $atts['offset'] ),
			'include_sticky'     => '1' === (string) $atts['sticky'],
		)
	);
}
add_shortcode( 'asosyoloji_posts', 'asosyoloji_posts_shortcode' );
