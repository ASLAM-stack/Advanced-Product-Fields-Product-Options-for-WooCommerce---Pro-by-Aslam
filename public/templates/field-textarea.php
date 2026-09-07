<?php
/**
 * Frontend Template: Multi-line Textarea Field.
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
$max_len     = absint( $field['max_length'] ?? 0 );
$badge       = APF_Pricing::format_price_badge( $p_type, $p_amount );
?>

<div class="apf-field-wrap apf-type-textarea" id="apf-wrap-<?php echo esc_attr( $id ); ?>" data-field-id="<?php echo esc_attr( $id ); ?>" data-field-type="textarea" data-pricing-type="<?php echo esc_attr( $p_type ); ?>" data-pricing-amount="<?php echo esc_attr( $p_amount ); ?>" data-free-chars="<?php echo esc_attr( $field['free_characters'] ?? 0 ); ?>" data-conditions="<?php echo esc_attr( wp_json_encode( $field['conditional_logic'] ?? array() ) ); ?>">
	<label for="apf-<?php echo esc_attr( $id ); ?>" class="apf-label">
		<span class="apf-label-text"><?php echo esc_html( $label ); ?></span>
		<?php if ( $required ) : ?>
			<span class="apf-required-mark">*</span>
		<?php endif; ?>
		<?php echo wp_kses_post( $badge ); ?>
	</label>

	<div class="apf-input-wrapper">
		<textarea 
			name="apf[<?php echo esc_attr( $id ); ?>]" 
			id="apf-<?php echo esc_attr( $id ); ?>" 
			class="apf-input apf-textarea" 
			rows="3"
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			<?php echo $max_len ? 'maxlength="' . esc_attr( $max_len ) . '"' : ''; ?>
			<?php echo $required ? 'data-required="yes"' : ''; ?>
		><?php echo esc_textarea( $field['default_value'] ?? '' ); ?></textarea>
	</div>

	<?php if ( $max_len || 'char_count' === $p_type ) : ?>
		<div class="apf-char-counter">
			<span class="apf-char-count">0</span><?php echo $max_len ? ' / ' . esc_html( $max_len ) : ''; ?> <?php esc_html_e( 'characters', 'apf-aslam' ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>
</div>
