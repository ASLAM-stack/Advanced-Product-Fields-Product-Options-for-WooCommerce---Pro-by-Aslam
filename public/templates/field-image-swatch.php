<?php
/**
 * Frontend Template: Image Swatches Field (Pro).
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

<div class="apf-field-wrap apf-type-image-swatch" id="apf-wrap-<?php echo esc_attr( $id ); ?>" data-field-id="<?php echo esc_attr( $id ); ?>" data-field-type="image_swatch" data-conditions="<?php echo esc_attr( wp_json_encode( $field['conditional_logic'] ?? array() ) ); ?>" <?php echo $required ? 'data-required="yes"' : ''; ?>>
	<label class="apf-label">
		<span class="apf-label-text"><?php echo esc_html( $label ); ?></span>
		<?php if ( $required ) : ?>
			<span class="apf-required-mark">*</span>
		<?php endif; ?>
		<span class="apf-selected-swatch-name"></span>
	</label>

	<div class="apf-swatches-grid apf-image-swatches">
		<?php foreach ( $options as $idx => $opt ) : 
			$opt_id    = "apf_img_{$id}_{$idx}";
			$opt_price = floatval( $opt['pricing_amount'] ?? 0 );
			$opt_ptype = $opt['pricing_type'] ?? 'flat';
			$img_url   = $opt['image_url'] ?? '';
			$badge     = APF_Pricing::format_price_badge( $opt_ptype, $opt_price );
			$is_chk    = ( (string) $opt['value'] === (string) $def_val );
		?>
			<label class="apf-swatch-item apf-image-item <?php echo $is_chk ? 'selected' : ''; ?>" for="<?php echo esc_attr( $opt_id ); ?>">
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
				<div class="apf-image-thumb-wrap">
					<?php if ( ! empty( $img_url ) ) : ?>
						<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $opt['label'] ); ?>" class="apf-image-thumb">
					<?php else : ?>
						<div class="apf-image-placeholder"><i class="fa-solid fa-utensils"></i></div>
					<?php endif; ?>
					<span class="apf-image-check-icon"><i class="fa-solid fa-check"></i></span>
				</div>
				<div class="apf-image-details">
					<span class="apf-swatch-title"><?php echo esc_html( $opt['label'] ); ?></span>
					<?php echo wp_kses_post( $badge ); ?>
				</div>
			</label>
		<?php endforeach; ?>
	</div>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>
</div>
