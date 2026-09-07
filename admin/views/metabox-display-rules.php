<?php
/**
 * Display Rules Metabox View.
 *
 * Configures conditions where the field group should appear.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$apply_to        = $rules['apply_to'] ?? 'all';
$sel_products    = $rules['products'] ?? array();
$sel_categories  = $rules['categories'] ?? array();
$sel_user_roles  = $rules['user_roles'] ?? array();

// Fetch WooCommerce product categories.
$categories = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
	)
);
if ( is_wp_error( $categories ) ) {
	$categories = array();
}

// Fetch all editable roles.
$roles = wp_roles()->get_names();
?>

<div class="apf-rules-wrap">
	<div class="apf-rule-line">
		<label class="apf-rule-label"><strong><?php esc_html_e( 'Display This Field Group On:', 'apf-aslam' ); ?></strong></label>
		<div class="apf-rule-controls">
			<label class="apf-radio-choice">
				<input type="radio" name="apf_rules[apply_to]" value="all" <?php checked( $apply_to, 'all' ); ?>>
				<span><?php esc_html_e( 'All Products in Store', 'apf-aslam' ); ?></span>
			</label>
			<label class="apf-radio-choice">
				<input type="radio" name="apf_rules[apply_to]" value="categories" <?php checked( $apply_to, 'categories' ); ?>>
				<span><?php esc_html_e( 'Specific Product Categories', 'apf-aslam' ); ?></span>
			</label>
			<label class="apf-radio-choice">
				<input type="radio" name="apf_rules[apply_to]" value="specific_products" <?php checked( $apply_to, 'specific_products' ); ?>>
				<span><?php esc_html_e( 'Specific Products Only', 'apf-aslam' ); ?></span>
			</label>
		</div>
	</div>

	<!-- Category Multi-Select -->
	<div class="apf-rule-sub-box apf-rule-categories-box" <?php echo 'categories' === $apply_to ? '' : 'style="display:none;"'; ?>>
		<label><strong><?php esc_html_e( 'Select Categories:', 'apf-aslam' ); ?></strong></label>
		<div class="apf-checkbox-list">
			<?php if ( ! empty( $categories ) ) : ?>
				<?php foreach ( $categories as $cat ) : ?>
					<label class="apf-checkbox-item">
						<input type="checkbox" name="apf_rules[categories][]" value="<?php echo esc_attr( $cat->term_id ); ?>" <?php checked( in_array( $cat->term_id, $sel_categories ) ); ?>>
						<span><?php echo esc_html( $cat->name ); ?> (<?php echo esc_html( $cat->count ); ?>)</span>
					</label>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="description"><?php esc_html_e( 'No product categories found.', 'apf-aslam' ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<!-- Specific Products Multi-Select / ID Input -->
	<div class="apf-rule-sub-box apf-rule-products-box" <?php echo 'specific_products' === $apply_to ? '' : 'style="display:none;"'; ?>>
		<label><strong><?php esc_html_e( 'Select Products (Enter IDs or Search):', 'apf-aslam' ); ?></strong></label>
		<select name="apf_rules[products][]" multiple="multiple" class="widefat apf-products-multiselect" style="min-height: 120px;">
			<?php
			$all_wc_products = wc_get_products( array( 'limit' => 100, 'status' => 'publish' ) );
			foreach ( $all_wc_products as $p ) :
			?>
				<option value="<?php echo esc_attr( $p->get_id() ); ?>" <?php selected( in_array( $p->get_id(), $sel_products ) ); ?>>
					#<?php echo esc_html( $p->get_id() ); ?> - <?php echo esc_html( $p->get_name() ); ?> (<?php echo wc_price( $p->get_price() ); ?>)
				</option>
			<?php endforeach; ?>
		</select>
		<p class="description"><?php esc_html_e( 'Hold Ctrl (or Cmd on Mac) to select multiple products.', 'apf-aslam' ); ?></p>
	</div>

	<!-- User Roles Filter -->
	<div class="apf-rule-line" style="margin-top: 18px; border-top: 1px dashed #ddd; padding-top: 18px;">
		<label class="apf-rule-label"><strong><?php esc_html_e( 'Restrict by User Role (Optional):', 'apf-aslam' ); ?></strong></label>
		<p class="description"><?php esc_html_e( 'Leave all unchecked to show to all visitors (including guests).', 'apf-aslam' ); ?></p>
		<div class="apf-checkbox-list">
			<?php foreach ( $roles as $r_key => $r_name ) : ?>
				<label class="apf-checkbox-item">
					<input type="checkbox" name="apf_rules[user_roles][]" value="<?php echo esc_attr( $r_key ); ?>" <?php checked( in_array( $r_key, $sel_user_roles ) ); ?>>
					<span><?php echo esc_html( $r_name ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
	</div>
</div>
