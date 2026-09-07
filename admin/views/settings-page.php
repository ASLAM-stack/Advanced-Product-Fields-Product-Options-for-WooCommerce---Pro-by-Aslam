<?php
/**
 * Settings & Style Customizer Page View.
 *
 * Allows store owners to customize styles, select presets (Barab Fast Food),
 * adjust colors, typography, border radius, and inject custom CSS.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$presets = APF_Style_Generator::get_presets();
$current_preset = $settings['preset'] ?? 'barab';
?>

<div class="wrap apf-settings-wrap">
	<div class="apf-settings-banner">
		<div class="apf-banner-text">
			<h1><i class="fa-solid fa-palette"></i> <?php esc_html_e( 'Product Fields Style Customizer', 'apf-aslam' ); ?></h1>
			<p><?php esc_html_e( 'Default styled for Barab Fast Food & Restaurant theme. Customize colors, fonts, border radiuses, and layout as you wish.', 'apf-aslam' ); ?></p>
		</div>
		<div class="apf-banner-brand">
			<span class="apf-author-tag">By Aslam Plugins</span>
		</div>
	</div>

	<?php if ( isset( $_GET['saved'] ) ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Style settings updated successfully!', 'apf-aslam' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['preset_loaded'] ) ) : ?>
		<div class="notice notice-info is-dismissible">
			<p><?php esc_html_e( 'Theme style preset loaded successfully!', 'apf-aslam' ); ?></p>
		</div>
	<?php endif; ?>

	<div class="apf-settings-grid">
		<!-- Left: Style Controls Form -->
		<div class="apf-settings-form-column">
			<form method="post" action="" id="apf-style-settings-form">
				<?php wp_nonce_field( 'apf_save_settings_action', 'apf_save_settings_nonce' ); ?>

				<!-- Preset Selector Card -->
				<div class="apf-card">
					<div class="apf-card-header">
						<h3><i class="fa-solid fa-wand-magic-sparkles"></i> <?php esc_html_e( '1. Choose a Style Preset', 'apf-aslam' ); ?></h3>
					</div>
					<div class="apf-card-body">
						<div class="apf-preset-cards">
							<?php foreach ( $presets as $p_key => $p_data ) : ?>
								<label class="apf-preset-card <?php echo ( $current_preset === $p_key ) ? 'active' : ''; ?>">
									<input type="radio" name="preset" value="<?php echo esc_attr( $p_key ); ?>" <?php checked( $current_preset, $p_key ); ?>>
									<div class="apf-preset-swatches">
										<span style="background: <?php echo esc_attr( $p_data['primary_color'] ); ?>;"></span>
										<span style="background: <?php echo esc_attr( $p_data['secondary_color'] ); ?>;"></span>
										<span style="background: <?php echo esc_attr( $p_data['accent_color'] ); ?>;"></span>
										<span style="background: <?php echo esc_attr( $p_data['bg_color'] ); ?>;"></span>
									</div>
									<strong><?php echo esc_html( $p_data['name'] ); ?></strong>
									<button type="submit" name="load_preset" value="<?php echo esc_attr( $p_key ); ?>" class="button button-small apf-btn-load-preset">
										<?php esc_html_e( 'Apply Preset', 'apf-aslam' ); ?>
									</button>
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<!-- Color Palette Customizer -->
				<div class="apf-card">
					<div class="apf-card-header">
						<h3><i class="fa-solid fa-droplet"></i> <?php esc_html_e( '2. Theme Colors', 'apf-aslam' ); ?></h3>
					</div>
					<div class="apf-card-body">
						<div class="apf-colors-grid">
							<div class="apf-color-control">
								<label><?php esc_html_e( 'Primary Brand Color (Barab Crimson)', 'apf-aslam' ); ?></label>
								<input type="text" name="primary_color" value="<?php echo esc_attr( $settings['primary_color'] ?? '#EB1400' ); ?>" class="apf-color-picker" data-default-color="#EB1400">
								<span class="description"><?php esc_html_e( 'Used for active states, checkmarks, primary buttons', 'apf-aslam' ); ?></span>
							</div>

							<div class="apf-color-control">
								<label><?php esc_html_e( 'Secondary Accent Color (Herb Green)', 'apf-aslam' ); ?></label>
								<input type="text" name="secondary_color" value="<?php echo esc_attr( $settings['secondary_color'] ?? '#3F9065' ); ?>" class="apf-color-picker" data-default-color="#3F9065">
								<span class="description"><?php esc_html_e( 'Used for quantity steppers and positive tags', 'apf-aslam' ); ?></span>
							</div>

							<div class="apf-color-control">
								<label><?php esc_html_e( 'Vibrant Accent (Warm Orange)', 'apf-aslam' ); ?></label>
								<input type="text" name="accent_color" value="<?php echo esc_attr( $settings['accent_color'] ?? '#FF9924' ); ?>" class="apf-color-picker" data-default-color="#FF9924">
								<span class="description"><?php esc_html_e( 'Used for price tags and discounts', 'apf-aslam' ); ?></span>
							</div>

							<div class="apf-color-control">
								<label><?php esc_html_e( 'Field & Swatch Background (Cream Parchment)', 'apf-aslam' ); ?></label>
								<input type="text" name="bg_color" value="<?php echo esc_attr( $settings['bg_color'] ?? '#F7F2E2' ); ?>" class="apf-color-picker" data-default-color="#F7F2E2">
								<span class="description"><?php esc_html_e( 'Background fill for inputs, cards, and select fields', 'apf-aslam' ); ?></span>
							</div>

							<div class="apf-color-control">
								<label><?php esc_html_e( 'Border Color', 'apf-aslam' ); ?></label>
								<input type="text" name="border_color" value="<?php echo esc_attr( $settings['border_color'] ?? '#E4E4E4' ); ?>" class="apf-color-picker" data-default-color="#E4E4E4">
							</div>

							<div class="apf-color-control">
								<label><?php esc_html_e( 'Heading & Label Text Color', 'apf-aslam' ); ?></label>
								<input type="text" name="text_color" value="<?php echo esc_attr( $settings['text_color'] ?? '#121212' ); ?>" class="apf-color-picker" data-default-color="#121212">
							</div>

							<div class="apf-color-control">
								<label><?php esc_html_e( 'Description Text Color', 'apf-aslam' ); ?></label>
								<input type="text" name="body_text_color" value="<?php echo esc_attr( $settings['body_text_color'] ?? '#6C6C6C' ); ?>" class="apf-color-picker" data-default-color="#6C6C6C">
							</div>

							<div class="apf-color-control">
								<label><?php esc_html_e( 'Pill Badge Background (Warm Biscuit)', 'apf-aslam' ); ?></label>
								<input type="text" name="badge_bg_color" value="<?php echo esc_attr( $settings['badge_bg_color'] ?? '#FDE1B9' ); ?>" class="apf-color-picker" data-default-color="#FDE1B9">
							</div>
						</div>
					</div>
				</div>

				<!-- Geometry & Typography -->
				<div class="apf-card">
					<div class="apf-card-header">
						<h3><i class="fa-solid fa-shapes"></i> <?php esc_html_e( '3. Geometry & Typography', 'apf-aslam' ); ?></h3>
					</div>
					<div class="apf-card-body">
						<div class="apf-form-row apf-col-2">
							<div class="apf-form-group">
								<label><?php esc_html_e( 'Border Radius (Shape)', 'apf-aslam' ); ?></label>
								<select name="border_radius" class="widefat">
									<option value="50" <?php selected( $settings['border_radius'] ?? '50', '50' ); ?>><?php esc_html_e( 'Full Pill (50px - Barab Theme Signature)', 'apf-aslam' ); ?></option>
									<option value="16" <?php selected( $settings['border_radius'] ?? '', '16' ); ?>><?php esc_html_e( 'Modern Rounded (16px)', 'apf-aslam' ); ?></option>
									<option value="8" <?php selected( $settings['border_radius'] ?? '', '8' ); ?>><?php esc_html_e( 'Subtle Curve (8px)', 'apf-aslam' ); ?></option>
									<option value="0" <?php selected( $settings['border_radius'] ?? '', '0' ); ?>><?php esc_html_e( 'Sharp / Square (0px)', 'apf-aslam' ); ?></option>
								</select>
							</div>
							<div class="apf-form-group">
								<label><?php esc_html_e( 'Typography Styling', 'apf-aslam' ); ?></label>
								<select name="font_family" class="widefat">
									<option value="barab" <?php selected( $settings['font_family'] ?? 'barab', 'barab' ); ?>><?php esc_html_e( 'Barab Theme Fonts (Barlow Condensed + Inter)', 'apf-aslam' ); ?></option>
									<option value="inter" <?php selected( $settings['font_family'] ?? '', 'inter' ); ?>><?php esc_html_e( 'Modern Inter Clean', 'apf-aslam' ); ?></option>
									<option value="inherit" <?php selected( $settings['font_family'] ?? '', 'inherit' ); ?>><?php esc_html_e( 'Inherit from Current Theme', 'apf-aslam' ); ?></option>
								</select>
							</div>
						</div>

						<div class="apf-form-row apf-col-2">
							<div class="apf-form-group">
								<label><?php esc_html_e( 'Swatch & Choice Layout Style', 'apf-aslam' ); ?></label>
								<select name="swatch_layout" class="widefat">
									<option value="cards" <?php selected( $settings['swatch_layout'] ?? 'cards', 'cards' ); ?>><?php esc_html_e( 'Elevated Food Cards with Shadows (Barab)', 'apf-aslam' ); ?></option>
									<option value="pills" <?php selected( $settings['swatch_layout'] ?? '', 'pills' ); ?>><?php esc_html_e( 'Compact Pill Badges', 'apf-aslam' ); ?></option>
									<option value="grid" <?php selected( $settings['swatch_layout'] ?? '', 'grid' ); ?>><?php esc_html_e( 'Modern Multi-Column Grid', 'apf-aslam' ); ?></option>
								</select>
							</div>
							<div class="apf-form-group">
								<label><?php esc_html_e( 'Live Order Total Summary Card', 'apf-aslam' ); ?></label>
								<select name="summary_layout" class="widefat">
									<option value="receipt" <?php selected( $settings['summary_layout'] ?? 'receipt', 'receipt' ); ?>><?php esc_html_e( 'Restaurant Receipt Card (Barab Check)', 'apf-aslam' ); ?></option>
									<option value="compact" <?php selected( $settings['summary_layout'] ?? '', 'compact' ); ?>><?php esc_html_e( 'Compact Inline Breakdown', 'apf-aslam' ); ?></option>
									<option value="sticky" <?php selected( $settings['summary_layout'] ?? '', 'sticky' ); ?>><?php esc_html_e( 'Sticky Floating Bottom Bar', 'apf-aslam' ); ?></option>
								</select>
							</div>
						</div>

						<div class="apf-form-row">
							<div class="apf-form-group apf-checkbox-toggle-wrap">
								<label class="apf-switch">
									<input type="checkbox" name="show_order_summary" value="yes" <?php checked( $settings['show_order_summary'] ?? 'yes', 'yes' ); ?>>
									<span class="apf-slider round"></span>
								</label>
								<strong><?php esc_html_e( 'Show Live Dynamic Price Summary Box on Product Page', 'apf-aslam' ); ?></strong>
							</div>
						</div>
					</div>
				</div>

				<!-- Custom CSS Editor -->
				<div class="apf-card">
					<div class="apf-card-header">
						<h3><i class="fa-solid fa-code"></i> <?php esc_html_e( '4. Custom CSS Overrides', 'apf-aslam' ); ?></h3>
					</div>
					<div class="apf-card-body">
						<textarea name="custom_css" class="widefat code" rows="6" placeholder="/* Enter custom CSS rules here to override styles as you wish */&#10;.apf-fields-container { ... }"><?php echo esc_textarea( $settings['custom_css'] ?? '' ); ?></textarea>
					</div>
				</div>

				<p class="submit">
					<button type="submit" class="button button-primary button-hero">
						<i class="fa-solid fa-floppy-disk"></i> <?php esc_html_e( 'Save Style Settings', 'apf-aslam' ); ?>
					</button>
				</p>
			</form>
		</div>

		<!-- Right: Live Visual Preview of Barab Food Field -->
		<div class="apf-settings-preview-column">
			<div class="apf-card apf-preview-sticky">
				<div class="apf-card-header">
					<h3><i class="fa-solid fa-eye"></i> <?php esc_html_e( 'Live Food Field Preview', 'apf-aslam' ); ?></h3>
				</div>
				<div class="apf-card-body apf-preview-body">
					<div class="barab-preview-food-item">
						<div class="preview-food-badge">Barab Gourmet Burger</div>
						<div class="preview-field-sample">
							<label class="sample-label">Choose Bun Style <span class="badge-price">+ $1.50</span></label>
							<div class="sample-pills">
								<span class="sample-pill active"><i class="fa-solid fa-check"></i> Brioche Bun</span>
								<span class="sample-pill">Sesame Bun</span>
								<span class="sample-pill">Gluten Free (+ $1.00)</span>
							</div>
						</div>

						<div class="preview-field-sample" style="margin-top: 15px;">
							<label class="sample-label">Extra Cheese Slice</label>
							<div class="sample-stepper">
								<button type="button" class="btn-step"><i class="fa-solid fa-minus"></i></button>
								<span class="step-val">2</span>
								<button type="button" class="btn-step"><i class="fa-solid fa-plus"></i></button>
								<span class="step-subtotal">+ $3.00 ($1.50 each)</span>
							</div>
						</div>

						<!-- Sample Receipt Breakdown -->
						<div class="sample-receipt-box">
							<div class="receipt-header">
								<i class="fa-solid fa-receipt"></i>
								<strong>Order Breakdown</strong>
							</div>
							<div class="receipt-row">
								<span>Base Burger</span>
								<span>$12.00</span>
							</div>
							<div class="receipt-row">
								<span>Brioche Bun</span>
								<span>+ $1.50</span>
							</div>
							<div class="receipt-row">
								<span>2x Extra Cheese</span>
								<span>+ $3.00</span>
							</div>
							<div class="receipt-total">
								<span>Total Amount</span>
								<strong class="total-num">$16.50</strong>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
