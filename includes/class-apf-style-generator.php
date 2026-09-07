<?php
/**
 * Dynamic Style Generator & Barab Theme Styler.
 *
 * Generates CSS custom properties and handles style presets & user custom styles.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class APF_Style_Generator {

	/**
	 * Constructor.
	 */
	public function __construct() {}

	/**
	 * Return built-in theme presets.
	 *
	 * @return array
	 */
	public static function get_presets() {
		return array(
			'barab'        => array(
				'name'            => __( 'Barab Fast Food (Default)', 'apf-aslam' ),
				'primary_color'   => '#EB1400',
				'secondary_color' => '#3F9065',
				'accent_color'    => '#FF9924',
				'bg_color'        => '#F7F2E2',
				'border_color'    => '#E4E4E4',
				'text_color'      => '#121212',
				'body_text_color' => '#6C6C6C',
				'badge_bg_color'  => '#FDE1B9',
				'border_radius'   => '50',
				'font_family'     => 'barab',
			),
			'dark_grill'   => array(
				'name'            => __( 'Smoky Dark Grill / BBQ', 'apf-aslam' ),
				'primary_color'   => '#FF5722',
				'secondary_color' => '#FF9800',
				'accent_color'    => '#FFC107',
				'bg_color'        => '#1E2125',
				'border_color'    => '#343A40',
				'text_color'      => '#F8F9FA',
				'body_text_color' => '#ADB5BD',
				'badge_bg_color'  => '#2C3036',
				'border_radius'   => '16',
				'font_family'     => 'barab',
			),
			'fresh_bistro' => array(
				'name'            => __( 'Fresh Bistro & Cafe', 'apf-aslam' ),
				'primary_color'   => '#2E7D32',
				'secondary_color' => '#388E3C',
				'accent_color'    => '#81C784',
				'bg_color'        => '#F1F8E9',
				'border_color'    => '#DCEDC8',
				'text_color'      => '#1B5E20',
				'body_text_color' => '#4E6E50',
				'badge_bg_color'  => '#C8E6C9',
				'border_radius'   => '12',
				'font_family'     => 'inter',
			),
			'modern_clean' => array(
				'name'            => __( 'Modern Minimalist', 'apf-aslam' ),
				'primary_color'   => '#2563EB',
				'secondary_color' => '#059669',
				'accent_color'    => '#F59E0B',
				'bg_color'        => '#F8FAFC',
				'border_color'    => '#E2E8F0',
				'text_color'      => '#0F172A',
				'body_text_color' => '#64748B',
				'badge_bg_color'  => '#E0E7FF',
				'border_radius'   => '8',
				'font_family'     => 'inter',
			),
		);
	}

	/**
	 * Compile dynamic CSS based on saved settings.
	 *
	 * @return string CSS string.
	 */
	public function generate_css() {
		$defaults = array(
			'primary_color'   => '#EB1400',
			'secondary_color' => '#3F9065',
			'accent_color'    => '#FF9924',
			'bg_color'        => '#F7F2E2',
			'border_color'    => '#E4E4E4',
			'text_color'      => '#121212',
			'body_text_color' => '#6C6C6C',
			'badge_bg_color'  => '#FDE1B9',
			'border_radius'   => '50',
			'font_family'     => 'barab',
			'custom_css'      => '',
		);

		$settings = wp_parse_args( get_option( 'apf_aslam_style_settings', array() ), $defaults );

		// Font styling selection.
		$font_heading = '"Barlow Condensed", sans-serif';
		$font_body    = '"Inter", sans-serif';

		if ( 'inter' === $settings['font_family'] ) {
			$font_heading = '"Inter", sans-serif';
			$font_body    = '"Inter", sans-serif';
		} elseif ( 'inherit' === $settings['font_family'] ) {
			$font_heading = 'inherit';
			$font_body    = 'inherit';
		}

		$radius_val = is_numeric( $settings['border_radius'] ) ? $settings['border_radius'] . 'px' : esc_attr( $settings['border_radius'] );

		$css  = ":root {\n";
		$css .= "  --apf-primary: " . esc_attr( $settings['primary_color'] ) . ";\n";
		$css .= "  --apf-secondary: " . esc_attr( $settings['secondary_color'] ) . ";\n";
		$css .= "  --apf-accent: " . esc_attr( $settings['accent_color'] ) . ";\n";
		$css .= "  --apf-bg: " . esc_attr( $settings['bg_color'] ) . ";\n";
		$css .= "  --apf-border: " . esc_attr( $settings['border_color'] ) . ";\n";
		$css .= "  --apf-text: " . esc_attr( $settings['text_color'] ) . ";\n";
		$css .= "  --apf-body-text: " . esc_attr( $settings['body_text_color'] ) . ";\n";
		$css .= "  --apf-badge-bg: " . esc_attr( $settings['badge_bg_color'] ) . ";\n";
		$css .= "  --apf-radius: " . $radius_val . ";\n";
		$css .= "  --apf-font-heading: " . $font_heading . ";\n";
		$css .= "  --apf-font-body: " . $font_body . ";\n";
		$css .= "}\n";

		// Append user custom CSS.
		if ( ! empty( $settings['custom_css'] ) ) {
			$css .= "\n/* User Custom CSS */\n" . wp_strip_all_tags( $settings['custom_css'] ) . "\n";
		}

		return $css;
	}
}
