<?php
/**
 * Manage ticket interaction with server
 * 
 * @package Inbox-For-WooCommerce-LTE 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_Sweito_TicketService' ) ) {
	class IBXFWL_Inbox_Sweito_TicketService {
		const SWEITO_API = 'https://api.sweito.com';

		/**
		 * Send New Ticket to Helpdesk
		 *
		 * @param string $ticketId
		 * @return void
		 */
		public static function sendNewTicketToHelpdesk( $ticketId) {
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/DatabaseController.php');

			$ticket = IBXFWL_Inbox_DatabaseController::getOnlyTicketDetailsByIdForAdmin($ticketId);

			$savedToken = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN);
			$savedSite = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);
			$savedReference = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_REFERENCE);

			// set post fields
			$post = [
				'ticket' => json_encode($ticket),
			];

			$response = self::sendRequest(self::SWEITO_API . '/api/v1/open/woocommerce/ticket/entry', $post, $savedToken, $savedSite, $savedReference);

		}

		/**
		 * Send Curl Request
		 *
		 * @param string $path
		 * @param string $params
		 * @param string $token
		 * @param string $email
		 * @return array
		 */
		public static function sendRequest( $path, $params, $token = '', $site = '', $reference) {
			// $ch = curl_init($path);
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

			// if ( $token && $site ) {
			// 	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			// 		'X-AUTH-ACCESS: ' . $token,
			// 		'X-AUTH-SITE: ' . $site,
			// 		'X-AUTH-REFERENCE: ' . $reference,
			// 		'Accept: application/json'
			// 	));
			// }

			// // execute!
			// $response = curl_exec($ch);

			// // close the connection, release resources used
			// curl_close($ch);

			$headers = array();
			if ($token && $site ) {
				$headers = array(
					'X-AUTH-ACCESS'=> $token,
					'X-AUTH-SITE' => $site,
					'X-AUTH-REFERENCE' => $reference,
					'Accept' => 'application/json'
				);
			}

			$args = array(
				'body'        => $params,
				'timeout'     => '5',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
				'cookies'     => array(),
			);

			$response = wp_remote_post( $path, $args );
			$body = wp_remote_retrieve_body( $response );

			$data = json_decode($body, true);

			return $data;
		}
	}

}
