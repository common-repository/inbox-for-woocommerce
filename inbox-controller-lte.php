<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_InboxForWooCommerceLte' ) ) {
	class IBXFWL_InboxForWooCommerceLte {
		public $plugin;
	
		public function __construct() {
			$this->plugin = plugin_basename( __FILE__ );
		}
	
		public function register() {
			require_once plugin_dir_path( __FILE__ ) . 'includes/AjaxController.php';
			require_once plugin_dir_path( __FILE__ ) . 'includes/webhook/InboxService.php';
			require_once plugin_dir_path( __FILE__ ) . 'includes/SetupController.php';
	
			// check for ticket management location
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'front_enqueue' ) );
			add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
	
			// add_action( 'woocommerce_single_product_summary', array( $this, 'add_inquire_box_beside_product' ), 35 );
			add_action( 'woocommerce_after_single_product', array( $this, 'add_cta_box_beneath_product' ), 1 );
			add_action( 'woocommerce_account_woocommerce_sweito_inbox_endpoint', array ( $this , 'woocommerce_account_inbox_page') );
			add_action( 'admin_head', array( $this, 'style_admin_inbox_page') );
			add_action( 'woocommerce_admin_field_wcs_button' , array( $this, 'wcs_add_admin_field_button') );
			add_action( 'woocommerce_admin_field_wcs_checkbox_disabled' , array( $this, 'wcs_add_admin_field_checkbox_disabled') );
			add_action( 'woocommerce_admin_field_wcs_input_disabled' , array( $this, 'wcs_add_admin_field_input_disabled') );
			add_action( 'woocommerce_admin_field_wcs_input_display' , array( $this, 'wcs_add_admin_field_input_display') );
			add_action( 'woocommerce_settings_wcs_inbox', array( $this, 'add_inbox_setting_sections_settings_tab'), 10 );
			add_action( 'woocommerce_settings_save_wcs_inbox', array( $this, 'add_inbox_setting_sections_settings_save'), 10 );
	
			add_filter( "plugin_action_links_$this->plugin", array( $this, 'settings_link' ) );
			add_filter( 'init', array( $this, 'add_author_more_query_var') );
			add_filter( 'init', array( $this, 'load_plugin_text_domain') );
			add_filter( 'woocommerce_account_menu_items', array( $this, 'add_account_inbox_tab' ) );
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_inbox_setting_tab'), 50 );
			add_filter( 'woocommerce_sections_wcs_inbox', array( $this, 'add_inbox_setting_sections_tab') );
			
	
			/**
			 * AJAX SECTION
			 */
			// Inquiry Form
			add_action( 'wp_ajax_nopriv_woocommerce_sweito_product_ajax', array('IBXFWL_Inbox_AjaxController', 'productAjax') );
			add_action( 'wp_ajax_woocommerce_sweito_product_ajax', array('IBXFWL_Inbox_AjaxController', 'productAjax') );
			add_action( 'wp_ajax_nopriv_woocommerce_sweito_submit_inquiry_form', array('IBXFWL_Inbox_AjaxController', 'submitInquiryForm') );
			add_action( 'wp_ajax_woocommerce_sweito_submit_inquiry_form', array('IBXFWL_Inbox_AjaxController', 'submitInquiryForm') );
	
			// MY ACCOUNT SECTION
			add_action( 'wp_ajax_nopriv_woocommerce_inbox_product_list_ajax', array('IBXFWL_Inbox_AjaxController', 'showProductList') );
			add_action( 'wp_ajax_woocommerce_inbox_product_list_ajax', array('IBXFWL_Inbox_AjaxController', 'showProductList') );
			add_action( 'wp_ajax_woocommerce_inbox_message_submission_ajax', array('IBXFWL_Inbox_AjaxController', 'sendInboxMessageSubmission') );
			add_action( 'wp_ajax_woocommerce_inbox_message_list_ajax', array('IBXFWL_Inbox_AjaxController', 'getUserInboxMessages') );
			add_action( 'wp_ajax_woocommerce_inbox_message_threads_ajax', array('IBXFWL_Inbox_AjaxController', 'showUserMessageThreads') );
			add_action( 'wp_ajax_woocommerce_inbox_message_reply_thread_ajax', array('IBXFWL_Inbox_AjaxController', 'userReplyMessageThread') );
			add_action( 'wp_ajax_woocommerce_inbox_message_upload_document', array('IBXFWL_Inbox_AjaxController', 'uploadDocumentByAjax') );
	
			// ADMIN
			add_action( 'wp_ajax_woocommerce_inbox_setup_create_account', array('IBXFWL_Inbox_AjaxController', 'signupSweitoAccount') );
			add_action( 'wp_ajax_woocommerce_inbox_setup_signin_account', array('IBXFWL_Inbox_AjaxController', 'signinSweitoAccount') );
			add_action( 'wp_ajax_woocommerce_inbox_setup_signin_verify_otp_account', array('IBXFWL_Inbox_AjaxController', 'verifySigninSweitoAccount') );
			add_action( 'wp_ajax_woocommerce_inbox_setup_save_ticket_location', array('IBXFWL_Inbox_AjaxController', 'setupSaveLocation') );
			// add_action( 'wp_ajax_woocommerce_inbox_setup_save_activation_key', array('IBXFWL_Inbox_AjaxController', 'verifyPurchaseKey') );
			add_action( 'wp_ajax_woocommerce_inbox_setup_zendesk_auth_token', array('IBXFWL_Inbox_AjaxController', 'authZendeskAccount') );
			add_action( 'wp_ajax_woocommerce_inbox_setup_freshdesk_auth_token', array('IBXFWL_Inbox_AjaxController', 'authFreshdeskAccount') );
			add_action( 'wp_ajax_woocommerce_inbox_setup_verify_helpdesk_auth', array('IBXFWL_Inbox_AjaxController', 'verifyHelpdeskAuth') );
			add_action( 'wp_ajax_woocommerce_inbox_setup_update_site', array('IBXFWL_Inbox_AjaxController', 'updateAccountSite') );
			add_action( 'wp_ajax_woocommerce_inbox_setup_personalization', array('IBXFWL_Inbox_AjaxController', 'updateAccountPersonalization') );
			add_action( 'wp_ajax_woocommerce_inbox_message_admin_reply_thread_ajax', array('IBXFWL_Inbox_AjaxController', 'adminUserReplyMessageThread') );
	
			// WEBHOOK
			add_action( 'wp_ajax_nopriv_woocommerce_inbox_webhook', array('WC_Inbox_Sweito_InboxService', 'runInboxService') );
		}
	
		public function add_author_more_query_var() {
			add_rewrite_endpoint('woocommerce_sweito_inbox', EP_PAGES);
			flush_rewrite_rules();
	
			$this->add_inbox_roles();
		}
	
		public function add_inbox_roles() {
			$role = get_role(  'inbox-for-woocommerce-agent' ); 
	
			if ( ! $role ) {
				add_role(
					'inbox-for-woocommerce-agent', //  System name of the role.
					esc_html__( 'WooCommerce Inbox Agent', 'inbox-for-woocommerce'  ), // Display name of the role.
					array(
						'read'  => true,
					)
				);
	
				$role = get_role(  'inbox-for-woocommerce-agent' );
			}
			
			$role->add_cap( 'woocommerce_inbox_agent' );
			$role->add_cap( 'view_admin_dashboard' );
	
			// add capacity to super-admin
			$role = get_role(  'administrator' );
			$role->add_cap( 'woocommerce_inbox_agent' );
			$role->add_cap( 'woocommerce_inbox_super_admin' );
		}
	
		public function load_plugin_text_domain() {
			$textdomain = 'inbox-for-woocommerce';
	
			$locale = is_admin() && is_callable( 'get_user_locale' ) ? get_user_locale() : get_locale();
	
			/**
			 * Load plugin locale
			 * 
			 * @since version 1.0.4
			 */
			$locale = apply_filters( 'plugin_locale', $locale, $textdomain );
	
			load_textdomain( $textdomain, WP_LANG_DIR . '/' . $textdomain . '/' . $textdomain . '-' . $locale . '.mo' );
	
			load_plugin_textdomain( $textdomain, false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}
	
		public function settings_link( $links ) {
			$settings_link = '<a href="' . admin_url('admin.php?page=wc-settings&tab=wcs_inbox') . '">' . esc_html__('Settings', 'inbox-for-woocommerce') . '</a>';
			array_push( $links, $settings_link );
			return $links;
		}
	
		public function add_admin_pages() {
			add_menu_page( 'WooCommerce Inbox Setup', 'WooCommerce Inbox', 'woocommerce_inbox_super_admin', 'woocommerce-inbox-setup', array( $this, 'admin_index' ) );
			add_submenu_page( 'woocommerce', 'Inbox', 'Inbox', 'woocommerce_inbox_agent', 'woocommerce-inbox-sweito', array($this, 'admin_inbox_page') );
	
			//manage_options
			add_menu_page('WooCommerce Inbox Preview', 'WooCommerce Inbox Preview', 'woocommerce_inbox_agent', 'woocommerce-inbox-sweito-preview', array($this, 'admin_inbox_preview'));
			remove_menu_page('woocommerce-inbox-sweito-preview');
			remove_menu_page('woocommerce-inbox-setup');
	
			$user = wp_get_current_user();
			$roles = ( array ) $user->roles;
	
			if ( !in_array('administrator', $roles) && in_array('inbox-for-woocommerce-agent', $roles) ) {
				add_menu_page( 'Woo Inbox', 'Woo Inbox', 'woocommerce_inbox_agent', 'woocommerce-inbox-sweito-agent', array( $this, 'admin_inbox_page' ) );
			}
		}
	
		public function admin_inbox_preview() {
			require_once plugin_dir_path( __FILE__ ) . 'templates/admin/TicketThreadPreview.php';
			IBXFWL_Inbox_Admin_TicketThreadPreview::showTicketThread();
		}
	
		public function admin_inbox_page() {
			require_once plugin_dir_path( __FILE__ ) . 'inbox-table-controller.php';
			IBXFWL_Inbox_TableController::runTable();
		}
	
		public function style_admin_inbox_page() {
			echo '<style type="text/css">';
			echo '.woocommerceinboxes #wcs_id { width: 7%; }';
			echo '.woocommerceinboxes #wcs_reference { width: 12%; }';
			echo '.woocommerceinboxes #wcs_status { width: 7%; }';
			echo '.woocommerceinboxes #wcs_type { width: 11%; }';
			echo '.woocommerceinboxes #wcs_sender { width: 15%; }';
			echo '.woocommerceinboxes #wcs_assigned { width: 15%; }';
			echo '.woocommerceinboxes #wcs_subject { width: 18%; }';
			echo '.woocommerceinboxes #wcs_created_at { width: 15%; }';
			echo '</style>';
		}
	
		public function admin_index() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				require_once plugin_dir_path( __FILE__ ) . 'templates/error/EssentialPluginMissing.php';
				IBXFWL_Inbox_Error_EssentialPluginMissing::showMissingMessage();
			} else {
				require_once plugin_dir_path( __FILE__ ) . 'templates/SetupWizard.php';
				IBXFWL_Inbox_SetupWizard::showHomeSection();
			}
		}
	
		public function enqueue() {
			// enqueue all our scripts
			wp_enqueue_style( 'woocommercesweitostyle', plugins_url( '/assets/css/woocommerce-sweito-main.css', __FILE__ ), [], '1.0.4' );
			wp_enqueue_script( 'woocommercesweitoscript', plugins_url( '/assets/js/woocommerce-sweito-main.js', __FILE__ ), [], '1.0.4' );
	
			wp_localize_script( 'woocommercesweitoscript', 'woocommercesweitoscript_object', array( 
				'ajax_url' => admin_url( 'admin-ajax.php' ), 
				'pdf_logo' => IBXFWL_HELPDESK_ASSETS_URL . '/images/pdf-icon.png',
				'wcs_setup_otp_failed' => esc_html__('Entered OTP does not match', 'inbox-for-woocommerce'),
				'wcs_setup_otp_mismatch' => esc_html__('The provided OTP does not match', 'inbox-for-woocommerce'),
				'wcs_setup_guest_user' => esc_html__('Guest User', 'inbox-for-woocommerce'),
				'wcs_setup_guest_thankyou' => esc_html__('Thank you for reaching out. We would get back to you as soon as possible.', 'inbox-for-woocommerce'),
				'wcs_setup_guest_needhelp' =>  esc_html__('Need help?', 'inbox-for-woocommerce'),
				'wcs_setup_guest_not_sure' => esc_html__('Not sure it matches your specification?', 'inbox-for-woocommerce'),
				'wcs_setup_guest_ask_seller' => esc_html__('Ask the Seller', 'inbox-for-woocommerce'),

				'wcs_upload_nonce' => wp_create_nonce('ajax-wcs-upload-nonce'),
				'wcs_setup_nonce' => wp_create_nonce('ajax-wcs-setup-nonce'),
				'wcs_thread_nonce' => wp_create_nonce('ajax-wcs-thread-nonce'),
				) );
		}
	
		public function front_enqueue() {
			require_once plugin_dir_path( __FILE__ ) . 'includes/SettingController.php';
	
			// enqueue all our scripts
			wp_enqueue_style( 'woocommercesweitostyle', plugins_url( '/assets/css/woocommerce-helpdesk-sweito.css', __FILE__ ), [], '1.0.4' );
			wp_enqueue_script( 'woocommercesweitoscript', plugins_url( '/assets/js/woocommerce-helpdesk-sweito.js', __FILE__ ), [], '1.0.4' );
	
			wp_localize_script( 'woocommercesweitoscript', 'woocommercesweitoscript_object', array( 
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'pdf_logo' => IBXFWL_HELPDESK_ASSETS_URL . '/images/pdf-icon.png',
				'recaptcha_key' =>  IBXFWL_Inbox_SettingController::defaultGoogleRecaptchaSiteKey(),
				'wcs_please_wait' => esc_html__('Please wait ...', 'inbox-for-woocommerce'),
				'wcs_just_now' => esc_html__('Just Now', 'inbox-for-woocommerce'),

				'wcs_upload_nonce' => wp_create_nonce('ajax-wcs-upload-nonce'),
				'wcs_thread_nonce' => wp_create_nonce('ajax-wcs-thread-nonce'),
				) );
		}
	
		public function activate() {
			require_once plugin_dir_path( __FILE__ ) . 'includes/ActivateController.php';
			IBXFWL_Inbox_ActivateController::activate();
		}
		public function add_cta_box_beneath_product() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			global $product;
			require_once plugin_dir_path( __FILE__ ) . 'templates/ProductAddOn.php';
			require_once plugin_dir_path( __FILE__ ) . 'includes/SettingController.php';
			require_once plugin_dir_path( __FILE__ ) . 'includes/SetupController.php';
	
			// if setup is imcomplete
			if ( !IBXFWL_Inbox_SetupController::checkInstallationStatus() ) {
return;
			}
	
			// setup personalization
			$showInquiryCTAButton = IBXFWL_Inbox_SettingController::defaultInquiryCTAStatus();
			if (!$showInquiryCTAButton) {
return;
			}
	
			IBXFWL_Inbox_ProductAddOn::showCTAButtonBeneathProductPage($product);
		}
	
		public function add_account_inbox_tab( $items ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/SettingController.php';
			require_once plugin_dir_path( __FILE__ ) . 'includes/SetupController.php';
	
			// if setup is imcomplete
			if ( !IBXFWL_Inbox_SetupController::checkInstallationStatus() ) {
return $items;
			}
	
			// setup personalization
			$customerInboxStatus = IBXFWL_Inbox_SettingController::defaultCustomerInboxStatus();
			if ( !$customerInboxStatus ) {
return $items;
			}
	
			$newItems = [];
			$count = 0;
			foreach ($items as $slug => $item) {
				if (( count($items) - 1 ) == $count) {
					$newItems['woocommerce_sweito_inbox'] = esc_html__('Inbox', 'inbox-for-woocommerce');
				}
				$newItems[$slug] = $item;
				$count++;
			}
			
			return $newItems;
		}
	
		public function add_inbox_setting_tab( $setting_tabs) {
			$newItems = [];
			$count = 0;
			foreach ($setting_tabs as $slug => $item) {
				if (( count($setting_tabs) - 1 ) == $count) {
					$newItems['wcs_inbox'] = esc_html__('Inbox', 'inbox-for-woocommerce');
				}
				$newItems[$slug] = $item;
				$count++;
			}
			
			return $newItems;
		}
	
		public function add_inbox_setting_sections_tab( $sections) {
			global $current_section;
			$tab_id = 'wcs_inbox';
			// Must contain more than one section to display the links
			// Make first element's key empty ('')
			$sections = array(
				''              => esc_html__( 'General', 'inbox-for-woocommerce' ),
				'theme'  => esc_html__( 'Theme', 'inbox-for-woocommerce' ),
				'security'  => esc_html__( 'Security', 'inbox-for-woocommerce' ),
				'third_party'  => esc_html__( 'Third-Party', 'inbox-for-woocommerce' ),
				'notifications'  => esc_html__( 'Notifications', 'inbox-for-woocommerce' )
			);
			echo '<ul class="subsubsub">';
			$array_keys = array_keys( $sections );
			foreach ( $sections as $id => $label ) {
				echo '<li><a href="' . esc_html(admin_url( 'admin.php?page=wc-settings&tab=' . $tab_id . '&section=' . sanitize_title( $id ) )) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . esc_html($label) . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
			}
			echo '</ul><br class="clear" />';
		}
	
		// Settings function
		public function get_inbox_custom_settings() {
			require_once plugin_dir_path( __FILE__ ) . 'includes/admin/settings/SettingFields.php';
	
			global $current_section;
			$settings = array();
			if ( 'theme' == $current_section ) {
				$settings = IBXFWL_Inbox_SettingFields::showThemeTabFields();
			} elseif ( 'security' == $current_section ) {
				$settings = IBXFWL_Inbox_SettingFields::showSecurityTabFields();
			} elseif ( 'third_party' == $current_section ) {
				$settings = IBXFWL_Inbox_SettingFields::showThirdPartyTabFields();
			} elseif ( 'notifications' == $current_section ) {
				$settings = IBXFWL_Inbox_SettingFields::showNotificationTabFields();
			} else {
				$settings = IBXFWL_Inbox_SettingFields::showGeneralTabFields();
			}
			return $settings;
		}
	
		public function wcs_add_admin_field_button( $value) {
			$option_value = (array) WC_Admin_Settings::get_option( $value['id'] );
			$description = WC_Admin_Settings::get_field_description( $value );
			
			?>
			
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
					<?php echo  esc_html($description['tooltip_html']); ?>
				</th>
				
				<td class="forminp forminp-<?php echo esc_html(sanitize_title( $value['type'] )); ?>">
					<a target="_blank" href="<?php echo esc_attr( $value['href'] ); ?>">
						<button 
						style="<?php echo esc_attr( $value['css'] ); ?>" 
						id="<?php echo esc_attr( $value['id'] ); ?>" 
						class="button <?php echo esc_attr( $value['class'] ); ?>">
							<?php echo esc_attr( $value['desc'] ); ?>
						</button>
					</a>                
				</td>
			</tr>
	
			<?php
		}

		public function wcs_add_admin_field_checkbox_disabled( $value) {
			$option_value = (array) WC_Admin_Settings::get_option( $value['id'] );
			$description = WC_Admin_Settings::get_field_description( $value );
			
			?>
			
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				</th>
				
				<td class="forminp forminp-<?php echo esc_html(sanitize_title( $value['type'] )); ?>">
					<input type="checkbox" disabled="disabled" /> <?php echo esc_attr( $value['desc'] ); ?>            
				</td>
			</tr>
	
			<?php
		}

		public function wcs_add_admin_field_input_disabled( $value) {
			$option_value = (array) WC_Admin_Settings::get_option( $value['id'] );
			$description = WC_Admin_Settings::get_field_description( $value );
			
			?>
			
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				</th>
				
				<td class="forminp forminp-<?php echo esc_html(sanitize_title( $value['type'] )); ?>">
					<input type="text" value="<?php echo esc_attr( $value['desc'] ); ?>" disabled="disabled" />             
				</td>
			</tr>
	
			<?php
		}

		public function wcs_add_admin_field_input_display( $value) {
			$option_value = (array) WC_Admin_Settings::get_option( $value['id'] );
			$description = WC_Admin_Settings::get_field_description( $value );
			
			?>
			
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"></label>
				</th>
				
				<td class="forminp forminp-<?php echo esc_html(sanitize_title( $value['type'] )); ?>">
					<div style="background-color: #a7aaad99; padding: 10px; width: fit-content;">
						<div style="margin-bottom: 10px;"><?php echo esc_html__('Unlock new chat themes', 'inbox-for-woocommerce') ?>, <a target="_blank" href="<?php echo IBXFWL_SWEITO_PRODUCT_URL; ?>"><?php echo esc_html__('upgrade to full version', 'inbox-for-woocommerce'); ?></a></div>
						<img style="max-width: 200px; height: 110px" src="<?php echo IBXFWL_HELPDESK_ASSETS_URL . '/images/chat-style-2.png'; ?>" />             
						<img style="max-width: 200px; height: 110px" src="<?php echo IBXFWL_HELPDESK_ASSETS_URL . '/images/chat-style-3.png'; ?>" />  
					</div>           
				</td>
			</tr>
	
			<?php
		}
	
		public function add_inbox_setting_sections_settings_save() {
			global $current_section;
			$tab_id = 'wcs_inbox';
			// Call settings function
			$settings = $this->get_inbox_custom_settings();
			WC_Admin_Settings::save_fields( $settings );
	
			if ( $current_section ) {
				/**
				 * Add hook for save action in admin
				 * 
				 * @since version 1.0.4
				 */
				do_action( 'woocommerce_update_options_' . $tab_id . '_' . $current_section );
			}
		}
	
		public function add_inbox_setting_sections_settings_tab() {
			// if plugin is not setup, to setup wizard
			if ( !IBXFWL_Inbox_SetupController::checkInstallationStatus() ) {
				wp_redirect( admin_url('admin.php?page=woocommerce-inbox-setup') );
				exit;
			}
	
			// Call settings function
			$settings = $this->get_inbox_custom_settings();
			WC_Admin_Settings::output_fields( $settings );
		}
	
		public function woocommerce_account_inbox_page() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			require_once plugin_dir_path( __FILE__ ) . 'templates/UserMyAccountInbox.php';
			require_once plugin_dir_path( __FILE__ ) . 'includes/SettingController.php';
			require_once plugin_dir_path( __FILE__ ) . 'includes/SetupController.php';
	
			// if setup is imcomplete
			if ( !IBXFWL_Inbox_SetupController::checkInstallationStatus() ) {
return $items;
			}
	
			$customerInboxStatus = IBXFWL_Inbox_SettingController::defaultCustomerInboxStatus();
			if ( !$customerInboxStatus ) {
return;
			}
	
			IBXFWL_Inbox_UserMyAccountInbox::showTickets();
		}
	}
}
