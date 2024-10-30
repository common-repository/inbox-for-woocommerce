<?php
/**
 * Verifying signin interface
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_Sweito_VerifySigninAccountParams' ) ) {

	class IBXFWL_Inbox_Sweito_VerifySigninAccountParams {

		/** Var string */
		public $emailAddress;

		/** Var string */
		public $otp;

		/**
		 * Set Email Address
		 *
		 * @param string $emailAddress
		 * @return IBXFWL_Inbox_Sweito_VerifySigninAccountParams
		 */
		public function setEmailAddress( $emailAddress) {
			$this->emailAddress = $emailAddress;
			return $this;
		}

		/**
		 * Set OTP
		 *
		 * @param string $otp
		 * @return IBXFWL_Inbox_Sweito_VerifySigninAccountParams
		 */
		public function setOtp( $otp) {
			$this->otp = $otp;
			return $this;
		}
	}

}
