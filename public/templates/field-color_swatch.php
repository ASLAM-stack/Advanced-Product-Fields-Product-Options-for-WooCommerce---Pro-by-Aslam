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
		<?php if ( empty( $options ) ) : ?>
			<div class="apf-no-options-notice"><?php esc_html_e( 'No color choices configured.', 'apf-aslam' ); ?></div>
		<?php else : ?>
			<?php foreach ( $options as $idx => $opt ) : 
				$opt_id    = "apf_color_{$id}_{$idx}";
				$opt_price = floatval( $opt['pricing_amount'] ?? 0 );
				$opt_ptype = $opt['pricing_type'] ?? 'flat';
				$color_hex = ! empty( $opt['color'] ) ? $opt['color'] : '#EB1400';
				$opt_label = ! empty( $opt['label'] ) ? $opt['label'] : ( $opt['value'] ?? '' );
				$badge     = APF_Pricing::format_price_badge( $opt_ptype, $opt_price );
				$is_chk    = ( (string) $opt['value'] === (string) $def_val );
			?>
				<label class="apf-swatch-item apf-color-item <?php echo $is_chk ? 'selected' : ''; ?>" for="<?php echo esc_attr( $opt_id ); ?>" title="<?php echo esc_attr( $opt_label ); ?>">
					<input 
						type="radio" 
						name="apf[<?php echo esc_attr( $id ); ?>]" 
						id="<?php echo esc_attr( $opt_id ); ?>" 
						value="<?php echo esc_attr( $opt['value'] ); ?>"
						data-price="<?php echo esc_attr( $opt_price ); ?>"
						data-pricing-type="<?php echo esc_attr( $opt_ptype ); ?>"
						data-label="<?php echo esc_attr( $opt_label ); ?>"
						<?php checked( $is_chk ); ?>
					>
					<span class="apf-color-circle" style="background-color: <?php echo esc_attr( $color_hex ); ?>;">
						<svg class="apf-swatch-check-svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="filter: drop-shadow(0 1px 2px rgba(0,0,0,0.6));"><polyline points="20 6 9 17 4 12"></polyline></svg>
					</span>
					<span class="apf-swatch-title"><?php echo esc_html( $opt_label ); ?></span>
					<?php echo wp_kses_post( $badge ); ?>
				</label>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>
</div>
