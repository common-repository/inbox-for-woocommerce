<?php
/**
 * Manage AJAX calls for extension
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_AjaxController' ) ) {
	class IBXFWL_Inbox_AjaxController {
		public static function importDirectories() {
			require_once('TicketController.php');
			require_once('SettingController.php');
		}

		public static function uploadDocumentByAjax() {
			self::importDirectories();

			// Check for nonce security      
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-upload-nonce' ) ) {
				wp_send_json_error('Could not verify nonce');
				wp_die();
				return;
			}

			$uploadContent = isset($_POST['upload']) ? sanitize_text_field($_POST['upload']) : '';

			list($type, $data) = explode(';', $uploadContent);
			list(, $data)  = explode(',', $data);
			$prtType = explode(':', $type);

			$formats = [
				'image/jpg' => 'jpg',
				'image/jpeg' => 'jpeg',
				'image/png' => 'png',
				'application/pdf' => 'pdf',
			];

			if (array_key_exists($prtType[1], $formats)) {
				$extension = $formats[$prtType[1]];
			} else {
				wp_send_json_error('Wrong file format uploaded');
				wp_die();
				return;
			}

			$newFileName = time() . rand(1100, 3000) . '.' . $extension;
			$newUpload = wp_upload_bits($newFileName , null, base64_decode($data));

			wp_send_json_success(['name' => $newFileName, 'url' => $newUpload['url']]);
			wp_die();
			return;
		}

		public static function updateAccountPersonalization() {
			self::importDirectories();

			// Check for nonce security      
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-setup-nonce' ) ) {
				wp_send_json_error('Could not verify nonce');
				wp_die();
				return;
			}

			$allowCustomerInbox = isset($_POST['allow_customer_inbox']) ? sanitize_text_field($_POST['allow_customer_inbox']) : '';
			$allowCTASection = isset($_POST['allow_cta_section']) ? sanitize_text_field($_POST['allow_cta_section']) : '';

			$customeInboxStatus = get_option(IBXFWL_Inbox_SettingController::SETTING_CUSTOMER_INBOX_STATUS);
			$ctaSectionStatus = get_option(IBXFWL_Inbox_SettingController::SETTING_INQUIRY_CTA_STATUS);

			if ($customeInboxStatus) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_CUSTOMER_INBOX_STATUS , $allowCustomerInbox);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_CUSTOMER_INBOX_STATUS , $allowCustomerInbox);
			}

			if ($ctaSectionStatus) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_INQUIRY_CTA_STATUS , $allowCTASection);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_INQUIRY_CTA_STATUS , $allowCTASection);
			}

			wp_send_json_success(true);
			wp_die();
			return;
		}

		public static function updateAccountSite() {
			self::importDirectories();
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/AccountService.php');

			// Check for nonce security      
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-setup-nonce' ) ) {
				wp_send_json_error('Could not verify nonce');
				wp_die();
				return;
			}

			$newSite = isset($_POST['site']) ? sanitize_text_field($_POST['site']) : '';

			$savedToken = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN);
			$savedSite = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);
			$savedEmail = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_EMAIL);

			$authSite = ( new IBXFWL_Inbox_Sweito_AccountService() )->updateAccountSite($newSite, $savedToken, $savedEmail);

			if ($savedSite && $authSite) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE , $authSite);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE , $authSite);
			}

			wp_send_json_success($authLink);
			wp_die();
			return;
		}

		public static function verifyHelpdeskAuth() {
			self::importDirectories();
			$savedLocation = get_option(IBXFWL_Inbox_SettingController::SETTING_GENERAL_TICKET_LOCATION);

			$isSuccessful = false;
			if ( 'wpadmin' !== $savedLocation ) {
				$savedReference = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_REFERENCE);
				$savedHelpdeskStatus = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_HELPDESK_STATUS);
	
				if ( 'zendesk' == $savedLocation ) {
					if ($savedReference && 'zendesk-active' == $savedHelpdeskStatus) {
						$isSuccessful = true;
					}
				} elseif ( 'freshdesk' == $savedLocation) {
					$stage = 33;
					if ($savedReference && 'freshdesk-active' == $savedHelpdeskStatus) {
						$isSuccessful = true;
					}
				}
			} else {
				$isSuccessful = true;
			}

			if ( ! $isSuccessful ) {
				wp_send_json_error('Not completed');
				wp_die();
			}

			$nextStage = 'personalize';
			wp_send_json_success($nextStage);
			wp_die();
		}

		public static function authFreshdeskAccount() {
			self::importDirectories();
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/AccountService.php');

			$savedToken = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN);
			$savedSite = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);
			$savedEmail = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_EMAIL);
			$savedAccessToken = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_ACCESS_TOKEN);

			if ( ! $savedAccessToken ) {
				$savedAccessToken = self::generateRandomString(58);
				add_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_ACCESS_TOKEN , $savedAccessToken);
			}

			$authLink = ( new IBXFWL_Inbox_Sweito_AccountService() )->getFreshdeskAuthNonceToken($savedToken, $savedEmail, $savedSite, $savedAccessToken);

			wp_send_json_success($authLink);
			wp_die();
			return;
		}

		public static function authZendeskAccount() {
			self::importDirectories();
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/AccountService.php');

			$savedToken = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN);
			$savedSite = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);
			$savedEmail = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_EMAIL);
			$savedAccessToken = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_ACCESS_TOKEN);

			if ( ! $savedAccessToken ) {
				$savedAccessToken = self::generateRandomString(58);
				add_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_ACCESS_TOKEN , $savedAccessToken);
			}

			$authLink = ( new IBXFWL_Inbox_Sweito_AccountService() )->getZendeskAuthNonceToken($savedToken, $savedEmail, $savedSite, $savedAccessToken);

			wp_send_json_success($authLink);
			wp_die();
			return;
		}

		/**
		 * Setup Location to save Tickets
		 *
		 * @return void
		 */
		public static function setupSaveLocation() {
			self::importDirectories();

			// Check for nonce security      
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-setup-nonce' ) ) {
				wp_send_json_error('Could not verify nonce');
				wp_die();
				return;
			}

			$location = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '';

			$savedLocation = get_option(IBXFWL_Inbox_SettingController::SETTING_GENERAL_TICKET_LOCATION);

			if ($savedLocation || ( '' == $savedLocation )) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_GENERAL_TICKET_LOCATION , $location);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_GENERAL_TICKET_LOCATION , $location);
			}

			$nextStage = 'personalize';
			if ('zendesk' == $location) {
				$nextStage = 'zendesk-auth';
			} else if ('freshdesk' == $location) {
				$nextStage = 'freshdesk-auth';
			}

			wp_send_json_success($nextStage);
			wp_die();
		}

		public static function signinSweitoAccount() {
			self::importDirectories();

			// Check for nonce security      
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-setup-nonce' ) ) {
				wp_send_json_error('Could not verify nonce');
				wp_die();
				return;
			}

			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/params/ContinueAccountParams.php');
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/AccountService.php');

			$savedToken = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN);
			$savedSite = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);
			$savedEmail = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);

			$site = isset($_POST['site']) ? sanitize_text_field($_POST['site']) : '';
			if ($savedToken && ( $savedSite == $site ) && $savedEmail) {
				wp_send_json_success('select-location');
				wp_die();
			}

			$email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : '';

			$params = ( new IBXFWL_Inbox_Sweito_ContinueAccountParams() )
							->setEmailAddress($email);

			$response = ( new IBXFWL_Inbox_Sweito_AccountService() )->continueAccount($params);

			wp_send_json_success('account-otp');
			wp_die();
		}

		public static function verifySigninSweitoAccount() {
			self::importDirectories();

			// Check for nonce security      
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-setup-nonce' ) ) {
				wp_send_json_error('Could not verify nonce');
				wp_die();
				return;
			}

			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/params/VerifySigninAccountParams.php');
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/AccountService.php');

			$savedToken = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN);
			$savedSite = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);
			$savedEmail = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);

			$site = isset($_POST['site']) ? sanitize_text_field($_POST['site']) : '';
			if ($savedToken && ( $savedSite == $site )) {
				wp_send_json_success('select-location');
				wp_die();
			}

			$emailAddress = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : '';
			$otp = isset($_POST['otp']) ? sanitize_text_field($_POST['otp']) : '';
			$site = get_site_url();

			$params = ( new IBXFWL_Inbox_Sweito_VerifySigninAccountParams() )
							->setEmailAddress($emailAddress)
							->setOtp($otp);

			$response = ( new IBXFWL_Inbox_Sweito_AccountService() )->verifyAccountOTP($params, $site);

			if ($savedToken) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN , $response->token);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN , $response->token);
			}
			
			if ($savedSite) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE , $site);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE , $site);
			}
			
			if ($savedEmail) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_EMAIL , $emailAddress);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_EMAIL , $emailAddress);
			}

			wp_send_json_success('select-location');
			wp_die();
		}

		/**
		 * Signup for Sweito Account
		 *
		 * @return void
		 */
		public static function signupSweitoAccount() {
			self::importDirectories();

			// Check for nonce security      
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-setup-nonce' ) ) {
				wp_send_json_error('Could not verify nonce');
				wp_die();
				return;
			}

			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/params/CreateAccountParams.php');
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/AccountService.php');

			$savedToken = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN);
			$savedSite = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);
			$savedEmail = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);

			$site = isset($_POST['site']) ? sanitize_text_field($_POST['site']) : '';
			if ($savedToken && ( $savedSite == $site )) {
				wp_send_json_success('select-location');
				wp_die();
			}

			$firstName = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
			$lastName = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
			$companyName = isset($_POST['company_name']) ? sanitize_text_field($_POST['company_name']) : '';
			$emailAddress = isset($_POST['email_address']) ? sanitize_text_field($_POST['email_address']) : '';
			$site = isset($_POST['site']) ? sanitize_text_field($_POST['site']) : '';

			$params = ( new IBXFWL_Inbox_Sweito_CreateAccountParams() )
							->setFirstName($firstName)
							->setLastName($lastName)
							->setEmailAddress($emailAddress)
							->setCompanyName($companyName)
							->setSiteAddress($site);

			
			$response = ( new IBXFWL_Inbox_Sweito_AccountService() )->registerAccount($params);

			if ($savedToken) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN , $response->token);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN , $response->token);
			}
			
			if ($savedSite) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE , $site);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE , $site);
			}
			
			if ($savedEmail) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_EMAIL , $emailAddress);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_EMAIL , $emailAddress);
			}

			wp_send_json_success('select-location');
			wp_die();
		}

		/**
		 * Signup for Sweito Account
		 *
		 * @return void
		 */
		public static function continueSweitoAccount() {
			self::importDirectories();

			// Check for nonce security      
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-setup-nonce' ) ) {
				wp_send_json_error('Could not verify nonce');
				wp_die();
				return;
			}

			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/params/ContinueAccountParams.php');
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/AccountService.php');

			$emailAddress = isset($_POST['email_address']) ? sanitize_text_field($_POST['email_address']) : '';

			$params = ( new IBXFWL_Inbox_Sweito_ContinueAccountParams() )
							->setEmailAddress($emailAddress);

			$response = ( new IBXFWL_Inbox_Sweito_AccountService() )->continueAccount($params);

			wp_send_json_success($response);
			wp_die();
		}

		/**
		 * Admin replied user message thread
		 *
		 * @return void
		 */
		public static function adminUserReplyMessageThread() {
			self::importDirectories();

			// Check for nonce security      
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-thread-nonce' ) ) {
				wp_send_json_error('Could not verify nonce');
				wp_die();
				return;
			}

			$reference = ( isset($_POST['reference']) ) ? sanitize_text_field($_POST['reference']) : '';
			$description = ( isset($_POST['description']) ) ? sanitize_textarea_field($_POST['description']) : '';
			$cleanAttachments = [];

			if ( ! $description ) {
				$sentResponse = esc_html__('Please enter a reply to proceed!', 'inbox-for-woocommerce');
				wp_send_json_error($sentResponse);
				wp_die();
				return;
			}

			if (  ! is_user_logged_in() ) {
				$sentResponse = esc_html__('You need to be logged in to access this endpoint', 'inbox-for-woocommerce');
				wp_send_json_error($sentResponse);
				wp_die();
				return;
			}

			$currentUser = wp_get_current_user();
			IBXFWL_Inbox_TicketController::adminReplyTicketThread($currentUser->user_email, $currentUser->ID, $reference, $description, $cleanAttachments);

			$sentResponse = IBXFWL_Inbox_SettingController::defaultInquirySentResponse();
			wp_send_json_success($sentResponse);
			wp_die();
		}

		/**
		 * Reply by user to message thread
		 *
		 * @return void
		 */
		public static function userReplyMessageThread() {
			self::importDirectories();

			// Check for nonce security      
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-thread-nonce' ) ) {
				wp_send_json_error('Could not verify nonce');
				wp_die();
				return;
			}

			$reference = isset($_POST['reference']) ? sanitize_text_field($_POST['reference']) : '';
			$description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
			$attachmentLength = ( isset($_POST['attachment_length']) ) ? sanitize_text_field($_POST['attachment_length']) : '';
			$cleanAttachments = [];
			for ($i = 0; $i < intval($attachmentLength); $i++) {
				$attName = ( isset($_POST['attachments']) && isset($_POST['attachments'][$i]) && isset($_POST['attachments'][$i]['name']) ) ? sanitize_text_field($_POST['attachments'][$i]['name']) : '';
				$attUrl = ( isset($_POST['attachments']) && isset($_POST['attachments'][$i]) && isset($_POST['attachments'][$i]['url']) ) ? esc_url_raw($_POST['attachments'][$i]['url']) : '';

				if ($attName && $attUrl) {
					$cleanAttachments[] = [
						'name' => $attName,
						'url' => $attUrl
					];
				}
			}

			if ( ! $description ) {
				$sentResponse = esc_html__('Please enter a reply to proceed!', 'inbox-for-woocommerce');
				wp_send_json_error($sentResponse);
				wp_die();
				return;
			}

			if (  ! is_user_logged_in() ) {
				$sentResponse = esc_html__('You need to be logged in to access this endpoint', 'inbox-for-woocommerce');
				wp_send_json_error($sentResponse);
				wp_die();
				return;
			}

			$currentUser = wp_get_current_user();
			$ticket = IBXFWL_Inbox_TicketController::userReplyTicketThread($currentUser->ID, $reference, $description, $cleanAttachments);

			wp_send_json_success($ticket);
			wp_die();
		}

		/**
		 * Show Ticket Threads
		 *
		 * @return void
		 */
		public static function showUserMessageThreads() {
			self::importDirectories();

			// Check for nonce security      
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-thread-nonce' ) ) {
				wp_send_json_error(esc_html__('Could not verify nonce', 'inbox-for-woocommerce'));
				wp_die();
				return;
			}

			$reference = isset($_POST['reference']) ? sanitize_text_field($_POST['reference']) : '';

			if (  ! is_user_logged_in() ) {
				$sentResponse = esc_html__('You need to be logged in to access this endpoint', 'inbox-for-woocommerce');
				wp_send_json_error($sentResponse);
				wp_die();
				return;
			}

			$currentUser = wp_get_current_user();
			$ticket = IBXFWL_Inbox_TicketController::getUserTicketThreads($currentUser->ID, $reference);

			wp_send_json_success($ticket);
			wp_die();
		}

		/**
		 * Get User Inbox Messages
		 *
		 * @return void
		 */
		public static function getUserInboxMessages() {
			self::importDirectories();

			if (  ! is_user_logged_in() ) {
				$sentResponse = esc_html__('You need to be logged in to access this endpoint', 'inbox-for-woocommerce');
				wp_send_json_error($sentResponse);
				wp_die();
				return;
			}

			$currentUser = wp_get_current_user();
			$tickets = IBXFWL_Inbox_TicketController::getUserTickets($currentUser->ID);

			wp_send_json_success($tickets);
			wp_die();
		}

		/**
		 * Send Inbox Message to Admin
		 *
		 * @return void
		 */
		public static function sendInboxMessageSubmission() {
			self::importDirectories();

			// check post nonce
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-thread-nonce' ) ) {
				wp_send_json_error(esc_html__('Could not verify nonce', 'inbox-for-woocommerce'));
				wp_die();
				return;
			}

			$current_user = wp_get_current_user();

			$user_ID = $current_user->ID;
			$productId = isset($_POST['id']) ? absint($_POST['id']) : '';
			$type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
			$description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
			$attachmentLength = ( isset($_POST['attachment_length']) ) ? sanitize_text_field($_POST['attachment_length']) : '';
			$cleanAttachments = [];
			for ($i = 0; $i < intval($attachmentLength); $i++) {
				$attName = ( isset($_POST['attachments']) && isset($_POST['attachments'][$i]) && isset($_POST['attachments'][$i]['name']) ) ? sanitize_text_field($_POST['attachments'][$i]['name']) : '';
				$attUrl = ( isset($_POST['attachments']) && isset($_POST['attachments'][$i]) && isset($_POST['attachments'][$i]['url']) ) ? esc_url_raw($_POST['attachments'][$i]['url']) : '';

				if ($attName && $attUrl) {
					$cleanAttachments[] = [
						'name' => $attName,
						'url' => $attUrl
					];
				}
			}

			if ( IBXFWL_Inbox_TicketController::TYPE_PRODUCT_RELATED == $type ) {
				$product = wc_get_product( intval( $productId ) );
				$subject = esc_html__('New inbox message regarding') . ' "' . $product->get_title() . '"';
			} else {
				$productId = null;
				$subject = esc_html__('New inbox message received');
			}

			if ( ! $type || ! in_array($type, [
				IBXFWL_Inbox_TicketController::TYPE_GENERAL,
				IBXFWL_Inbox_TicketController::TYPE_INQUIRY,
				IBXFWL_Inbox_TicketController::TYPE_PRODUCT_RELATED,
				]) ) {
				$sentResponse = esc_html__('Please select a message type!', 'inbox-for-woocommerce');
				wp_send_json_error($sentResponse);
				wp_die();
				return;
			}

			if ( ! $description ) {
				$sentResponse = esc_html__('Please enter a message to proceed!', 'inbox-for-woocommerce');
				wp_send_json_error($sentResponse);
				wp_die();
				return;
			}

			if (  ! is_user_logged_in() ) {
				$sentResponse = esc_html__('You need to be logged in to access this endpoint', 'inbox-for-woocommerce');
				wp_send_json_error($sentResponse);
				wp_die();
				return;
			}

			$wordPressOffset = get_option('gmt_offset');
			$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;
			IBXFWL_Inbox_TicketController::addNewTicketByIDFromAccountInbox(
				$current_user->display_name,
				$user_ID,
				$type,
				$subject,
				$description,
				$productId,
				null,
				gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours")),
				$cleanAttachments
			);

			$sentResponse = IBXFWL_Inbox_SettingController::defaultInboxSentResponse();
			wp_send_json_success($sentResponse);
			wp_die();
		}

		/**
		 * Show Products
		 *
		 * @return JSON
		 */
		public static function showProductList() {
			// check post nonce
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-thread-nonce' ) ) {
				wp_send_json_error(esc_html__('Could not verify nonce', 'inbox-for-woocommerce'));
				wp_die();
				return;
			}

			$search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

			if ( !$search ) {
				$args = array(
					'orderby'  => 'name',
				);
				$products = wc_get_products( $args );
			} else {
				$products = self::get_wc_products_by_title( $search );
			}

			$productList = [];
			foreach ($products as $product) {
				$productList[] = [
					'id' => $product->get_id(),
					'title' => $product->get_title(),
					'description' => $product->get_short_description(),
					'price' => $product->get_price_html(),
					'img' => wp_get_attachment_image_src( $product->get_image_id() ) ? wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) : [IBXFWL_HELPDESK_ASSETS_URL . '/images/default-product-img.png']
				];
			}

			wp_send_json_success($productList);
			wp_die();
		}

		/**
		 * Submit Inquiry Form
		 *
		 * @return JSON
		 */
		public static function submitInquiryForm() {
			self::importDirectories();
			require_once('api/google/GoogleRecaptchaService.php');

			// check post nonce
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-thread-nonce' ) ) {
				wp_send_json_error(esc_html__('Could not verify nonce', 'inbox-for-woocommerce'));
				wp_die();
				return;
			}

			$current_user = wp_get_current_user();

			$user_ID = $current_user->ID;
			$productId = isset($_POST['id']) ? absint($_POST['id']) : '';
			$email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
			$description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';

			// if SITE Key config is set, proceed with challenge
			if ( IBXFWL_Inbox_SettingController::defaultGoogleRecaptchaSiteKey() ) {
				$token = isset($_POST['token']) ? sanitize_textarea_field($_POST['token']) : '';
				if ( ! WC_Inbox_GoogleRecaptchaService::checkRecaptchaToken($token) ) {
					$sentResponse = esc_html__('Recaptcha Challenge Failed. Please try again', 'inbox-for-woocommerce');
					wp_send_json_error($sentResponse);
					wp_die();
					return;
				}
			}
			

			$product = wc_get_product( intval( $productId ) );
			$subject = esc_html__('Inquiry on', 'inbox-for-woocommerce') . ' ' . $product->get_title();

			if ( ! $description ) {
				$sentResponse = esc_html__('Please enter a message to proceed!', 'inbox-for-woocommerce');
				wp_send_json_error($sentResponse);
				wp_die();
				return;
			}

			$wordPressOffset = get_option('gmt_offset');
			$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;

			if ( is_user_logged_in() ) {
				IBXFWL_Inbox_TicketController::addNewTicketByIDForInquiry(
					$current_user->display_name,
					$user_ID,
					$subject,
					$description,
					$productId,
					null,
					gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours"))
				);
			} else {
				$guestName = IBXFWL_Inbox_SettingController::defaultGuestName();

				// validate Email is real email
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$sentResponse = esc_html__('Invalid email format provided', 'inbox-for-woocommerce');
					wp_send_json_error($sentResponse);
					wp_die();
					return;
				}

				IBXFWL_Inbox_TicketController::addNewTicketByEmailForInquiry(
					$guestName,
					$email,
					$subject,
					$description,
					$productId,
					null,
					gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours"))
				);
			}

			$sentResponse = IBXFWL_Inbox_SettingController::defaultInquirySentResponse();
			wp_send_json_success($sentResponse);
			wp_die();
		}

		/**
		 * Get Selected Product Inquiry Form Information
		 *
		 * @return JSON
		 */
		public static function productAjax() {
			// check post nonce
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, 'ajax-wcs-thread-nonce' ) ) {
				wp_send_json_error(esc_html__('Could not verify nonce', 'inbox-for-woocommerce'));
				wp_die();
				return;
			}

			$productId = isset($_POST['id']) ? absint($_POST['id']) : '';

			$product = wc_get_product( $productId );

			if ( ! $product ) {
				wp_send_json_error(json_encode(['text' => esc_html__('Could not find product', 'inbox-for-woocommerce')]));
				wp_die();
				return;
			}

			$userId = get_current_user_id();

			$productResponse = [
				'is_logged_in' => is_user_logged_in() ? true : false,
				'title' => $product->get_title(),
				'description' => $product->get_short_description(),
				'price' => $product->get_price_html(),
				'img' => wp_get_attachment_image_src( $product->get_image_id(), 'full' ) ? wp_get_attachment_image_src( $product->get_image_id(), 'full' ) : [IBXFWL_HELPDESK_ASSETS_URL . '/images/default-product-img.png']
			];

			// wp_send_json_success($productId);
			wp_send_json_success($productResponse);
			wp_die();
		}

		private static function get_wc_products_by_title( $title ) {
			global $wpdb;
		
			$post_title = strval($title);
		
			$post_table = $wpdb->prefix . 'posts';
			$results = $wpdb->get_col($wpdb->prepare('
                SELECT ID
                FROM %1s
                WHERE post_title LIKE %s
                AND post_type LIKE "product"
            ', $post_table, '%' . $post_title . '%'));
		
			// We exit if title doesn't match
			$products = [];
			foreach ($results as $result) {
				$products[] = wc_get_product( intval( $result ) );
			}

			return $products;
		}

		private static function generateRandomString( $length = 10) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
		}

		private static function verifyPostNonce( $type) {
			// Check for nonce security   
			$uploadNonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			if ( ! wp_verify_nonce( $uploadNonce, $type ) ) {
				wp_send_json_error(esc_html__('Could not verify nonce', 'inbox-for-woocommerce'));
				wp_die();
				return;
			}
		}
	}

}
