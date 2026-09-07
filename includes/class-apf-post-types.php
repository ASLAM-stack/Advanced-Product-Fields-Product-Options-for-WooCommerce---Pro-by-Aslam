<?php
/**
 * Custom Post Type and Metabox Registrar.
 *
 * Handles 'apf_field_group' CPT, display rules, and per-product custom field overrides.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class APF_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post_apf_field_group', array( $this, 'save_field_group_meta' ) );
		add_action( 'save_post_product', array( $this, 'save_product_meta' ) );

		// Custom columns for CPT.
		add_filter( 'manage_apf_field_group_posts_columns', array( $this, 'set_custom_columns' ) );
		add_action( 'manage_apf_field_group_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
	}

	/**
	 * Register Custom Post Type 'apf_field_group'.
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Product Field Groups', 'Post type general name', 'apf-aslam' ),
			'singular_name'         => _x( 'Product Field Group', 'Post type singular name', 'apf-aslam' ),
			'menu_name'             => _x( 'Product Fields', 'Admin Menu text', 'apf-aslam' ),
			'name_admin_bar'        => _x( 'Product Field Group', 'Add New on Toolbar', 'apf-aslam' ),
			'add_new'               => __( 'Add New Field Group', 'apf-aslam' ),
			'add_new_item'          => __( 'Add New Product Field Group', 'apf-aslam' ),
			'new_item'              => __( 'New Field Group', 'apf-aslam' ),
			'edit_item'             => __( 'Edit Field Group', 'apf-aslam' ),
			'view_item'             => __( 'View Field Group', 'apf-aslam' ),
			'all_items'             => __( 'Field Groups', 'apf-aslam' ),
			'search_items'          => __( 'Search Field Groups', 'apf-aslam' ),
			'not_found'             => __( 'No field groups found.', 'apf-aslam' ),
			'not_found_in_trash'    => __( 'No field groups found in Trash.', 'apf-aslam' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'woocommerce',
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 56,
			'supports'           => array( 'title' ),
			'show_in_rest'       => false,
		);

		register_post_type( 'apf_field_group', $args );
	}

	/**
	 * Register Metaboxes for Field Groups and Individual Products.
	 */
	public function register_meta_boxes() {
		// Field Group Metaboxes.
		add_meta_box(
			'apf_fields_builder_metabox',
			__( 'Product Fields Builder (Pro)', 'apf-aslam' ),
			array( $this, 'render_fields_builder_metabox' ),
			'apf_field_group',
			'normal',
			'high'
		);

		add_meta_box(
			'apf_display_rules_metabox',
			__( 'Display Rules & Conditions', 'apf-aslam' ),
			array( $this, 'render_display_rules_metabox' ),
			'apf_field_group',
			'normal',
			'default'
		);

		// Per-Product Metabox (Optional override for individual products).
		add_meta_box(
			'apf_product_fields_metabox',
			__( 'Custom Product Options (Advanced Product Fields)', 'apf-aslam' ),
			array( $this, 'render_product_override_metabox' ),
			'product',
			'normal',
			'default'
		);
	}

	/**
	 * Render Fields Builder Metabox.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_fields_builder_metabox( $post ) {
		wp_nonce_field( 'apf_save_fields_builder', 'apf_fields_builder_nonce' );
		$fields = get_post_meta( $post->ID, '_apf_fields', true );
		if ( ! is_array( $fields ) ) {
			$fields = array();
		}
		require APF_ASLAM_PATH . 'admin/views/metabox-fields-builder.php';
	}

	/**
	 * Render Display Rules Metabox.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_display_rules_metabox( $post ) {
		wp_nonce_field( 'apf_save_display_rules', 'apf_display_rules_nonce' );
		$rules = get_post_meta( $post->ID, '_apf_rules', true );
		if ( ! is_array( $rules ) ) {
			$rules = array(
				'apply_to'   => 'all', // all, specific_products, categories
				'products'   => array(),
				'categories' => array(),
				'user_roles' => array(),
			);
		}
		require APF_ASLAM_PATH . 'admin/views/metabox-display-rules.php';
	}

	/**
	 * Render Per-Product Override Metabox on Single Product Edit Screen.
	 *
	 * @param WP_Post $post Product post object.
	 */
	public function render_product_override_metabox( $post ) {
		wp_nonce_field( 'apf_save_product_fields', 'apf_product_fields_nonce' );
		$fields = get_post_meta( $post->ID, '_apf_product_fields', true );
		if ( ! is_array( $fields ) ) {
			$fields = array();
		}
		echo '<p class="description">' . esc_html__( 'Add custom options specific to this product only. These fields will be displayed in addition to any global field groups.', 'apf-aslam' ) . '</p>';
		$is_product_screen = true;
		require APF_ASLAM_PATH . 'admin/views/metabox-fields-builder.php';
	}

	/**
	 * Save Field Group Meta Data.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_field_group_meta( $post_id ) {
		// Nonce check.
		if ( ! isset( $_POST['apf_fields_builder_nonce'] ) || ! wp_verify_nonce( $_POST['apf_fields_builder_nonce'], 'apf_save_fields_builder' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save fields array.
		if ( isset( $_POST['apf_fields'] ) && is_array( $_POST['apf_fields'] ) ) {
			$sanitized_fields = $this->sanitize_fields_array( $_POST['apf_fields'] );
			update_post_meta( $post_id, '_apf_fields', $sanitized_fields );
		} else {
			delete_post_meta( $post_id, '_apf_fields' );
		}

		// Save display rules.
		if ( isset( $_POST['apf_rules'] ) && is_array( $_POST['apf_rules'] ) ) {
			$rules = array(
				'apply_to'   => sanitize_text_field( $_POST['apf_rules']['apply_to'] ?? 'all' ),
				'products'   => isset( $_POST['apf_rules']['products'] ) ? array_map( 'absint', (array) $_POST['apf_rules']['products'] ) : array(),
				'categories' => isset( $_POST['apf_rules']['categories'] ) ? array_map( 'absint', (array) $_POST['apf_rules']['categories'] ) : array(),
				'user_roles' => isset( $_POST['apf_rules']['user_roles'] ) ? array_map( 'sanitize_text_field', (array) $_POST['apf_rules']['user_roles'] ) : array(),
			);
			update_post_meta( $post_id, '_apf_rules', $rules );
		}
	}

	/**
	 * Save Per-Product Override Fields.
	 *
	 * @param int $post_id Product ID.
	 */
	public function save_product_meta( $post_id ) {
		if ( ! isset( $_POST['apf_product_fields_nonce'] ) || ! wp_verify_nonce( $_POST['apf_product_fields_nonce'], 'apf_save_product_fields' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['apf_fields'] ) && is_array( $_POST['apf_fields'] ) ) {
			$sanitized_fields = $this->sanitize_fields_array( $_POST['apf_fields'] );
			update_post_meta( $post_id, '_apf_product_fields', $sanitized_fields );
		} else {
			delete_post_meta( $post_id, '_apf_product_fields' );
		}
	}

	/**
	 * Sanitize fields array recursively.
	 *
	 * @param array $fields Raw fields input.
	 * @return array Sanitized fields.
	 */
	public function sanitize_fields_array( $fields ) {
		$sanitized = array();

		foreach ( $fields as $key => $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$id = sanitize_key( $field['id'] ?? 'field_' . uniqid() );

			$sanitized[ $id ] = array(
				'id'                   => $id,
				'type'                 => sanitize_text_field( $field['type'] ?? 'text' ),
				'label'                => sanitize_text_field( $field['label'] ?? '' ),
				'description'          => wp_kses_post( $field['description'] ?? '' ),
				'placeholder'          => sanitize_text_field( $field['placeholder'] ?? '' ),
				'default_value'        => sanitize_text_field( $field['default_value'] ?? '' ),
				'required'             => ! empty( $field['required'] ) ? 'yes' : 'no',
				'pricing_type'         => sanitize_text_field( $field['pricing_type'] ?? 'none' ), // none, flat, quantity, percentage, char_count, formula
				'pricing_amount'       => floatval( $field['pricing_amount'] ?? 0 ),
				'pricing_formula'      => sanitize_text_field( $field['pricing_formula'] ?? '' ),
				'free_characters'      => absint( $field['free_characters'] ?? 0 ),
				'min_value'            => isset( $field['min_value'] ) && '' !== $field['min_value'] ? floatval( $field['min_value'] ) : '',
				'max_value'            => isset( $field['max_value'] ) && '' !== $field['max_value'] ? floatval( $field['max_value'] ) : '',
				'step'                 => floatval( $field['step'] ?? 1 ),
				'max_length'           => absint( $field['max_length'] ?? 0 ),
				'allowed_extensions'   => sanitize_text_field( $field['allowed_extensions'] ?? 'jpg,jpeg,png,webp,pdf' ),
				'max_file_size_mb'     => floatval( $field['max_file_size_mb'] ?? 5 ),
				'options'              => array(),
				'conditional_logic'    => array(
					'enabled' => ! empty( $field['conditional_logic']['enabled'] ) ? 'yes' : 'no',
					'action'  => sanitize_text_field( $field['conditional_logic']['action'] ?? 'show' ), // show, hide
					'match'   => sanitize_text_field( $field['conditional_logic']['match'] ?? 'all' ), // all (AND), any (OR)
					'rules'   => array(),
				),
			);

			// Sanitize options list (for select, radio, checkbox, color_swatch, image_swatch).
			if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {
				foreach ( $field['options'] as $opt_key => $opt ) {
					$sanitized[ $id ]['options'][] = array(
						'label'          => sanitize_text_field( $opt['label'] ?? '' ),
						'value'          => sanitize_text_field( $opt['value'] ?? '' ),
						'pricing_type'   => sanitize_text_field( $opt['pricing_type'] ?? 'flat' ),
						'pricing_amount' => floatval( $opt['pricing_amount'] ?? 0 ),
						'color'          => sanitize_hex_color( $opt['color'] ?? '#000000' ),
						'image_url'      => esc_url_raw( $opt['image_url'] ?? '' ),
						'image_id'       => absint( $opt['image_id'] ?? 0 ),
					);
				}
			}

			// Sanitize conditional rules.
			if ( ! empty( $field['conditional_logic']['rules'] ) && is_array( $field['conditional_logic']['rules'] ) ) {
				foreach ( $field['conditional_logic']['rules'] as $rule ) {
					$sanitized[ $id ]['conditional_logic']['rules'][] = array(
						'field'    => sanitize_key( $rule['field'] ?? '' ),
						'operator' => sanitize_text_field( $rule['operator'] ?? 'is' ), // is, is_not, is_empty, is_not_empty, contains, greater_than, less_than
						'value'    => sanitize_text_field( $rule['value'] ?? '' ),
					);
				}
			}
		}

		return $sanitized;
	}

	/**
	 * Set custom columns for CPT.
	 *
	 * @param array $columns Default columns.
	 * @return array Modified columns.
	 */
	public function set_custom_columns( $columns ) {
		$new_columns = array(
			'cb'         => $columns['cb'],
			'title'      => __( 'Field Group Title', 'apf-aslam' ),
			'fields_qty' => __( 'Fields Count', 'apf-aslam' ),
			'rules'      => __( 'Assigned To', 'apf-aslam' ),
			'date'       => $columns['date'],
		);
		return $new_columns;
	}

	/**
	 * Render custom column content.
	 *
	 * @param string $column Column name.
	 * @param int    $post_id Post ID.
	 */
	public function render_custom_columns( $column, $post_id ) {
		if ( 'fields_qty' === $column ) {
			$fields = get_post_meta( $post_id, '_apf_fields', true );
			$count  = is_array( $fields ) ? count( $fields ) : 0;
			echo '<strong>' . esc_html( $count ) . '</strong> ' . esc_html__( 'fields', 'apf-aslam' );
		} elseif ( 'rules' === $column ) {
			$rules = get_post_meta( $post_id, '_apf_rules', true );
			if ( ! empty( $rules['apply_to'] ) ) {
				if ( 'all' === $rules['apply_to'] ) {
					echo '<span class="badge badge-all">' . esc_html__( 'All Products', 'apf-aslam' ) . '</span>';
				} elseif ( 'categories' === $rules['apply_to'] ) {
					$cat_ids = $rules['categories'] ?? array();
					$names   = array();
					foreach ( $cat_ids as $cid ) {
						$term = get_term( $cid, 'product_cat' );
						if ( $term && ! is_wp_error( $term ) ) {
							$names[] = $term->name;
						}
					}
					echo esc_html__( 'Categories: ', 'apf-aslam' ) . esc_html( implode( ', ', $names ) );
				} elseif ( 'specific_products' === $rules['apply_to'] ) {
					$pids  = $rules['products'] ?? array();
					$names = array();
					foreach ( $pids as $pid ) {
						$names[] = get_the_title( $pid );
					}
					echo esc_html__( 'Products: ', 'apf-aslam' ) . esc_html( implode( ', ', $names ) );
				}
			} else {
				echo '&mdash;';
			}
		}
	}
}
