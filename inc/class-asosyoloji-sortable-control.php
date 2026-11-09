<?php
/**
 * Sortable Customizer control class.
 *
 * @package Asosyoloji
 */

/**
 * Sortable control used for homepage section ordering.
 */
class Asosyoloji_Sortable_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'aso-sortable';

	/**
	 * Enqueue control assets.
	 */
	public function enqueue() {
		wp_enqueue_script(
			'asosyoloji-customizer-sortable',
			get_template_directory_uri() . '/assets/js/customizer-sortable.js',
			array( 'jquery', 'jquery-ui-sortable', 'customize-controls' ),
			ASOSYOLOJI_VERSION,
			true
		);

		// The front-end stylesheet is not loaded in the Customizer controls pane.
		wp_add_inline_style(
			'customize-controls',
			'.aso-sortable-control{margin:10px 0 0;padding:0}.aso-sortable-control li{display:flex;align-items:center;gap:8px;margin:0 0 7px;padding:10px 12px;border:1px solid #dcdcde;background:#fff;cursor:move}.aso-sortable-control .dashicons-menu{color:#646970}'
		);
	}

	/**
	 * Render control markup.
	 */
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
