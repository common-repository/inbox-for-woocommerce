<?php
/**
 * When plugin is uninstalled
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

if ( ! class_exists( 'WC_Inbox_UninstallPlugin' ) ) {

	class WC_Inbox_UninstallPlugin {

		public static function uninstall() {

			/*
			* Only remove ALL extension data if WC_INBOX_WCS_REMOVE_ALL_DATA constant is set to true in user's
			* wp-config.php. This is to prevent data loss when deleting the plugin from the backend
			* and to ensure only the site owner can perform this action.
			*/
			if ( defined( 'WC_INBOX_WCS_REMOVE_ALL_DATA' ) && ( WC_INBOX_WCS_REMOVE_ALL_DATA === true ) ) {

				require_once plugin_dir_path( __FILE__ ) . 'includes/SettingController.php';
				require_once plugin_dir_path( __FILE__ ) . 'includes/DatabaseController.php';

				// remove saved options
				$optionNames = IBXFWL_Inbox_SettingController::getSavedOptionNames();

				// remove DB version
				delete_option(IBXFWL_Inbox_DatabaseController::OPTION_VERSION);

				// remove database tables
				IBXFWL_Inbox_DatabaseController::removedSavedOptions($optionNames);
				IBXFWL_Inbox_DatabaseController::dropExtensionTables();

			}
			
		}

	}

	/*
	* Only remove ALL extension data if WC_INBOX_WCS_REMOVE_ALL_DATA constant is set to true in user's
	* wp-config.php. This is to prevent data loss when deleting the plugin from the backend
	* and to ensure only the site owner can perform this action.
	*/
	if ( defined( 'WC_INBOX_WCS_REMOVE_ALL_DATA' ) && ( WC_INBOX_WCS_REMOVE_ALL_DATA === true ) ) {

		WC_Inbox_UninstallPlugin::uninstall();

	}

}

