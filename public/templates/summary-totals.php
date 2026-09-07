<?php
/**
 * Frontend Template: Live Order Summary & Totals Breakdown Card.
 *
 * Designed in the authentic Barab restaurant receipt / guest check aesthetic.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="apf-order-summary-card barab-receipt-card" id="apf-order-summary">
	<div class="apf-receipt-header">
		<div class="apf-receipt-title">
			<i class="fa-solid fa-receipt"></i>
			<span><?php esc_html_e( 'Your Custom Order Summary', 'apf-aslam' ); ?></span>
		</div>
		<span class="apf-receipt-badge"><?php esc_html_e( 'Live Calculation', 'apf-aslam' ); ?></span>
	</div>

	<div class="apf-receipt-body">
		<!-- Base Product Line -->
		<div class="apf-receipt-row apf-row-base">
			<span class="apf-item-name"><?php esc_html_e( 'Base Item Price', 'apf-aslam' ); ?></span>
			<span class="apf-item-cost" id="apf-summary-base-price">&mdash;</span>
		</div>

		<!-- Dynamic Options Breakdown List (Populated via JS) -->
		<div class="apf-summary-options-list" id="apf-summary-options-list">
			<!-- Injected in real-time by apf-frontend.js -->
		</div>

		<div class="apf-receipt-dashed-line"></div>

		<!-- Addons Total -->
		<div class="apf-receipt-row apf-row-addons">
			<span class="apf-item-name"><?php esc_html_e( 'Selected Add-ons', 'apf-aslam' ); ?></span>
			<span class="apf-item-cost" id="apf-summary-addons-total">&mdash;</span>
		</div>

		<!-- Grand Total -->
		<div class="apf-receipt-row apf-row-grand-total">
			<span class="apf-item-name">
				<strong><?php esc_html_e( 'Total Per Item', 'apf-aslam' ); ?></strong>
			</span>
			<span class="apf-item-cost apf-grand-total-highlight" id="apf-summary-grand-total">&mdash;</span>
		</div>
	</div>
</div>
