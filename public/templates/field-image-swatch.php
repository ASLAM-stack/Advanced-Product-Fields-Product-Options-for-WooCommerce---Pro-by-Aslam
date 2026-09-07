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
		<?php if ( empty( $options ) ) : ?>
			<div class="apf-no-options-notice"><?php esc_html_e( 'No image choices configured.', 'apf-aslam' ); ?></div>
		<?php else : ?>
			<?php foreach ( $options as $idx => $opt ) : 
				$opt_id    = "apf_img_{$id}_{$idx}";
				$opt_price = floatval( $opt['pricing_amount'] ?? 0 );
				$opt_ptype = $opt['pricing_type'] ?? 'flat';
				$img_url   = $opt['image_url'] ?? '';
				$opt_label = ! empty( $opt['label'] ) ? $opt['label'] : ( $opt['value'] ?? '' );
				$badge     = APF_Pricing::format_price_badge( $opt_ptype, $opt_price, true );
				$is_chk    = ( (string) $opt['value'] === (string) $def_val );
			?>
				<label class="apf-swatch-item apf-image-item <?php echo $is_chk ? 'selected' : ''; ?>" for="<?php echo esc_attr( $opt_id ); ?>" title="<?php echo esc_attr( $opt_label ); ?>">
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
					<div class="apf-image-thumb-wrap">
						<?php if ( ! empty( $img_url ) ) : ?>
							<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $opt_label ); ?>" class="apf-image-thumb">
						<?php else : ?>
							<div class="apf-image-placeholder">
								<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
							</div>
						<?php endif; ?>
						<span class="apf-image-check-icon">
							<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						</span>
					</div>
					<div class="apf-image-details">
						<span class="apf-swatch-title"><?php echo esc_html( $opt_label ); ?></span>
						<?php echo wp_kses_post( $badge ); ?>
					</div>
				</label>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>
</div>
