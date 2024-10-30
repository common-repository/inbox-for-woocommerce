<?php

/**
 * Mail to Admin when new inbox message created
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('IBXFWL_Inbox_Mail_AdminNewInboxMessage')) {
	class IBXFWL_Inbox_Mail_AdminNewInboxMessage {
	
		public static function adminInboxNoticeTemplate() {
			return '
                [senderName] ' . esc_html__('has sent you a new inbox message. The message type created is under', 'inbox-for-woocommerce') . ' "<strong>[messageType]</strong>" 
                
                ' . esc_html__('Kindly log into your store to view the message and reply.', 'inbox-for-woocommerce') . '
            ';
		}

		public static function adminInboxNoticeWithProductTemplate() {
			return '
                [senderName] ' . esc_html__('has sent you a new inbox message. The message type created is under', 'inbox-for-woocommerce') . ' "<strong>[messageType]</strong>"

                <br/><br/>
                <img src="[productImage]" />
                <br/>
                [productTitle]
                <br/><br/>
                
                ' . esc_html__('Kindly log into your store to view the message and reply.', 'inbox-for-woocommerce') . '
            ';
		}
	}
}
