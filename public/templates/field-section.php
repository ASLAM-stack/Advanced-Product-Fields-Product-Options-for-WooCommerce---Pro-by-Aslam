<?php
/**
 * Frontend Template: Section Header / Divider Field.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = $field['id'];
$label = $field['label'] ?? '';
$desc  = $field['description'] ?? '';
?>

<div class="apf-field-wrap apf-type-section" id="apf-wrap-<?php echo esc_attr( $id ); ?>" data-field-id="<?php echo esc_attr( $id ); ?>" data-field-type="section" data-conditions="<?php echo esc_attr( wp_json_encode( $field['conditional_logic'] ?? array() ) ); ?>">
	<div class="apf-section-divider">
		<h4 class="apf-section-title">
			<span class="apf-section-pill"></span>
			<?php echo esc_html( $label ); ?>
		</h4>
		<?php if ( ! empty( $desc ) ) : ?>
			<div class="apf-section-desc"><?php echo wp_kses_post( $desc ); ?></div>
		<?php endif; ?>
	</div>
</div>
