<?php
/**
 * Setup controller for checking extension setup 
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_SetupController' ) ) {
	class IBXFWL_Inbox_SetupController {

		/**
		 * Check Extension Installation
		 *
		 * @return bool
		 */
		public static function checkInstallationStatus() {
			require_once IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php';

			$stage = 1;

			$savedToken = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN);
			$savedSite = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);
			$savedEmail = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);
			$savedLocation = get_option(IBXFWL_Inbox_SettingController::SETTING_GENERAL_TICKET_LOCATION);

			if ($savedToken && $savedSite && $savedEmail) {
				$stage = 3;

				if ( $savedLocation ) {
					$stage = 4;

					$savedReference = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_REFERENCE);
					$savedHelpdeskStatus = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_HELPDESK_STATUS);

					if ( 'zendesk' == $savedLocation ) {
						$stage = 32;
						if ($savedReference && 'zendesk-active' == $savedHelpdeskStatus) {
							$stage = 4;
						}
					} elseif ( 'freshdesk' == $savedLocation ) {
						$stage = 33;
						if ($savedReference && 'freshdesk-active' == $savedHelpdeskStatus) {
							$stage = 4;
						}
					}
				}
			} 

			if ( 4 === $stage ) {
return true;
			}

			return false;
		}
		
	}

}
