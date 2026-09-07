<?php
/**
 * WooCommerce Cart Integration.
 *
 * Injects fields on single product page, validates submissions, persists cart item data,
 * dynamically recalculates cart prices, and displays choices in cart & checkout.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class APF_Cart {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Frontend Display on Product Page.
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'render_product_fields' ), 25 );

		// Validation on Add to Cart.
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_to_cart' ), 10, 3 );

		// Add custom data to cart item.
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 3 );

		// Dynamic cart item price adjustment.
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'calculate_cart_totals' ), 10, 1 );

		// Display custom options in cart & checkout.
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );

		// Preserve custom options during re-ordering.
		add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'order_again_cart_item_data' ), 10, 3 );
	}

	/**
	 * Render all applicable fields on single product page before the Add to Cart button.
	 */
	public function render_product_fields() {
		global $product;

		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		$product_id = $product->get_id();
		$loader     = APF_Loader::get_instance();
		$fields     = $loader->fields_manager->get_fields_for_product( $product_id );

		if ( empty( $fields ) ) {
			return;
		}

		$style_settings = get_option( 'apf_aslam_style_settings', array() );
		$swatch_layout  = $style_settings['swatch_layout'] ?? 'cards';
		$summary_show   = $style_settings['show_order_summary'] ?? 'yes';
		$summary_layout = $style_settings['summary_layout'] ?? 'receipt';

		echo '<div class="apf-fields-container barab-theme-wrap ' . esc_attr( 'swatch-layout-' . $swatch_layout ) . '" data-product-id="' . esc_attr( $product_id ) . '" data-base-price="' . esc_attr( $product->get_price() ) . '">';
		echo '<div class="apf-fields-inner">';

		foreach ( $fields as $field_id => $field ) {
			echo $loader->fields_manager->render_field( $field, $product_id );
		}

		echo '</div><!-- /.apf-fields-inner -->';

		// Live Order Summary / Restaurant Check breakdown card.
		if ( 'yes' === $summary_show ) {
			$template_summary = APF_ASLAM_PATH . 'public/templates/summary-totals.php';
			if ( file_exists( $template_summary ) ) {
				include $template_summary;
			}
		}

		echo '</div><!-- /.apf-fields-container -->';
	}

	/**
	 * Validate posted fields upon clicking Add to Cart.
	 *
	 * @param bool $passed Current validation state.
	 * @param int  $product_id Product ID.
	 * @param int  $quantity Quantity.
	 * @return bool
	 */
	public function validate_add_to_cart( $passed, $product_id, $quantity ) {
		$loader = APF_Loader::get_instance();
		$fields = $loader->fields_manager->get_fields_for_product( $product_id );

		if ( empty( $fields ) ) {
			return $passed;
		}

		$posted_data = $_POST['apf'] ?? array();

		foreach ( $fields as $fid => $field ) {
			// If field is conditionally hidden, skip validation.
			if ( $this->is_field_hidden_by_conditions( $field, $posted_data ) ) {
				continue;
			}

			$val = $posted_data[ $fid ] ?? null;
			$res = $loader->fields_manager->validate_field( $field, $val );

			if ( is_wp_error( $res ) ) {
				wc_add_notice( $res->get_error_message(), 'error' );
				$passed = false;
			}
		}

		return $passed;
	}

	/**
	 * Attach custom selected options and calculated extra price to cart item data.
	 *
	 * @param array $cart_item_data Existing cart item data.
	 * @param int   $product_id Product ID.
	 * @param int   $variation_id Variation ID.
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
		if ( ! isset( $_POST['apf'] ) || ! is_array( $_POST['apf'] ) ) {
			return $cart_item_data;
		}

		$loader     = APF_Loader::get_instance();
		$fields     = $loader->fields_manager->get_fields_for_product( $product_id );
		$target_id  = $variation_id ? $variation_id : $product_id;
		$product    = wc_get_product( $target_id );
		$base_price = $product ? (float) $product->get_price() : 0.0;

		$selected_options = array();
		$total_extra_cost = 0.0;
		$posted_apf       = $_POST['apf'];

		foreach ( $fields as $fid => $field ) {
			// Skip hidden fields.
			if ( $this->is_field_hidden_by_conditions( $field, $posted_apf ) ) {
				continue;
			}

			if ( ! isset( $posted_apf[ $fid ] ) ) {
				continue;
			}

			$val = $posted_apf[ $fid ];
			if ( '' === $val || ( is_array( $val ) && empty( $val ) ) ) {
				continue;
			}

			// Calculate individual field price delta.
			$field_price = $loader->pricing->calculate_field_price( $field, $val, $base_price, $posted_apf );
			$total_extra_cost += $field_price;

			// Prepare human-readable display value.
			$display_value = $this->format_option_display_value( $field, $val );

			$selected_options[ $fid ] = array(
				'id'            => $fid,
				'type'          => $field['type'] ?? 'text',
				'label'         => $field['label'] ?? '',
				'value'         => $val,
				'display_value' => $display_value,
				'price_delta'   => $field_price,
			);
		}

		if ( ! empty( $selected_options ) ) {
			$cart_item_data['apf_options']     = $selected_options;
			$cart_item_data['apf_extra_price'] = $total_extra_cost;
			$cart_item_data['apf_base_price']  = $base_price;
			// Unique hash prevents different custom option configurations from combining in cart.
			$cart_item_data['unique_key']      = md5( microtime() . wp_json_encode( $selected_options ) );
		}

		return $cart_item_data;
	}

	/**
	 * Recalculate cart item prices dynamically.
	 *
	 * Uses idempotent base price + extra price formula so taxes, coupons, and shipping
	 * calculations never alter or multiply the custom addons fee.
	 *
	 * @param WC_Cart $cart Cart object.
	 */
	public function calculate_cart_totals( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['apf_extra_price'] ) && 0.0 != $cart_item['apf_extra_price'] ) {
				$base_price = isset( $cart_item['apf_base_price'] ) ? (float) $cart_item['apf_base_price'] : (float) $cart_item['data']->get_regular_price();
				$new_price  = max( 0, $base_price + (float) $cart_item['apf_extra_price'] );
				$cart_item['data']->set_price( $new_price );
			}
		}
	}

	/**
	 * Display selected custom options in cart and checkout item rows.
	 *
	 * @param array $item_data Existing item data.
	 * @param array $cart_item Cart item data.
	 * @return array
	 */
	public function get_item_data( $item_data, $cart_item ) {
		if ( empty( $cart_item['apf_options'] ) || ! is_array( $cart_item['apf_options'] ) ) {
			return $item_data;
		}

		foreach ( $cart_item['apf_options'] as $option ) {
			$price_label = '';
			if ( ! empty( $option['price_delta'] ) && 0.0 != $option['price_delta'] ) {
				$sign        = $option['price_delta'] > 0 ? '+' : '-';
				$price_label = ' (' . $sign . wc_price( abs( $option['price_delta'] ) ) . ')';
			}

			$item_data[] = array(
				'key'     => esc_html( $option['label'] ),
				'value'   => wp_kses_post( $option['display_value'] . $price_label ),
				'display' => '',
			);
		}

		return $item_data;
	}

	/**
	 * Check if a field is hidden based on its conditional logic rules and submitted data.
	 *
	 * @param array $field Field data.
	 * @param array $posted_data Posted form data.
	 * @return bool True if hidden.
	 */
	private function is_field_hidden_by_conditions( $field, $posted_data ) {
		$cond = $field['conditional_logic'] ?? null;
		if ( empty( $cond ) || empty( $cond['enabled'] ) || 'yes' !== $cond['enabled'] || empty( $cond['rules'] ) ) {
			return false; // Not hidden.
		}

		$match_all = ( 'all' === ( $cond['match'] ?? 'all' ) );
		$action    = $cond['action'] ?? 'show'; // show or hide

		$conditions_met = $match_all ? true : false;

		foreach ( $cond['rules'] as $rule ) {
			$target_field = $rule['field'] ?? '';
			$operator     = $rule['operator'] ?? 'is';
			$target_val   = $rule['value'] ?? '';

			$actual_val = $posted_data[ $target_field ] ?? null;

			$rule_matched = false;
			switch ( $operator ) {
				case 'is':
					$rule_matched = ( (string) $actual_val === (string) $target_val );
					break;
				case 'is_not':
					$rule_matched = ( (string) $actual_val !== (string) $target_val );
					break;
				case 'is_empty':
					$rule_matched = ( is_null( $actual_val ) || '' === $actual_val );
					break;
				case 'is_not_empty':
					$rule_matched = ( ! is_null( $actual_val ) && '' !== $actual_val );
					break;
				case 'contains':
					if ( is_array( $actual_val ) ) {
						$rule_matched = in_array( (string) $target_val, array_map( 'strval', $actual_val ), true );
					} else {
						$rule_matched = ( false !== stripos( (string) $actual_val, (string) $target_val ) );
					}
					break;
				case 'greater_than':
					$rule_matched = ( floatval( $actual_val ) > floatval( $target_val ) );
					break;
				case 'less_than':
					$rule_matched = ( floatval( $actual_val ) < floatval( $target_val ) );
					break;
			}

			if ( $match_all ) {
				if ( ! $rule_matched ) {
					$conditions_met = false;
					break;
				}
			} else {
				if ( $rule_matched ) {
					$conditions_met = true;
					break;
				}
			}
		}

		if ( 'show' === $action ) {
			return ! $conditions_met; // If action is "show", field is hidden if conditions NOT met.
		} else {
			return $conditions_met;   // If action is "hide", field is hidden if conditions ARE met.
		}
	}

	/**
	 * Format value into user-readable representation for cart and emails.
	 *
	 * @param array $field Field data.
	 * @param mixed $val Submitted value.
	 * @return string Formatted display text.
	 */
	private function format_option_display_value( $field, $val ) {
		$type = $field['type'] ?? 'text';

		if ( in_array( $type, array( 'select', 'radio', 'color_swatch', 'image_swatch' ), true ) ) {
			if ( ! empty( $field['options'] ) ) {
				if ( is_array( $val ) ) {
					$labels = array();
					foreach ( $field['options'] as $opt ) {
						if ( in_array( (string) $opt['value'], array_map( 'strval', $val ), true ) ) {
							$labels[] = $opt['label'] ?: $opt['value'];
						}
					}
					return implode( ', ', $labels );
				}
				foreach ( $field['options'] as $opt ) {
					if ( (string) $opt['value'] === (string) $val ) {
						return $opt['label'] ?: $val;
					}
				}
			}
			return is_array( $val ) ? implode( ', ', $val ) : (string) $val;
		}

		if ( 'checkbox' === $type ) {
			if ( is_array( $val ) && ! empty( $field['options'] ) ) {
				$labels = array();
				foreach ( $field['options'] as $opt ) {
					if ( in_array( (string) $opt['value'], array_map( 'strval', $val ), true ) ) {
						$labels[] = $opt['label'] ?: $opt['value'];
					}
				}
				return implode( ', ', $labels );
			}
			if ( 'yes' === $val || '1' === (string) $val ) {
				return __( 'Yes', 'apf-aslam' );
			}
			return is_array( $val ) ? implode( ', ', $val ) : (string) $val;
		}

		if ( 'file_upload' === $type ) {
			if ( is_array( $val ) && ! empty( $val['url'] ) ) {
				$file_name = basename( $val['url'] );
				return '<a href="' . esc_url( $val['url'] ) . '" target="_blank" rel="noopener">' . esc_html( $file_name ) . '</a>';
			}
			return esc_html( (string) $val );
		}

		if ( 'stepper' === $type ) {
			return esc_html( $val ) . ' ' . esc_html__( 'qty', 'apf-aslam' );
		}

		return esc_html( (string) $val );
	}

	/**
	 * Re-order support for custom product options.
	 *
	 * @param array $cart_item_data Cart item data.
	 * @param array $item Order item object.
	 * @param WC_Order $order Order object.
	 * @return array
	 */
	public function order_again_cart_item_data( $cart_item_data, $item, $order ) {
		$apf_meta = $item->get_meta( '_apf_options' );
		if ( ! empty( $apf_meta ) && is_array( $apf_meta ) ) {
			$cart_item_data['apf_options']     = $apf_meta;
			$cart_item_data['apf_extra_price'] = (float) $item->get_meta( '_apf_extra_price' );
			$cart_item_data['unique_key']      = md5( microtime() . wp_json_encode( $apf_meta ) );
		}
		return $cart_item_data;
	}
}
