<?php
/**
 * Fields Builder Metabox View.
 *
 * Renders the drag-and-drop field builder with options, pricing, and conditional logic editors.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$field_types = APF_Fields_Manager::get_field_types();
?>

<div class="apf-builder-wrap">
	<div class="apf-builder-header">
		<div class="apf-header-intro">
			<span class="apf-badge-pro"><i class="fa-solid fa-crown"></i> PRO UNLOCKED</span>
			<h3><?php esc_html_e( 'Configure Product Options & Fields', 'apf-aslam' ); ?></h3>
		</div>
		<div class="apf-header-actions">
			<button type="button" class="button button-primary button-hero apf-btn-add-field">
				<i class="fa-solid fa-circle-plus"></i> <?php esc_html_e( 'Add New Field', 'apf-aslam' ); ?>
			</button>
		</div>
	</div>

	<!-- Field List Container -->
	<div class="apf-fields-list" id="apf-fields-sortable">
		<?php
		if ( ! empty( $fields ) && is_array( $fields ) ) :
			foreach ( $fields as $field_id => $field_data ) :
				include APF_ASLAM_PATH . 'admin/views/field-card-template.php';
			endforeach;
		endif;
		?>
	</div>

	<!-- Empty State Notice -->
	<div class="apf-empty-state" <?php echo ! empty( $fields ) ? 'style="display:none;"' : ''; ?>>
		<div class="apf-empty-icon"><i class="fa-solid fa-layer-group"></i></div>
		<h4><?php esc_html_e( 'No fields added yet', 'apf-aslam' ); ?></h4>
		<p><?php esc_html_e( 'Click the "Add New Field" button above to add custom options (swatches, steppers, file uploads, text, etc.) to your products.', 'apf-aslam' ); ?></p>
		<button type="button" class="button button-primary apf-btn-add-field">
			<i class="fa-solid fa-plus"></i> <?php esc_html_e( 'Add Your First Field', 'apf-aslam' ); ?>
		</button>
	</div>
</div>

<!-- Hidden Javascript Underscore/Mustache-style Template for New Field -->
<script type="text/template" id="tmpl-apf-field-card">
	<?php
	// Empty dummy field data for dynamic JS template clone.
	$field_id   = '{{field_id}}';
	$field_data = array(
		'id'                => '{{field_id}}',
		'type'              => 'text',
		'label'             => __( 'New Product Option', 'apf-aslam' ),
		'description'       => '',
		'placeholder'       => '',
		'default_value'     => '',
		'required'          => 'no',
		'pricing_type'      => 'none',
		'pricing_amount'    => 0,
		'pricing_formula'   => '',
		'free_characters'   => 0,
		'min_value'         => '',
		'max_value'         => '',
		'step'              => 1,
		'max_length'        => '',
		'allowed_extensions'=> 'jpg,jpeg,png,webp,pdf',
		'max_file_size_mb'  => 5,
		'options'           => array(),
		'conditional_logic' => array(
			'enabled' => 'no',
			'action'  => 'show',
			'match'   => 'all',
			'rules'   => array(),
		),
	);
	include APF_ASLAM_PATH . 'admin/views/field-card-template.php';
	?>
</script>
