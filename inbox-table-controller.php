<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_TableController' ) ) {
	class IBXFWL_Inbox_TableController {
		public static function runTable() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				require_once plugin_dir_path( __FILE__ ) . 'templates/error/EssentialPluginMissing.php';
				IBXFWL_Inbox_Error_EssentialPluginMissing::showMissingMessage();
				return;
			}

			require_once plugin_dir_path( __FILE__ ) . 'includes/SetupController.php';
			
			
			if ( !IBXFWL_Inbox_SetupController::checkInstallationStatus() ) {
				wp_redirect( admin_url('admin.php?page=woocommerce-inbox-setup') );
				exit;
			}
	
			if (!class_exists('WP_List_Table')) {
				require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
			}
			require_once plugin_dir_path( __FILE__ ) . 'includes/admin/woocommerce-sweito-tickets-table.php';

			$currentPage = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : '';
	
			//Prepare Table of elements
			$wooCommerceSweitoTicketsTable = new IBXFWL_WCSweitoTicketsTable();
	
			echo '<h1>' . esc_html__('WooCommerce Inbox', 'inbox-for-woocommerce') . '</h1>';

			/** Process Extenson Version Check */
			self::checkExtensionVersion();
	
			/** Process bulk action */
			$wooCommerceSweitoTicketsTable->process_bulk_action();
			
			/** Process Ticket Sections */
			self::showTableFilterSections();
	
			$wooCommerceSweitoTicketsTable->prepare_items();
	
			echo '<form id="events-filter" style="max-width:99%" method="POST">';
			echo wp_kses(wp_nonce_field( 'wcs-ticket-table-nonce', 'wcs_wpnonce' ), array('input' => array('type' => array(), 'name' => array(), 'value' => array(), 'id' => array()))); 
			echo '<input type="hidden" name="page" value="' . esc_html($currentPage) . '" />';
				$wooCommerceSweitoTicketsTable->display();
			echo '</form>';
		}

		public static function showTableFilterSections() {
			require_once plugin_dir_path( __FILE__ ) . 'includes/TicketController.php';

			// WooCommerce Inbox Page Filters
			$totalNew = IBXFWL_Inbox_TicketController::adminTicketCount('new');
			$totalOpen = IBXFWL_Inbox_TicketController::adminTicketCount('open');
			$totalClosed = IBXFWL_Inbox_TicketController::adminTicketCount('closed');
			$totalArchive = IBXFWL_Inbox_TicketController::adminTicketCount('archive');
			$totalRecords = $totalNew + $totalOpen + $totalClosed + $totalArchive;
			$sections = array(
				''              => __( 'All', 'inbox-for-woocommerce' ) . '(' . $totalRecords . ')',
				'new'  => __( 'New', 'inbox-for-woocommerce' ) . '(' . $totalNew . ')',
				'open'  => __( 'Open', 'inbox-for-woocommerce' ) . '(' . $totalOpen . ')',
				'closed'  => __( 'Closed', 'inbox-for-woocommerce' ) . '(' . $totalClosed . ')',
				'archive'  => __( 'Archived', 'inbox-for-woocommerce' ) . '(' . $totalArchive . ')'
			);
			$tab_id = 'woocommerce-inbox-sweito';
			$current_section = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : '';
			echo '<ul class="subsubsub">';
			$array_keys = array_keys( $sections );
			foreach ( $sections as $id => $label ) {
				echo '<li><a href="' . esc_url(admin_url( 'admin.php?page=' . $tab_id . '&section=' . sanitize_title( $id ) )) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . esc_html($label) . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
			}
			echo '</ul><br class="clear" />';
		}

		public static function checkExtensionVersion() {
			require_once plugin_dir_path( __FILE__ ) . 'includes/api/sweito/AccountService.php';

			$updateState = IBXFWL_Inbox_Sweito_AccountService::checkExtensionVersion();
			$updateExist = 'outdated' == $updateState ? true : false;
			$outdatedVersion = 'expired' == $updateState ? true : false;

			$updateDownloadLink = IBXFWL_SWEITO_PRODUCT_URL;
			if ($outdatedVersion) {
				echo '<div class="error">
                    <p>' . esc_html__('The version you are using has expired and not up to date with the latest security updates. Please update to the latest version as soon as possible.', 'inbox-for-woocommerce') . ' <a href="' . esc_html($updateDownloadLink) . '">' . esc_html__('Click here to get the latest version', 'inbox-for-woocommerce') . '</a></p></div>';
			} elseif ($updateExist) {
				echo '<div class="updated">
                    <p>' . esc_html__('A new update exist for your \'inbox-for-woocommerce\' extension. Please update to latest version to get the latest update in security, features and others.', 'inbox-for-woocommerce') . ' <a href="' . esc_html($updateDownloadLink) . '">' . esc_html__('Click here to get the latest version', 'inbox-for-woocommerce') . '</a></p></div>';
			} else {
				echo '<div class="updated">
                    <p>' . esc_html__('You are currently using the free version (light-version). UPGRADE to the full version to access more features like different chat display themes, different inbox categories and others.', 'inbox-for-woocommerce') . ' <a target="_blank" href="' . esc_html(IBXFWL_SWEITO_PRODUCT_URL) . '">' . esc_html__('Click here to purchase full version now', 'inbox-for-woocommerce') . '</a></p></div>';
			}
		}
	}
}
