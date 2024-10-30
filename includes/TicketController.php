<?php
/**
 * Ticket controller for managing tickets
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_TicketController' ) ) {
	class IBXFWL_Inbox_TicketController {
		const STATUS_NEW = 'new';
		const STATUS_OPEN = 'open';
		const STATUS_CLOSED = 'closed';
		const STATUS_ARCHIVE = 'archive';

		const TYPE_INQUIRY = 'inquiry';
		const TYPE_GENERAL = 'general';
		const TYPE_PRODUCT_RELATED = 'product-related';

		public static function importDirectories() {
			require_once('DatabaseController.php');
			require_once('MailController.php');
			require_once('SettingController.php');
		}

		/**
		 * Bulk move ticket to status
		 *
		 * @param array $ticketIds
		 * @param string $status
		 * @return void
		 */
		public static function adminMoveTicketToStatus( $ticketIds, $status) {
			self::importDirectories();

			if ( !in_array($status, [self::STATUS_NEW, self::STATUS_OPEN, self::STATUS_CLOSED, self::STATUS_ARCHIVE]) ) {
return;
			}

			IBXFWL_Inbox_DatabaseController::updateBulkTicketStatus($ticketIds, $status);
		}

		/**
		 * Delete Ticket entry
		 *
		 * @param array $ticketIds
		 * @return void
		 */
		public static function adminDeleteTickets( $ticketIds) {
			self::importDirectories();

			// get all threads ids
			$threadWithAttachmentIds = IBXFWL_Inbox_DatabaseController::getTicketThreadIdsThatHasAttachmentsForAdmin($ticketIds);

			// remove saved attachments
			if (count($threadWithAttachmentIds) > 0) {
				IBXFWL_Inbox_DatabaseController::deleteTicketAttachmentsForAdmin($threadWithAttachmentIds);
			}

			// remove ticket products
			IBXFWL_Inbox_DatabaseController::deleteTicketProductsForAdmin($ticketIds);

			// remove save threads
			IBXFWL_Inbox_DatabaseController::deleteTicketForAdmin($ticketIds);
		}

		/**
		 * Count number of tickets
		 *
		 * @param string $status
		 * @return int
		 */
		public static function adminTicketCount( $status) {
			self::importDirectories();
			
			$selectedStatus = [];

			if ('new' == $status) {
				$selectedStatus = [self::STATUS_NEW];
			} elseif ('open' == $status) {
				$selectedStatus = [self::STATUS_OPEN];
			} elseif ('closed' == $status) {
				$selectedStatus = [self::STATUS_CLOSED];
			} elseif ('archive' == $status) {
				$selectedStatus = [self::STATUS_ARCHIVE];
			} else {
				$selectedStatus = [self::STATUS_NEW, self::STATUS_OPEN, self::STATUS_CLOSED, self::STATUS_ARCHIVE];
			}

			return IBXFWL_Inbox_DatabaseController::getTicketCount($selectedStatus);
		}

		/**
		 * Bulk Update to Ticket Status
		 *
		 * @param array $references
		 * @return void
		 */
		public static function adminUpdateTicketReadAt( $references) {
			self::importDirectories();

			foreach ($references as $reference) {
				$ticketId = IBXFWL_Inbox_DatabaseController::getTicketIdByReferenceForAdmin($reference);
				if (!$ticketId ) {
return;
				}

				$wordPressOffset = get_option('gmt_offset');
				$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;
				IBXFWL_Inbox_DatabaseController::updateTicketAdminReadAt($ticketId, gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours")));
			}
		}

		/**
		 * Bulk Update to Ticket Assigned Agent
		 *
		 * @param array $references
		 * @param string $name
		 * @param string $email
		 * @param int $agentWPUserId
		 * @return void
		 */
		public static function adminUpdateTicketAssignedAgent( $references, $name, $email, $wpAgentId) {
			self::importDirectories();

			// Create Users
			$agentId = IBXFWL_Inbox_DatabaseController::createOrGetInboxUser($name, $email, $wpAgentId);

			foreach ($references as $reference) {
				$ticketId = IBXFWL_Inbox_DatabaseController::getTicketIdByReferenceForAdmin($reference);
				if (!$ticketId ) {
return;
				}

				$ticket = IBXFWL_Inbox_DatabaseController::getTicketByReferenceForAdmin($reference);

				if ( $ticket['assigned_agent_id'] == $agentId ) {
					continue;
				}

				IBXFWL_Inbox_DatabaseController::updateTicketAssignedAgent($ticketId, $agentId);

				$currentUser = wp_get_current_user();
				if ($currentUser->ID != $wpAgentId) {
					IBXFWL_Inbox_MailController::sendAgentNewTicketNotice($email, $name);
				}
			}
		}

		/**
		 * Bulk Update to Ticket Status
		 *
		 * @param array $references
		 * @param string $status
		 * @return void
		 */
		public static function adminUpdateTicketStatus( $references, $status) {
			self::importDirectories();

			foreach ($references as $reference) {
				$ticketId = IBXFWL_Inbox_DatabaseController::getTicketIdByReferenceForAdmin($reference);
				if (!$ticketId ) {
return;
				}

				IBXFWL_Inbox_DatabaseController::updateTicketStatus($ticketId, $status);
			}
		}

		/**
		 * Admin Replies to Thread 
		 *
		 * @param int $wpUserId
		 * @param string $ref
		 * @param string $description
		 * @return void
		 */
		public static function adminReplyTicketThread( $wpUserEmail, $wpUserId, $ref, $description, $attachments) {
			self::importDirectories();

			$inboxUserId = IBXFWL_Inbox_DatabaseController::getInboxUser($wpUserEmail, $wpUserId);

			$ticketId = IBXFWL_Inbox_DatabaseController::getTicketIdByReferenceForAdmin($ref);

			if (!$ticketId ) {
return;
			}

			// get ticket
			$ticket = IBXFWL_Inbox_DatabaseController::getTicketByReferenceForAdmin($ref);

			// Add Ticket Thread
			$wordPressOffset = get_option('gmt_offset');
			$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;
			IBXFWL_Inbox_DatabaseController::addTicketThread($ticketId, $inboxUserId, $description, self::STATUS_OPEN, gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours")), null, $attachments);

			if (( self::TYPE_INQUIRY == $ticket['type'] ) && ( 0 < count($ticket['products']) ) ) {
				$lastCustomerMessage = '';

				foreach ($ticket['threads'] as $thread) {
					if ($thread['user_id'] == $ticket['user_id']) {
						$lastCustomerMessage = $thread['comment'];
					}
				}

				// send inquiry type of mail
				IBXFWL_Inbox_MailController::sendCustomerInquiryEmailReply(
					$ticket['user_email'], $ticket['products'][0], $lastCustomerMessage, $description
				);
			} else {

				// send inquiry type of mail
				IBXFWL_Inbox_MailController::sendCustomerMessageEmailReply(
					$ticket['user_email'], $description
				);
			}
		}

		/**
		 * Get Active Tickets for Admin user
		 *
		 * @return array
		 */
		public static function getAdminUserActiveTickets() {
			self::importDirectories();

			$tickets = IBXFWL_Inbox_DatabaseController::getActiveTicketsForAdmin([
				self::STATUS_OPEN,
				self::STATUS_NEW
			]);

			return $tickets;
		}

		/**
		 * Get Ticket Thread
		 *
		 * @param [type] $wpUserId
		 * @return void
		 */
		public static function getAdminUserTicketThreads( $wpUserEmail, $wpUserId, $ref) {
			self::importDirectories();

			$inboxUserId = IBXFWL_Inbox_DatabaseController::getInboxUser($wpUserEmail, $wpUserId);
			$ticket = IBXFWL_Inbox_DatabaseController::getTicketThreadByUserIdAndReferenceForAdmin($inboxUserId, $ref);

			return $ticket;
		}

		/**
		 * User Replies to Thread 
		 *
		 * @param int $wpUserId
		 * @param string $ref
		 * @param string $description
		 * @return void
		 */
		public static function userReplyTicketThread( $wpUserId, $ref, $description, $attachments) {
			self::importDirectories();

			$inboxUserId = IBXFWL_Inbox_DatabaseController::getInboxUser('', $wpUserId);

			$ticketId = IBXFWL_Inbox_DatabaseController::getTicketIdByReference($inboxUserId, $ref);

			if (!$ticketId ) {
return;
			}

			// get ticket
			$ticket = IBXFWL_Inbox_DatabaseController::getTicketByReference($inboxUserId, $ref);

			// Add Ticket Thread
			$wordPressOffset = get_option('gmt_offset');
			$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;
			IBXFWL_Inbox_DatabaseController::addTicketThread($ticketId, $inboxUserId, $description, self::STATUS_OPEN, null, gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours")), $attachments);

			// check store location
			if ( self::checkIfExternalHelpdesk() ) {
				require_once IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/TicketService.php';
				IBXFWL_Inbox_Sweito_TicketService::sendNewTicketToHelpdesk($ticketId);
				return;
			}

			if ($ticket['assigned_agent_email']) {
				// send admin email notice
				IBXFWL_Inbox_MailController::sendAdminInboxMessageReply(
					$ticket['assigned_agent_email'], $description
				);
			}
		}

		/**
		 * Get Ticket Thread
		 *
		 * @param [type] $wpUserId
		 * @return void
		 */
		public static function getUserTicketThreads( $wpUserId, $ref) {
			self::importDirectories();

			$inboxUserId = IBXFWL_Inbox_DatabaseController::getInboxUser('', $wpUserId);
			$ticket = IBXFWL_Inbox_DatabaseController::getTicketThreadByUserIdAndReference($inboxUserId, $ref);

			if ( !$ticket['read_at'] ) {
				$wordPressOffset = get_option('gmt_offset');
				$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;
				IBXFWL_Inbox_DatabaseController::updateTicketUserReadAt($ticket['id'], gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours")));
			}

			if ( isset($ticket['id']) ) {
unset($ticket['id']);
			}

			return $ticket;
		}

		/**
		 * Get list of user tickets
		 * 
		 * @param int $wpUserId
		 */
		public static function getUserTickets( $wpUserId) {
			self::importDirectories();

			$inboxUserId = IBXFWL_Inbox_DatabaseController::getInboxUser('', $wpUserId); 
			$tickets = IBXFWL_Inbox_DatabaseController::getTicketsByUserId($inboxUserId);
			return $tickets;
		}

		/**
		 * Logged In User Add New Inquiry Ticket for product
		 *
		 * @param string $name
		 * @param string $email
		 * @param string $subject
		 * @param string $content
		 * @param int $productId
		 * @param datetime $readAt
		 * @param datetime $userReadAt
		 * @param array $attachments
		 * @return void
		 */
		public static function addNewTicketByIDFromAccountInbox(
			$wpUserName,
			$wpUserId,
			$messageType,
			$subject,
			$content,
			$productId,
			$readAt,
			$userReadAt,
			$attachments = []
		) {
			self::importDirectories();

			$adminCredentials = self::adminCredentials();
			$agentID = null;
			if (isset($adminCredentials['name']) && isset($adminCredentials['email'])) {
				$agentName = $adminCredentials['name'];
				$agentEmail = $adminCredentials['email'];
				$agentID = $adminCredentials['id'];
			} else {
				$agentName = get_bloginfo('name');
				$agentEmail = get_bloginfo('admin_email');
			}

			// Create Users
			$userId = IBXFWL_Inbox_DatabaseController::createOrGetInboxUser($wpUserName, '', $wpUserId);
			$agentId = IBXFWL_Inbox_DatabaseController::createOrGetInboxUser($agentName, $agentEmail, $agentID);

			// Create Inbox Ticket
			$ticketId = IBXFWL_Inbox_DatabaseController::createInboxTicket($subject, self::STATUS_NEW, $userId, $agentId, $messageType, $readAt, $userReadAt);

			// Add Ticket Thread
			IBXFWL_Inbox_DatabaseController::addTicketThread($ticketId, $userId, $content, self::STATUS_NEW, $readAt, $userReadAt, $attachments);

			if ( self::TYPE_PRODUCT_RELATED == $messageType ) {
				// Link to Product
				IBXFWL_Inbox_DatabaseController::createTicketProduct($ticketId, $productId);
			}

			// check store location
			if ( self::checkIfExternalHelpdesk() ) {
				require_once IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/TicketService.php';
				IBXFWL_Inbox_Sweito_TicketService::sendNewTicketToHelpdesk($ticketId);
				return;
			}

			// Notify admin
			IBXFWL_Inbox_MailController::sendAdminInboxNoticeEmail($wpUserName, $agentEmail, $productId, $messageType);
		}

		/**
		 * Logged In User Add New Inquiry Ticket for product
		 *
		 * @param string $name
		 * @param string $email
		 * @param string $subject
		 * @param string $content
		 * @param int $productId
		 * @param datetime $readAt
		 * @param datetime $userReadAt
		 * @param array $attachments
		 * @return void
		 */
		public static function addNewTicketByIDForInquiry(
			$wpUserName,
			$wpUserId,
			$subject,
			$content,
			$productId,
			$readAt,
			$userReadAt,
			$attachments = []
		) {
			self::importDirectories();

			$adminCredentials = self::adminCredentials();
			$agentID = null;
			if (isset($adminCredentials['name']) && isset($adminCredentials['email'])) {
				$agentName = $adminCredentials['name'];
				$agentEmail = $adminCredentials['email'];
				$agentID = $adminCredentials['id'];
			} else {
				$agentName = get_bloginfo('name');
				$agentEmail = get_bloginfo('admin_email');
			}

			// Create Users
			$userId = IBXFWL_Inbox_DatabaseController::createOrGetInboxUser($wpUserName, '', $wpUserId);
			$agentId = IBXFWL_Inbox_DatabaseController::createOrGetInboxUser($agentName, $agentEmail, $agentID);

			// Create Inbox Ticket
			$ticketId = IBXFWL_Inbox_DatabaseController::createInboxTicket($subject, self::STATUS_NEW, $userId, $agentId, self::TYPE_INQUIRY, $readAt, $userReadAt);

			// Link to Product
			IBXFWL_Inbox_DatabaseController::createTicketProduct($ticketId, $productId);

			// Add Ticket Thread
			IBXFWL_Inbox_DatabaseController::addTicketThread($ticketId, $userId, $content, self::STATUS_NEW, $readAt, $userReadAt, $attachments);

			// check store location
			if ( self::checkIfExternalHelpdesk() ) {
				require_once IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/TicketService.php';
				IBXFWL_Inbox_Sweito_TicketService::sendNewTicketToHelpdesk($ticketId);
				return;
			}

			// Notify admin
			IBXFWL_Inbox_MailController::sendAdminInquiryEmail($wpUserName, $agentEmail, $productId);
		}
		
		/**
		 * Guest User Add New Inquiry Ticket for product 
		 *
		 * @param string $name
		 * @param string $email
		 * @param string $subject
		 * @param string $content
		 * @param int $productId
		 * @param datetime $readAt
		 * @param datetime $userReadAt
		 * @param array $attachments
		 * @return void
		 */
		public static function addNewTicketByEmailForInquiry(
			$name,
			$email,
			$subject,
			$content,
			$productId,
			$readAt,
			$userReadAt,
			$attachments = []
		) {
			self::importDirectories();

			$adminCredentials = self::adminCredentials();
			$agentID = null;
			if (isset($adminCredentials['name']) && isset($adminCredentials['email'])) {
				$agentName = $adminCredentials['name'];
				$agentEmail = $adminCredentials['email'];
				$agentID = $adminCredentials['id'];
			} else {
				$agentName = get_bloginfo('name');
				$agentEmail = get_bloginfo('admin_email');
			}

			// Create Users
			$userId = IBXFWL_Inbox_DatabaseController::createOrGetInboxUser($name, $email);
			$agentId = IBXFWL_Inbox_DatabaseController::createOrGetInboxUser($agentName, $agentEmail, $agentID);

			// Create Inbox Ticket
			$ticketId = IBXFWL_Inbox_DatabaseController::createInboxTicket($subject, self::STATUS_NEW, $userId, $agentId, self::TYPE_INQUIRY, $readAt, $userReadAt);

			// Link to Product
			IBXFWL_Inbox_DatabaseController::createTicketProduct($ticketId, $productId);

			// Add Ticket Thread
			IBXFWL_Inbox_DatabaseController::addTicketThread($ticketId, $userId, $content, self::STATUS_NEW, $readAt, $userReadAt, $attachments);

			// check store location
			if ( self::checkIfExternalHelpdesk() ) {
				require_once IBXFWL_SWEITO_INCLUDES_URL . '/api/sweito/TicketService.php';
				IBXFWL_Inbox_Sweito_TicketService::sendNewTicketToHelpdesk($ticketId);
				return;
			}

			// Notify admin
			IBXFWL_Inbox_MailController::sendAdminInquiryEmail($name, $agentEmail, $productId);
		}

		private static function checkIfExternalHelpdesk() {
			$isAllowed = false;
			$defaultTicketLocation = IBXFWL_Inbox_SettingController::defaultTicketLocation();

			if ( 'wpadmin' != $defaultTicketLocation ) {
				$savedReference = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_REFERENCE);
				$savedHelpdeskStatus = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_HELPDESK_STATUS);

				if ( 'zendesk' == $defaultTicketLocation ) {
					if ($savedReference && 'zendesk-active' == $savedHelpdeskStatus) {
						$isAllowed = true;
					}
				} elseif ( 'freshdesk' == $defaultTicketLocation ) {
					$stage = 33;
					if ($savedReference && 'freshdesk-active' == $savedHelpdeskStatus) {
						$isAllowed = true;
					}
				}
			}

			return $isAllowed;
		}

		private static function adminCredentials() {
			global $wp_roles;
			$all_roles = $wp_roles->roles;

			$adminUser = [];
			foreach ($all_roles as $roleName => $roleCapacity) {
				// skip customers
				if ( 'administrator' != $roleName ) {
continue;
				}

				$args = array(
					'role'    => $roleName,
					'orderby' => 'user_nicename',
					'order'   => 'ASC'
				);
				$users = get_users( $args );

				foreach ($users as $user) {
					$adminUser = [
						'id' => $user->ID,
						'name' => $user->display_name,
						'email' => $user->user_email,
					];

					break;
				}
				break;
			}
			
			return $adminUser;
		}
	}
}
