<?php
/**
 * Plugin Name: Advanced Product Fields (Product Options) for WooCommerce - Pro by Aslam
 * Plugin URI: https://github.com/aslam/advanced-product-fields-woocommerce
 * Description: The complete, professional product options and custom add-ons builder for WooCommerce with unlocked Pro capabilities: formula-based pricing, color & image swatches, file uploads, quantity steppers, advanced conditional logic, native Barab fast-food restaurant styling, and an interactive style customizer.
 * Version: 1.0.0
 * Author: Aslam
 * Author URI: https://aslamplugins.com
 * Text Domain: apf-aslam
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 9.3
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define Plugin Constants.
define( 'APF_ASLAM_VERSION', '1.0.0' );
define( 'APF_ASLAM_FILE', __FILE__ );
define( 'APF_ASLAM_PATH', plugin_dir_path( __FILE__ ) );
define( 'APF_ASLAM_URL', plugin_dir_url( __FILE__ ) );
define( 'APF_ASLAM_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Declare WooCommerce HPOS (High-Performance Order Storage) compatibility.
 */
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
	}
} );

/**
 * Check if WooCommerce is active before loading the plugin.
 */
function apf_aslam_check_dependencies() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'apf_aslam_woocommerce_missing_notice' );
		return false;
	}
	return true;
}

/**
 * Display admin notice if WooCommerce is missing.
 */
function apf_aslam_woocommerce_missing_notice() {
	echo '<div class="notice notice-error is-dismissible">';
	echo '<p>' . esc_html__( 'Advanced Product Fields by Aslam requires WooCommerce to be installed and active.', 'apf-aslam' ) . '</p>';
	echo '</div>';
}

/**
 * Load core plugin loader class.
 */
require_once APF_ASLAM_PATH . 'includes/class-apf-loader.php';

/**
 * Initialize the plugin after plugins are loaded.
 */
function apf_aslam_init() {
	if ( apf_aslam_check_dependencies() ) {
		APF_Loader::get_instance();
	}
}
add_action( 'plugins_loaded', 'apf_aslam_init', 20 );

/**
 * Add Settings and Field Groups action links to plugins list page.
 */
add_filter( 'plugin_action_links_' . APF_ASLAM_BASENAME, function ( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=apf-style-settings' ) ) . '">' . esc_html__( 'Field Styles', 'apf-aslam' ) . '</a>';
	$fields_link   = '<a href="' . esc_url( admin_url( 'edit.php?post_type=apf_field_group' ) ) . '">' . esc_html__( 'Field Groups', 'apf-aslam' ) . '</a>';
	array_unshift( $links, $settings_link );
	array_unshift( $links, $fields_link );
	return $links;
} );

/**
 * Plugin activation hook.
 */
register_activation_hook( __FILE__, function () {
	// Create secure uploads folder for file upload fields.
	$upload_dir = wp_upload_dir();
	if ( ! empty( $upload_dir['basedir'] ) ) {
		$apf_dir = $upload_dir['basedir'] . '/apf-uploads';
		if ( ! file_exists( $apf_dir ) ) {
			wp_mkdir_p( $apf_dir );
			@file_put_contents( $apf_dir . '/.htaccess', "deny from all\n<FilesMatch \"\.(?i:jpg|jpeg|png|gif|webp|pdf|svg|txt|doc|docx|zip)$\">\nallow from all\n</FilesMatch>" );
			@file_put_contents( $apf_dir . '/index.php', '<?php /* Silence is golden */' );
		}
	}

	// Set default Barab styling options if not already set.
	if ( ! get_option( 'apf_aslam_style_settings' ) ) {
		$default_styles = array(
			'preset'             => 'barab',
			'primary_color'      => '#EB1400',
			'secondary_color'    => '#3F9065',
			'accent_color'       => '#FF9924',
			'bg_color'           => '#F7F2E2',
			'border_color'       => '#E4E4E4',
			'text_color'         => '#121212',
			'body_text_color'    => '#6C6C6C',
			'badge_bg_color'     => '#FDE1B9',
			'border_radius'      => '50',
			'font_family'        => 'barab', // Barlow Condensed + Inter
			'swatch_layout'      => 'cards', // cards, pills, grid
			'summary_layout'     => 'receipt', // receipt, compact, sticky
			'show_order_summary' => 'yes',
			'custom_css'         => '',
		);
		update_option( 'apf_aslam_style_settings', $default_styles );
	}
} );
