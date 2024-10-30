<?php
/**
 * Activation controller for migration of tables
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_ActivateController' ) ) {
	class IBXFWL_Inbox_ActivateController {

		public static function activate() {
			// install database
			self::databaseInstallation();

			// flush rules
			flush_rewrite_rules();
		}

		/**
		 * Install database for plugin
		 *
		 * @return void
		 */
		public static function databaseInstallation() { 
			self::importDirectories();

			$existingDbVersion = get_option(IBXFWL_Inbox_DatabaseController::OPTION_VERSION);
			$currentDbVersion = IBXFWL_Inbox_DatabaseController::CURRENT_VERSION;
			
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();

			// set up inbox tables
			if ( ! $existingDbVersion ) {

				self::installWooCommerceUsersTable($wpdb, $charset_collate);
				self::installWooCommerceTicketsTable($wpdb, $charset_collate);
				self::installWooCommerceTicketProductsTable($wpdb, $charset_collate);
				self::installWooCommerceTicketThreadsTable($wpdb, $charset_collate);
				self::installWooCommerceTicketAttachmentsTable($wpdb, $charset_collate);

				add_option(IBXFWL_Inbox_DatabaseController::OPTION_VERSION, $currentDbVersion);
			} 
		}

		/**
		 * Import used classes
		 *
		 * @return void
		 */
		public static function importDirectories() {
			require_once('DatabaseController.php');
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		}

		/**
		 * Create WooCommerce Inbox Users Table
		 *
		 * @param $wpdb
		 * @param $charset_collate
		 * @return void
		 */
		public static function installWooCommerceUsersTable( $wpdb, $charset_collate) { 

			$table_name = $wpdb->prefix . IBXFWL_Inbox_DatabaseController::DB_USERS_TABLE;
			if ( $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_name)) != $table_name ) {

				$sql = "CREATE TABLE $table_name (
						`ID` bigint(9) NOT NULL AUTO_INCREMENT,
						`name` varchar(255) NOT NULL,
						`email` varchar(255) NOT NULL,
						`wp_user_id` int(9),
						`external_id` int(9),
						`updated_at` datetime,
						`created_at` datetime,
						PRIMARY KEY  (ID)
				) $charset_collate;";

				dbDelta($sql);
			}

		}

		/**
		 * Create WooCommerce Inbox Tickets Table
		 *
		 * @param $wpdb
		 * @param $charset_collate
		 * @return void
		 */
		public static function installWooCommerceTicketsTable( $wpdb, $charset_collate) { 
			
			$table_name = $wpdb->prefix . IBXFWL_Inbox_DatabaseController::DB_TICKETS_TABLE;
			if ( $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_name)) != $table_name ) {

				$sql = "CREATE TABLE $table_name (
						`ID` bigint(9) NOT NULL AUTO_INCREMENT,
						`reference` varchar(255) NOT NULL,
						`status` varchar(255) NOT NULL,
						`subject` varchar(255) NOT NULL,
						`type` varchar(255) NOT NULL,
						`user_id` bigint(9) NOT NULL,
						`assigned_agent_id` bigint(9) NOT NULL,
						`read_at` datetime,
						`user_read_at` datetime,
						`updated_at` datetime,
						`created_at` datetime,
						PRIMARY KEY  (ID)
				) $charset_collate;";

				dbDelta($sql);
			}
		}

		/**
		 * Create WooCommerce Inbox Tickets Table
		 *
		 * @param $wpdb
		 * @param $charset_collate
		 * @return void
		 */
		public static function installWooCommerceTicketThreadsTable( $wpdb, $charset_collate) { 
			
			$table_name = $wpdb->prefix . IBXFWL_Inbox_DatabaseController::DB_THREADS_TABLE;
			if ( $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_name)) != $table_name ) {

				$sql = "CREATE TABLE $table_name (
						`ID` bigint(9) NOT NULL AUTO_INCREMENT,
						`ticket_id` bigint(9) NOT NULL,
						`user_id` bigint(9) NOT NULL,
						`content` text NOT NULL,
						`has_attachment` boolean default 0,
						`read_at` datetime,
						`user_read_at` datetime,
						`updated_at` datetime,
						`created_at` datetime,
						PRIMARY KEY  (ID)
				) $charset_collate;";

				dbDelta($sql);
			}
		}

		/**
		 * Create WooCommerce Inbox Ticket Product Table
		 *
		 * @param $wpdb
		 * @param $charset_collate
		 * @return void
		 */
		public static function installWooCommerceTicketProductsTable( $wpdb, $charset_collate) { 
			
			$table_name = $wpdb->prefix . IBXFWL_Inbox_DatabaseController::DB_TICKET_PRODUCTS_TABLE;
			if ( $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_name)) != $table_name ) {

				$sql = "CREATE TABLE $table_name (
						`ID` bigint(9) NOT NULL AUTO_INCREMENT,
						`ticket_id` bigint(9) NOT NULL,
						`product_id` bigint(9) NOT NULL,
						`updated_at` datetime,
						`created_at` datetime,
						PRIMARY KEY  (ID)
				) $charset_collate;";

				dbDelta($sql);
			}
		}

		/**
		 * Create WooCommerce Inbox Tickets Table
		 *
		 * @param $wpdb
		 * @param $charset_collate
		 * @return void
		 */
		public static function installWooCommerceTicketAttachmentsTable( $wpdb, $charset_collate) { 
			
			$table_name = $wpdb->prefix . IBXFWL_Inbox_DatabaseController::DB_THREAD_ATTACHMENTS_TABLE;
			if ( $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_name)) != $table_name ) {

				$sql = "CREATE TABLE $table_name (
						`ID` bigint(9) NOT NULL AUTO_INCREMENT,
						`thread_id` bigint(9) NOT NULL,
						`url` text NOT NULL,
						`name` varchar(255),
						`updated_at` datetime,
						`created_at` datetime,
						PRIMARY KEY  (ID)
				) $charset_collate;";

				dbDelta($sql);
			}

		}
	}

}
