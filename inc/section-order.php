<?php
/**
 * Sortable Customizer control for homepage sections.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) ) {
	class Asosyoloji_Sortable_Control extends WP_Customize_Control {
		public $type = 'aso-sortable';

		public function enqueue() {
			wp_enqueue_script(
				'asosyoloji-customizer-sortable',
				get_template_directory_uri() . '/assets/js/customizer-sortable.js',
				array( 'jquery', 'jquery-ui-sortable', 'customize-controls' ),
				ASOSYOLOJI_VERSION,
				true
			);
		}

		public function render_content() {
			if ( empty( $this->choices ) ) {
				return;
			}
			?>
			<label>
				<?php if ( $this->label ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>
				<?php if ( $this->description ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
			</label>

			<ul class="aso-sortable-control" data-aso-sortable>
				<?php
				$value = array_filter( array_map( 'sanitize_key', explode( ',', (string) $this->value() ) ) );
				foreach ( $value as $key ) :
					if ( ! isset( $this->choices[ $key ] ) ) {
						continue;
					}
					?>
					<li data-value="<?php echo esc_attr( $key ); ?>">
						<span class="dashicons dashicons-menu" aria-hidden="true"></span>
						<?php echo esc_html( $this->choices[ $key ] ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>">
			<?php
		}
	}
}

function asosyoloji_sanitize_section_order( $value ) {
	$allowed = array( 'slider', 'hero', 'latest', 'featured', 'archive' );
	$items   = array_filter( array_map( 'sanitize_key', explode( ',', (string) $value ) ) );
	$items   = array_values( array_intersect( $items, $allowed ) );

	foreach ( $allowed as $key ) {
		if ( ! in_array( $key, $items, true ) ) {
			$items[] = $key;
		}
	}

	return implode( ',', $items );
}

function asosyoloji_section_order_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'aso_home_order',
		array(
			'title' => __( 'Ana Sayfa: Bölüm Sırası', 'asosyoloji' ),
			'panel' => 'aso_theme_options',
		)
	);

	$wp_customize->add_setting(
		'aso_home_section_order',
		array(
			'default'           => 'slider,hero,latest,featured,archive',
			'sanitize_callback' => 'asosyoloji_sanitize_section_order',
		)
	);

	$wp_customize->add_control(
		new Asosyoloji_Sortable_Control(
			$wp_customize,
			'aso_home_section_order',
			array(
				'label'       => __( 'Ana sayfa bölüm sırası', 'asosyoloji' ),
				'description' => __( 'Bölümleri sürükleyip bırakarak sıralayın.', 'asosyoloji' ),
				'section'     => 'aso_home_order',
				'choices'     => array(
					'slider'   => __( 'Slider', 'asosyoloji' ),
					'hero'     => __( 'Öne Çıkan', 'asosyoloji' ),
					'latest'   => __( 'Son Yazılar', 'asosyoloji' ),
					'featured' => __( 'Kategori Bölümü', 'asosyoloji' ),
					'archive'  => __( 'Arşiv', 'asosyoloji' ),
				),
			)
		)
	);
}
add_action( 'customize_register', 'asosyoloji_section_order_customize_register', 60 );
