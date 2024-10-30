<?php
/**
 * Manage account registration with server
 * 
 * @package Inbox-For-WooCommerce-LTE 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_Sweito_AccountService' ) ) {
	class IBXFWL_Inbox_Sweito_AccountService {
		const SWEITO_API = 'https://api.sweito.com';

		/**
		 * Register Account
		 *
		 * @param class $params
		 * @return IBXFWL_Inbox_Sweito_CreateAccountResponse
		 */
		public static function registerAccount( $params) {
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/responses/CreateAccountResponse.php');

			// set post fields
			$post = [
				'first_name' => $params->firstName,
				'last_name' => $params->lastName,
				'email' => $params->emailAddress,
				'company_name' => $params->companyName,
				'site' => $params->siteAddress,
			];

			$response = self::sendRequest(self::SWEITO_API . '/api/v1/open/woocommerce/register/account', $post);

			if (null === $response) {
				wp_send_json_error(esc_html__('Could not connect to the server. Please check internet connection', 'inbox-for-woocommerce'));
				wp_die();
				return;
			}

			if ( isset($response['success']) && !$response['success'] ) {
				if ('Validation errors' == $response['message']) {
					foreach ($response['data'] as $key => $error) {
						wp_send_json_error($error);
						wp_die();
						return;
					}
				}
			} elseif ( 'success' != $response['status'] ) {
				wp_send_json_error($response);
				wp_die();
				return;
			};

			return ( new IBXFWL_Inbox_Sweito_CreateAccountResponse() )->setToken($response['data']);
		}

		/**
		 * Continue with existing account
		 *
		 * @param class $params
		 * @return bool
		 */
		public static function continueAccount( $params) {
			// set post fields
			$post = [
				'email' => $params->emailAddress
			];

			$response = self::sendRequest(self::SWEITO_API . '/api/v1/open/woocommerce/continue/account', $post);

			if (null === $response) {
				wp_send_json_error(esc_html__('Could not connect to the server. Please check internet connection', 'inbox-for-woocommerce'));
				wp_die();
				return;
			}

			if ( 'success' != $response['status'] ) {
return false;
			}

			return false;
		}

		/**
		 * Continue with existing account
		 *
		 * @param class $params
		 * @return IBXFWL_Inbox_Sweito_CreateAccountResponse
		 */
		public static function verifyAccountOTP( $params, $site) {
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/responses/CreateAccountResponse.php');
			
			// set post fields
			$post = [
				'email' => $params->emailAddress,
				'otp' => $params->otp,
				'site' => $site,
			];

			$response = self::sendRequest(self::SWEITO_API . '/api/v1/open/woocommerce/verify/user/account', $post);

			if (null === $response) {
				wp_send_json_error(esc_html__('Could not connect to the server. Please check internet connection', 'inbox-for-woocommerce'));
				wp_die();
				return;
			}

			if ( isset($response['success']) && !$response['success'] ) {
				if ('Validation errors' == $response['message']) {
					foreach ($response['data'] as $key => $error) {
						wp_send_json_error($error);
						wp_die();
						return;
					}
				}
			} elseif ( 'success' != $response['status'] ) {
				wp_send_json_error($response);
				wp_die();
				return;
			};

			return ( new IBXFWL_Inbox_Sweito_CreateAccountResponse() )->setToken($response['data']);
		}

		/**
		 * Verify Zendesk Token
		 *
		 * @param class $params
		 * @return bool
		 */
		public static function verifyPurchaseKey( $params, $token, $email, $site) {
			// set post fields
			$post = [
				'activation_key' => $params->purchaseKey,
				'site' => $site,
				// 'site' => 'https://smithsmotto.com',
			];

			$response = self::sendRequest(self::SWEITO_API . '/api/v1/open/woocommerce/verify/purchase/key', $post, $token, $email);

			self::checkResponse($response);

			if ( 'success' != $response['status'] ) {
return false;
			}

			return $response['data'];
		}

		/**
		 * Verify Zendesk Token
		 *
		 * @param class $params
		 * @return bool
		 */
		public static function getZendeskAuthNonceToken( $token, $email, $site, $accessToken) {
			// set post fields
			$post = [
				'site' => $site,
			];

			$response = self::sendRequest(self::SWEITO_API . '/api/v1/open/woocommerce/auth/zendesk/nonce', $post, $token, $email);

			self::checkResponse($response);

			return $response['data'] . '&apptoken=' . $token . '&accessKey=' . $accessToken;
		}

		/**
		 * Verify Freshdesk Token
		 *
		 * @param class $params
		 * @return bool
		 */
		public static function getFreshdeskAuthNonceToken( $token, $email, $site, $accessToken) {
			// set post fields
			$post = [
				'site' => $site,
			];

			$response = self::sendRequest(self::SWEITO_API . '/api/v1/open/woocommerce/auth/freshdesk/nonce', $post, $token, $email);

			self::checkResponse($response);

			return $response['data'] . '&apptoken=' . $token . '&accessKey=' . $accessToken;
		}

		/**
		 * Update user saved site 
		 *
		 * @param string $site
		 * @param string $token
		 * @param string $email
		 * @return string
		 */
		public static function updateAccountSite( $site, $token, $email) {
			$post = [
				'site' => $site,
			];

			$response = self::sendRequest(self::SWEITO_API . '/api/v1/open/woocommerce/update/account/site', $post, $token, $email);

			self::checkResponse($response);

			return $response['data'];
		}

		/**
		 * Update user saved site 
		 *
		 * @param string $site
		 * @param string $token
		 * @param string $email
		 * @return string
		 */
		public static function checkExtensionVersion() {
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php');

			$lastCheck = get_option(IBXFWL_Inbox_SettingController::SETTING_UPDATE_LAST_CHECKED);
			$lastState = get_option(IBXFWL_Inbox_SettingController::SETTING_UPDATE_STATE);

			$shouldCheck = true;
			if ( $lastCheck ) {
				$now = gmdate('Y-m-d H:i:s');
				$diff = strtotime($now) - strtotime($lastCheck);

				if (86400 > $diff) {
					return $lastState;
				}
			}

			$post = [
				'version' => '1.0.4'
			];

			
			$response = self::sendRequest(self::SWEITO_API . '/api/v1/open/woocommerce/check/extension/version', $post);

			$state = 'ok';
			if ($response && isset($response['data'])) {
				$updateResponse = $response['data'];
				$isOutdated = $updateResponse['is_outdated'];
				$isExpired = $updateResponse['is_expired'];

				
				if ($isExpired) {
					$state = 'expired';
				} else if ($isOutdated) {
					$state = 'outdated';
				}

				if ($lastState) {
					update_option(IBXFWL_Inbox_SettingController::SETTING_UPDATE_STATE, $state);
					update_option(IBXFWL_Inbox_SettingController::SETTING_UPDATE_LAST_CHECKED, gmdate('Y-m-d H:i:s'));
				} else {
					add_option(IBXFWL_Inbox_SettingController::SETTING_UPDATE_STATE, $state);
					update_option(IBXFWL_Inbox_SettingController::SETTING_UPDATE_LAST_CHECKED, gmdate('Y-m-d H:i:s'));
				}
			}

			return $state;
		}

		/**
		 * Check Sweito Server Response
		 *
		 * @param array $response
		 * @return void
		 */
		public static function checkResponse( $response) {
			if ( isset($response['success']) && !$response['success'] ) {
				if ('Validation errors' == $response['message']) {
					foreach ($response['data'] as $key => $error) {
						wp_send_json_error($error);
						wp_die();
						return;
					}
				}
			} elseif ( 'success' != $response['status'] ) {
				wp_send_json_error($response['data']);
				wp_die();
				return;
			};
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
		public static function sendRequest( $path, $params, $token = '', $email = '') {
			// $ch = curl_init($path);
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

			// if ( $token && $email ) {
			// 	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			// 		'X-AUTH-ACCESS: ' . $token,
			// 		'Accept: application/json'
			// 	));
			// }

			// try {
			// 	// execute!
			// 	$response = curl_exec($ch);
			// } catch (\Exception $ex) {
			// 	return 'ok';
			// }

			// // close the connection, release resources used
			// curl_close($ch);

			$headers = array();
			if ( $token && $email ) {
				$headers = array(
					'X-AUTH-ACCESS' => $token,
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
