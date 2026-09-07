<?php
/**
 * Pricing Calculation Engine (Pro).
 *
 * Evaluates flat, quantity, percentage, character-count, and mathematical formula pricing.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class APF_Pricing {

	/**
	 * Constructor.
	 */
	public function __construct() {}

	/**
	 * Calculate the price adjustment for a specific field and user input.
	 *
	 * @param array $field Field configuration array.
	 * @param mixed $value Submitted field value.
	 * @param float $base_price Base product price.
	 * @param array $all_values All submitted values in the current form (for formula lookup).
	 * @return float Price delta to add (can be positive, negative, or zero).
	 */
	public function calculate_field_price( $field, $value, $base_price = 0.0, $all_values = array() ) {
		$type = $field['type'] ?? 'text';

		if ( is_null( $value ) || '' === $value ) {
			return 0.0;
		}

		$total_price_delta = 0.0;

		// 1. Choice fields (select, radio, color_swatch, image_swatch).
		if ( in_array( $type, array( 'select', 'radio', 'color_swatch', 'image_swatch' ), true ) ) {
			if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {
				$val_array = is_array( $value ) ? $value : array( $value );
				foreach ( $field['options'] as $opt ) {
					if ( in_array( (string) $opt['value'], array_map( 'strval', $val_array ), true ) ) {
						$total_price_delta += $this->compute_amount_by_type(
							$opt['pricing_type'] ?? 'flat',
							floatval( $opt['pricing_amount'] ?? 0 ),
							$base_price
						);
					}
				}
			}
			return $total_price_delta;
		}

		// 2. Checkbox field (can be single toggle or array of multiple checked values).
		if ( 'checkbox' === $type ) {
			if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {
				$checked_vals = is_array( $value ) ? $value : array( $value );
				foreach ( $field['options'] as $opt ) {
					if ( in_array( (string) $opt['value'], array_map( 'strval', $checked_vals ), true ) ) {
						$total_price_delta += $this->compute_amount_by_type(
							$opt['pricing_type'] ?? 'flat',
							floatval( $opt['pricing_amount'] ?? 0 ),
							$base_price
						);
					}
				}
				return $total_price_delta;
			} else {
				// Single toggle checkbox without choices list.
				if ( 'yes' === $value || '1' === (string) $value || true === $value || ! empty( $value ) ) {
					$p_type   = $field['pricing_type'] ?? 'flat';
					$p_amount = floatval( $field['pricing_amount'] ?? 0 );
					if ( 'none' !== $p_type && 0.0 !== $p_amount ) {
						return $this->compute_amount_by_type( $p_type, $p_amount, $base_price );
					}
				}
				return 0.0;
			}
		}

		// 3. Stepper field (quantity multiplier).
		if ( 'stepper' === $type ) {
			$qty_count = max( 0, floatval( $value ) );
			$unit_rate = floatval( $field['pricing_amount'] ?? 0 );
			return $qty_count * $unit_rate;
		}

		// 4. Character count pricing (for text & textarea).
		if ( in_array( $type, array( 'text', 'textarea' ), true ) && 'char_count' === ( $field['pricing_type'] ?? '' ) ) {
			$char_len        = mb_strlen( (string) $value );
			$free_characters = absint( $field['free_characters'] ?? 0 );
			$billable_chars  = max( 0, $char_len - $free_characters );
			$per_char_price  = floatval( $field['pricing_amount'] ?? 0 );
			return $billable_chars * $per_char_price;
		}

		// 5. Formula pricing (Pro).
		if ( 'formula' === ( $field['pricing_type'] ?? '' ) && ! empty( $field['pricing_formula'] ) ) {
			return $this->evaluate_formula( $field['pricing_formula'], $base_price, $all_values );
		}

		// 6. Generic field pricing (flat, percentage, quantity).
		$p_type   = $field['pricing_type'] ?? 'none';
		$p_amount = floatval( $field['pricing_amount'] ?? 0 );

		if ( 'none' === $p_type || 0.0 === $p_amount ) {
			return 0.0;
		}

		return $this->compute_amount_by_type( $p_type, $p_amount, $base_price );
	}

	/**
	 * Compute price delta based on pricing model type.
	 *
	 * @param string $type flat, percentage, or quantity.
	 * @param float  $amount Configured amount.
	 * @param float  $base_price Base product price.
	 * @return float Price delta.
	 */
	public function compute_amount_by_type( $type, $amount, $base_price ) {
		switch ( $type ) {
			case 'percentage':
				return ( $amount / 100 ) * $base_price;
			case 'quantity':
			case 'flat':
			default:
				return $amount;
		}
	}

	/**
	 * Safely evaluate mathematical formula with dynamic variable substitution.
	 *
	 * Supported placeholders:
	 * - [price] : product base price
	 * - [field_id] : numeric value of any other form field
	 *
	 * @param string $formula Formula string e.g. "[price] + [qty] * 2.5".
	 * @param float  $base_price Base product price.
	 * @param array  $all_values Array of field values keyed by field ID.
	 * @return float Evaluated result.
	 */
	public function evaluate_formula( $formula, $base_price, $all_values = array() ) {
		// Replace [price]
		$expression = str_ireplace( '[price]', (string) floatval( $base_price ), $formula );

		// Replace [field_xyz]
		foreach ( $all_values as $f_key => $f_val ) {
			$f_num = is_numeric( $f_val ) ? floatval( $f_val ) : 0.0;
			$expression = str_ireplace( "[{$f_key}]", (string) $f_num, $expression );
		}

		// Replace any remaining unrecognized brackets with 0
		$expression = preg_replace( '/\[[^\]]+\]/', '0', $expression );

		// Sanitize formula string: strictly allow only numbers, spaces, decimals, and operators + - * / ( )
		$clean_expr = preg_replace( '/[^0-9\.\+\-\*\/\(\)\s]/', '', $expression );

		if ( empty( $clean_expr ) ) {
			return 0.0;
		}

		// Safe evaluation without eval() using custom math parser or controlled expression execution.
		try {
			$result = $this->calculate_math_expression( $clean_expr );
			return is_numeric( $result ) ? floatval( $result ) : 0.0;
		} catch ( Exception $e ) {
			return 0.0;
		}
	}

	/**
	 * Safe recursive descent parser for basic math (+, -, *, /, parentheses).
	 *
	 * @param string $expr Clean math expression string.
	 * @return float
	 */
	private function calculate_math_expression( $expr ) {
		// Tokens extraction.
		$tokens = array();
		preg_match_all( '/\d+(?:\.\d+)?|[\+\-\*\/\(\)]/', $expr, $matches );
		if ( empty( $matches[0] ) ) {
			return 0.0;
		}
		$tokens = $matches[0];
		$index  = 0;

		$parse_factor = function () use ( &$tokens, &$index, &$parse_factor, &$parse_expr ) {
			if ( ! isset( $tokens[ $index ] ) ) {
				return 0.0;
			}
			$token = $tokens[ $index ];
			if ( '(' === $token ) {
				$index++; // skip '('
				$val = $parse_expr();
				if ( isset( $tokens[ $index ] ) && ')' === $tokens[ $index ] ) {
					$index++; // skip ')'
				}
				return $val;
			}
			if ( is_numeric( $token ) ) {
				$index++;
				return floatval( $token );
			}
			if ( '-' === $token ) {
				$index++;
				return - $parse_factor();
			}
			if ( '+' === $token ) {
				$index++;
				return $parse_factor();
			}
			return 0.0;
		};

		$parse_term = function () use ( &$tokens, &$index, &$parse_factor ) {
			$val = $parse_factor();
			while ( isset( $tokens[ $index ] ) && ( '*' === $tokens[ $index ] || '/' === $tokens[ $index ] ) ) {
				$op = $tokens[ $index ];
				$index++;
				$next_val = $parse_factor();
				if ( '*' === $op ) {
					$val *= $next_val;
				} elseif ( '/' === $op ) {
					$val = ( 0.0 != $next_val ) ? ( $val / $next_val ) : 0.0;
				}
			}
			return $val;
		};

		$parse_expr = function () use ( &$tokens, &$index, &$parse_term ) {
			$val = $parse_term();
			while ( isset( $tokens[ $index ] ) && ( '+' === $tokens[ $index ] || '-' === $tokens[ $index ] ) ) {
				$op = $tokens[ $index ];
				$index++;
				$next_val = $parse_term();
				if ( '+' === $op ) {
					$val += $next_val;
				} elseif ( '-' === $op ) {
					$val -= $next_val;
				}
			}
			return $val;
		};

		return $parse_expr();
	}

	/**
	 * Format price badge string for frontend option labels.
	 *
	 * @param string $type Pricing type.
	 * @param float  $amount Pricing amount.
	 * @return string Formatted price tag HTML (e.g. "+ $2.50").
	 */
	public static function format_price_badge( $type, $amount ) {
		if ( 'none' === $type || 0.0 == $amount ) {
			return '';
		}

		$is_negative = $amount < 0;
		$abs_amount  = abs( $amount );
		$sign        = $is_negative ? '-' : '+';

		if ( 'percentage' === $type ) {
			return '<span class="apf-price-badge">' . $sign . ' ' . esc_html( $abs_amount ) . '%</span>';
		}

		$formatted = wc_price( $abs_amount );
		return '<span class="apf-price-badge">' . $sign . ' ' . $formatted . '</span>';
	}
}
