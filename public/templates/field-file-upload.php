<?php
/**
 * Frontend Template: File Upload Field (Pro).
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
$max_mb      = floatval( $field['max_file_size_mb'] ?? 5 );
$allowed     = $field['allowed_extensions'] ?? 'jpg,jpeg,png,webp,pdf';
?>

<div class="apf-field-wrap apf-type-file-upload" id="apf-wrap-<?php echo esc_attr( $id ); ?>" data-field-id="<?php echo esc_attr( $id ); ?>" data-field-type="file_upload" data-pricing-type="<?php echo esc_attr( $p_type ); ?>" data-pricing-amount="<?php echo esc_attr( $p_amount ); ?>" data-conditions="<?php echo esc_attr( wp_json_encode( $field['conditional_logic'] ?? array() ) ); ?>" <?php echo $required ? 'data-required="yes"' : ''; ?>>
	<label class="apf-label">
		<span class="apf-label-text"><?php echo esc_html( $label ); ?></span>
		<?php if ( $required ) : ?>
			<span class="apf-required-mark">*</span>
		<?php endif; ?>
		<?php echo wp_kses_post( $badge ); ?>
	</label>

	<div class="apf-dropzone-wrap" data-field-id="<?php echo esc_attr( $id ); ?>">
		<input type="file" class="apf-file-input" style="display: none;" accept="<?php echo esc_attr( '.' . str_replace( ',', ',.', $allowed ) ); ?>">
		
		<!-- Hidden input holding JSON data of uploaded file when complete -->
		<input type="hidden" name="apf[<?php echo esc_attr( $id ); ?>]" class="apf-uploaded-file-data" value="">

		<div class="apf-dropzone-box">
			<div class="apf-dropzone-content">
				<div class="apf-dropzone-icon">
					<i class="fa-solid fa-cloud-arrow-up"></i>
				</div>
				<p class="apf-dropzone-title">
					<strong><?php esc_html_e( 'Click or drag file here to upload', 'apf-aslam' ); ?></strong>
				</p>
				<p class="apf-dropzone-meta">
					<?php printf( esc_html__( 'Allowed: %s (Max: %sMB)', 'apf-aslam' ), esc_html( $allowed ), esc_html( $max_mb ) ); ?>
				</p>
			</div>

			<div class="apf-upload-progress" style="display: none;">
				<div class="apf-progress-bar"><div class="apf-progress-fill"></div></div>
				<span class="apf-progress-text"><?php esc_html_e( 'Uploading...', 'apf-aslam' ); ?></span>
			</div>
		</div>

		<!-- Uploaded File Preview Card -->
		<div class="apf-uploaded-preview" style="display: none;">
			<div class="apf-preview-thumb">
				<i class="fa-solid fa-file"></i>
			</div>
			<div class="apf-preview-info">
				<span class="apf-filename"></span>
				<span class="apf-filesize"></span>
			</div>
			<button type="button" class="apf-btn-remove-uploaded" title="<?php esc_attr_e( 'Remove file', 'apf-aslam' ); ?>">
				<i class="fa-solid fa-circle-xmark"></i>
			</button>
		</div>
	</div>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>
</div>
