<?php

/**
 * Mail to Admin when new inquiry received
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('IBXFWL_Inbox_Mail_AdminNewInquiryMessage')) {
	class IBXFWL_Inbox_Mail_AdminNewInquiryMessage {
	
		public static function adminInquiryTemplate() {
			return '
                [senderName] ' . esc_html__('has sent you a new message regarding an item you listed in your store. The details of the item being inquired is below:', 'inbox-for-woocommerce') . ' 
                
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
