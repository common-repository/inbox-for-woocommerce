<?php
/**
 * Manage inbox service for extension
 * 
 * @package Inbox-For-WooCommerce-LTE 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Inbox_Sweito_InboxService' ) ) {
	class WC_Inbox_Sweito_InboxService {
		/**
		 * Initialize main 
		 *
		 * @return 
		 */
		public static function runInboxService() { 
			$accessKey = isset($_SERVER['HTTP_SWEITO_ACCESS_TOKEN']) ? sanitize_text_field($_SERVER['HTTP_SWEITO_ACCESS_TOKEN']) : '';
			if ( !self::checkAccessToken($accessKey) ) {
				wp_send_json_success('invalid access token');
				wp_die();
				return;
			}

			$actionType = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';

			if ( 'test' == $actionType ) {
				self::validateConnection();
			} elseif ( 'setup-completed' == $actionType ) {
				self::completeSetupProcess();
			} elseif ( 'send-inbox-message' == $actionType ) {
				self::postTicketEntry();
			} elseif ( 'ping-test' == $actionType ) {
				self::pingConnection();
			}
		}

		/**
		 * Run complete helpdesk setup
		 *
		 * @return void
		 */
		public static function postTicketEntry() {
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/TicketController.php');
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/DatabaseController.php');

			$data = json_decode(file_get_contents('php://input'), true);
			$description = $data['body'];
			$attachments = $data['attachments'];
			$ticketReference = $data['ticket_reference'];

			$agentEmail = get_bloginfo('admin_email');
			$user = get_user_by('email', $agentEmail);

			$cleanAttachments = [];
			foreach ($attachments as $attachment) {
				$fileContent = file_get_contents($attachment['url']);
				$newUpload = wp_upload_bits($attachment['name'] , null, $fileContent);

				$cleanAttachments[] = [
					'name' => $attachment['name'],
					'url' => $newUpload['url']
				];
			}

			IBXFWL_Inbox_TicketController::adminReplyTicketThread($user->user_email, $user->ID, $ticketReference, $description, $cleanAttachments);

			// Create Users
			$ticketId = IBXFWL_Inbox_DatabaseController::getTicketIdByReferenceForAdmin($ticketReference);

			if ( ! $ticketId ) {
return;
			}

			$ticket = IBXFWL_Inbox_DatabaseController::getOnlyTicketDetailsByIdForAdmin($ticketId);

			$latestThreadId = 0;
			foreach ($ticket['threads'] as $thread) {
				$latestThreadId = $thread['id'];
			}

			wp_send_json_success(['status' => 'success', 'data' => $latestThreadId]);
			wp_die();
			return;
		}

		/**
		 * Run complete helpdesk setup
		 *
		 * @return void
		 */
		public static function completeSetupProcess() {
			$data = json_decode(file_get_contents('php://input'), true);
			$reference = $data['reference'];
			$status = $data['status'];

			$savedReference = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_REFERENCE);
			if ($savedReference) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_REFERENCE , $reference);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_REFERENCE , $reference);
			}


			$savedStatus = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_HELPDESK_STATUS);
			if ($savedStatus) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_HELPDESK_STATUS , $status);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_HELPDESK_STATUS , $status);
			}

			wp_send_json_success('success');
			wp_die();
			return;
		}

		/**
		 * Run test endpoint
		 *
		 * @return void
		 */
		public static function validateConnection() {
			$data = json_decode(file_get_contents('php://input'), true);
			$reference = $data['reference'];
			$savedReference = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_REFERENCE);

			if ($savedReference) {
				update_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_REFERENCE , $reference);
			} else {
				add_option( IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_REFERENCE , $reference);
			}

			wp_send_json_success('success');
			wp_die();
			return;
		}

		/**
		 * Run test endpoint
		 *
		 * @return void
		 */
		public static function pingConnection() {
			$data = json_decode(file_get_contents('php://input'), true);

			wp_send_json_success('success');
			wp_die();
			return;
		}

		/**
		 * Check access token
		 *
		 * @param string $token
		 * @return void
		 */
		public static function checkAccessToken( $token) {
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php');

			$savedAccessToken = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_ACCESS_TOKEN);

			if ( ! $savedAccessToken ) {
return false;
			}

			return $savedAccessToken === $token;
		}
	}

}
