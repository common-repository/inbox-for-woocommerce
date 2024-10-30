<?php

/**
 * Customer Mail when inbox message is replied
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('IBXFWL_Inbox_Mail_CustomerMessageReply')) {
	class IBXFWL_Inbox_Mail_CustomerMessageReply {
	
		public static function customerMessageReplyTemplate() {
			return ' 
                <i>' . esc_html__('The seller wrote:', 'inbox-for-woocommerce') . '</i><br/>

                [adminMessage]
                <hr style="border-color: #dbdbdb;">
                <br/><br/>
                <br/><br/>
                <i>
                ' . esc_html__('Kindly log back into your account to view and reply if need be to the message.', 'inbox-for-woocommerce') . '
                </i>
            ';
		}
	}
}
