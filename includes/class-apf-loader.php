<?php
/**
 * Plugin Central Loader.
 *
 * Coordinates initialization of all plugin subsystems, hooks, and services.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class APF_Loader {

	/**
	 * Singleton instance.
	 *
	 * @var APF_Loader|null
	 */
	private static $instance = null;

	/**
	 * Subsystem instances.
	 */
	public $post_types;
	public $fields_manager;
	public $pricing;
	public $cart;
	public $order;
	public $file_uploader;
	public $settings;
	public $style_generator;

	/**
	 * Get singleton instance.
	 *
	 * @return APF_Loader
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include all required core files.
	 */
	private function includes() {
		require_once APF_ASLAM_PATH . 'includes/class-apf-post-types.php';
		require_once APF_ASLAM_PATH . 'includes/class-apf-fields-manager.php';
		require_once APF_ASLAM_PATH . 'includes/class-apf-pricing.php';
		require_once APF_ASLAM_PATH . 'includes/class-apf-cart.php';
		require_once APF_ASLAM_PATH . 'includes/class-apf-order.php';
		require_once APF_ASLAM_PATH . 'includes/class-apf-file-uploader.php';
		require_once APF_ASLAM_PATH . 'includes/class-apf-style-generator.php';
		require_once APF_ASLAM_PATH . 'includes/class-apf-settings.php';
	}

	/**
	 * Initialize hooks and instantiate components.
	 */
	private function init_hooks() {
		$this->post_types      = new APF_Post_Types();
		$this->fields_manager  = new APF_Fields_Manager();
		$this->pricing         = new APF_Pricing();
		$this->cart            = new APF_Cart();
		$this->order           = new APF_Order();
		$this->file_uploader   = new APF_File_Uploader();
		$this->style_generator = new APF_Style_Generator();
		$this->settings        = new APF_Settings();

		// Enqueue scripts and styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		// Load plugin textdomain for localization.
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load translation files.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'apf-aslam', false, dirname( APF_ASLAM_BASENAME ) . '/languages' );
	}

	/**
	 * Enqueue frontend scripts and styles on product pages or anywhere product options appear.
	 */
	public function enqueue_frontend_assets() {
		// Enqueue on single products, shop archives, taxonomy, cart, checkout, or AJAX requests.
		if ( ! is_product() && ! is_shop() && ! is_product_taxonomy() && ! is_cart() && ! is_checkout() && ! wp_doing_ajax() ) {
			return;
		}

		// Google Fonts for Barab theme aesthetic (Barlow Condensed & Inter).
		wp_enqueue_style(
			'apf-google-fonts',
			'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap',
			array(),
			null
		);

		// Font Awesome 6 for icons and checkmarks.
		wp_enqueue_style(
			'apf-font-awesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
			array(),
			'6.4.0'
		);

		// Base layout styles.
		wp_enqueue_style(
			'apf-frontend-base',
			APF_ASLAM_URL . 'public/css/apf-frontend-base.css',
			array(),
			APF_ASLAM_VERSION
		);

		// Barab Theme styles.
		wp_enqueue_style(
			'apf-frontend-barab',
			APF_ASLAM_URL . 'public/css/apf-frontend-barab.css',
			array( 'apf-frontend-base' ),
			APF_ASLAM_VERSION
		);

		// Dynamic customizer styles (Inline CSS).
		$dynamic_css = $this->style_generator->generate_css();
		if ( ! empty( $dynamic_css ) ) {
			wp_add_inline_style( 'apf-frontend-barab', $dynamic_css );
		}

		// Frontend reactive engine JS.
		wp_enqueue_script(
			'apf-frontend',
			APF_ASLAM_URL . 'public/js/apf-frontend.js',
			array( 'jquery' ),
			APF_ASLAM_VERSION,
			true
		);

		// Get current product currency and settings for client JS.
		global $product;
		$currency_symbol = get_woocommerce_currency_symbol();
		$currency_pos    = get_option( 'woocommerce_currency_pos', 'left' );
		$decimals        = wc_get_price_decimals();
		$decimal_sep     = wc_get_price_decimal_separator();
		$thousand_sep    = wc_get_price_thousand_separator();
		$product_price   = 0.0;

		if ( $product && is_a( $product, 'WC_Product' ) ) {
			if ( $product->is_type( 'variable' ) ) {
				$product_price = (float) $product->get_variation_price( 'min' );
			} else {
				$product_price = (float) $product->get_price();
			}
		}

		wp_localize_script(
			'apf-frontend',
			'apfParams',
			array(
				'ajax_url'        => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'apf_frontend_nonce' ),
				'currency_symbol' => $currency_symbol,
				'currency_pos'    => $currency_pos,
				'decimals'        => $decimals,
				'decimal_sep'     => $decimal_sep,
				'thousand_sep'    => $thousand_sep,
				'base_price'      => $product_price,
				'i18n'            => array(
					'uploading'    => esc_html__( 'Uploading...', 'apf-aslam' ),
					'upload_error' => esc_html__( 'Upload failed. Please try again.', 'apf-aslam' ),
					'required'     => esc_html__( 'This field is required.', 'apf-aslam' ),
					'select_file'  => esc_html__( 'Select or drop file here', 'apf-aslam' ),
					'remove_file'  => esc_html__( 'Remove', 'apf-aslam' ),
					'order_total'  => esc_html__( 'Order Total', 'apf-aslam' ),
					'base_label'   => esc_html__( 'Base Item', 'apf-aslam' ),
					'options_fee'  => esc_html__( 'Add-ons Total', 'apf-aslam' ),
				),
			)
		);
	}

	/**
	 * Enqueue admin scripts and styles for field builder.
	 *
	 * @param string $hook Screen hook name.
	 */
	public function enqueue_admin_assets( $hook ) {
		global $post_type;

		$is_apf_screen = ( 'apf_field_group' === $post_type ) ||
						( isset( $_GET['page'] ) && 'apf-style-settings' === $_GET['page'] ) ||
						( 'product' === $post_type );

		if ( ! $is_apf_screen ) {
			return;
		}

		// WordPress color picker and media uploader.
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_media();

		// Font Awesome in admin.
		wp_enqueue_style(
			'apf-font-awesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
			array(),
			'6.4.0'
		);

		// Admin CSS.
		wp_enqueue_style(
			'apf-admin-style',
			APF_ASLAM_URL . 'admin/css/apf-admin.css',
			array( 'wp-color-picker' ),
			APF_ASLAM_VERSION
		);

		// Admin JS with jQuery UI Sortable for drag-and-drop fields.
		wp_enqueue_script(
			'apf-admin-script',
			APF_ASLAM_URL . 'admin/js/apf-admin.js',
			array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker' ),
			APF_ASLAM_VERSION,
			true
		);

		wp_localize_script(
			'apf-admin-script',
			'apfAdminParams',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'apf_admin_nonce' ),
				'i18n'     => array(
					'delete_confirm' => esc_html__( 'Are you sure you want to delete this field?', 'apf-aslam' ),
					'choose_image'   => esc_html__( 'Choose Swatch Image', 'apf-aslam' ),
					'use_image'      => esc_html__( 'Use Image', 'apf-aslam' ),
					'new_field'      => esc_html__( 'New Field', 'apf-aslam' ),
				),
			)
		);
	}
}
