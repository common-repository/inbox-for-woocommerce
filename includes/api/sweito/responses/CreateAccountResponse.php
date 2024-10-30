<?php
/**
 * Response from server after creating account
 * 
 * @package Inbox-For-WooCommerce-LTE 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_Sweito_CreateAccountResponse' ) ) {
	class IBXFWL_Inbox_Sweito_CreateAccountResponse {
		
		/** Variable: string */
		public $token;

		/**
		 * Set Site
		 *
		 * @param string $token
		 * @return IBXFWL_Inbox_Sweito_CreateAccountParams
		 */
		public function setToken( $token) {
			$this->token = $token;
			return $this;
		}
	}

}
