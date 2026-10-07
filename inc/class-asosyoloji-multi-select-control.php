<?php
/**
 * Multi-select Customizer control.
 *
 * @package Asosyoloji
 */

/**
 * Simple multiple select control.
 */
class Asosyoloji_Multi_Select_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'aso-multi-select';

	/**
	 * Render the control.
	 */
	public function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}

		$selected = array_map( 'strval', (array) $this->value() );
		?>
		<label>
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>

			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<select multiple size="7" style="width:100%;" <?php $this->link(); ?>>
				<?php foreach ( $this->choices as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( in_array( (string) $value, $selected, true ) ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>
		<?php
	}
}
