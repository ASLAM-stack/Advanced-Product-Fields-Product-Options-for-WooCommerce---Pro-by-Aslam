<?php
/**
 * Frontend Template: Checkbox / Multi-Select Field.
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

<div class="apf-field-wrap apf-type-checkbox" id="apf-wrap-<?php echo esc_attr( $id ); ?>" data-field-id="<?php echo esc_attr( $id ); ?>" data-field-type="checkbox" data-pricing-type="<?php echo esc_attr( $field['pricing_type'] ?? 'none' ); ?>" data-pricing-amount="<?php echo esc_attr( floatval( $field['pricing_amount'] ?? 0 ) ); ?>" data-conditions="<?php echo esc_attr( wp_json_encode( $field['conditional_logic'] ?? array() ) ); ?>" <?php echo $required ? 'data-required="yes"' : ''; ?>>
	<label class="apf-label">
		<span class="apf-label-text"><?php echo esc_html( $label ); ?></span>
		<?php if ( $required ) : ?>
			<span class="apf-required-mark">*</span>
		<?php endif; ?>
	</label>

	<div class="apf-choices-wrap apf-checkbox-group">
		<?php 
		// If options list is empty, render as a single toggle checkbox.
		if ( empty( $options ) ) : 
			$p_type   = $field['pricing_type'] ?? 'none';
			$p_amount = floatval( $field['pricing_amount'] ?? 0 );
			$badge    = APF_Pricing::format_price_badge( $p_type, $p_amount );
			$is_chk   = ( 'yes' === (string) $def_val || '1' === (string) $def_val );
		?>
			<label class="apf-choice-card apf-checkbox-card <?php echo $is_chk ? 'selected' : ''; ?>" for="apf_<?php echo esc_attr( $id ); ?>">
				<input 
					type="checkbox" 
					name="apf[<?php echo esc_attr( $id ); ?>]" 
					id="apf_<?php echo esc_attr( $id ); ?>" 
					value="yes"
					data-price="<?php echo esc_attr( $p_amount ); ?>"
					data-pricing-type="<?php echo esc_attr( $p_type ); ?>"
					<?php checked( $is_chk ); ?>
				>
				<span class="apf-card-indicator"><i class="fa-solid fa-check"></i></span>
				<span class="apf-card-label"><?php echo esc_html( $label ); ?></span>
				<?php echo wp_kses_post( $badge ); ?>
			</label>
		<?php else : 
			// Multiple choice checkboxes.
			$def_arr = is_array( $def_val ) ? $def_val : array_filter( array_map( 'trim', explode( ',', (string) $def_val ) ) );
			foreach ( $options as $idx => $opt ) : 
				$opt_id    = "apf_{$id}_{$idx}";
				$opt_price = floatval( $opt['pricing_amount'] ?? 0 );
				$opt_ptype = $opt['pricing_type'] ?? 'flat';
				$badge     = APF_Pricing::format_price_badge( $opt_ptype, $opt_price );
				$is_chk    = in_array( (string) $opt['value'], array_map( 'strval', $def_arr ), true );
		?>
				<label class="apf-choice-card apf-checkbox-card <?php echo $is_chk ? 'selected' : ''; ?>" for="<?php echo esc_attr( $opt_id ); ?>">
					<input 
						type="checkbox" 
						name="apf[<?php echo esc_attr( $id ); ?>][]" 
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
		<?php 
			endforeach; 
		endif; 
		?>
	</div>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>
</div>
