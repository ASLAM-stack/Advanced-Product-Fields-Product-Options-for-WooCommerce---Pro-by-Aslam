<?php
/**
 * Frontend Template: Dropdown Select Field.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id          = $field['id'];
$label       = $field['label'] ?? '';
$desc        = $field['description'] ?? '';
$placeholder = $field['placeholder'] ?? __( '-- Please Select --', 'apf-aslam' );
$required    = ( ! empty( $field['required'] ) && 'yes' === $field['required'] );
$options     = $field['options'] ?? array();
$is_multiple = ( ! empty( $field['multiple'] ) && 'yes' === $field['multiple'] ) || ( ! empty( $field['allow_multiple'] ) && 'yes' === $field['allow_multiple'] );
$def_val     = $field['default_value'] ?? '';
$def_arr     = is_array( $def_val ) ? $def_val : array_filter( array_map( 'trim', explode( ',', (string) $def_val ) ) );
?>

<div class="apf-field-wrap apf-type-select <?php echo $is_multiple ? 'apf-is-multiselect' : ''; ?>" id="apf-wrap-<?php echo esc_attr( $id ); ?>" data-field-id="<?php echo esc_attr( $id ); ?>" data-field-type="select" data-conditions="<?php echo esc_attr( wp_json_encode( $field['conditional_logic'] ?? array() ) ); ?>" <?php echo $required ? 'data-required="yes"' : ''; ?>>
	<label for="apf-<?php echo esc_attr( $id ); ?>" class="apf-label">
		<span class="apf-label-text"><?php echo esc_html( $label ); ?></span>
		<?php if ( $required ) : ?>
			<span class="apf-required-mark">*</span>
		<?php endif; ?>
	</label>

	<div class="apf-input-wrapper apf-select-wrapper <?php echo $is_multiple ? 'apf-multiselect-wrap' : ''; ?>">
		<select 
			name="apf[<?php echo esc_attr( $id ); ?>]<?php echo $is_multiple ? '[]' : ''; ?>" 
			id="apf-<?php echo esc_attr( $id ); ?>" 
			class="apf-input apf-select <?php echo $is_multiple ? 'apf-select-multiple' : ''; ?>"
			<?php echo $is_multiple ? 'multiple="multiple"' : ''; ?>
			<?php echo $required ? 'data-required="yes"' : ''; ?>
		>
			<?php if ( ! $is_multiple ) : ?>
				<option value=""><?php echo esc_html( $placeholder ); ?></option>
			<?php endif; ?>
			<?php foreach ( $options as $opt ) : 
				$opt_price = floatval( $opt['pricing_amount'] ?? 0 );
				$opt_ptype = $opt['pricing_type'] ?? 'flat';
				$badge_txt = '';
				if ( 0.0 != $opt_price ) {
					$sign = $opt_price > 0 ? '+' : '-';
					$badge_txt = ' (' . $sign . ( 'percentage' === $opt_ptype ? abs( $opt_price ) . '%' : wc_price( abs( $opt_price ) ) ) . ')';
				}
				$is_opt_selected = $is_multiple 
					? in_array( (string) $opt['value'], array_map( 'strval', $def_arr ), true )
					: ( (string) $def_val === (string) $opt['value'] );
			?>
				<option 
					value="<?php echo esc_attr( $opt['value'] ); ?>" 
					data-price="<?php echo esc_attr( $opt_price ); ?>" 
					data-pricing-type="<?php echo esc_attr( $opt_ptype ); ?>"
					<?php selected( $is_opt_selected, true ); ?>
				>
					<?php echo esc_html( $opt['label'] . wp_strip_all_tags( $badge_txt ) ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if ( ! $is_multiple ) : ?>
			<span class="apf-select-arrow"><i class="fa-solid fa-angle-down"></i></span>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>
</div>
