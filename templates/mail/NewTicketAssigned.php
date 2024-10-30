<?php

/**
 * Message to admin staff when assigned ticket
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('IBXFWL_Inbox_Mail_NewTicketAssigned')) {
	class IBXFWL_Inbox_Mail_NewTicketAssigned {
	
		public static function agentNewTicketTemplate() {
			return ' 
                Hello [agentName],

                ' . esc_html__('You have been assigned a new ticket on the store WooCommerce Inbox.', 'inbox-for-woocommerce') . '
                <br/><br/>
                <i>
                ' . esc_html__('Kindly log back into your account to view/reply to this ticker.', 'inbox-for-woocommerce') . '
                </i>
            ';
		}
	}
}
