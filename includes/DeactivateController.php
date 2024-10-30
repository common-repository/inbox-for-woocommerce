<?php
/**
 * Deactivation controller for when extension is deactivated
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_DeactivateController' ) ) {
	class IBXFWL_Inbox_DeactivateController {
		public static function deactivate() {
			flush_rewrite_rules();
		}
	}

}
