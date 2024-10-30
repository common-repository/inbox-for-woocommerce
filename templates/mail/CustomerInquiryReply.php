<?php

/**
 * Customer Mail when inquiry replied
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('IBXFWL_Inbox_Mail_CustomerInquiryReply')) {
	class IBXFWL_Inbox_Mail_CustomerInquiryReply {
	
		public static function customerInquiryReplyTemplate() {
			return ' 
                <i>' . esc_html__('The seller wrote:', 'inbox-for-woocommerce') . '</i><br/>

                [adminMessage]
                <hr style="border-color: #dbdbdb;">
                <br/><br/>
                <i>' . esc_html__('You wrote:', 'inbox-for-woocommerce') . '</i><br/>

                [customerMessage]
                <hr style="border-color: #dbdbdb;">
                <br/><br/>
                <div style="display: flex; flex-flow: row wrap">
                    <div style="flex: 30%">
                        <img style="max-width: 100px" src="[productImage]" />
                    </div>
                    <div style="flex: 70%">
                        <a href="[productLink]">[productTitle]</a>
                        <div>[productDescription]</div>
                    </div>
                </div>
                <br/>
                <br/><br/>
                <i>
                ' . esc_html__('You can still visit the shop and make a purchase for this item using the link below.', 'inbox-for-woocommerce') . '
                </i>
                <a href="[purchaseLink]">purchase now</a>
            ';
		}
	}
}
