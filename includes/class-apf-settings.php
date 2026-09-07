<?php
/**
 * Admin Settings & Style Customizer Panel.
 *
 * Provides administrative interface to customize colors, border-radii, fonts, layouts, and custom CSS.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class APF_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_settings_pages' ), 60 );
		add_action( 'admin_init', array( $this, 'handle_save_settings' ) );
	}

	/**
	 * Register Submenu under WooCommerce.
	 */
	public function register_settings_pages() {
		add_submenu_page(
			'woocommerce',
			__( 'Product Fields Style Settings', 'apf-aslam' ),
			__( 'Field Styles (Barab)', 'apf-aslam' ),
			'manage_woocommerce',
			'apf-style-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Handle saving of style settings.
	 */
	public function handle_save_settings() {
		if ( ! isset( $_POST['apf_save_settings_nonce'] ) || ! wp_verify_nonce( $_POST['apf_save_settings_nonce'], 'apf_save_settings_action' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Handle preset loading if triggered.
		if ( ! empty( $_POST['load_preset'] ) ) {
			$preset_key = sanitize_text_field( $_POST['load_preset'] );
			$presets    = APF_Style_Generator::get_presets();
			if ( isset( $presets[ $preset_key ] ) ) {
				$preset_data = $presets[ $preset_key ];
				$preset_data['preset']             = $preset_key;
				$preset_data['swatch_layout']      = 'cards';
				$preset_data['summary_layout']     = 'receipt';
				$preset_data['show_order_summary'] = 'yes';
				$preset_data['custom_css']         = '';
				update_option( 'apf_aslam_style_settings', $preset_data );
				wp_safe_redirect( add_query_arg( array( 'page' => 'apf-style-settings', 'preset_loaded' => '1' ), admin_url( 'admin.php' ) ) );
				exit;
			}
		}

		$settings = array(
			'preset'             => sanitize_text_field( $_POST['preset'] ?? 'custom' ),
			'primary_color'      => sanitize_hex_color( $_POST['primary_color'] ?? '#EB1400' ),
			'secondary_color'    => sanitize_hex_color( $_POST['secondary_color'] ?? '#3F9065' ),
			'accent_color'       => sanitize_hex_color( $_POST['accent_color'] ?? '#FF9924' ),
			'bg_color'           => sanitize_hex_color( $_POST['bg_color'] ?? '#F7F2E2' ),
			'border_color'       => sanitize_hex_color( $_POST['border_color'] ?? '#E4E4E4' ),
			'text_color'         => sanitize_hex_color( $_POST['text_color'] ?? '#121212' ),
			'body_text_color'    => sanitize_hex_color( $_POST['body_text_color'] ?? '#6C6C6C' ),
			'badge_bg_color'     => sanitize_hex_color( $_POST['badge_bg_color'] ?? '#FDE1B9' ),
			'border_radius'      => sanitize_text_field( $_POST['border_radius'] ?? '50' ),
			'font_family'        => sanitize_text_field( $_POST['font_family'] ?? 'barab' ),
			'swatch_layout'      => sanitize_text_field( $_POST['swatch_layout'] ?? 'cards' ),
			'summary_layout'     => sanitize_text_field( $_POST['summary_layout'] ?? 'receipt' ),
			'show_order_summary' => ! empty( $_POST['show_order_summary'] ) ? 'yes' : 'no',
			'custom_css'         => wp_strip_all_tags( $_POST['custom_css'] ?? '' ),
		);

		update_option( 'apf_aslam_style_settings', $settings );

		wp_safe_redirect( add_query_arg( array( 'page' => 'apf-style-settings', 'saved' => '1' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Render settings page template.
	 */
	public function render_settings_page() {
		$settings = get_option( 'apf_aslam_style_settings', array() );
		$presets  = APF_Style_Generator::get_presets();
		require APF_ASLAM_PATH . 'admin/views/settings-page.php';
	}
}
