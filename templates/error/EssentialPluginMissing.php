<?php
/**
 * Display WooCommerce Missing
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_Error_EssentialPluginMissing' ) ) {
	class IBXFWL_Inbox_Error_EssentialPluginMissing {
		public static function showMissingMessage() {
			echo '<h1>' . esc_html__('Inbox for WooCommerce', 'inbox-for-woocommerce') . '</h1>';
			echo '<div class="notice notice-error settings-error">
                    <h4>' . esc_html__('WooCommerce Plugin Not Installed', 'inbox-for-woocommerce') . '</h4>
                    <p>' . esc_html__('Could not find WooCommerce installed on your WordPress, please install WooCommerce to proceed!', 'inbox-for-woocommerce') . '</p>
                </div>';
		}
	}

}
