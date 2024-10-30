<?php
/**
 * Admin WooCommerce Settings Page
 * 
 * @package Inbox-For-WooCommerce-LTE
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class IBXFWL_Inbox_SettingFields {
	/**
	 * Show Notification Tab Fields
	 *
	 * @return array
	 */
	public static function showNotificationTabFields() {
		require_once(IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php');

		return array(
			// Title
			array(
				'title'     => esc_html__( 'Notification Settings', 'inbox-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'custom_settings_1'
			),
			// Text
			array(
				'title'     => esc_html__( 'Send Email to Admin', 'inbox-for-woocommerce' ),
				'type'      => 'checkbox',
				'desc'      => esc_html__( 'When a new inquiry message is received', 'inbox-for-woocommerce' ),
				'desc_tip'  => false,
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_NOTIFICATION_WHEN_INQUIRY_RECEIVED,
				'id'        => 'wcs_notification_when_inquiry_received',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( 'Send Email to Admin', 'inbox-for-woocommerce' ),
				'type'      => 'checkbox',
				'desc'      => esc_html__( 'When a new inbox message is received', 'inbox-for-woocommerce' ),
				'desc_tip'  => false,
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_NOTIFICATION_WHEN_INBOX_RECEIVED,
				'id'        => 'wcs_notification_when_inbox_received',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( 'Send Email to Admin', 'inbox-for-woocommerce' ),
				'type'      => 'checkbox',
				'desc'      => esc_html__( 'When a customer replies inbox thread', 'inbox-for-woocommerce' ),
				'desc_tip'  => false,
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_NOTIFICATION_WHEN_CUSTOMER_REPLY_INBOX_RECEIVED,
				'id'        => 'wcs_notification_when_inbox_replied',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( 'Send Email to Customer', 'inbox-for-woocommerce' ),
				'type'      => 'checkbox',
				'desc'      => esc_html__( 'When admin replied to an inquiry message received', 'inbox-for-woocommerce' ),
				'desc_tip'  => false,
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_NOTIFICATION_WHEN_ADMIN_REPLY_INQUIRY_RECEIVED,
				'id'        => 'wcs_notification_when_admin_reply_inquiry',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( 'Send Email to Customer', 'inbox-for-woocommerce' ),
				'type'      => 'checkbox',
				'desc'      => esc_html__( 'When admin replied to inbox message received', 'inbox-for-woocommerce' ),
				'desc_tip'  => false,
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_NOTIFICATION_WHEN_ADMIN_REPLY_INBOX_RECEIVED,
				'id'        => 'wcs_notification_when_admin_reply_inbox',
				'css'       => 'min-width:300px;'
			),
			// Section end
			array(
				'type'      => 'sectionend',
				'id'        => 'custom_settings_2'
			),
		);
	}

	/**
	 * Show General Tab Fields
	 *
	 * @return array
	 */
	public static function showGeneralTabFields() {
		require_once(IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php');

		return array(
			// Title
			array(
				'title'     => esc_html__( 'General Settings', 'inbox-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'wcs_general_settings_1'
			),
			// Inquiry
			array(
				'title'     => esc_html__( 'Where do you want to manage inbox messages?', 'inbox-for-woocommerce' ),
				'desc'      => esc_html__( 'Where inbox messages would be sent too and vice versa', 'inbox-for-woocommerce' ),
				'id'        => 'wcs_manage_inbox_location',
				'class'     => 'wc-enhanced-select',
				'css'       => 'min-width:300px;',
				'type'      => 'select',
				'options'   => array(
					'wpadmin'        => esc_html__( 'Wordpress Admin', 'inbox-for-woocommerce' ),
					'zendesk'        => esc_html__( 'Zendesk', 'inbox-for-woocommerce' ),
					'freshdesk'        => esc_html__( 'Freshdesk', 'inbox-for-woocommerce' ),
				),
				'desc_tip' => true,
			),
			array(
				'title'     => esc_html__( 'Allow inquiry button?', 'inbox-for-woocommerce' ),
				'type'      => 'wcs_checkbox_disabled',
				'desc'      => esc_html__( 'This feature available on the full version only', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_allow',
				'css'       => 'min-width:300px;',
				'disabled'	=> 'disabled'
			),
			array(
				'title'     => esc_html__( 'Allow CTA section on product page?', 'inbox-for-woocommerce' ),
				'type'      => 'checkbox',
				'desc'      => esc_html__( 'Do you want to allow display of Call-To-Action underneath listing', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_allow_inquiry_cta',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( 'Allow Customer "My Account" inbox section?', 'inbox-for-woocommerce' ),
				'type'      => 'checkbox',
				'desc'      => esc_html__( 'Do you want to allow customers to be able to send inbox messages from the "My Account" page', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_allow_customer_myaccount_inbox',
				'css'       => 'min-width:300px;'
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'wcs_general_settings_1'
			),
			array(
				'title'     => esc_html__( 'Allowed Inbox Message Types', 'inbox-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'wcs_general_settings_2'
			),
			array(
				'title'     => esc_html__( '- "General" type?', 'inbox-for-woocommerce' ),
				'type'      => 'checkbox',
				'desc_tip'  => false,
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_GENERAL_INBOX_MESSAGE_TYPE,
				'id'        => 'wcs_allow_general_inbox_message_type',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( '- "Product Related" type?', 'inbox-for-woocommerce' ),
				'type'      => 'checkbox',
				'desc_tip'  => false,
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_PRODUCT_RELATED_INBOX_MESSAGE_TYPE,
				'id'        => 'wcs_allow_product_related_inbox_message_type',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( '- "Order Related" type?', 'inbox-for-woocommerce' ),
				'type'      => 'wcs_checkbox_disabled',
				'desc'      => esc_html__( 'This feature available on the full version only', 'inbox-for-woocommerce' ),
				'desc_tip'  => false,
				// 'default'   => IBXFWL_Inbox_SettingController::DEFAULT_ORDER_RELATED_INBOX_MESSAGE_TYPE,
				'id'        => 'wcs_allow',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( '- "Refund Request" type?', 'inbox-for-woocommerce' ),
				'type'      => 'wcs_checkbox_disabled',
				'desc'      => esc_html__( 'This feature available on the full version only', 'inbox-for-woocommerce' ),
				'desc_tip'  => false,
				// 'default'   => IBXFWL_Inbox_SettingController::DEFAULT_REFUND_INBOX_MESSAGE_TYPE,
				'id'        => 'wcs_allow',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( '- "Dispute" type?', 'inbox-for-woocommerce' ),
				'type'      => 'wcs_checkbox_disabled',
				'desc'      => esc_html__( 'This feature available on the full version only', 'inbox-for-woocommerce' ),
				'desc_tip'  => false,
				// 'default'   => IBXFWL_Inbox_SettingController::DEFAULT_DISPUTE_INBOX_MESSAGE_TYPE,
				'id'        => 'wcs_allow',
				'css'       => 'min-width:300px;'
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'wcs_general_settings_2'
			),
			array(
				'title'     => esc_html__( 'Response Message Settings', 'inbox-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'wcs_general_settings_3'
			),
			array(
				'title'     => esc_html__( 'Once submitted inquiry form on product', 'inbox-for-woocommerce' ),
				'type'      => 'textarea',
				'desc'      => esc_html__( 'Do you want to allow customer/guest users ask questions about listing', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'default'   => esc_html__(IBXFWL_Inbox_SettingController::DEFAULT_INQUIRY_SENT_RESPONSE, 'inbox-for-woocommerce'),
				'id'        => 'wcs_submitted_inquiry_form_response',
				'css'       => 'min-width:300px;'
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'wcs_general_settings_3'
			),
		);
	}

	/**
	 * Show Third Party Tab Fields
	 *
	 * @return array
	 */
	public static function showThirdPartyTabFields() {
		return array(
			// Title
			array(
				'title'     => esc_html__( 'Sweito Integration', 'inbox-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'custom_settings_1'
			),
			// Text
			array(
				'title'     => esc_html__( 'Sweito App Token', 'inbox-for-woocommerce' ),
				'type'      => 'password',
				'desc'      => esc_html__( 'Sweito app token generated from https://sweito.com', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_sweito_app_token',
				'css'       => 'min-width:300px;'
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'custom_settings_1'
			)
		);
	}

	/**
	 * Show Security Tab Fields
	 *
	 * @return void
	 */
	public static function showSecurityTabFields() {
		return array(
			// Title
			array(
				'title'     => esc_html__( 'Google Recaptcha Settings (v3)', 'inbox-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'custom_settings_2'
			),
			// Text
			array(
				'title'     => esc_html__( 'Google Recaptcha Site Key', 'inbox-for-woocommerce' ),
				'type'      => 'text',
				'desc'      => esc_html__( 'Your public key as given from google', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_google_recaptcha_site_key',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( 'Google Recaptcha Secret Key', 'inbox-for-woocommerce' ),
				'type'      => 'password',
				'desc'      => esc_html__( 'Your secret key as given from google', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_google_recaptcha_secret_key',
				'css'       => 'min-width:300px;'
			),
			// Section end
			array(
				'type'      => 'sectionend',
				'id'        => 'custom_settings_2'
			),
			array(
				'title'     => esc_html__( 'User Access Control Settings', 'inbox-for-woocommerce' ),
				'desc'      => esc_html__( 'Only users with role "WooCommerce Inbox Agent" & "Administrator" can be assigned tickets', 'inbox-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'control_settings_3'
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'custom_settings_3'
			)
		);
	}

	/**
	 * Show Theme Tab Fields
	 *
	 * @return array
	 */
	public static function showThemeTabFields() {
		require_once(IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php');
		return array(
			// Title
			array(
				'title'     => esc_html__( 'Theme Settings', 'inbox-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'wcs_theme_settings_1'
			),
			array(
				'title'     => esc_html__( 'Select ticket chat style to use?', 'inbox-for-woocommerce' ),
				'desc'      => esc_html__( 'Get more theme choice in the full version', 'inbox-for-woocommerce' ),
				'id'        => 'wcs_ticket_thread_style',
				'class'     => 'wc-enhanced-select',
				'css'       => 'min-width:300px;',
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_TICKET_THREAD_STYLE,
				'type'      => 'select',
				'options'   => array(
					'style-1'        => esc_html__( 'Default Style', 'inbox-for-woocommerce' ),
				),
				'desc_tip' => false,
			),
			array(
				'title'     => esc_html__( 'Inquiry Button Text', 'inbox-for-woocommerce' ),
				'type'      => 'wcs_input_display',
				'default'   => 'Got a question?',
				'desc'      => esc_html__( 'Default', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_allow',
				'css'       => 'min-width:300px;'
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'wcs_theme_settings_1'
			),
			array(
				'title'     => esc_html__( 'Inquiry Button Display Settings', 'inbox-for-woocommerce' ),
				'type'      => 'title',
				'desc'      => esc_html__( 'Skip this section if you didn\'t "allow inquiry button" in the General section', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_theme_settings_2'
			),
			array(
				'title'     => esc_html__( 'Inquiry Button Text', 'inbox-for-woocommerce' ),
				'type'      => 'wcs_input_disabled',
				'default'   => 'Got a question?',
				'desc'      => esc_html__( 'Default', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_allow',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( 'Select button type?', 'inbox-for-woocommerce' ),
				'desc'      => esc_html__( 'outlined-button', 'inbox-for-woocommerce' ),
				'id'        => 'wcs_inquiry_button_type',
				'class'     => 'wc-enhanced-select',
				'css'       => 'min-width:300px;',
				// 'default'   => IBXFWL_Inbox_SettingController::DEFAULT_INQUIRY_BUTTON_TYPE,
				'type'      => 'wcs_input_disabled',
				'desc_tip' => true,
			),
			array(
				'title'     => esc_html__( 'Button Color', 'inbox-for-woocommerce' ),
				'type'      => 'wcs_input_disabled',
				// 'default'   => IBXFWL_Inbox_SettingController::DEFAULT_INQUIRY_BUTTON_COLOR,
				'desc'      => esc_html__( 'Default', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_inquiry_button_color',
				'css'       => ''
			),
			array(
				'title'     => esc_html__( 'Button Text Color', 'inbox-for-woocommerce' ),
				'type'      => 'wcs_input_disabled',
				'default'   => esc_html__(IBXFWL_Inbox_SettingController::DEFAULT_INQUIRY_BUTTON_TEXT_COLOR, 'inbox-for-woocommerce' ),
				'desc'      => esc_html__( 'Default', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_inquiry_button_text_color',
				'css'       => ''
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'wcs_theme_settings_2'
			),
			array(
				'title'     => esc_html__( 'Inquiry CTA Display Settings', 'inbox-for-woocommerce' ),
				'type'      => 'title',
				'desc'      => esc_html__( 'Skip this section if you didn\'t "allow CTA button" in the General section', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_theme_settings_3'
			),
			array(
				'title'     => esc_html__( 'CTA Background Style', 'inbox-for-woocommerce' ),
				'desc'      => esc_html__( 'Do you want button icon displayed?', 'inbox-for-woocommerce' ),
				'id'        => 'wcs_inquiry_cta_background_type',
				'class'     => 'wc-enhanced-select',
				'css'       => 'min-width:300px;',
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_INQUIRY_CTA_BACKGROUND_TYPE,
				'type'      => 'select',
				'options'   => array(
					'color-filled'        => esc_html__( 'Color-Filled', 'inbox-for-woocommerce' ),
					'background-image'        => esc_html__( 'Background-Image', 'inbox-for-woocommerce' ),
				),
				'desc_tip' => true,
			),
			array(
				'title'     => esc_html__( 'CTA Description Text', 'inbox-for-woocommerce' ),
				'type'      => 'text',
				'default'   => esc_html__(IBXFWL_Inbox_SettingController::DEFAULT_INQUIRY_CTA_TEXT, 'inbox-for-woocommerce' ),
				'desc'      => esc_html__( 'The text displayed on CTA Banner', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_inquiry_cta_description_text',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( 'CTA Button Text', 'inbox-for-woocommerce' ),
				'type'      => 'text',
				'default'   => esc_html__(IBXFWL_Inbox_SettingController::DEFAULT_INQUIRY_CTA_BUTTON_TEXT, 'inbox-for-woocommerce' ),
				'desc'      => esc_html__( 'The text in the CTA Button', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_inquiry_cta_button_text',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( 'CTA Background Color', 'inbox-for-woocommerce' ),
				'type'      => 'color',
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_INQUIRY_CTA_BACKGROUND_COLOR,
				'desc'      => esc_html__( 'The background color of the CTA Banner (skip if "CTA Background Style" is not "Color-Filled")', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_inquiry_cta_background_color',
				'css'       => 'min-width:300px;'
			),
			// http://localhost:8200/wp-content/uploads/2022/10/magnet-me-LDcC7aCWVlo-unsplash.jpg
			array(
				'title'     => esc_html__( 'CTA Background Image Link', 'inbox-for-woocommerce' ),
				'type'      => 'text',
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_INQUIRY_CTA_BACKGROUND_LINK,
				'desc'      => esc_html__( 'The background color of the CTA Banner (skip if "CTA Background Style" is not "Background-Image")', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_inquiry_cta_background_link',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( 'CTA Text Color', 'inbox-for-woocommerce' ),
				'type'      => 'color',
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_INQUIRY_CTA_TEXT_COLOR,
				'desc'      => esc_html__( 'The color of the CTA Banner Text', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_inquiry_cta_text_color',
				'css'       => 'min-width:300px;'
			),
			array(
				'title'     => esc_html__( 'CTA Button Color', 'inbox-for-woocommerce' ),
				'type'      => 'color',
				'default'   => IBXFWL_Inbox_SettingController::DEFAULT_INQUIRY_CTA_BUTTON_COLOR,
				'desc'      => esc_html__( 'The color of the button in CTA Banner', 'inbox-for-woocommerce' ),
				'desc_tip'  => true,
				'id'        => 'wcs_inquiry_cta_button_color',
				'css'       => 'min-width:300px;'
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'wcs_theme_settings_3'
			),
		);
	}
}
