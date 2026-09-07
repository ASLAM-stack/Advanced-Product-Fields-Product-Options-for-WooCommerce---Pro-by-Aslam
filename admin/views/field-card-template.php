<?php
/**
 * Individual Field Card Template for Admin Field Builder.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$field_id    = $field_id ?? ( $field_data['id'] ?? 'field_' . uniqid() );
$field_type  = $field_data['type'] ?? 'text';
$field_label = ! empty( $field_data['label'] ) ? $field_data['label'] : __( '(Untitled Field)', 'apf-aslam' );
$field_types = APF_Fields_Manager::get_field_types();
$has_options = in_array( $field_type, array( 'select', 'radio', 'checkbox', 'color_swatch', 'image_swatch' ), true );
$is_products = in_array( $field_type, array( 'products', 'recommended_products' ), true );
$rec_products_selected = $field_data['recommended_product_ids'] ?? array();
if ( ! is_array( $rec_products_selected ) ) {
	$rec_products_selected = array();
}
?>

<div class="apf-field-card" data-field-id="<?php echo esc_attr( $field_id ); ?>">
	<!-- Hidden input holding unique ID -->
	<input type="hidden" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][id]" value="<?php echo esc_attr( $field_id ); ?>" class="apf-field-id-input">

	<!-- Card Header / Collapsible Title Bar -->
	<div class="apf-field-card-header">
		<div class="apf-header-left">
			<span class="apf-drag-handle" title="<?php esc_attr_e( 'Drag to reorder', 'apf-aslam' ); ?>">
				<i class="fa-solid fa-grip-vertical"></i>
			</span>
			<span class="apf-type-badge apf-type-<?php echo esc_attr( $field_type ); ?>">
				<i class="fa-solid <?php echo esc_attr( $field_types[ $field_type ]['icon'] ?? 'fa-circle-dot' ); ?>"></i>
				<span class="apf-type-badge-text"><?php echo esc_html( $field_types[ $field_type ]['label'] ?? ucfirst( $field_type ) ); ?></span>
			</span>
			<strong class="apf-field-title-preview"><?php echo esc_html( $field_label ); ?></strong>
			<?php if ( ! empty( $field_data['required'] ) && 'yes' === $field_data['required'] ) : ?>
				<span class="apf-required-pill">* <?php esc_html_e( 'Required', 'apf-aslam' ); ?></span>
			<?php endif; ?>
		</div>
		<div class="apf-header-right">
			<button type="button" class="apf-btn-icon apf-btn-duplicate" title="<?php esc_attr_e( 'Duplicate field', 'apf-aslam' ); ?>">
				<i class="fa-regular fa-copy"></i>
			</button>
			<button type="button" class="apf-btn-icon apf-btn-delete" title="<?php esc_attr_e( 'Delete field', 'apf-aslam' ); ?>">
				<i class="fa-regular fa-trash-can"></i>
			</button>
			<button type="button" class="apf-btn-icon apf-btn-toggle" title="<?php esc_attr_e( 'Expand / Collapse', 'apf-aslam' ); ?>">
				<i class="fa-solid fa-chevron-down"></i>
			</button>
		</div>
	</div>

	<!-- Card Body / Configuration Panels -->
	<div class="apf-field-card-body">
		<!-- Tabs Navigation -->
		<ul class="apf-card-tabs">
			<li class="active" data-tab="general"><i class="fa-solid fa-gear"></i> <?php esc_html_e( 'General', 'apf-aslam' ); ?></li>
			<li class="tab-options-link" data-tab="options" <?php echo ! $has_options ? 'style="display:none;"' : ''; ?>><i class="fa-solid fa-list-check"></i> <?php esc_html_e( 'Options / Choices', 'apf-aslam' ); ?></li>
			<li class="tab-products-link" data-tab="products" <?php echo ! $is_products ? 'style="display:none;"' : ''; ?>><i class="fa-solid fa-basket-shopping"></i> <?php esc_html_e( 'Recommended Products', 'apf-aslam' ); ?></li>
			<li data-tab="pricing"><i class="fa-solid fa-tag"></i> <?php esc_html_e( 'Pricing', 'apf-aslam' ); ?></li>
			<li data-tab="conditions"><i class="fa-solid fa-code-branch"></i> <?php esc_html_e( 'Conditional Logic', 'apf-aslam' ); ?></li>
			<li data-tab="advanced"><i class="fa-solid fa-sliders"></i> <?php esc_html_e( 'Validation & Limits', 'apf-aslam' ); ?></li>
		</ul>

		<div class="apf-tab-contents">
			<!-- 1. GENERAL TAB -->
			<div class="apf-tab-pane active" data-pane="general">
				<div class="apf-form-row apf-col-2">
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Field Type', 'apf-aslam' ); ?> <span class="required">*</span></label>
						<select name="apf_fields[<?php echo esc_attr( $field_id ); ?>][type]" class="apf-field-type-select widefat">
							<?php foreach ( $field_types as $type_key => $type_info ) : ?>
								<option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $field_type, $type_key ); ?> data-has-options="<?php echo $type_info['has_options'] ? '1' : '0'; ?>">
									<?php echo esc_html( $type_info['label'] ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Field Label', 'apf-aslam' ); ?> <span class="required">*</span></label>
						<input type="text" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][label]" value="<?php echo esc_attr( $field_data['label'] ?? '' ); ?>" class="apf-field-label-input widefat" placeholder="<?php esc_attr_e( 'e.g., Choose Your Bread / Bun', 'apf-aslam' ); ?>">
					</div>
				</div>

				<div class="apf-form-row">
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Help / Description Text', 'apf-aslam' ); ?></label>
						<textarea name="apf_fields[<?php echo esc_attr( $field_id ); ?>][description]" class="widefat" rows="2" placeholder="<?php esc_attr_e( 'Short explanation shown to customers under the option...', 'apf-aslam' ); ?>"><?php echo esc_textarea( $field_data['description'] ?? '' ); ?></textarea>
					</div>
				</div>

				<div class="apf-form-row apf-col-2">
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Placeholder Text', 'apf-aslam' ); ?></label>
						<input type="text" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][placeholder]" value="<?php echo esc_attr( $field_data['placeholder'] ?? '' ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g., Select an option or type here...', 'apf-aslam' ); ?>">
					</div>
					<div class="apf-form-group apf-checkbox-toggle-wrap">
						<label><?php esc_html_e( 'Required Field?', 'apf-aslam' ); ?></label>
						<label class="apf-switch">
							<input type="checkbox" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][required]" value="yes" <?php checked( $field_data['required'] ?? 'no', 'yes' ); ?>>
							<span class="apf-slider round"></span>
						</label>
						<span class="apf-switch-desc"><?php esc_html_e( 'Customer must fill or select this option before adding to cart', 'apf-aslam' ); ?></span>
					</div>
				</div>
			</div>

			<!-- 2. OPTIONS / CHOICES TAB (Select, Radio, Checkbox, Color Swatches, Image Swatches) -->
			<div class="apf-tab-pane" data-pane="options">
				<div class="apf-options-table-wrap">
					<table class="apf-options-table widefat">
						<thead>
							<tr>
								<th width="30"></th>
								<th class="col-option-visual" <?php echo in_array( $field_type, array( 'color_swatch', 'image_swatch' ), true ) ? '' : 'style="display:none;"'; ?>>
									<?php esc_html_e( 'Visual (Color / Image)', 'apf-aslam' ); ?>
								</th>
								<th><?php esc_html_e( 'Option Label', 'apf-aslam' ); ?></th>
								<th><?php esc_html_e( 'Option Value', 'apf-aslam' ); ?></th>
								<th><?php esc_html_e( 'Pricing Type', 'apf-aslam' ); ?></th>
								<th><?php esc_html_e( 'Price Amount', 'apf-aslam' ); ?></th>
								<th width="40"></th>
							</tr>
						</thead>
						<tbody class="apf-options-tbody">
							<?php
							$options_list = $field_data['options'] ?? array();
							if ( empty( $options_list ) ) {
								$options_list = array(
									array( 'label' => '', 'value' => '', 'pricing_type' => 'flat', 'pricing_amount' => 0, 'color' => '#EB1400', 'image_url' => '', 'image_id' => 0 ),
								);
							}
							foreach ( $options_list as $opt_idx => $opt ) :
							?>
								<tr class="apf-option-row">
									<td class="apf-opt-handle"><i class="fa-solid fa-grip-lines"></i></td>
									<td class="col-option-visual" <?php echo in_array( $field_type, array( 'color_swatch', 'image_swatch' ), true ) ? '' : 'style="display:none;"'; ?>>
										<!-- Color Swatch Picker -->
										<div class="visual-color-wrap" <?php echo 'color_swatch' === $field_type ? '' : 'style="display:none;"'; ?>>
											<input type="text" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][options][<?php echo esc_attr( $opt_idx ); ?>][color]" value="<?php echo esc_attr( $opt['color'] ?? '#EB1400' ); ?>" class="apf-color-picker-input">
										</div>
										<!-- Image Swatch Media Uploader -->
										<div class="visual-image-wrap" <?php echo 'image_swatch' === $field_type ? '' : 'style="display:none;"'; ?>>
											<div class="apf-image-preview-box">
												<?php if ( ! empty( $opt['image_url'] ) ) : ?>
													<img src="<?php echo esc_url( $opt['image_url'] ); ?>" alt="Swatch">
												<?php else : ?>
													<i class="fa-regular fa-image"></i>
												<?php endif; ?>
											</div>
											<input type="hidden" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][options][<?php echo esc_attr( $opt_idx ); ?>][image_url]" value="<?php echo esc_url( $opt['image_url'] ?? '' ); ?>" class="apf-opt-image-url">
											<input type="hidden" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][options][<?php echo esc_attr( $opt_idx ); ?>][image_id]" value="<?php echo esc_attr( $opt['image_id'] ?? 0 ); ?>" class="apf-opt-image-id">
											<button type="button" class="button button-small apf-btn-upload-image"><?php esc_html_e( 'Select', 'apf-aslam' ); ?></button>
										</div>
									</td>
									<td>
										<input type="text" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][options][<?php echo esc_attr( $opt_idx ); ?>][label]" value="<?php echo esc_attr( $opt['label'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. Brioche Bun', 'apf-aslam' ); ?>" class="widefat apf-opt-label">
									</td>
									<td>
										<input type="text" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][options][<?php echo esc_attr( $opt_idx ); ?>][value]" value="<?php echo esc_attr( $opt['value'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'brioche_bun', 'apf-aslam' ); ?>" class="widefat apf-opt-val">
									</td>
									<td>
										<select name="apf_fields[<?php echo esc_attr( $field_id ); ?>][options][<?php echo esc_attr( $opt_idx ); ?>][pricing_type]" class="widefat">
											<option value="flat" <?php selected( $opt['pricing_type'] ?? 'flat', 'flat' ); ?>><?php esc_html_e( 'Flat Fee (+/-)', 'apf-aslam' ); ?></option>
											<option value="quantity" <?php selected( $opt['pricing_type'] ?? '', 'quantity' ); ?>><?php esc_html_e( 'Per Item Qty (+/-)', 'apf-aslam' ); ?></option>
											<option value="percentage" <?php selected( $opt['pricing_type'] ?? '', 'percentage' ); ?>><?php esc_html_e( 'Percentage (%)', 'apf-aslam' ); ?></option>
										</select>
									</td>
									<td>
										<input type="number" step="0.01" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][options][<?php echo esc_attr( $opt_idx ); ?>][pricing_amount]" value="<?php echo esc_attr( $opt['pricing_amount'] ?? 0 ); ?>" class="widefat">
									</td>
									<td>
										<button type="button" class="apf-btn-icon apf-btn-remove-option" title="<?php esc_attr_e( 'Remove Choice', 'apf-aslam' ); ?>">
											<i class="fa-solid fa-xmark"></i>
										</button>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class="apf-options-table-footer">
						<button type="button" class="button button-secondary apf-btn-add-option">
							<i class="fa-solid fa-plus"></i> <?php esc_html_e( 'Add Another Option', 'apf-aslam' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- 2B. RECOMMENDED PRODUCTS CONFIGURATION TAB (Pro) -->
			<div class="apf-tab-pane" data-pane="products">
				<div class="apf-form-row apf-col-2">
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Minimum Selection Allowed', 'apf-aslam' ); ?></label>
						<input type="number" min="0" max="6" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][min_products]" value="<?php echo esc_attr( $field_data['min_products'] ?? 0 ); ?>" class="widefat" placeholder="0">
						<span class="description"><?php esc_html_e( 'Default is 0 (customer can select none).', 'apf-aslam' ); ?></span>
					</div>
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Maximum Selection Allowed', 'apf-aslam' ); ?></label>
						<input type="number" min="1" max="12" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][max_products]" value="<?php echo esc_attr( $field_data['max_products'] ?? 6 ); ?>" class="widefat" placeholder="6">
						<span class="description"><?php esc_html_e( 'Default is 6 (user can select up to 6 products).', 'apf-aslam' ); ?></span>
					</div>
				</div>

				<div class="apf-form-row apf-col-2">
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Product Pricing Mode', 'apf-aslam' ); ?></label>
						<select name="apf_fields[<?php echo esc_attr( $field_id ); ?>][product_pricing_mode]" class="apf-rec-pricing-mode widefat">
							<option value="regular" <?php selected( $field_data['product_pricing_mode'] ?? 'regular', 'regular' ); ?>><?php esc_html_e( 'Product Regular / Sale Price (Default)', 'apf-aslam' ); ?></option>
							<option value="discount_pct" <?php selected( $field_data['product_pricing_mode'] ?? '', 'discount_pct' ); ?>><?php esc_html_e( 'Percentage Discount (%)', 'apf-aslam' ); ?></option>
							<option value="flat" <?php selected( $field_data['product_pricing_mode'] ?? '', 'flat' ); ?>><?php esc_html_e( 'Custom Flat Fee (+/-)', 'apf-aslam' ); ?></option>
							<option value="free" <?php selected( $field_data['product_pricing_mode'] ?? '', 'free' ); ?>><?php esc_html_e( 'Free / Included ($0.00)', 'apf-aslam' ); ?></option>
						</select>
					</div>
					<div class="apf-form-group apf-rec-pricing-amount-group">
						<label><?php esc_html_e( 'Discount Percentage or Flat Amount', 'apf-aslam' ); ?></label>
						<input type="number" step="0.01" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][product_pricing_amount]" value="<?php echo esc_attr( $field_data['product_pricing_amount'] ?? 0 ); ?>" class="widefat" placeholder="0.00">
						<span class="description"><?php esc_html_e( 'e.g. 10 for 10% combo discount, or 2.50 for fixed add-on rate.', 'apf-aslam' ); ?></span>
					</div>
				</div>

				<div class="apf-form-row">
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Select Products to Recommend (Hold Ctrl/Cmd to select multiple):', 'apf-aslam' ); ?></label>
						<select name="apf_fields[<?php echo esc_attr( $field_id ); ?>][recommended_product_ids][]" multiple="multiple" class="widefat apf-rec-products-select" style="min-height: 140px;">
							<?php
							$all_products = function_exists( 'wc_get_products' ) ? wc_get_products( array( 'limit' => 100, 'status' => 'publish' ) ) : array();
							if ( ! empty( $all_products ) ) :
								foreach ( $all_products as $p ) :
									$p_id    = $p->get_id();
									$is_sel  = in_array( $p_id, array_map( 'absint', $rec_products_selected ), true );
									$p_price = function_exists( 'wc_price' ) ? wc_price( $p->get_price() ) : '$' . $p->get_price();
							?>
									<option value="<?php echo esc_attr( $p_id ); ?>" <?php selected( $is_sel ); ?>>
										#<?php echo esc_html( $p_id ); ?> — <?php echo esc_html( $p->get_name() ); ?> (<?php echo wp_strip_all_tags( $p_price ); ?>)
									</option>
							<?php 
								endforeach; 
							else :
							?>
								<option value="" disabled><?php esc_html_e( 'No published products found.', 'apf-aslam' ); ?></option>
							<?php endif; ?>
						</select>
						<span class="description"><?php esc_html_e( 'Leave unselected to automatically recommend up to 6 latest products from your store.', 'apf-aslam' ); ?></span>
					</div>
				</div>
			</div>

			<!-- 3. PRICING TAB (Dynamic Formulas, Character Counting, Flat & Quantity) -->
			<div class="apf-tab-pane" data-pane="pricing">
				<div class="apf-form-row apf-col-2">
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Field Base Pricing Model', 'apf-aslam' ); ?></label>
						<select name="apf_fields[<?php echo esc_attr( $field_id ); ?>][pricing_type]" class="apf-pricing-type-select widefat">
							<option value="none" <?php selected( $field_data['pricing_type'] ?? 'none', 'none' ); ?>><?php esc_html_e( 'No Extra Charge (Free)', 'apf-aslam' ); ?></option>
							<option value="flat" <?php selected( $field_data['pricing_type'] ?? '', 'flat' ); ?>><?php esc_html_e( 'Flat Fee (+/- fixed amount)', 'apf-aslam' ); ?></option>
							<option value="quantity" <?php selected( $field_data['pricing_type'] ?? '', 'quantity' ); ?>><?php esc_html_e( 'Quantity Based (Multiplied by item quantity)', 'apf-aslam' ); ?></option>
							<option value="percentage" <?php selected( $field_data['pricing_type'] ?? '', 'percentage' ); ?>><?php esc_html_e( 'Percentage of Base Product Price (%)', 'apf-aslam' ); ?></option>
							<option value="char_count" <?php selected( $field_data['pricing_type'] ?? '', 'char_count' ); ?>><?php esc_html_e( 'Character Count Fee (Text / Engraving)', 'apf-aslam' ); ?></option>
							<option value="formula" <?php selected( $field_data['pricing_type'] ?? '', 'formula' ); ?>><?php esc_html_e( 'Custom Math Formula (Pro)', 'apf-aslam' ); ?></option>
						</select>
					</div>
					<div class="apf-form-group apf-pricing-amount-wrap">
						<label><?php esc_html_e( 'Price Amount / Rate', 'apf-aslam' ); ?></label>
						<input type="number" step="0.01" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][pricing_amount]" value="<?php echo esc_attr( $field_data['pricing_amount'] ?? 0 ); ?>" class="widefat" placeholder="0.00">
						<span class="description"><?php esc_html_e( 'Enter negative numbers (e.g. -1.50) for discounts/deductions.', 'apf-aslam' ); ?></span>
					</div>
				</div>

				<!-- Character Count Pricing Options -->
				<div class="apf-form-row apf-char-pricing-wrap" <?php echo 'char_count' === ( $field_data['pricing_type'] ?? '' ) ? '' : 'style="display:none;"'; ?>>
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Free Characters Allowance', 'apf-aslam' ); ?></label>
						<input type="number" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][free_characters]" value="<?php echo esc_attr( $field_data['free_characters'] ?? 0 ); ?>" class="widefat" min="0" step="1">
						<span class="description"><?php esc_html_e( 'E.g. first 10 characters are free, subsequent characters charged at rate above.', 'apf-aslam' ); ?></span>
					</div>
				</div>

				<!-- Formula Pricing Options -->
				<div class="apf-form-row apf-formula-pricing-wrap" <?php echo 'formula' === ( $field_data['pricing_type'] ?? '' ) ? '' : 'style="display:none;"'; ?>>
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Mathematical Pricing Formula', 'apf-aslam' ); ?></label>
						<input type="text" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][pricing_formula]" value="<?php echo esc_attr( $field_data['pricing_formula'] ?? '' ); ?>" class="widefat code" placeholder="[price] + [qty] * 2.5">
						<span class="description">
							<?php esc_html_e( 'Supported tags: [price], [field_id]. Arithmetic: +, -, *, /, (, ). Example: [price] * 0.15 + 2.00', 'apf-aslam' ); ?>
						</span>
					</div>
				</div>
			</div>

			<!-- 4. CONDITIONAL LOGIC TAB -->
			<div class="apf-tab-pane" data-pane="conditions">
				<div class="apf-conditions-builder">
					<div class="apf-form-group apf-checkbox-toggle-wrap">
						<label class="apf-switch">
							<input type="checkbox" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][conditional_logic][enabled]" value="yes" class="apf-cond-enable-toggle" <?php checked( $field_data['conditional_logic']['enabled'] ?? 'no', 'yes' ); ?>>
							<span class="apf-slider round"></span>
						</label>
						<strong><?php esc_html_e( 'Enable Conditional Logic for this field', 'apf-aslam' ); ?></strong>
					</div>

					<div class="apf-conditions-rules-wrap" <?php echo 'yes' === ( $field_data['conditional_logic']['enabled'] ?? 'no' ) ? '' : 'style="display:none;"'; ?>>
						<div class="apf-cond-match-statement">
							<select name="apf_fields[<?php echo esc_attr( $field_id ); ?>][conditional_logic][action]">
								<option value="show" <?php selected( $field_data['conditional_logic']['action'] ?? 'show', 'show' ); ?>><?php esc_html_e( 'Show', 'apf-aslam' ); ?></option>
								<option value="hide" <?php selected( $field_data['conditional_logic']['action'] ?? '', 'hide' ); ?>><?php esc_html_e( 'Hide', 'apf-aslam' ); ?></option>
							</select>
							<span><?php esc_html_e( 'this field if', 'apf-aslam' ); ?></span>
							<select name="apf_fields[<?php echo esc_attr( $field_id ); ?>][conditional_logic][match]">
								<option value="all" <?php selected( $field_data['conditional_logic']['match'] ?? 'all', 'all' ); ?>><?php esc_html_e( 'ALL (AND)', 'apf-aslam' ); ?></option>
								<option value="any" <?php selected( $field_data['conditional_logic']['match'] ?? '', 'any' ); ?>><?php esc_html_e( 'ANY (OR)', 'apf-aslam' ); ?></option>
							</select>
							<span><?php esc_html_e( 'of the following rules match:', 'apf-aslam' ); ?></span>
						</div>

						<div class="apf-cond-rules-list">
							<?php
							$rules = $field_data['conditional_logic']['rules'] ?? array();
							if ( empty( $rules ) ) {
								$rules = array( array( 'field' => '', 'operator' => 'is', 'value' => '' ) );
							}
							foreach ( $rules as $r_idx => $rule ) :
							?>
								<div class="apf-cond-rule-row">
									<select name="apf_fields[<?php echo esc_attr( $field_id ); ?>][conditional_logic][rules][<?php echo esc_attr( $r_idx ); ?>][field]" class="apf-rule-field-select">
										<option value=""><?php esc_html_e( '-- Select Field --', 'apf-aslam' ); ?></option>
										<?php
										// Will be populated dynamically via JS from other fields in the group
										if ( ! empty( $rule['field'] ) ) :
										?>
											<option value="<?php echo esc_attr( $rule['field'] ); ?>" selected><?php echo esc_html( $rule['field'] ); ?></option>
										<?php endif; ?>
									</select>
									<select name="apf_fields[<?php echo esc_attr( $field_id ); ?>][conditional_logic][rules][<?php echo esc_attr( $r_idx ); ?>][operator]">
										<option value="is" <?php selected( $rule['operator'] ?? 'is', 'is' ); ?>><?php esc_html_e( 'is equal to', 'apf-aslam' ); ?></option>
										<option value="is_not" <?php selected( $rule['operator'] ?? '', 'is_not' ); ?>><?php esc_html_e( 'is not equal to', 'apf-aslam' ); ?></option>
										<option value="contains" <?php selected( $rule['operator'] ?? '', 'contains' ); ?>><?php esc_html_e( 'contains', 'apf-aslam' ); ?></option>
										<option value="greater_than" <?php selected( $rule['operator'] ?? '', 'greater_than' ); ?>><?php esc_html_e( 'is greater than', 'apf-aslam' ); ?></option>
										<option value="less_than" <?php selected( $rule['operator'] ?? '', 'less_than' ); ?>><?php esc_html_e( 'is less than', 'apf-aslam' ); ?></option>
										<option value="is_empty" <?php selected( $rule['operator'] ?? '', 'is_empty' ); ?>><?php esc_html_e( 'is empty', 'apf-aslam' ); ?></option>
										<option value="is_not_empty" <?php selected( $rule['operator'] ?? '', 'is_not_empty' ); ?>><?php esc_html_e( 'is not empty', 'apf-aslam' ); ?></option>
									</select>
									<input type="text" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][conditional_logic][rules][<?php echo esc_attr( $r_idx ); ?>][value]" value="<?php echo esc_attr( $rule['value'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Value to match...', 'apf-aslam' ); ?>">
									<button type="button" class="apf-btn-icon apf-btn-remove-rule" title="<?php esc_attr_e( 'Delete Rule', 'apf-aslam' ); ?>">
										<i class="fa-solid fa-trash-can"></i>
									</button>
								</div>
							<?php endforeach; ?>
						</div>

						<button type="button" class="button button-secondary button-small apf-btn-add-rule">
							<i class="fa-solid fa-plus"></i> <?php esc_html_e( 'Add Another Rule', 'apf-aslam' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- 5. ADVANCED & VALIDATION TAB (Limits, File extensions, Steppers) -->
			<div class="apf-tab-pane" data-pane="advanced">
				<div class="apf-form-row apf-col-3">
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Minimum Value (Numbers / Steppers)', 'apf-aslam' ); ?></label>
						<input type="number" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][min_value]" value="<?php echo esc_attr( $field_data['min_value'] ?? '' ); ?>" class="widefat" placeholder="0">
					</div>
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Maximum Value (Numbers / Steppers)', 'apf-aslam' ); ?></label>
						<input type="number" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][max_value]" value="<?php echo esc_attr( $field_data['max_value'] ?? '' ); ?>" class="widefat" placeholder="10">
					</div>
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Step (Increment)', 'apf-aslam' ); ?></label>
						<input type="number" step="any" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][step]" value="<?php echo esc_attr( $field_data['step'] ?? 1 ); ?>" class="widefat">
					</div>
				</div>

				<div class="apf-form-row apf-col-2">
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Max Character Length (Text & Textarea)', 'apf-aslam' ); ?></label>
						<input type="number" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][max_length]" value="<?php echo esc_attr( $field_data['max_length'] ?? '' ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g. 50 (shows live countdown)', 'apf-aslam' ); ?>">
					</div>
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Max File Size in MB (File Uploads)', 'apf-aslam' ); ?></label>
						<input type="number" step="0.5" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][max_file_size_mb]" value="<?php echo esc_attr( $field_data['max_file_size_mb'] ?? 5 ); ?>" class="widefat">
					</div>
				</div>

				<div class="apf-form-row">
					<div class="apf-form-group">
						<label><?php esc_html_e( 'Allowed File Extensions (Comma-separated)', 'apf-aslam' ); ?></label>
						<input type="text" name="apf_fields[<?php echo esc_attr( $field_id ); ?>][allowed_extensions]" value="<?php echo esc_attr( $field_data['allowed_extensions'] ?? 'jpg,jpeg,png,webp,pdf' ); ?>" class="widefat">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
