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
					<svg viewBox="0 0 24 24" width="44" height="44" fill="none" stroke="var(--apf-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
						<polyline points="17 8 12 3 7 8"></polyline>
						<line x1="12" y1="3" x2="12" y2="15"></line>
					</svg>
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
				<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="var(--apf-secondary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
					<polyline points="14 2 14 8 20 8"></polyline>
				</svg>
			</div>
			<div class="apf-preview-info">
				<span class="apf-filename"></span>
				<span class="apf-filesize"></span>
			</div>
			<button type="button" class="apf-btn-remove-uploaded" title="<?php esc_attr_e( 'Remove file', 'apf-aslam' ); ?>">
				<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="12" cy="12" r="10"></circle>
					<line x1="15" y1="9" x2="9" y2="15"></line>
					<line x1="9" y1="9" x2="15" y2="15"></line>
				</svg>
			</button>
		</div>
	</div>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>
</div>
