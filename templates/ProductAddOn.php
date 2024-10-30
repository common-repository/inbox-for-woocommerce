<?php

/**
 * Display Product Page Addons
 * 
 * @package Inbox-For-WooCommerce-LTE
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('IBXFWL_Inbox_ProductAddOn')) {
	class IBXFWL_Inbox_ProductAddOn {
	
		public static function showCTAButtonBeneathProductPage( $product) {
			require_once IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php';

			$defaultCTAText = IBXFWL_Inbox_SettingController::defaultInquiryCTAText();
			$defaultCTATextColor = IBXFWL_Inbox_SettingController::defaultInquiryCTATextColor();
			$defaultCTAButtonText = IBXFWL_Inbox_SettingController::defaultInquiryCTAButtonText();
			$defaultCTAButtonColor = IBXFWL_Inbox_SettingController::defaultInquiryCTAButtonColor();
			$defaultCTABackgroundColor = IBXFWL_Inbox_SettingController::defaultInquiryCTABackgroundColor();
			$defaultCTABackgroundType = IBXFWL_Inbox_SettingController::defaultInquiryCTABackgroundType();
			$defaultCTABackgroundLink = '';

			if ('background-image' == $defaultCTABackgroundType) {
				$defaultCTABackgroundLink = IBXFWL_Inbox_SettingController::defaultInquiryCTABackgroundLink();
				$defaultCTABackgroundColor = 'rgba(0,0,0,0.4)';
			}

			$ctaStyle = '';
			if ( $defaultCTABackgroundLink ) {
				$ctaStyle = 'background-image: url(' . $defaultCTABackgroundLink . ')';
			}

			echo '
            <div class="wcs-cta-background" style="' . esc_html($ctaStyle) . '">
                <div style="background-color: ' . esc_attr($defaultCTABackgroundColor) . '; position: absolute; top: 0; left: 0; right: 0; bottom: 0">
                    <div class="wcs-cta-text" style="color: ' . esc_attr($defaultCTATextColor) . '">
                    ' . esc_html($defaultCTAText) . '
                    </div>
                    <div style="text-align: center;">
                        <button style="border-color: ' . esc_attr($defaultCTAButtonColor) . '; color: ' . esc_attr($defaultCTAButtonColor) . '" data-product-id="' . esc_attr($product->get_id()) . '" class="wcs-cta-button wcs-inquiry-button" style="">
                        ' . esc_html($defaultCTAButtonText) . '
                        </button>
                    </div>
                </div>
            </div>';

			self::showInquiryModal();
		}

		public static function showInquiryModal() {
			echo '
                <div id="wooCommerceSweitoModal" class="wcs-modal">

                    <!-- Modal content -->
                    <div class="wcs-modal-content">
                        <span class="wcs-close">&times;</span>
                        <div class="wcs-row" id="wooCommerceSweitoPreviewLoading" style="display: block">
                            ' . esc_html__('Please wait ...', 'inbox-for-woocommerce') . '
                        </div>
                        <div class="wcs-row" id="wooCommerceSweitoPreviewForm" style="display: none">
                            <div class="wcs-col-9">
                                <!-- Leave empty to be able to populate later with ajax -->
                                <br/>
                                <div style="margin-right: 10px;">
                                <div class="form-group" id="wooCommerceSweitoEmailFieldSection">
                                    <label>' . esc_html__('Email Address', 'inbox-for-woocommerce') . '</label>
                                    <input type="email" id="wooCommerceSweitoEmailField" class="wcs-input-field" style="border-color: #d3d3d3" />
                                    <div id="wooCommerceSweitoEmailError" style="display: none"><small><small>' . esc_html__('This field is required', 'inbox-for-woocommerce') . '</small></small></div>
                                </div>
                                <br/>
                                <div class="form-group" id="wooCommerceSweitoInquiryFieldSection">
                                    <label>' . esc_html__('What do you want to know about this item?', 'inbox-for-woocommerce') . '</label>
                                    <textarea class="wcs-input-textarea" style="width: 100%" id="wooCommerceSweitoInquiryField" rows="8"></textarea>
                                    <div id="wooCommerceSweitoInquiryError" style="display: none"><small><small>' . esc_html__('This field is required', 'inbox-for-woocommerce') . '</small></small></div>
                                </div>
                                <br/>
                                <div>
                                    <input type="hidden" id="wooCommerceSweitoProductId" />
                                    <input type="hidden" id="wooCommerceSweitoProductAccess" />
                                    <button class="button wcs-button-inquiry" id="wooCommerceSweitoInquiryButton" onClick="sendWooCommerceSweitoInquiryForm()">' . esc_html__('Send', 'inbox-for-woocommerce') . '</button>
                                    <div id="wooCommerceSweitoSendError" style="display: none"></div>
                                </div>
                                </div>
                                <br/>
                            </div>
                        
                            <div class="wcs-col-3">
                                <!-- Put your form here -->
                                <br/>
                                <br/>
                                <div style="border: 1px solid #d3d3d3; box-shadow: 1px 2px 5px 1px #d3d3d3; text-align: center;">
                                    <img src="" id="wooCommerceSweitoPreviewImg" />
                                </div>
                                <div style="padding: 10px; text-align: center;">
                                    <strong><div id="wooCommerceSweitoPreviewTitle"></div></strong>
                                    <div id="wooCommerceSweitoPreviewDescription"></div>
                                    <div id="wooCommerceSweitoPreviewPrice"></div>
                                </div>
                            </div>
                        </div>
                        <div id="wooCommerceSweitoSentSuccess" style="display: none">
                            <br/>
                            <div class="wcs-alert-success">
                                <div><strong>' . esc_html__('Sent Successfully!', 'inbox-for-woocommerce') . '</strong></div> 
                                <div id="wooCommerceSentResponse"></div>
                            </div>
                        </div>
                    </div>
                
                </div>
            ';
		}

		public static function getButtonIcon( $type, $position, $color) {
			if ('circle' == $type) {
				return self::getCircleChatIcon($color, $position);
			} elseif ('square' == $type) {
				return self::getSquareChatIcon($color, $position);
			}

			return '';
		}

		public static function getSquareChatIcon( $color, $position = '') {
			$itemPosition = '';
			if ('icon-before' == $position) {
				$itemPosition = 'transform: rotateY(180deg)';
			} elseif ('icon-after' == $position) {
				$itemPosition = '';
			}
			return '<svg style="' . esc_attr($itemPosition) . '" xmlns="http://www.w3.org/2000/svg" fill="' . esc_attr($color) . '" width="14" viewBox="0 0 512 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 7.1 5.8 12 12 12 2.4 0 4.9-.7 7.1-2.4L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64zm16 352c0 8.8-7.2 16-16 16H288l-12.8 9.6L208 428v-60H64c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16h384c8.8 0 16 7.2 16 16v288z"/></svg>';
		}

		public static function getCircleChatIcon( $color, $position = '') {
			$itemPosition = '';
			if ('icon-before' == $position) {
				$itemPosition = 'transform: rotateY(180deg)';
			} elseif ('icon-after' == $position) {
				$itemPosition = '';
			}
			return '<svg style="' . esc_attr($itemPosition) . '" xmlns="http://www.w3.org/2000/svg" fill="' . esc_attr($color) . '" width="14"  viewBox="0 0 512 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><path d="M256 32C114.6 32 0 125.1 0 240c0 47.6 19.9 91.2 52.9 126.3C38 405.7 7 439.1 6.5 439.5c-6.6 7-8.4 17.2-4.6 26S14.4 480 24 480c61.5 0 110-25.7 139.1-46.3C192 442.8 223.2 448 256 448c141.4 0 256-93.1 256-208S397.4 32 256 32zm0 368c-26.7 0-53.1-4.1-78.4-12.1l-22.7-7.2-19.5 13.8c-14.3 10.1-33.9 21.4-57.5 29 7.3-12.1 14.4-25.7 19.9-40.2l10.6-28.1-20.6-21.8C69.7 314.1 48 282.2 48 240c0-88.2 93.3-160 208-160s208 71.8 208 160-93.3 160-208 160z"/></svg>';
		}
	}
}
