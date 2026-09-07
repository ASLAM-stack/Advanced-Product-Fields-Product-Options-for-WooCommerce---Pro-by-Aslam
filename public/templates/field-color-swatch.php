<?php
/**
 * Frontend Template: Color Swatches Field (Pro).
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id       = $field['id'];
$label    = $field['label'] ?? '';
$desc     = $field['description'] ?? '';
$required = ( ! empty( $field['required'] ) && 'yes' === $field['required'] );
$options  = $field['options'] ?? array();
$def_val  = $field['default_value'] ?? '';
?>

<div class="apf-field-wrap apf-type-color-swatch" id="apf-wrap-<?php echo esc_attr( $id ); ?>" data-field-id="<?php echo esc_attr( $id ); ?>" data-field-type="color_swatch" data-conditions="<?php echo esc_attr( wp_json_encode( $field['conditional_logic'] ?? array() ) ); ?>" <?php echo $required ? 'data-required="yes"' : ''; ?>>
	<label class="apf-label">
		<span class="apf-label-text"><?php echo esc_html( $label ); ?></span>
		<?php if ( $required ) : ?>
			<span class="apf-required-mark">*</span>
		<?php endif; ?>
		<span class="apf-selected-swatch-name"></span>
	</label>

	<div class="apf-swatches-grid apf-color-swatches">
		<?php foreach ( $options as $idx => $opt ) : 
			$opt_id    = "apf_color_{$id}_{$idx}";
			$opt_price = floatval( $opt['pricing_amount'] ?? 0 );
			$opt_ptype = $opt['pricing_type'] ?? 'flat';
			$color_hex = $opt['color'] ?? '#EB1400';
			$badge     = APF_Pricing::format_price_badge( $opt_ptype, $opt_price );
			$is_chk    = ( (string) $opt['value'] === (string) $def_val );
		?>
			<label class="apf-swatch-item apf-color-item <?php echo $is_chk ? 'selected' : ''; ?>" for="<?php echo esc_attr( $opt_id ); ?>" title="<?php echo esc_attr( $opt['label'] ); ?>">
				<input 
					type="radio" 
					name="apf[<?php echo esc_attr( $id ); ?>]" 
					id="<?php echo esc_attr( $opt_id ); ?>" 
					value="<?php echo esc_attr( $opt['value'] ); ?>"
					data-price="<?php echo esc_attr( $opt_price ); ?>"
					data-pricing-type="<?php echo esc_attr( $opt_ptype ); ?>"
					data-label="<?php echo esc_attr( $opt['label'] ); ?>"
					<?php checked( $is_chk ); ?>
				>
				<span class="apf-color-circle" style="background-color: <?php echo esc_attr( $color_hex ); ?>;">
					<i class="fa-solid fa-check apf-swatch-check"></i>
				</span>
				<span class="apf-swatch-title"><?php echo esc_html( $opt['label'] ); ?></span>
				<?php echo wp_kses_post( $badge ); ?>
			</label>
		<?php endforeach; ?>
	</div>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>
</div>
