<?php
/**
 * Settings controller for managing settings used all through extension
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_SettingController' ) ) {
	class IBXFWL_Inbox_SettingController {

		const SETTING_GUEST_NAME = 'woocommerce_inbox_guest_name';
		const SETTING_INQUIRY_SENT_RESPONSE = 'wcs_submitted_inquiry_form_response';
		const SETTING_INBOX_SENT_RESPONSE = 'woocommerce_inbox_inbox_sent_response';
		const SETTING_CUSTOMER_INBOX_STATUS = 'wcs_allow_customer_myaccount_inbox';
		const SETTING_INQUIRY_CTA_STATUS = 'wcs_allow_inquiry_cta';
		const SETTING_INQUIRY_CTA_TYPE = 'woocommerce_inbox_inquiry_cta_type';
		const SETTING_INQUIRY_CTA_TEXT = 'wcs_inquiry_cta_description_text';
		const SETTING_INQUIRY_CTA_TEXT_COLOR = 'wcs_inquiry_cta_text_color';
		const SETTING_INQUIRY_CTA_BACKGROUND_COLOR = 'wcs_inquiry_cta_background_color';
		const SETTING_INQUIRY_CTA_BACKGROUND_LINK = 'wcs_inquiry_cta_background_link';
		const SETTING_INQUIRY_CTA_BUTTON_TEXT = 'wcs_inquiry_cta_button_text';
		const SETTING_INQUIRY_CTA_BUTTON_COLOR = 'wcs_inquiry_cta_button_color';
		const SETTING_INQUIRY_CTA_BACKGROUND_TYPE = 'wcs_inquiry_cta_background_type';
		const SETTING_TICKET_THREAD_STYLE = 'wcs_ticket_thread_style';
		const SETTING_GENERAL_INBOX_MESSAGE_TYPE = 'wcs_allow_general_inbox_message_type';
		const SETTING_PRODUCT_RELATED_INBOX_MESSAGE_TYPE = 'wcs_allow_product_related_inbox_message_type';
		const SETTING_ORDER_RELATED_INBOX_MESSAGE_TYPE = 'wcs_allow_order_related_inbox_message_type';
		const SETTING_REFUND_INBOX_MESSAGE_TYPE = 'wcs_allow_refund_inbox_message_type';
		const SETTING_DISPUTE_INBOX_MESSAGE_TYPE = 'wcs_allow_dispute_inbox_message_type';
		const SETTING_SECURITY_GOOGLE_RECAPTCHA_SITE_KEY = 'wcs_google_recaptcha_site_key';
		const SETTING_SECURITY_GOOGLE_RECAPTCHA_SECRET_KEY = 'wcs_google_recaptcha_secret_key';
		const SETTING_THIRDPARTY_SWEITO_APP_TOKEN = 'wcs_sweito_app_token';
		const SETTING_THIRDPARTY_SWEITO_APP_SITE = 'wcs_sweito_app_site';
		const SETTING_THIRDPARTY_SWEITO_APP_EMAIL = 'wcs_sweito_app_email';
		const SETTING_THIRDPARTY_SWEITO_ACCESS_TOKEN = 'wcs_sweito_access_token';
		const SETTING_THIRDPARTY_SWEITO_REFERENCE = 'wcs_sweito_reference';
		const SETTING_THIRDPARTY_SWEITO_HELPDESK_STATUS = 'wcs_sweito_helpdesk_status';
		const SETTING_GENERAL_TICKET_LOCATION = 'wcs_manage_inbox_location';
		const SETTING_NOTIFICATION_WHEN_INQUIRY_RECEIVED = 'wcs_notification_when_inquiry_received';
		const SETTING_NOTIFICATION_WHEN_INBOX_RECEIVED = 'wcs_notification_when_inbox_received';
		const SETTING_NOTIFICATION_WHEN_ADMIN_REPLY_INQUIRY_RECEIVED = 'wcs_notification_when_admin_reply_inquiry';
		const SETTING_NOTIFICATION_WHEN_ADMIN_REPLY_INBOX_RECEIVED = 'wcs_notification_when_admin_reply_inbox';
		const SETTING_NOTIFICATION_WHEN_CUSTOMER_REPLY_INBOX_RECEIVED = 'wcs_notification_when_inbox_replied';
		const SETTING_UPDATE_LAST_CHECKED = 'wcs_update_last_checked';
		const SETTING_UPDATE_STATE = 'wcs_update_last_state';

		const DEFAULT_GUEST_NAME = 'Guest User';
		const DEFAULT_INQUIRY_SENT_RESPONSE = 'Thank you for reaching out. We would get back to you as soon as possible.';
		const DEFAULT_INBOX_SENT_RESPONSE = 'Thank you for reaching out. We would get back to you as soon as possible.';
		const DEFAULT_CUSTOMER_INBOX_STATUS = 'yes';
		const DEFAULT_INQUIRY_CTA_STATUS = 'yes';
		const DEFAULT_INQUIRY_CTA_TYPE = 'plain';
		const DEFAULT_INQUIRY_CTA_TEXT = 'Not sure it matches your specification?';
		const DEFAULT_INQUIRY_CTA_TEXT_COLOR = '#ffffff';

		const DEFAULT_INQUIRY_BUTTON_TEXT_COLOR = '#fff';
		const DEFAULT_INQUIRY_CTA_BACKGROUND_COLOR = '#007cba';
		const DEFAULT_INQUIRY_CTA_BACKGROUND_LINK = IBXFWL_HELPDESK_ASSETS_URL . '/images/default-cta-background.jpg';
		const DEFAULT_INQUIRY_CTA_BUTTON_TEXT = 'Ask the Seller';
		const DEFAULT_INQUIRY_CTA_BUTTON_COLOR = '#ffffff';
		const DEFAULT_INQUIRY_CTA_BACKGROUND_TYPE = 'background-image';
		const DEFAULT_TICKET_THREAD_STYLE = 'style-1';
		const DEFAULT_GENERAL_INBOX_MESSAGE_TYPE = 'yes';
		const DEFAULT_PRODUCT_RELATED_INBOX_MESSAGE_TYPE = 'yes';
		const DEFAULT_ORDER_RELATED_INBOX_MESSAGE_TYPE = 'yes';
		const DEFAULT_REFUND_INBOX_MESSAGE_TYPE = 'yes';
		const DEFAULT_DISPUTE_INBOX_MESSAGE_TYPE = 'yes';
		const DEFAULT_SECURITY_GOOGLE_RECAPTCHA_SITE_KEY = '';
		const DEFAULT_SECURITY_GOOGLE_RECAPTCHA_SECRET_KEY = '';
		const DEFAULT_THIRDPARTY_SWEITO_APP_TOKEN = '';
		const DEFAULT_THIRDPARTY_SWEITO_APP_SITE = '';
		const DEFAULT_THIRDPARTY_SWEITO_APP_EMAIL = '';
		const DEFAULT_THIRDPARTY_SWEITO_ACCESS_TOKEN = '';
		const DEFAULT_THIRDPARTY_SWEITO_REFERENCE = '';
		const DEFAULT_THIRDPARTY_SWEITO_HELPDESK_STATUS = '';
		const DEFAULT_GENERAL_TICKET_LOCATION = 'wpadmin';
		const DEFAULT_NOTIFICATION_WHEN_INQUIRY_RECEIVED = 'yes';
		const DEFAULT_NOTIFICATION_WHEN_INBOX_RECEIVED = 'yes';
		const DEFAULT_NOTIFICATION_WHEN_ADMIN_REPLY_INQUIRY_RECEIVED = 'yes';
		const DEFAULT_NOTIFICATION_WHEN_ADMIN_REPLY_INBOX_RECEIVED = 'yes';
		const DEFAULT_NOTIFICATION_WHEN_CUSTOMER_REPLY_INBOX_RECEIVED = 'yes';

		public static function getSavedOptionNames() {
			return [
				self::SETTING_GUEST_NAME,
				self::SETTING_INQUIRY_SENT_RESPONSE,
				self::SETTING_INBOX_SENT_RESPONSE,
				self::SETTING_CUSTOMER_INBOX_STATUS,
				self::SETTING_INQUIRY_CTA_STATUS,
				self::SETTING_INQUIRY_CTA_TYPE,
				self::SETTING_INQUIRY_CTA_TEXT,
				self::SETTING_INQUIRY_CTA_TEXT_COLOR,
				self::SETTING_INQUIRY_CTA_BACKGROUND_COLOR,
				self::SETTING_INQUIRY_CTA_BACKGROUND_LINK,
				self::SETTING_INQUIRY_CTA_BUTTON_TEXT,
				self::SETTING_INQUIRY_CTA_BUTTON_COLOR,
				self::SETTING_INQUIRY_CTA_BACKGROUND_TYPE,
				self::SETTING_TICKET_THREAD_STYLE,
				self::SETTING_GENERAL_INBOX_MESSAGE_TYPE,
				self::SETTING_PRODUCT_RELATED_INBOX_MESSAGE_TYPE,
				self::SETTING_ORDER_RELATED_INBOX_MESSAGE_TYPE,
				self::SETTING_REFUND_INBOX_MESSAGE_TYPE,
				self::SETTING_DISPUTE_INBOX_MESSAGE_TYPE,
				self::SETTING_SECURITY_GOOGLE_RECAPTCHA_SITE_KEY,
				self::SETTING_SECURITY_GOOGLE_RECAPTCHA_SECRET_KEY,
				self::SETTING_THIRDPARTY_SWEITO_APP_TOKEN,
				self::SETTING_THIRDPARTY_SWEITO_APP_SITE,
				self::SETTING_THIRDPARTY_SWEITO_APP_EMAIL,
				self::SETTING_THIRDPARTY_SWEITO_ACCESS_TOKEN,
				self::SETTING_THIRDPARTY_SWEITO_REFERENCE,
				self::SETTING_THIRDPARTY_SWEITO_HELPDESK_STATUS,
				self::SETTING_GENERAL_TICKET_LOCATION,
				self::SETTING_NOTIFICATION_WHEN_INQUIRY_RECEIVED,
				self::SETTING_NOTIFICATION_WHEN_INBOX_RECEIVED,
				self::SETTING_NOTIFICATION_WHEN_ADMIN_REPLY_INQUIRY_RECEIVED,
				self::SETTING_NOTIFICATION_WHEN_ADMIN_REPLY_INBOX_RECEIVED,
				self::SETTING_NOTIFICATION_WHEN_CUSTOMER_REPLY_INBOX_RECEIVED,
				self::SETTING_UPDATE_LAST_CHECKED,
				self::SETTING_UPDATE_STATE,
			];
		}

		public static function defaultTicketLocation() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_GENERAL_TICKET_LOCATION );
			$buttonIconStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_GENERAL_TICKET_LOCATION;

			if ( $buttonIconStatus ) {
return $buttonIconStatus;
			}

			return self::DEFAULT_GENERAL_TICKET_LOCATION;
		}

		public static function defaultNotificationWhenCustomerReplyInbox() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_NOTIFICATION_WHEN_CUSTOMER_REPLY_INBOX_RECEIVED );
			$buttonStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_NOTIFICATION_WHEN_CUSTOMER_REPLY_INBOX_RECEIVED;

			if ( $buttonStatus ) {
return 'yes' == $buttonStatus ? true : false;
			}

			return self::DEFAULT_NOTIFICATION_WHEN_CUSTOMER_REPLY_INBOX_RECEIVED == 'yes' ? true : false;
		}

		public static function defaultNotificationWhenAdminReplyInbox() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_NOTIFICATION_WHEN_ADMIN_REPLY_INBOX_RECEIVED );
			$buttonStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_NOTIFICATION_WHEN_ADMIN_REPLY_INBOX_RECEIVED;

			if ( $buttonStatus ) {
return 'yes' == $buttonStatus ? true : false;
			}

			return self::DEFAULT_NOTIFICATION_WHEN_ADMIN_REPLY_INBOX_RECEIVED == 'yes' ? true : false;
		}

		public static function defaultNotificationWhenAdminReplyInquiry() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_NOTIFICATION_WHEN_ADMIN_REPLY_INQUIRY_RECEIVED );
			$buttonStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_NOTIFICATION_WHEN_ADMIN_REPLY_INQUIRY_RECEIVED;

			if ( $buttonStatus ) {
return 'yes' == $buttonStatus ? true : false;
			}

			return self::DEFAULT_NOTIFICATION_WHEN_ADMIN_REPLY_INQUIRY_RECEIVED == 'yes' ? true : false;
		}

		public static function defaultNotificationWhenInboxReceived() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_NOTIFICATION_WHEN_INBOX_RECEIVED );
			$buttonStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_NOTIFICATION_WHEN_INBOX_RECEIVED;

			if ( $buttonStatus ) {
return 'yes' == $buttonStatus ? true : false;
			}

			return self::DEFAULT_NOTIFICATION_WHEN_INBOX_RECEIVED == 'yes' ? true : false;
		}

		public static function defaultNotificationWhenInquiryReceived() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_NOTIFICATION_WHEN_INQUIRY_RECEIVED );
			$buttonStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_NOTIFICATION_WHEN_INQUIRY_RECEIVED;

			if ( $buttonStatus ) {
return 'yes' == $buttonStatus ? true : false;
			}

			return self::DEFAULT_NOTIFICATION_WHEN_INQUIRY_RECEIVED == 'yes' ? true : false;
		}

		public static function defaultCustomerInboxMessageTypes() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_GENERAL_INBOX_MESSAGE_TYPE );
			$generalType = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_GENERAL_INBOX_MESSAGE_TYPE;

			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_PRODUCT_RELATED_INBOX_MESSAGE_TYPE );
			$productType = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_PRODUCT_RELATED_INBOX_MESSAGE_TYPE;

			$types = [];

			if ('yes' == $generalType) {
$types['general'] = esc_html__('General', 'inbox-for-woocommerce');
			}
			if ('yes' == $productType) {
$types['product-related'] = esc_html__('Product Related', 'inbox-for-woocommerce');
			}

			return $types;
		}

		public static function defaultCustomerInboxStatus() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_CUSTOMER_INBOX_STATUS );
			$buttonStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_CUSTOMER_INBOX_STATUS;

			if ( $buttonStatus ) {
return 'yes' == $buttonStatus ? true : false;
			}

			return self::DEFAULT_CUSTOMER_INBOX_STATUS == 'yes' ? true : false;
		}

		public static function defaultThirdPartySweitoAppToken() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_SECURITY_GOOGLE_RECAPTCHA_SECRET_KEY );
			$buttonIconStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_SECURITY_GOOGLE_RECAPTCHA_SECRET_KEY;

			if ( $buttonIconStatus ) {
return $buttonIconStatus;
			}

			return self::DEFAULT_SECURITY_GOOGLE_RECAPTCHA_SECRET_KEY;
		}

		public static function defaultGoogleRecaptchaSecretKey() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_SECURITY_GOOGLE_RECAPTCHA_SECRET_KEY );
			$buttonIconStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_SECURITY_GOOGLE_RECAPTCHA_SECRET_KEY;

			if ( $buttonIconStatus ) {
return $buttonIconStatus;
			}

			return self::DEFAULT_SECURITY_GOOGLE_RECAPTCHA_SECRET_KEY;
		}

		public static function defaultGoogleRecaptchaSiteKey() {
			if ( ! class_exists( 'WC_Admin_Settings' ) ) {
return '';
			}

			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_SECURITY_GOOGLE_RECAPTCHA_SITE_KEY );
			$buttonIconStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_SECURITY_GOOGLE_RECAPTCHA_SITE_KEY;

			if ( $buttonIconStatus ) {
return $buttonIconStatus;
			}

			return self::DEFAULT_SECURITY_GOOGLE_RECAPTCHA_SITE_KEY;
		}

		public static function defaultTicketThreadStyle() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_TICKET_THREAD_STYLE );
			$buttonIconStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_TICKET_THREAD_STYLE;

			if ( $buttonIconStatus ) {
return $buttonIconStatus;
			}

			return self::DEFAULT_TICKET_THREAD_STYLE;
		}

		// public static function defaultInquiryButtonIcon() {
		// 	$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_BUTTON_ICON );
		// 	$buttonIconStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_INQUIRY_BUTTON_ICON;

// 			if ( $buttonIconStatus ) {
// return $buttonIconStatus;
// 			}

// 			return self::DEFAULT_INQUIRY_BUTTON_ICON;
// 		}

// 		public static function defaultInquiryButtonIconStatus() {
// 			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_BUTTON_ICON_STATUS );
// 			$buttonIconStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_INQUIRY_BUTTON_ICON_STATUS;

// 			if ( $buttonIconStatus ) {
// return $buttonIconStatus;
// 			}

// 			return self::DEFAULT_INQUIRY_BUTTON_ICON_STATUS;
// 		}

		public static function defaultInquiryCTABackgroundLink() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_CTA_BACKGROUND_LINK );
			$buttonText = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_INQUIRY_CTA_BACKGROUND_LINK;

			if ( $buttonText ) {
return $buttonText;
			}

			return self::DEFAULT_INQUIRY_CTA_BACKGROUND_LINK;
		}

		public static function defaultInquiryCTABackgroundType() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_CTA_BACKGROUND_TYPE );
			$buttonText = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_INQUIRY_CTA_BACKGROUND_TYPE;

			if ( $buttonText ) {
return $buttonText;
			}

			return self::DEFAULT_INQUIRY_CTA_BACKGROUND_TYPE;
		}

		public static function defaultInquiryCTABackgroundColor() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_CTA_BACKGROUND_COLOR );
			$buttonText = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_INQUIRY_CTA_BACKGROUND_COLOR;

			if ( $buttonText ) {
return $buttonText;
			}

			return self::DEFAULT_INQUIRY_CTA_BACKGROUND_COLOR;
		}

		public static function defaultInquiryCTAButtonColor() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_CTA_BUTTON_COLOR );
			$buttonText = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_INQUIRY_CTA_BUTTON_COLOR;

			if ( $buttonText ) {
return $buttonText;
			}

			return self::DEFAULT_INQUIRY_CTA_BUTTON_COLOR;
		}

		public static function defaultInquiryCTAButtonText() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_CTA_BUTTON_TEXT );
			$buttonText = ( count($option_value) > 0 ) ? ( $option_value[0] ) : esc_html__(self::DEFAULT_INQUIRY_CTA_BUTTON_TEXT, 'inbox-for-woocommerce');

			if ( $buttonText ) {
return $buttonText;
			}

			return esc_html__(self::DEFAULT_INQUIRY_CTA_BUTTON_TEXT, 'inbox-for-woocommerce');
		}

		public static function defaultInquiryCTATextColor() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_CTA_TEXT_COLOR );
			$buttonText = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_INQUIRY_CTA_TEXT_COLOR;

			if ( $buttonText ) {
return $buttonText;
			}

			return self::DEFAULT_INQUIRY_CTA_TEXT_COLOR;
		}

		public static function defaultInquiryCTAText() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_CTA_TEXT );
			$buttonText = ( count($option_value) > 0 ) ? ( $option_value[0] ) : esc_html__(self::DEFAULT_INQUIRY_CTA_TEXT, 'inbox-for-woocommerce');

			if ( $buttonText ) {
return $buttonText;
			}

			return esc_html__(self::DEFAULT_INQUIRY_CTA_TEXT, 'inbox-for-woocommerce');
		}

		public static function defaultInquiryCTAType() {
			$buttonType = get_option(self::SETTING_INQUIRY_CTA_TYPE);

			if ( $buttonType ) {
return $buttonType;
			}

			return self::DEFAULT_INQUIRY_CTA_TYPE;
		}

		public static function defaultInquiryCTAStatus() {
			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_CTA_STATUS );
			$buttonStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_INQUIRY_CTA_STATUS;

			if ( $buttonStatus ) {
return 'yes' == $buttonStatus ? true : false;
			}

			return self::DEFAULT_INQUIRY_CTA_STATUS == 'yes' ? true : false;
		}

// 		public static function defaultInquiryButtonTextColor() {
// 			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_BUTTON_TEXT_COLOR );
// 			$buttonText = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_INQUIRY_BUTTON_TEXT_COLOR;

// 			if ( $buttonText ) {
// return $buttonText;
// 			}

// 			return self::DEFAULT_INQUIRY_BUTTON_TEXT_COLOR;
// 		}

// 		public static function defaultInquiryButtonColor() {
// 			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_BUTTON_COLOR );
// 			$buttonText = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_INQUIRY_BUTTON_COLOR;

// 			if ( $buttonText ) {
// return $buttonText;
// 			}

// 			return self::DEFAULT_INQUIRY_BUTTON_COLOR;
// 		}

// 		public static function defaultInquiryButtonText() {
// 			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_BUTTON_TEXT );
// 			$buttonText = ( count($option_value) > 0 ) ? ( $option_value[0] ) : esc_html__(self::DEFAULT_INQUIRY_BUTTON_TEXT, 'inbox-for-woocommerce');

// 			if ( $buttonText ) {
// return $buttonText;
// 			}

// 			return esc_html__(self::DEFAULT_INQUIRY_BUTTON_TEXT, 'inbox-for-woocommerce');
// 		}

// 		public static function defaultInquiryButtonType() {
// 			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_BUTTON_TYPE );
// 			$buttonType = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_INQUIRY_BUTTON_TYPE;

// 			if ( $buttonType ) {
// return $buttonType;
// 			}

// 			return self::DEFAULT_INQUIRY_BUTTON_TYPE;
// 		}

// 		public static function defaultInquiryButtonStatus() {
// 			$option_value = (array) WC_Admin_Settings::get_option( self::SETTING_INQUIRY_BUTTON_STATUS );
// 			$buttonStatus = ( count($option_value) > 0 ) ? ( $option_value[0] ) : self::DEFAULT_INQUIRY_BUTTON_STATUS;

// 			if ( $buttonStatus ) {
// return 'yes' == $buttonStatus ? true : false;
// 			}

// 			return self::DEFAULT_INQUIRY_BUTTON_STATUS == 'yes' ? true : false;
// 		}

		public static function defaultGuestName() {
			$guestName = get_option(self::SETTING_GUEST_NAME);

			if ( $guestName ) {
return $guestName;
			}

			return esc_html__(self::DEFAULT_GUEST_NAME, 'inbox-for-woocommerce');
		}

		public static function defaultInquirySentResponse() {
			$sentResponse = get_option(self::SETTING_INQUIRY_SENT_RESPONSE);

			if ( $sentResponse ) {
return $sentResponse;
			}

			return esc_html__(self::DEFAULT_INQUIRY_SENT_RESPONSE, 'inbox-for-woocommerce');
		}

		public static function defaultInboxSentResponse() {
			$sentResponse = get_option(self::SETTING_INBOX_SENT_RESPONSE);

			if ( $sentResponse ) {
return $sentResponse;
			}

			return esc_html__(self::DEFAULT_INBOX_SENT_RESPONSE, 'inbox-for-woocommerce');
		}
	}

}
