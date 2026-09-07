<?php
/**
 * Frontend Template: Add-on Quantity Stepper (+/-) Field.
 *
 * Essential for fast-food toppings, extra sauce cups, patties, and cheese.
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
$p_amount = floatval( $field['pricing_amount'] ?? 0 );
$min_val  = '' !== ( $field['min_value'] ?? '' ) ? floatval( $field['min_value'] ) : 0;
$max_val  = '' !== ( $field['max_value'] ?? '' ) ? floatval( $field['max_value'] ) : 10;
$step     = floatval( $field['step'] ?? 1 );
$def_val  = '' !== ( $field['default_value'] ?? '' ) ? floatval( $field['default_value'] ) : $min_val;
?>

<div class="apf-field-wrap apf-type-stepper" id="apf-wrap-<?php echo esc_attr( $id ); ?>" data-field-id="<?php echo esc_attr( $id ); ?>" data-field-type="stepper" data-pricing-type="stepper" data-pricing-amount="<?php echo esc_attr( $p_amount ); ?>" data-min="<?php echo esc_attr( $min_val ); ?>" data-max="<?php echo esc_attr( $max_val ); ?>" data-step="<?php echo esc_attr( $step ); ?>" data-conditions="<?php echo esc_attr( wp_json_encode( $field['conditional_logic'] ?? array() ) ); ?>" <?php echo $required ? 'data-required="yes"' : ''; ?>>
	<div class="apf-stepper-row">
		<div class="apf-stepper-info">
			<span class="apf-label-text"><?php echo esc_html( $label ); ?></span>
			<?php if ( $required ) : ?>
				<span class="apf-required-mark">*</span>
			<?php endif; ?>
			<?php if ( 0.0 != $p_amount ) : ?>
				<span class="apf-stepper-unit-price">+ <?php echo wc_price( $p_amount ); ?> <?php esc_html_e( 'each', 'apf-aslam' ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $desc ) ) : ?>
				<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
			<?php endif; ?>
		</div>

		<div class="apf-stepper-control">
			<button type="button" class="apf-step-btn apf-step-down" aria-label="<?php esc_attr_e( 'Decrease quantity', 'apf-aslam' ); ?>">
				<i class="fa-solid fa-minus"></i>
			</button>
			<input 
				type="number" 
				name="apf[<?php echo esc_attr( $id ); ?>]" 
				id="apf-<?php echo esc_attr( $id ); ?>" 
				class="apf-stepper-input" 
				value="<?php echo esc_attr( $def_val ); ?>"
				min="<?php echo esc_attr( $min_val ); ?>"
				max="<?php echo esc_attr( $max_val ); ?>"
				step="<?php echo esc_attr( $step ); ?>"
				readonly
			>
			<button type="button" class="apf-step-btn apf-step-up" aria-label="<?php esc_attr_e( 'Increase quantity', 'apf-aslam' ); ?>">
				<i class="fa-solid fa-plus"></i>
			</button>
		</div>
	</div>
</div>
