<?php
/**
 * Frontend Template: Numeric Field.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id          = $field['id'];
$label       = $field['label'] ?? '';
$desc        = $field['description'] ?? '';
$placeholder = $field['placeholder'] ?? '';
$required    = ( ! empty( $field['required'] ) && 'yes' === $field['required'] );
$p_type      = $field['pricing_type'] ?? 'none';
$p_amount    = floatval( $field['pricing_amount'] ?? 0 );
$min_val     = $field['min_value'] ?? '';
$max_val     = $field['max_value'] ?? '';
$step_val    = $field['step'] ?? 1;
$badge       = APF_Pricing::format_price_badge( $p_type, $p_amount );
?>

<div class="apf-field-wrap apf-type-number" id="apf-wrap-<?php echo esc_attr( $id ); ?>" data-field-id="<?php echo esc_attr( $id ); ?>" data-field-type="number" data-pricing-type="<?php echo esc_attr( $p_type ); ?>" data-pricing-amount="<?php echo esc_attr( $p_amount ); ?>" data-conditions="<?php echo esc_attr( wp_json_encode( $field['conditional_logic'] ?? array() ) ); ?>">
	<label for="apf-<?php echo esc_attr( $id ); ?>" class="apf-label">
		<span class="apf-label-text"><?php echo esc_html( $label ); ?></span>
		<?php if ( $required ) : ?>
			<span class="apf-required-mark">*</span>
		<?php endif; ?>
		<?php echo wp_kses_post( $badge ); ?>
	</label>

	<div class="apf-input-wrapper">
		<input 
			type="number" 
			name="apf[<?php echo esc_attr( $id ); ?>]" 
			id="apf-<?php echo esc_attr( $id ); ?>" 
			class="apf-input apf-number-input" 
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			value="<?php echo esc_attr( $field['default_value'] ?? '' ); ?>"
			<?php echo '' !== $min_val ? 'min="' . esc_attr( $min_val ) . '"' : ''; ?>
			<?php echo '' !== $max_val ? 'max="' . esc_attr( $max_val ) . '"' : ''; ?>
			step="<?php echo esc_attr( $step_val ); ?>"
			<?php echo $required ? 'data-required="yes"' : ''; ?>
		>
	</div>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>
</div>
