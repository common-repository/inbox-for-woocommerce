<?php

/**
 * Mail to Admin
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('IBXFWL_Inbox_Mail_AdminInboxMessageReplied')) {
	class IBXFWL_Inbox_Mail_AdminInboxMessageReplied {
	
		public static function customerMessageReplyTemplate() {
			return ' 
                <i>' . esc_html__('The customer wrote:', 'inbox-for-woocommerce') . '</i><br/>

                [customerMessage]
                <hr style="border-color: #dbdbdb;">
                <br/><br/>
                <br/><br/>
                <i>
                ' . esc_html__('Kindly log back into your account to view/reply to this message.', 'inbox-for-woocommerce') . '
                </i>
            ';
		}
	}
}
