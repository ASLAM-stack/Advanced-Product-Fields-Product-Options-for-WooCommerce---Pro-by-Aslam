<?php
/**
 * Frontend Template: Recommended Products Field (Pro).
 *
 * Displays up to 6 recommended products (sides, drinks, combo add-ons)
 * which customers can select / deselect (min 0, max 6).
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id              = $field['id'];
$label           = $field['label'] ?? __( 'Recommended For You', 'apf-aslam' );
$desc            = $field['description'] ?? '';
$required        = ( ! empty( $field['required'] ) && 'yes' === $field['required'] );
$min_products    = isset( $field['min_products'] ) && '' !== $field['min_products'] ? absint( $field['min_products'] ) : 0;
$max_products    = isset( $field['max_products'] ) && '' !== $field['max_products'] ? absint( $field['max_products'] ) : 6;
$pricing_mode    = $field['product_pricing_mode'] ?? 'regular';
$override_amount = floatval( $field['product_pricing_amount'] ?? 0 );
$configured_ids  = $field['recommended_product_ids'] ?? array();
$default_ids     = $field['default_value'] ?? array();

if ( ! is_array( $default_ids ) ) {
	$default_ids = array_filter( array_map( 'absint', explode( ',', (string) $default_ids ) ) );
}

// 1. Resolve Products to Display (up to 6).
$products_to_render = array();

if ( ! empty( $configured_ids ) && is_array( $configured_ids ) ) {
	foreach ( $configured_ids as $p_id ) {
		$p_id = absint( $p_id );
		if ( $p_id && function_exists( 'wc_get_product' ) ) {
			$prod = wc_get_product( $p_id );
			if ( $prod && 'publish' === $prod->get_status() ) {
				$products_to_render[] = $prod;
			}
		}
		if ( count( $products_to_render ) >= $max_products ) {
			break;
		}
	}
}

// Fallback: If no products configured, fetch published store products.
if ( empty( $products_to_render ) && function_exists( 'wc_get_products' ) ) {
	$exclude_id = isset( $product_id ) ? absint( $product_id ) : 0;
	$auto_prods = wc_get_products(
		array(
			'status'  => 'publish',
			'limit'   => $max_products,
			'exclude' => $exclude_id ? array( $exclude_id ) : array(),
			'orderby' => 'menu_order title',
			'order'   => 'ASC',
		)
	);
	if ( ! empty( $auto_prods ) ) {
		$products_to_render = $auto_prods;
	}
}
?>

<div 
	class="apf-field-wrap apf-type-products" 
	id="apf-wrap-<?php echo esc_attr( $id ); ?>" 
	data-field-id="<?php echo esc_attr( $id ); ?>" 
	data-field-type="products" 
	data-min-products="<?php echo esc_attr( $min_products ); ?>" 
	data-max-products="<?php echo esc_attr( $max_products ); ?>" 
	data-pricing-mode="<?php echo esc_attr( $pricing_mode ); ?>" 
	data-pricing-amount="<?php echo esc_attr( $override_amount ); ?>" 
	data-conditions="<?php echo esc_attr( wp_json_encode( $field['conditional_logic'] ?? array() ) ); ?>" 
	<?php echo $required ? 'data-required="yes"' : ''; ?>
>
	<div class="apf-products-header">
		<label class="apf-label">
			<span class="apf-label-text"><?php echo esc_html( $label ); ?></span>
			<?php if ( $required ) : ?>
				<span class="apf-required-mark">*</span>
			<?php endif; ?>
		</label>
		<div class="apf-products-meta-info">
			<span class="apf-prod-selection-count" data-max="<?php echo esc_attr( $max_products ); ?>">
				0 / <?php echo esc_html( $max_products ); ?> <?php esc_html_e( 'Selected', 'apf-aslam' ); ?>
			</span>
		</div>
	</div>

	<?php if ( ! empty( $desc ) ) : ?>
		<div class="apf-description"><?php echo wp_kses_post( $desc ); ?></div>
	<?php endif; ?>

	<!-- Maximum selection limit notification -->
	<div class="apf-max-products-alert" style="display: none;">
		<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
		<span class="apf-alert-msg"><?php echo sprintf( esc_html__( 'You can select at most %d recommended items.', 'apf-aslam' ), esc_attr( $max_products ) ); ?></span>
	</div>

	<div class="apf-recommended-products-grid">
		<?php if ( empty( $products_to_render ) ) : ?>
			<div class="apf-no-options-notice">
				<?php esc_html_e( 'No recommended products available at the moment.', 'apf-aslam' ); ?>
			</div>
		<?php else : ?>
			<?php 
			foreach ( $products_to_render as $prod ) : 
				$p_id      = $prod->get_id();
				$opt_id    = "apf_prod_{$id}_{$p_id}";
				$raw_price = (float) $prod->get_price();
				$p_name    = $prod->get_name();
				$is_chk    = in_array( $p_id, array_map( 'absint', $default_ids ), true );

				// Calculate effective display price for this product.
				$final_price = $raw_price;
				$price_html  = '';

				if ( 'free' === $pricing_mode ) {
					$final_price = 0.0;
					$price_html  = '<span class="apf-rec-price-free">' . esc_html__( 'FREE', 'apf-aslam' ) . '</span>';
				} elseif ( 'flat' === $pricing_mode ) {
					$final_price = $override_amount;
					$price_html  = '<span class="apf-rec-price-final">+' . wc_price( $final_price ) . '</span>';
				} elseif ( 'discount_pct' === $pricing_mode && $override_amount > 0 ) {
					$final_price = max( 0.0, $raw_price * ( 1.0 - ( $override_amount / 100.0 ) ) );
					$price_html  = '<del class="apf-rec-price-old">' . wc_price( $raw_price ) . '</del> ' .
					               '<span class="apf-rec-price-final">+' . wc_price( $final_price ) . '</span> ' .
					               '<span class="apf-rec-discount-badge">-' . esc_html( $override_amount ) . '%</span>';
				} else {
					$final_price = $raw_price;
					$price_html  = '<span class="apf-rec-price-final">+' . wc_price( $final_price ) . '</span>';
				}

				// Thumbnail URL.
				$thumb_id  = $prod->get_image_id();
				$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '';
			?>
				<label class="apf-product-card <?php echo $is_chk ? 'selected' : ''; ?>" for="<?php echo esc_attr( $opt_id ); ?>">
					<input 
						type="checkbox" 
						name="apf[<?php echo esc_attr( $id ); ?>][]" 
						id="<?php echo esc_attr( $opt_id ); ?>" 
						value="<?php echo esc_attr( $p_id ); ?>" 
						data-price="<?php echo esc_attr( $final_price ); ?>" 
						data-label="<?php echo esc_attr( $p_name ); ?>" 
						class="apf-rec-prod-checkbox" 
						<?php checked( $is_chk ); ?>
					>
					<div class="apf-prod-card-inner">
						<div class="apf-prod-thumb-wrap">
							<?php if ( ! empty( $thumb_url ) ) : ?>
								<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $p_name ); ?>" class="apf-prod-thumb">
							<?php else : ?>
								<div class="apf-prod-placeholder">
									<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
								</div>
							<?php endif; ?>
						</div>

						<div class="apf-prod-info">
							<h4 class="apf-prod-title"><?php echo esc_html( $p_name ); ?></h4>
							<div class="apf-prod-pricing">
								<?php echo wp_kses_post( $price_html ); ?>
							</div>
						</div>

						<div class="apf-prod-action">
							<span class="apf-prod-toggle-btn">
								<svg class="apf-icon-plus" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
								<svg class="apf-icon-check" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
							</span>
						</div>
					</div>
				</label>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>
