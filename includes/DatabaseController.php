<?php
/**
 * Database controller for managing database relationships
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_DatabaseController' ) ) {
	class IBXFWL_Inbox_DatabaseController {
		const CURRENT_VERSION = '1.0.4';
		const OPTION_VERSION = 'woocommerce_inbox_db_version';
		
		const DB_USERS_TABLE = 'woocommerce_inbox_users';
		const DB_TICKETS_TABLE = 'woocommerce_inbox_tickets';
		const DB_THREADS_TABLE = 'woocommerce_inbox_ticket_threads';
		const DB_TICKET_PRODUCTS_TABLE = 'woocommerce_inbox_ticket_products';
		const DB_THREAD_ATTACHMENTS_TABLE = 'woocommerce_inbox_ticket_attachments';

		/**
		 * Get list of all table
		 *
		 * @return array
		 */
		public static function getTableNames() {
			return [
				self::DB_USERS_TABLE,
				self::DB_TICKETS_TABLE,
				self::DB_THREADS_TABLE,
				self::DB_TICKET_PRODUCTS_TABLE,
				self::DB_THREAD_ATTACHMENTS_TABLE,
			];
		}

		/**
		 * Drop all extension tables
		 *
		 * @return void
		 */
		public static function dropExtensionTables() {
			global $wpdb;
			$tables = self::getTableNames();

			foreach ($tables as $table) {
				$tableName = $wpdb->prefix . $table;
				$wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS %1s', $tableName ));
			}
		}

		/**
		 * Removed all saved option names
		 *
		 * @param array $optionNames
		 * @return void
		 */
		public static function removedSavedOptions( $optionNames) {
			global $wpdb;

			$tableName = $wpdb->prefix . 'options';

			foreach ($optionNames as $optionName) {
				$wpdb->get_results($wpdb->prepare('DELETE FROM %1s WHERE `option_name` IN (%s)', $tableName, $optionName));
			}
		}

		/**
		 * Delete Ticket
		 *
		 * @param array $ticketIds
		 * @return void
		 */
		public static function deleteTicketForAdmin( $ticketIds) {
			global $wpdb;

			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$wpdb->get_results($wpdb->prepare('DELETE FROM %1s WHERE `ID` IN (%1s)', $tableName, implode(',', $ticketIds)));
		}

		/**
		 * Delete from Ticket Products
		 *
		 * @param array $threadIds
		 * @return void
		 */
		public static function deleteTicketProductsForAdmin( $threadIds) {
			global $wpdb;

			$tableName = $wpdb->prefix . self::DB_TICKET_PRODUCTS_TABLE;

			$wpdb->get_results($wpdb->prepare('DELETE FROM %1s WHERE `ticket_id` IN (%1s)', $tableName, implode(',', $threadIds)));
		}

		/**
		 * Delete from Ticket Attachments Table
		 *
		 * @param array $threadIds
		 * @return void
		 */
		public static function deleteTicketAttachmentsForAdmin( $threadIds) {
			global $wpdb;

			$tableName = $wpdb->prefix . self::DB_THREAD_ATTACHMENTS_TABLE;

			$wpdb->get_results($wpdb->prepare('DELETE FROM %1s WHERE `thread_id` IN (%1s)', $tableName, implode(',', $threadIds)));
		}

		/**
		 * Get Tickets That Has Attachments
		 *
		 * @param array $ticketIds
		 * @return array
		 */
		public static function getTicketThreadIdsThatHasAttachmentsForAdmin( $ticketIds) {
			global $wpdb;

			$tableName = $wpdb->prefix . self::DB_THREADS_TABLE;
			$results = $wpdb->get_results($wpdb->prepare('SELECT `ID`, `has_attachment` FROM %1s WHERE `ID` IN (%1s)', $tableName, implode(',', $ticketIds)));
			
			if ( count($results) == 0 ) {
				return [];
			}

			$threadIds = [];
			foreach ($results as $result) {
				if ($result->has_attachment) {
$threadIds[] = $result->ID;
				}
			}

			return $threadIds;
		}

		/**
		 * Get All Active Tickets
		 *
		 * @param array $statuses
		 * @return void
		 */
		public static function getActiveTicketsForAdmin( $statuses) {
			global $wpdb;

			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;
			$activeStatus = implode(',', $statuses);
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s ORDER BY `created_at` DESC LIMIT %d', $tableName, 100));
			//WHERE `status` IN ('$activeStatus') 

			$tickets = [];
			$activeUsers = [];
			foreach ($results as $result) {
				$tickets[] = [
					'ref' => ( $result )->reference,
					'status' => ( $result )->status,
					'type' => ( $result )->type,
					'subject' => ( $result )->subject,
					'user' => self::getTicketThreadUser($wpdb, ( $result )->user_id, $activeUsers),
					'assigned_agent' => self::getTicketThreadUser($wpdb, ( $result )->assigned_agent_id, $activeUsers),
					'created_at' => gmdate('jS M, Y H:i', strtotime(( $result )->created_at)),
					'agent_wp_user_id' => isset($activeUsers[( $result )->assigned_agent_id]) ? self::getInboxUserWpUserId($wpdb, ( $result )->assigned_agent_id) : false,
				];
			}
			// echo print_r($tickets);
			return $tickets;
		}

		/**
		 * Get Ticket Information and Thread Data For ADMIN
		 *
		 * @param int $id
		 * @param string $ref
		 * @return void
		 */
		public static function getTicketThreadByUserIdAndReferenceForAdmin( $id, $ref) {
			global $wpdb;

			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `reference` = %s', $tableName, $ref));
			
			if ( 0 == count($results) ) {
				return [];
			}

			$activeUsers = [];

			$ticket = [
				'id' => ( $results[0] )->ID,
				'ref' => ( $results[0] )->reference,
				'status' => ( $results[0] )->status,
				'type' => ( $results[0] )->type,
				'user' => self::getTicketThreadUser($wpdb, ( $results[0] )->user_id, $activeUsers),
				'read_at' => ( $results[0] )->read_at,
				'assigned_agent' => self::getTicketThreadUser($wpdb, ( $results[0] )->assigned_agent_id, $activeUsers),
				'is_customer' => isset($activeUsers[( $results[0] )->user_id]) ? true : false,
				'user_wp_user_id' => isset($activeUsers[( $results[0] )->user_id]) ? self::getInboxUserWpUserId($wpdb, ( $results[0] )->user_id) : false,
				'agent_wp_user_id' => isset($activeUsers[( $results[0] )->assigned_agent_id]) ? self::getInboxUserWpUserId($wpdb, ( $results[0] )->assigned_agent_id) : false,
				'created_at' => gmdate('jS M, Y H:i', strtotime(( $results[0] )->created_at)),
			];

			$ticketId = ( $results[0] )->ID;
			$tableName = $wpdb->prefix . self::DB_THREADS_TABLE;
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ticket_id` = %s ORDER BY `created_at` ASC', $tableName, $ticketId));

			$threads = [];
			foreach ($results as $thread) {
				$threads[] = [
					'comment' => nl2br(esc_html($thread->content)),
					'created_at' => gmdate('jS M, Y H:i', strtotime($thread->created_at)),
					'attachments' => $thread->has_attachment ? self::getTicketThreadAttachments($wpdb, $thread->ID) : [],
					'sender' => esc_html(self::getTicketThreadUser($wpdb, $thread->user_id, $activeUsers)),
					'is_you' => $id == $thread->user_id ? true : false,
				];
			}

			$ticket['threads'] = $threads;

			$tableName = $wpdb->prefix . self::DB_TICKET_PRODUCTS_TABLE;
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ticket_id` = %s ORDER BY `created_at` ASC', $tableName, $ticketId));
			$products = [];
			foreach ($results as $prod) {
				$product = wc_get_product( $prod->product_id );

				$products[] = [
					'title' => $product->get_title(),
					'description' => $product->get_short_description(),
					'price' => $product->get_price_html(),
					'link' => $product->get_permalink(),
					'img' => wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ? wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) : [IBXFWL_HELPDESK_ASSETS_URL . '/images/default-product-img.png']
				];
			}

			$ticket['products'] = $products;

			return $ticket;
		}

		/**
		 * Get Ticket Information and Thread Data
		 *
		 * @param int $id
		 * @param string $ref
		 * @return void
		 */
		public static function getTicketThreadByUserIdAndReference( $id, $ref) {
			global $wpdb;

			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `user_id` = %s AND `reference` = %s', $tableName, $id, $ref));
			
			if ( 0 == count($results) ) {
				return [];
			}

			$activeUsers = [];

			$ticket = [
				'id' => ( $results[0] )->ID,
				'ref' => ( $results[0] )->reference,
				'status' => ( $results[0] )->status,
				'type' => ( $results[0] )->type,
				'read_at' => ( $results[0] )->user_read_at,
				'user' => self::getTicketThreadUser($wpdb, ( $results[0] )->user_id, $activeUsers),
				'created_at' => gmdate('jS M, Y H:i', strtotime(( $results[0] )->created_at)),
			];

			$ticketId = ( $results[0] )->ID;
			$tableName = $wpdb->prefix . self::DB_THREADS_TABLE;
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ticket_id` = %s ORDER BY `created_at` ASC', $tableName, $ticketId));

			$threads = [];
			foreach ($results as $thread) {
				$threads[] = [
					'comment' => nl2br(esc_html($thread->content)),
					'created_at' => gmdate('jS M, Y H:i', strtotime($thread->created_at)),
					'attachments' => $thread->has_attachment ? self::getTicketThreadAttachments($wpdb, $thread->ID) : [],
					'sender' => esc_html(self::getTicketThreadUser($wpdb, $thread->user_id, $activeUsers)),
					'is_you' => $id == $thread->user_id ? true : false
				];
			}

			$ticket['threads'] = $threads;

			$tableName = $wpdb->prefix . self::DB_TICKET_PRODUCTS_TABLE;
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ticket_id` = %s ORDER BY `created_at` ASC', $tableName, $ticketId));
			$products = [];
			foreach ($results as $prod) {
				$product = wc_get_product( $prod->product_id );

				$products[] = [
					'title' => $product->get_title(),
					'description' => $product->get_short_description(),
					'price' => $product->get_price_html(),
					'link' => $product->get_permalink(),
					'img' => wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ? wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) : [IBXFWL_HELPDESK_ASSETS_URL . '/images/default-product-img.png']
				];
			}

			$ticket['products'] = $products;

			return $ticket;
		}

		/**
		 * Get Inbox User WP USER ID
		 *
		 * @param mixed $wpdb
		 * @param id $threadUserId
		 * @return void
		 */
		public static function getInboxUserEmail( $wpdb, $threadUserId) {
			$tableName = $wpdb->prefix . self::DB_USERS_TABLE;
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ID` = %s', $tableName, $threadUserId));

			if ( 0 < count($results) ) {
				if ( ( $results[0] )->email ) {
					return ( $results[0] )->email;
				} else if (( $results[0] )->wp_user_id) {
					$wpUserId = ( $results[0] )->wp_user_id;
					$tableName = $wpdb->prefix . 'users';
					$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ID` = %s', $tableName, $wpUserId));

					if ( 0 < count($results) ) {
						return ( $results[0] )->user_email;
					}
				}
			}

			return 0;
		}

		/**
		 * Get Inbox User WP USER ID
		 *
		 * @param mixed $wpdb
		 * @param id $threadUserId
		 * @return void
		 */
		public static function getInboxUserWpUserId( $wpdb, $threadUserId) {
			$tableName = $wpdb->prefix . self::DB_USERS_TABLE;
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ID` = %s', $tableName, $threadUserId));

			if ( 0 < count($results) ) {
				if ( ( $results[0] )->wp_user_id ) {
					return ( $results[0] )->wp_user_id;
				}
			}

			return 0;
		}

		/**
		 * Get Thread User Name
		 *
		 * @param mixed $wpdb
		 * @param int $threadId
		 * @return array
		 */
		public static function getTicketThreadUser( $wpdb, $threadUserId, &$users) {
			if ( array_key_exists($threadUserId, $users) ) {
				return $users[$threadUserId];
			}

			$tableName = $wpdb->prefix . self::DB_USERS_TABLE;
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ID` = %s', $tableName, $threadUserId));

			if ( 0 < count($results) ) {
				if ( ( $results[0] )->wp_user_id ) {
					$wpUserId = ( $results[0] )->wp_user_id;
					$tableName = $wpdb->prefix . 'users';
					$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ID` = %s', $tableName, $wpUserId));

					if ( 0 < count($results) ) {
						$name = ( $results[0] )->display_name;
						$users[$threadUserId] = $name;
						return ucfirst($name);
					}
				} else {
					$name = $results[0]->name ? $results[0]->name : $results[0]->email;

					if ('Guest User' === $name && $results[0]->email) {
						$name = $results[0]->email;
					}

					$users[$threadUserId] = $name;
					return ucfirst($name);
				}
			}

			return 'Unknown';
		}

		/**
		 * Get Thread Attachments
		 *
		 * @param mixed $wpdb
		 * @param int $threadId
		 * @return array
		 */
		public static function getTicketThreadAttachments( $wpdb, $threadId) {
			$tableName = $wpdb->prefix . self::DB_THREAD_ATTACHMENTS_TABLE;
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `thread_id` = %s ORDER BY `created_at` ASC', $tableName, $threadId));

			$attachments = [];
			foreach ($results as $result) {
				$attachments[] = [
					'name' => $result->name,
					'url' => $result->url,
				];
			}

			return $attachments;
		}

		/**
		 * Get Tickets for User
		 *
		 * @param int $id
		 * @return void
		 */
		public static function getOnlyTicketDetailsByIdForAdmin( $id) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ID` = %s', $tableName, $id));

			$activeUsers = [];
			if ( 0 < count($results) ) {
				$ticket = [
					'id' => ( $results[0] )->ID,
					'ref' => ( $results[0] )->reference,
					'type' => ( $results[0] )->type,
					'status' => ( $results[0] )->status,
					'user_id' => ( $results[0] )->user_id,
					'assigned_agent_id' => ( $results[0] )->assigned_agent_id,
					'user_email' => self::getInboxUserEmail($wpdb, ( $results[0] )->user_id),
					'user' => self::getTicketThreadUser($wpdb, ( $results[0] )->user_id, $activeUsers),
				];

				$ticketId = ( $results[0] )->ID;
				$tableName = $wpdb->prefix . self::DB_THREADS_TABLE;
				$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ticket_id` = %s ORDER BY `created_at` ASC', $tableName, $ticketId));

				$threads = [];
				foreach ($results as $thread) {
					$threads[] = [
						'id' => $thread->ID,
						'comment' => nl2br(esc_html($thread->content)),
						'attachments' => $thread->has_attachment ? self::getTicketThreadAttachments($wpdb, $thread->ID) : [],
						'sender_id' => $thread->user_id,
						'sender_email' => esc_html(self::getInboxUserEmail($wpdb, $thread->user_id)),
						'sender' => esc_html(self::getTicketThreadUser($wpdb, $thread->user_id, $activeUsers)),
						'created_at' => gmdate('Y-m-d H:i:s', strtotime($thread->created_at)),
					];
				}

				$ticket['threads'] = $threads;

				$tableName = $wpdb->prefix . self::DB_TICKET_PRODUCTS_TABLE;
				$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ticket_id` = %s ORDER BY `created_at` ASC', $tableName, $ticketId));
				$products = [];
				foreach ($results as $prod) {
					$product = wc_get_product( $prod->product_id );

					$products[] = [
						'id' => $product->get_id(),
						'title' => $product->get_title(),
						'description' => $product->get_short_description(),
						'price' => $product->get_price_html(),
						'link' => $product->get_permalink(),
						'img' => wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ? wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) : [IBXFWL_HELPDESK_ASSETS_URL . '/images/default-product-img.png']
					];
				}

				$ticket['products'] = $products;
				return $ticket;
			};

			return 0;
		}

		/**
		 * Get Tickets for User
		 *
		 * @param int $id
		 * @return void
		 */
		public static function getTicketByReferenceForAdmin( $ref) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `reference` = %s', $tableName, $ref));

			if ( 0 < count($results) ) {
				$ticket = [
					'id' => ( $results[0] )->ID,
					'ref' => ( $results[0] )->reference,
					'type' => ( $results[0] )->type,
					'status' => ( $results[0] )->status,
					'user_id' => ( $results[0] )->user_id,
					'assigned_agent_id' => ( $results[0] )->assigned_agent_id,
					'user_email' => self::getInboxUserEmail($wpdb, ( $results[0] )->user_id),
				];

				$ticketId = ( $results[0] )->ID;
				$tableName = $wpdb->prefix . self::DB_THREADS_TABLE;
				$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ticket_id` = %s ORDER BY `created_at` ASC', $tableName, $ticketId));

				$threads = [];
				foreach ($results as $thread) {
					$threads[] = [
						'comment' => nl2br(esc_html($thread->content)),
						'user_id' => $thread->user_id,
						'created_at' => gmdate('jS M, Y H:i', strtotime($thread->created_at)),
					];
				}

				$ticket['threads'] = $threads;

				$tableName = $wpdb->prefix . self::DB_TICKET_PRODUCTS_TABLE;
				$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ticket_id` = %s ORDER BY `created_at` ASC', $tableName, $ticketId));
				$products = [];
				foreach ($results as $prod) {
					$product = wc_get_product( $prod->product_id );

					$products[] = [
						'title' => $product->get_title(),
						'description' => $product->get_short_description(),
						'price' => $product->get_price_html(),
						'link' => $product->get_permalink(),
						'img' => wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ? wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) : [IBXFWL_HELPDESK_ASSETS_URL . '/images/default-product-img.png']
					];
				}

				$ticket['products'] = $products;
				return $ticket;
			};

			return 0;
		}

		/**
		 * Get Tickets for User
		 *
		 * @param int $id
		 * @return void
		 */
		public static function getTicketIdByReferenceForAdmin( $ref) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$results = $wpdb->get_results($wpdb->prepare('SELECT ID FROM %1s WHERE `reference` = %s', $tableName, $ref));

			if ( 0 < count($results) ) {
return ( $results[0] )->ID;
			}

			return 0;
		}

		/**
		 * Get Tickets for User
		 *
		 * @param int $id
		 * @return void
		 */
		public static function getTicketReferenceByIdForAdmin( $id) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$results = $wpdb->get_results($wpdb->prepare('SELECT ID FROM %1s WHERE `ID` = %s', $tableName, $id));

			if ( 0 < count($results) ) {
return ( $results[0] )->reference;
			}

			return 0;
		}

		/**
		 * Get Tickets for User
		 *
		 * @param int $id
		 * @return void
		 */
		public static function getTicketByReference( $id, $ref) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `user_id` = %s AND `reference` = %s', $tableName, $id, $ref));

			if ( 0 < count($results) ) {
				$ticket = [
					'id' => ( $results[0] )->ID,
					'ref' => ( $results[0] )->reference,
					'type' => ( $results[0] )->type,
					'status' => ( $results[0] )->status,
					'assigned_agent_id' => ( $results[0] )->assigned_agent_id,
					'assigned_agent_email' => self::getInboxUserEmail($wpdb, ( $results[0] )->assigned_agent_id),
				];

				$ticketId = ( $results[0] )->ID;
				$tableName = $wpdb->prefix . self::DB_THREADS_TABLE;
				$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ticket_id` = %s ORDER BY `created_at` ASC', $tableName, $ticketId));

				$threads = [];
				foreach ($results as $thread) {
					$threads[] = [
						'comment' => nl2br(esc_html($thread->content)),
						'user_id' => $thread->user_id,
						'created_at' => gmdate('jS M, Y H:i', strtotime($thread->created_at)),
					];
				}

				$ticket['threads'] = $threads;

				$tableName = $wpdb->prefix . self::DB_TICKET_PRODUCTS_TABLE;
				$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ticket_id` = %s ORDER BY `created_at` ASC', $tableName, $ticketId));
				$products = [];
				foreach ($results as $prod) {
					$product = wc_get_product( $prod->product_id );

					$products[] = [
						'title' => $product->get_title(),
						'description' => $product->get_short_description(),
						'price' => $product->get_price_html(),
						'link' => $product->get_permalink(),
						'img' => wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ? wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) : [IBXFWL_HELPDESK_ASSETS_URL . '/images/default-product-img.png']
					];
				}

				$ticket['products'] = $products;
				return $ticket;
			};

			return 0;
		}

		/**
		 * Get Tickets for User
		 *
		 * @param int $id
		 * @return void
		 */
		public static function getTicketIdByReference( $id, $ref) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$results = $wpdb->get_results($wpdb->prepare('SELECT ID FROM %1s WHERE `user_id` = %s AND `reference` = %s', $tableName, $id, $ref));

			if ( 0 < count($results) ) {
return ( $results[0] )->ID;
			}

			return 0;
		}

		/**
		 * Get Tickets for User
		 *
		 * @param int $id
		 * @return void
		 */
		public static function getTicketsByUserId( $id) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `user_id` = %s ORDER BY `updated_at` DESC', $tableName, $id));
			$tickets = [];

			foreach ($results as $result) {
				$tickets[] = [
					'ref' => $result->reference,
					'subject' => $result->subject,
					'status' => ( 'archive' == $result->status ) ? 'closed' : $result->status,
					'type' => $result->type,
					'read_at' => $result->user_read_at,
					'updated_at' => gmdate('jS M, Y', strtotime($result->updated_at))
				];
			}

			return $tickets;
		}

		/**
		 * Add Ticket Thread Item
		 *
		 * @param $ticketId
		 * @param $userId
		 * @param $content
		 * @param $readAt
		 * @param $userReadAt
		 * @param $attachments
		 * @return void
		 */
		public static function addTicketThread( $ticketId, $userId, $content, $status, $readAt, $userReadAt, $attachments = []) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_THREADS_TABLE;

			$wordPressOffset = get_option('gmt_offset');
			$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;

			$wpdb->insert( 
				$tableName, 
				array( 
					'ticket_id'    => $ticketId,
					'user_id' => $userId,
					'content' => $content,
					'has_attachment' => ( count($attachments) > 0 ) ? true : false,
					'read_at' => $readAt,
					'user_read_at' => $userReadAt,
					'updated_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours")),
					'created_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours"))
				)
			);

			$threadId = $wpdb->insert_id;

			foreach ($attachments as $attachment) {
				$tableName = $wpdb->prefix . self::DB_THREAD_ATTACHMENTS_TABLE;

				$wpdb->insert( 
					$tableName, 
					array( 
						'thread_id' => $threadId,
						'name' => $attachment['name'],
						'url' => $attachment['url'],
						'updated_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours")),
						'created_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours"))
					)
				);
			}

			self::updateTicketAdminReadAt($ticketId, $readAt);
			self::updateTicketUserReadAt($ticketId, $userReadAt);
			self::updateTicketStatus($ticketId, $status);

			return $threadId;
		}

		/**
		 * Update Ticket Assigned Agent
		 *
		 * @param int $ticketId
		 * @param int $agentId
		 * @return void
		 */
		public static function updateTicketAssignedAgent( $ticketId, $agentId) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$wpdb->update( 
				$tableName, 
				array( 
					'assigned_agent_id' => $agentId,
					'read_at' => null
				),
				array(
					'ID' => $ticketId
				)
			);
		}

		/**
		 * Get Ticket Count
		 *
		 * @param array $statuses
		 * @return int
		 */
		public static function getTicketCount( $statuses) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$statusString = implode(',', $statuses);
			$entryCount = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM %1s WHERE `status` IN (%s)', $tableName, $statusString));

			return $entryCount;
		}

		/**
		 * Update Inbox Ticket Status
		 *
		 * @param $ticketId
		 * @param $readAt
		 * @return void
		 */
		public static function updateBulkTicketStatus( $ticketIds, $status) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$wpdb->get_results($wpdb->prepare('UPDATE %1s SET `status` = %s WHERE `ID` IN (%1s)', $tableName, $status, implode(',', $ticketIds)));
		}

		/**
		 * Create Inbox Ticket Status
		 *
		 * @param $ticketId
		 * @param $readAt
		 * @return void
		 */
		public static function updateTicketStatus( $ticketId, $status) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$wordPressOffset = get_option('gmt_offset');
			$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;
			$wpdb->update( 
				$tableName, 
				array( 
					'status' => $status,
					'updated_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours"))
				),
				array(
					'ID' => $ticketId
				)
			);
		}

		/**
		 * Create Inbox Ticket ReadAt
		 *
		 * @param $ticketId
		 * @param $readAt
		 * @return void
		 */
		public static function updateTicketAdminReadAt( $ticketId, $readAt = null) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$wordPressOffset = get_option('gmt_offset');
			$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;
			$wpdb->update( 
				$tableName, 
				array( 
					'read_at' => $readAt,
					'updated_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours"))
				),
				array(
					'ID' => $ticketId
				)
			);
		}

		/**
		 * Create Inbox Ticket ReadAt
		 *
		 * @param $ticketId
		 * @param $readAt
		 * @return void
		 */
		public static function updateTicketUserReadAt( $ticketId, $readAt = null) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$wordPressOffset = get_option('gmt_offset');
			$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;
			$wpdb->update( 
				$tableName, 
				array( 
					'user_read_at'    => $readAt,
					'updated_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours"))
				),
				array(
					'ID' => $ticketId
				)
			);
		}

		/**
		 * Link Ticket to Product
		 *
		 * @param $subject
		 * @param $status
		 * @param $userId
		 * @param $assignedAgentId
		 * @return int
		 */
		public static function createTicketProduct(
			$ticketId,
			$productId
		) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKET_PRODUCTS_TABLE;

			$wordPressOffset = get_option('gmt_offset');
			$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;
			$wpdb->insert( 
				$tableName, 
				array( 
					'ticket_id'    => $ticketId,
					'product_id' => $productId,
					'updated_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours")),
					'created_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours"))
				)
			);

			return $wpdb->insert_id;
		}

		/**
		 * Create Inbox Ticket
		 *
		 * @param $subject
		 * @param $status
		 * @param $userId
		 * @param $assignedAgentId
		 * @return int
		 */
		public static function createInboxTicket(
			$subject,
			$status,
			$userId,
			$assignedAgentId,
			$type, 
			$readAt, 
			$userReadAt
		) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_TICKETS_TABLE;

			$reference = self::generateRandomString(12);
			$check = true;
			while ($check) {
				$results = $wpdb->get_col($wpdb->prepare('SELECT ID FROM %1s WHERE reference = %s', $tableName, $reference));
				if ( count($results) == 0 ) {
					$check = false;
				} else {
					$reference = self::generateRandomString(12);
				}
			}
			
			$wordPressOffset = get_option('gmt_offset');
			$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;
			$wpdb->insert( 
				$tableName, 
				array( 
					'status'    => $status,
					'reference' => $reference,
					'subject' => $subject,
					'type' => $type,
					'user_id' => $userId,
					'read_at' => $readAt,
					'user_read_at' => $userReadAt,
					'assigned_agent_id' => $assignedAgentId,
					'updated_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours")),
					'created_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours"))
				)
			);

			return $wpdb->insert_id;
		}

		/**
		 * Get Inbox User
		 *
		 * @param string $email
		 * @param int $wpUserId
		 * @param string $externalId
		 * @return int
		 */
		public static function getInboxUser( $email = null, $wpUserId = null, $externalId = null) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_USERS_TABLE;

			if ( $wpUserId ) {
				$results = $wpdb->get_col($wpdb->prepare('SELECT ID FROM %1s WHERE wp_user_id = %s', $tableName, $wpUserId));
				if ( 0 < count($results) ) {
return intval( $results[0] );
				}
			}

			if ( $externalId ) {
				$results = $wpdb->get_col($wpdb->prepare('SELECT ID FROM %1s WHERE external_id = %s', $tableName, $externalId));
				if ( 0 < count($results) ) {
return intval( $results[0] );
				}
			}

			if ( $email ) {
				$results = $wpdb->get_col($wpdb->prepare('SELECT ID FROM %1s WHERE email = %s', $tableName, $email));
				if ( 0 < count($results) ) {
return intval( $results[0] );
				}
			}

			return 0;
		}

		/**
		 * Create or Get Inbox users
		 *
		 * @param $name
		 * @param $email
		 * @param $wpUserId
		 * @param $externalId
		 * @return int
		 */
		public static function createOrGetInboxUser(
			$name,
			$email,
			$wpUserId = null,
			$externalId = null
		) {
			global $wpdb;
			$tableName = $wpdb->prefix . self::DB_USERS_TABLE;

			if ( $wpUserId ) {
				$results = $wpdb->get_col($wpdb->prepare('SELECT ID FROM %1s WHERE wp_user_id = %s', $tableName, $wpUserId));
				if ( 0 < count($results) ) {
return intval( $results[0] );
				}
			}

			if ( $externalId ) {
				$results = $wpdb->get_col($wpdb->prepare('SELECT ID FROM %1s WHERE external_id = %s', $tableName, $externalId));
				if ( 0 < count($results) ) {
return intval( $results[0] );
				}
			}

			if ( $email ) {
				$results = $wpdb->get_col($wpdb->prepare('SELECT ID FROM %1s WHERE email = %s', $tableName, $email));
				if ( 0 < count($results) ) {
return intval( $results[0] );
				}
			}

			$wordPressOffset = get_option('gmt_offset');
			$wordPressOffset = $wordPressOffset ? $wordPressOffset : 0;
			$wpdb->insert( 
				$tableName, 
				array( 
					'name'    => $name,
					'email' => $email,
					'wp_user_id' => $wpUserId,
					'external_id' => $externalId,
					'updated_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours")),
					'created_at' => gmdate('Y-m-d H:i:s', strtotime("+$wordPressOffset hours"))
				)
			);

			return $wpdb->insert_id;
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
	}

}
