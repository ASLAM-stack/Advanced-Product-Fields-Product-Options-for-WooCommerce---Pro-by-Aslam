<?php
/**
 * WooCommerce Order Persistence & Display.
 *
 * Saves custom options to order line items during checkout (HPOS compatible),
 * displays options in customer emails, thank you page, account page, and admin order view.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class APF_Order {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Save line item meta on checkout.
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'save_order_line_item_meta' ), 10, 4 );

		// Format line item meta display in emails and views.
		add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'format_order_item_meta_value' ), 10, 3 );
	}

	/**
	 * Save custom options to order line item meta during checkout.
	 *
	 * @param WC_Order_Item_Product $item Order line item.
	 * @param string                $cart_item_key Cart item key.
	 * @param array                 $values Cart item values.
	 * @param WC_Order              $order WooCommerce Order object.
	 */
	public function save_order_line_item_meta( $item, $cart_item_key, $values, $order ) {
		if ( empty( $values['apf_options'] ) || ! is_array( $values['apf_options'] ) ) {
			return;
		}

		// Save structured data as hidden meta.
		$item->add_meta_data( '_apf_options', $values['apf_options'], true );

		if ( isset( $values['apf_extra_price'] ) ) {
			$item->add_meta_data( '_apf_extra_price', (float) $values['apf_extra_price'], true );
		}

		// Save visible line item meta for each selected option.
		foreach ( $values['apf_options'] as $option ) {
			$label = ! empty( $option['label'] ) ? $option['label'] : __( 'Option', 'apf-aslam' );
			$val   = $option['display_value'] ?? $option['value'];

			// Append price delta if applicable.
			if ( ! empty( $option['price_delta'] ) && 0.0 != $option['price_delta'] ) {
				$sign = $option['price_delta'] > 0 ? '+' : '-';
				$val .= ' (' . $sign . wc_price( abs( $option['price_delta'] ) ) . ')';
			}

			$item->add_meta_data( $label, $val, true );
		}
	}

	/**
	 * Format order item meta value for display (ensuring HTML links for files render safely).
	 *
	 * @param string        $display_value Formatted meta value.
	 * @param WC_Meta_Data  $meta Meta object.
	 * @param WC_Order_Item $item Order item object.
	 * @return string
	 */
	public function format_order_item_meta_value( $display_value, $meta, $item ) {
		// Allow safe HTML for file links or badges.
		return wp_kses_post( $display_value );
	}
}
