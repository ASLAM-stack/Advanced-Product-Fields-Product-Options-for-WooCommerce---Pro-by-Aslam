<?php
/**
 * Frontend Template: Date Picker Field (Pro).
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id          = $field['id'];
$label       = $field['label'] ?? '';
$desc        = $field['description'] ?? '';
$required    = ( ! empty( $field['required'] ) && 'yes' === $field['required'] );
$p_type      = $field['pricing_type'] ?? 'none';
$p_amount    = floatval( $field['pricing_amount'] ?? 0 );
$badge       = APF_Pricing::format_price_badge( $p_type, $p_amount );
?>

<div class="apf-field-wrap apf-type-date" id="apf-wrap-<?php echo esc_attr( $id ); ?>" data-field-id="<?php echo esc_attr( $id ); ?>" data-field-type="date" data-pricing-type="<?php echo esc_attr( $p_type ); ?>" data-pricing-amount="<?php echo esc_attr( $p_amount ); ?>" data-conditions="<?php echo esc_attr( wp_json_encode( $field['conditional_logic'] ?? array() ) ); ?>" <?php echo $required ? 'data-required="yes"' : ''; ?>>
	<label for="apf-<?php echo esc_attr( $id ); ?>" class="apf-label">
		<span class="apf-label-text"><?php echo esc_html( $label ); ?></span>
		<?php if ( $required ) : ?>
			<span class="apf-required-mark">*</span>
		<?php endif; ?>
		<?php echo wp_kses_post( $badge ); ?>
	</label>

	<div class="apf-input-wrapper apf-icon-input-wrap">
		<input 
			type="date" 
			name="apf[<?php echo esc_attr( $id ); ?>]" 
			id="apf-<?php echo esc_attr( $id ); ?>" 
			class="apf-input apf-date-input"
			value="<?php echo esc_attr( $field['default_value'] ?? '' ); ?>"
			<?php echo $required ? 'data-required="yes"' : ''; ?>
		>
		<span class="apf-input-icon"><i class="fa-regular fa-calendar"></i></span>
	</div>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>
</div>
