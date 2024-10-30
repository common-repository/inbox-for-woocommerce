<?php
/**
 * Mail controller for managing mails sent from extension
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_MailController' ) ) {
	class IBXFWL_Inbox_MailController {
		const STATUS_NEW = 'new';
		const STATUS_OPEN = 'open';
		const STATUS_CLOSED = 'open';
		const STATUS_ARCHIVE = 'archive';

		/**
		 * Email sent to Admin when customer ask an inquiry
		 *
		 * @param string $name
		 * @param string $reply
		 * @return void
		 */
		public static function sendAgentNewTicketNotice(
			$email,
			$name
		) {
			require_once(IBXFWL_SWEITO_TEMPLATES_URL . '/mail/NewTicketAssigned.php');

			$mailTemplate = IBXFWL_Inbox_Mail_NewTicketAssigned::agentNewTicketTemplate();
			$mailTemplate = str_replace('[agentName]', $name, $mailTemplate);

			$heading = esc_html__('New Ticket Assigned', 'inbox-for-woocommerce');
			$subject = esc_html__('A new ticket has been assigned to you', 'inbox-for-woocommerce');

			self::sendMail($email, $subject, $mailTemplate, $heading);
		}

		/**
		 * Email sent to Admin when customer ask an inquiry
		 *
		 * @param string $name
		 * @param string $reply
		 * @return void
		 */
		public static function sendAdminInboxMessageReply(
			$email,
			$reply
		) {
			require_once(IBXFWL_SWEITO_TEMPLATES_URL . '/mail/AdminInboxMessageReplied.php');
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php');

			if ( !IBXFWL_Inbox_SettingController::defaultNotificationWhenCustomerReplyInbox() ) {
return;
			}

			$mailTemplate = IBXFWL_Inbox_Mail_AdminInboxMessageReplied::customerMessageReplyTemplate();
			$mailTemplate = str_replace('[customerMessage]', $reply, $mailTemplate);

			$heading = esc_html__('The customer replied', 'inbox-for-woocommerce');
			$subject = esc_html__('Re: Inbox Message', 'inbox-for-woocommerce');

			self::sendMail($email, $subject, $mailTemplate, $heading);
		}

		/**
		 * Email sent to Admin when customer ask an inquiry
		 *
		 * @param string $name
		 * @param string $reply
		 * @return void
		 */
		public static function sendCustomerMessageEmailReply(
			$email,
			$reply
		) {
			require_once(IBXFWL_SWEITO_TEMPLATES_URL . '/mail/CustomerMessageReply.php');
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php');

			if ( !IBXFWL_Inbox_SettingController::defaultNotificationWhenAdminReplyInbox() ) {
return;
			}

			$mailTemplate = IBXFWL_Inbox_Mail_CustomerMessageReply::customerMessageReplyTemplate();
			$mailTemplate = str_replace('[adminMessage]', $reply, $mailTemplate);

			$heading = esc_html__('The seller replied your inbox message', 'inbox-for-woocommerce');
			$subject = esc_html__('Re: Inbox Message', 'inbox-for-woocommerce');

			self::sendMail($email, $subject, $mailTemplate, $heading);
		}

		/**
		 * Email sent to Admin when customer ask an inquiry
		 *
		 * @param string $email
		 * @param array $product
		 * @param string $customerMessage
		 * @param string $reply
		 * @return void
		 */
		public static function sendCustomerInquiryEmailReply(
			$email,
			$product,
			$customerMessage,
			$reply
		) {
			require_once(IBXFWL_SWEITO_TEMPLATES_URL . '/mail/CustomerInquiryReply.php');
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php');

			if ( !IBXFWL_Inbox_SettingController::defaultNotificationWhenAdminReplyInquiry() ) {
return;
			}

			// $productUrl = wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' );

			$mailTemplate = IBXFWL_Inbox_Mail_CustomerInquiryReply::customerInquiryReplyTemplate();
			$mailTemplate = str_replace('[productImage]', $product['img'][0], $mailTemplate);
			$mailTemplate = str_replace('[productTitle]', $product['title'], $mailTemplate);
			$mailTemplate = str_replace('[productLink]', $product['link'], $mailTemplate);
			$mailTemplate = str_replace('[purchaseLink]', $product['link'], $mailTemplate);
			$mailTemplate = str_replace('[productDescription]', $product['description'], $mailTemplate);
			$mailTemplate = str_replace('[customerMessage]', $customerMessage, $mailTemplate);
			$mailTemplate = str_replace('[adminMessage]', $reply, $mailTemplate);

			$heading = esc_html__('The seller replied', 'inbox-for-woocommerce');
			$subject = esc_html__('Re: Inquiry Message on', 'inbox-for-woocommerce') . ' "' . $product['title'] . '"';

			self::sendMail($email, $subject, $mailTemplate, $heading);
		}

		/**
		 * Email sent to Admin when customer sends an inbox message
		 *
		 * @param string $name
		 * @param string $email
		 * @param string $productId
		 * @param string $messageType
		 * @return void
		 */
		public static function sendAdminInboxNoticeEmail(
			$name,
			$email,
			$productId = null,
			$messageType = null
		) {
			require_once(IBXFWL_SWEITO_TEMPLATES_URL . '/mail/AdminNewInboxMessage.php');
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php');

			if ( !IBXFWL_Inbox_SettingController::defaultNotificationWhenInboxReceived() ) {
return;
			}

			if ( $productId ) {
				$product = wc_get_product( $productId );
				$productUrl = wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' );

				$mailTemplate = IBXFWL_Inbox_Mail_AdminNewInboxMessage::adminInboxNoticeWithProductTemplate();
				$mailTemplate = str_replace('[senderName]', $name, $mailTemplate);
				$mailTemplate = str_replace('[productImage]', $productUrl[0], $mailTemplate);
				$mailTemplate = str_replace('[productTitle]', $product->get_title(), $mailTemplate);

				$heading = esc_html__('New Inbox Message', 'inbox-for-woocommerce');
				$subject = esc_html__('New Inbox Message regarding', 'inbox-for-woocommerce') . ' "' . $product->get_title() . '"';
			} else {
				$mailTemplate = IBXFWL_Inbox_Mail_AdminNewInboxMessage::adminInboxNoticeTemplate();
				$mailTemplate = str_replace('[senderName]', $name, $mailTemplate);
				$mailTemplate = str_replace('[messageType]', $messageType, $mailTemplate);

				$heading = esc_html__('New Inbox Message', 'inbox-for-woocommerce');
				$subject = esc_html__('New inbox message received', 'inbox-for-woocommerce');
			}
			
			self::sendMail($email, $subject, $mailTemplate, $heading);
		}
		
		/**
		 * Email sent to Admin when customer ask an inquiry
		 *
		 * @param string $name
		 * @param string $email
		 * @param string $productId
		 * @return void
		 */
		public static function sendAdminInquiryEmail(
			$name,
			$email,
			$productId
		) {
			require_once(IBXFWL_SWEITO_TEMPLATES_URL . '/mail/AdminNewInquiryMessage.php');
			require_once(IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php');

			if ( !IBXFWL_Inbox_SettingController::defaultNotificationWhenInquiryReceived() ) {
return;
			}

			$product = wc_get_product( $productId );
			$productUrl = wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' );

			$mailTemplate = IBXFWL_Inbox_Mail_AdminNewInquiryMessage::adminInquiryTemplate();
			$mailTemplate = str_replace('[senderName]', $name, $mailTemplate);
			$mailTemplate = str_replace('[productImage]', $productUrl[0], $mailTemplate);
			$mailTemplate = str_replace('[productTitle]', $product->get_title(), $mailTemplate);

			$heading = esc_html__('New message about an item', 'inbox-for-woocommerce');
			$subject = esc_html__('New Inquiry Message on', 'inbox-for-woocommerce') . ' "' . $product->get_title() . '"';

			self::sendMail($email, $subject, $mailTemplate, $heading);
		}

		public static function sendMail( $email, $subject, $template, $heading) {
			// send mail to email address configured
			$message = $template;

			$headers = ['Content-Type: text/html; charset=utf-8'];

			// Get woocommerce mailer from instance
			$mailer = WC()->mailer();

			// Wrap message using woocommerce html email template
			$wrapped_message = $mailer->wrap_message($heading, $message);

			// Create new WC_Email instance
			$wc_email = new WC_Email();

			// Style the wrapped message with woocommerce inline styles
			$html_message = $wc_email->style_inline($wrapped_message);

			wp_mail($email, $subject, $html_message, $headers);
		}
	}

}
