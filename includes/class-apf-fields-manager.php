<?php
/**
 * Fields Manager.
 *
 * Registers field types, fetches applicable fields for a product, and validates inputs.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class APF_Fields_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {}

	/**
	 * Get list of all supported field types.
	 *
	 * @return array
	 */
	public static function get_field_types() {
		return array(
			'text'         => array(
				'label'       => __( 'Text (Single Line)', 'apf-aslam' ),
				'icon'        => 'fa-font',
				'category'    => 'standard',
				'has_options' => false,
			),
			'textarea'     => array(
				'label'       => __( 'Textarea (Multi-Line)', 'apf-aslam' ),
				'icon'        => 'fa-align-left',
				'category'    => 'standard',
				'has_options' => false,
			),
			'number'       => array(
				'label'       => __( 'Number', 'apf-aslam' ),
				'icon'        => 'fa-hashtag',
				'category'    => 'standard',
				'has_options' => false,
			),
			'select'       => array(
				'label'       => __( 'Select Dropdown', 'apf-aslam' ),
				'icon'        => 'fa-caret-square-down',
				'category'    => 'choices',
				'has_options' => true,
			),
			'radio'        => array(
				'label'       => __( 'Radio Buttons (Pills / Cards)', 'apf-aslam' ),
				'icon'        => 'fa-dot-circle',
				'category'    => 'choices',
				'has_options' => true,
			),
			'checkbox'     => array(
				'label'       => __( 'Checkbox / Multi-Select', 'apf-aslam' ),
				'icon'        => 'fa-check-square',
				'category'    => 'choices',
				'has_options' => true,
			),
			'color_swatch' => array(
				'label'       => __( 'Color Swatches (Pro)', 'apf-aslam' ),
				'icon'        => 'fa-palette',
				'category'    => 'swatches',
				'has_options' => true,
			),
			'image_swatch' => array(
				'label'       => __( 'Image Swatches (Pro)', 'apf-aslam' ),
				'icon'        => 'fa-image',
				'category'    => 'swatches',
				'has_options' => true,
			),
			'file_upload'  => array(
				'label'       => __( 'File Upload (Pro)', 'apf-aslam' ),
				'icon'        => 'fa-cloud-upload-alt',
				'category'    => 'advanced',
				'has_options' => false,
			),
			'date'         => array(
				'label'       => __( 'Date Picker (Pro)', 'apf-aslam' ),
				'icon'        => 'fa-calendar-alt',
				'category'    => 'advanced',
				'has_options' => false,
			),
			'time'         => array(
				'label'       => __( 'Time Picker (Pro)', 'apf-aslam' ),
				'icon'        => 'fa-clock',
				'category'    => 'advanced',
				'has_options' => false,
			),
			'stepper'      => array(
				'label'       => __( 'Add-on Quantity Stepper (+/-)', 'apf-aslam' ),
				'icon'        => 'fa-plus-minus',
				'category'    => 'advanced',
				'has_options' => false,
			),
			'section'      => array(
				'label'       => __( 'Section Header / Divider', 'apf-aslam' ),
				'icon'        => 'fa-heading',
				'category'    => 'layout',
				'has_options' => false,
			),
		);
	}

	/**
	 * Retrieve all active fields for a given WooCommerce Product.
	 *
	 * @param int $product_id Product ID.
	 * @return array Array of fields.
	 */
	public function get_fields_for_product( $product_id ) {
		static $cache = array();
		if ( isset( $cache[ $product_id ] ) ) {
			return $cache[ $product_id ];
		}

		$fields = array();

		// 1. Fetch Global Field Groups.
		$groups = get_posts(
			array(
				'post_type'      => 'apf_field_group',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
			)
		);

		$product_cats = wc_get_product_term_ids( $product_id, 'product_cat' );
		$current_user = wp_get_current_user();
		$user_roles   = (array) $current_user->roles;

		foreach ( $groups as $group ) {
			$rules = get_post_meta( $group->ID, '_apf_rules', true );
			if ( ! is_array( $rules ) ) {
				$rules = array( 'apply_to' => 'all' );
			}

			// Check user role rule.
			if ( ! empty( $rules['user_roles'] ) && is_array( $rules['user_roles'] ) ) {
				$role_matched = false;
				foreach ( $user_roles as $role ) {
					if ( in_array( $role, $rules['user_roles'], true ) ) {
						$role_matched = true;
						break;
					}
				}
				if ( ! $role_matched && ! empty( $rules['user_roles'] ) ) {
					continue;
				}
			}

			// Check assignment rule.
			$applies = false;
			$apply_to = $rules['apply_to'] ?? 'all';

			if ( 'all' === $apply_to ) {
				$applies = true;
			} elseif ( 'specific_products' === $apply_to && ! empty( $rules['products'] ) ) {
				if ( in_array( $product_id, $rules['products'] ) ) {
					$applies = true;
				}
			} elseif ( 'categories' === $apply_to && ! empty( $rules['categories'] ) ) {
				$intersection = array_intersect( $rules['categories'], $product_cats );
				if ( ! empty( $intersection ) ) {
					$applies = true;
				}
			}

			if ( $applies ) {
				$group_fields = get_post_meta( $group->ID, '_apf_fields', true );
				if ( is_array( $group_fields ) ) {
					foreach ( $group_fields as $fid => $f_data ) {
						$fields[ $fid ] = $f_data;
					}
				}
			}
		}

		// 2. Fetch Product-Specific Override Fields.
		$product_fields = get_post_meta( $product_id, '_apf_product_fields', true );
		if ( is_array( $product_fields ) ) {
			foreach ( $product_fields as $fid => $f_data ) {
				$fields[ $fid ] = $f_data;
			}
		}

		$filtered_fields = apply_filters( 'apf_fields_for_product', $fields, $product_id );
		$cache[ $product_id ] = $filtered_fields;
		return $filtered_fields;
	}

	/**
	 * Render a field HTML using its modular template.
	 *
	 * @param array $field Field configuration data.
	 * @param int   $product_id Product ID.
	 * @return string HTML output.
	 */
	public function render_field( $field, $product_id ) {
		$type = sanitize_key( $field['type'] ?? 'text' );
		$template_file = APF_ASLAM_PATH . "public/templates/field-{$type}.php";

		if ( ! file_exists( $template_file ) ) {
			$template_file = APF_ASLAM_PATH . 'public/templates/field-text.php';
		}

		ob_start();
		include $template_file;
		return ob_get_clean();
	}

	/**
	 * Validate posted field value against field rules.
	 *
	 * @param array $field Field configuration.
	 * @param mixed $value Posted value.
	 * @return true|WP_Error
	 */
	public function validate_field( $field, $value ) {
		$is_required = ! empty( $field['required'] ) && 'yes' === $field['required'];
		$label       = ! empty( $field['label'] ) ? $field['label'] : __( 'This field', 'apf-aslam' );

		// 1. Required Check.
		if ( $is_required ) {
			if ( 'file_upload' === $field['type'] ) {
				$file_json = ! empty( $value ) ? json_decode( stripslashes( (string) $value ), true ) : null;
				if ( empty( $file_json ) || empty( $file_json['url'] ) ) {
					return new WP_Error( 'apf_required', sprintf( __( '"%s" requires a file to be uploaded.', 'apf-aslam' ), esc_html( $label ) ) );
				}
			} elseif ( is_null( $value ) || '' === $value || ( is_array( $value ) && empty( $value ) ) ) {
				return new WP_Error( 'apf_required', sprintf( __( '"%s" is a required option.', 'apf-aslam' ), esc_html( $label ) ) );
			}
		}

		if ( empty( $value ) && ! is_numeric( $value ) ) {
			return true;
		}

		// 2. Number validation.
		if ( 'number' === $field['type'] || 'stepper' === $field['type'] ) {
			$num_val = floatval( $value );
			if ( '' !== $field['min_value'] && $num_val < floatval( $field['min_value'] ) ) {
				return new WP_Error( 'apf_min_num', sprintf( __( '"%s" must be at least %s.', 'apf-aslam' ), esc_html( $label ), $field['min_value'] ) );
			}
			if ( '' !== $field['max_value'] && $num_val > floatval( $field['max_value'] ) ) {
				return new WP_Error( 'apf_max_num', sprintf( __( '"%s" cannot exceed %s.', 'apf-aslam' ), esc_html( $label ), $field['max_value'] ) );
			}
		}

		// 3. Text length validation.
		if ( in_array( $field['type'], array( 'text', 'textarea' ), true ) ) {
			$len = function_exists( 'mb_strlen' ) ? mb_strlen( (string) $value, 'UTF-8' ) : strlen( (string) $value );
			if ( ! empty( $field['max_length'] ) && $len > absint( $field['max_length'] ) ) {
				return new WP_Error( 'apf_max_len', sprintf( __( '"%s" cannot exceed %d characters.', 'apf-aslam' ), esc_html( $label ), $field['max_length'] ) );
			}
		}

		return true;
	}
}
