<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Cleans up custom post types, post meta, and plugin options.
 * Core WooCommerce products, orders, and customer data are strictly preserved.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// 1. Delete all Field Group posts (custom post type 'apf_field_group').
$field_groups = get_posts(
	array(
		'post_type'   => 'apf_field_group',
		'post_status' => 'any',
		'numberposts' => -1,
		'fields'      => 'ids',
	)
);

if ( ! empty( $field_groups ) ) {
	foreach ( $field_groups as $group_id ) {
		wp_delete_post( $group_id, true ); // Bypass trash and delete post + postmeta.
	}
}

// 2. Delete plugin-specific postmeta from products (preserving standard WooCommerce product data).
$wpdb->query(
	"DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ('_apf_fields', '_apf_rules', '_apf_product_fields')"
);

// 3. Delete plugin options and transients.
delete_option( 'apf_aslam_style_settings' );
$wpdb->query(
	"DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_apf_%' OR option_name LIKE '_transient_timeout_apf_%'"
);
