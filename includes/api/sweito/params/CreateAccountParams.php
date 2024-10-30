<?php
/**
 * Create account interface
 * 
 * @package Inbox-For-WooCommerce-LTE 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_Sweito_CreateAccountParams' ) ) {

	class IBXFWL_Inbox_Sweito_CreateAccountParams {
		
		/** Variable: string */
		public $firstName;

		/** Variable: string */
		public $lastName;

		/** Variable: string */
		public $companyName;

		/** Variable: string */
		public $emailAddress;

		/** Variable: string */
		public $siteAddress;

		/**
		 * Set Site
		 *
		 * @param string $emailAddress
		 * @return IBXFWL_Inbox_Sweito_CreateAccountParams
		 */
		public function setEmailAddress( $emailAddress) {
			$this->emailAddress = $emailAddress;
			return $this;
		}

		/**
		 * Set Site
		 *
		 * @param string $siteAddress
		 * @return IBXFWL_Inbox_Sweito_CreateAccountParams
		 */
		public function setSiteAddress( $siteAddress) {
			$this->siteAddress = $siteAddress;
			return $this;
		}

		/**
		 * Set Company Name
		 *
		 * @param string $companyName
		 * @return IBXFWL_Inbox_Sweito_CreateAccountParams
		 */
		public function setCompanyName( $companyName) {
			$this->companyName = $companyName;
			return $this;
		}

		/**
		 * Set Last Name
		 *
		 * @param string $lastName
		 * @return IBXFWL_Inbox_Sweito_CreateAccountParams
		 */
		public function setLastName( $lastName) {
			$this->lastName = $lastName;
			return $this;
		}

		/**
		 * Set First Name
		 *
		 * @param string $firstName
		 * @return IBXFWL_Inbox_Sweito_CreateAccountParams
		 */
		public function setFirstName( $firstName) {
			$this->firstName = $firstName;
			return $this;
		}
	}

}
