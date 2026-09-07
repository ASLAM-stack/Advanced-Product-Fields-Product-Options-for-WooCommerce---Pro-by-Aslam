<?php
/**
 * Frontend Template: Radio Buttons (Pills / Cards).
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

<div class="apf-field-wrap apf-type-radio" id="apf-wrap-<?php echo esc_attr( $id ); ?>" data-field-id="<?php echo esc_attr( $id ); ?>" data-field-type="radio" data-conditions="<?php echo esc_attr( wp_json_encode( $field['conditional_logic'] ?? array() ) ); ?>" <?php echo $required ? 'data-required="yes"' : ''; ?>>
	<label class="apf-label">
		<span class="apf-label-text"><?php echo esc_html( $label ); ?></span>
		<?php if ( $required ) : ?>
			<span class="apf-required-mark">*</span>
		<?php endif; ?>
	</label>

	<div class="apf-choices-wrap apf-radio-group">
		<?php foreach ( $options as $idx => $opt ) : 
			$opt_id    = "apf_{$id}_{$idx}";
			$opt_price = floatval( $opt['pricing_amount'] ?? 0 );
			$opt_ptype = $opt['pricing_type'] ?? 'flat';
			$badge     = APF_Pricing::format_price_badge( $opt_ptype, $opt_price );
			$is_chk    = ( (string) $opt['value'] === (string) $def_val );
		?>
			<label class="apf-choice-card apf-radio-card <?php echo $is_chk ? 'selected' : ''; ?>" for="<?php echo esc_attr( $opt_id ); ?>">
				<input 
					type="radio" 
					name="apf[<?php echo esc_attr( $id ); ?>]" 
					id="<?php echo esc_attr( $opt_id ); ?>" 
					value="<?php echo esc_attr( $opt['value'] ); ?>"
					data-price="<?php echo esc_attr( $opt_price ); ?>"
					data-pricing-type="<?php echo esc_attr( $opt_ptype ); ?>"
					<?php checked( $is_chk ); ?>
				>
				<span class="apf-card-indicator"><i class="fa-solid fa-check"></i></span>
				<span class="apf-card-label"><?php echo esc_html( $opt['label'] ); ?></span>
				<?php echo wp_kses_post( $badge ); ?>
			</label>
		<?php endforeach; ?>
	</div>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>
</div>
