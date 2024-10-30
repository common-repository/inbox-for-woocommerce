<?php
/**
 * User myaccount page
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_UserMyAccountInbox' ) ) {
	class IBXFWL_Inbox_UserMyAccountInbox {
		public static function showTickets() {
			require_once IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php';
			$setStyle = IBXFWL_Inbox_SettingController::defaultTicketThreadStyle();
			$messageTypes = IBXFWL_Inbox_SettingController::defaultCustomerInboxMessageTypes();

			echo '<h5>' . esc_html__('Messages (Inbox)', 'inbox-for-woocommerce') . '</h5>';
			echo '<p><small>' . esc_html__('Send messages to the store owner here or ask the seller about a product.', 'inbox-for-woocommerce') . '</small></p>';

			echo '<div id="mainInboxLoading">';
			echo '<p>' . esc_html__('Please wait ...', 'inbox-for-woocommerce') . '</p>';
			echo '</div>';

			echo '<div id="mainInboxPage" style="display: none">';
			
			self::showNewButton();

			echo '
                <br/>
                <div id="wcsNoMessageFoundInInbox" class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                ' . esc_html__('No message found in your inbox', 'inbox-for-woocommerce') . '
                </div>
                <div>
                    <table class="woocommerce-inbox-table woocommerce-MyAccount-inbox shop_table shop_table_responsive my_account_orders account-orders-table">
                        <thead>
                            <tr>
                                <th>' . esc_html__('Ref', 'inbox-for-woocommerce') . '</th>
                                <th>' . esc_html__('Status', 'inbox-for-woocommerce') . '</th>
                                <th>' . esc_html__('Type', 'inbox-for-woocommerce') . '</th>
                                <th>' . esc_html__('Last Updated', 'inbox-for-woocommerce') . '</th>
                                <th>' . esc_html__('Action', 'inbox-for-woocommerce') . '</th>
                            </tr>
                        </thead>
                        <tbody id="wcsTicketListSection"></tbody>
                    </table>
                </div>
            ';

			echo '</div>';
			echo '<div id="newInboxPage" style="display: none;">';
			echo '
                <div class="form-group">
                    <label class="">' . esc_html__('Select Message type', 'inbox-for-woocommerce') . '</label>
                    <select class="wcs-input-select" id="wcsMessageType" onChange="showWooCommerceSweitoProductList()">
                        <option value="">' . esc_html__('~ Select a type ~', 'inbox-for-woocommerce') . '</option>
                ';
				
			foreach ($messageTypes as $key => $type) {
				echo '<option value="' . esc_attr($key) . '">' . esc_html($type) . '</option>';
			}
				
			echo '
                    </select>
                    <div id="wcsMessageTypeError" style="display: none"><small><small>' . esc_html__('This field is required', 'inbox-for-woocommerce') . '</small></small></div>
                </div>
                <br/>
            ';

			self::showProductRelatedForm();

			echo '
                <div class="form-group">
                    <label class="">' . esc_html__('Enter Description', 'inbox-for-woocommerce') . '</label>
                    <textarea class="wcs-input-textarea-100" id="wcsMessageContent" rows="12"></textarea>
                    <div id="wcsMessageContentError" style="display: none"><small><small>' . esc_html__('This field is required', 'inbox-for-woocommerce') . '</small></small></div>
                </div>
                <div id="uploadedDocumentPreview" class="wcs-ticket-upload-documents" style="display: none;">
                    <div class="wcs-row">
                        <div class="wcs-col-10">
                            <img src="' . esc_html__(IBXFWL_HELPDESK_ASSETS_URL) . '/images/paperclip-solid.svg" style="width: 14px; margin-left: 10px" /> <span id="uploadedDocumentCount">1</span> ' . esc_html__('attachment(s) added', 'inbox-for-woocommerce') . '
                        </div>
                        <div class="wcs-col-2 wcs-text-right">
                            <a onClick="dropUploadAttachment()">' . esc_html__('Drop', 'inbox-for-woocommerce') . '</a>
                        </div>
                    </div>
                </div>
                <div class="woocommerce" style="margin-top: 10px;">
                    <button class="button" onClick="sendNewInboxMessage()" id="wooCommerceSweitoInboxButton">' . esc_html__('Send', 'inbox-for-woocommerce') . '</button> 
                    <div class="wcs-margin-top-10"><a onClick="addMessageAttachment()"> <img src="' . esc_html__(IBXFWL_HELPDESK_ASSETS_URL) . '/images/paperclip-solid.svg" style="width: 14px; margin-left: 10px" /> ' . esc_html__('Attach File', 'inbox-for-woocommerce') . '</a></div> <br/>
                    <input type="file" onChange="handleFileUpload()" accept=".doc,.docx,.pdf,.jpg,.png,.jpeg" style="display: none" id="uploadFileField" />
                    <div id="messageAttachmentContents"></div>
                    <div id="wooCommerceSweitoSendError"></div>
                </div>
                <br/>
                <div class="wcs-text-center">
                    <div class="wcs-margin-top-10"><a onClick="showAccountInboxSection(\'tickets\')">' . esc_html__('Go Back', 'inbox-for-woocommerce') . '</a></div>
                </div>
            ';

			echo '</div>';
			echo '';

			echo '<div class="woocommerce" id="mainInboxThreadPreview" style="display: none;">';
			echo '<input type="hidden" id="wooCommerSweitoDisplayStyle" value="' . esc_attr($setStyle) . '" />';

			self::showMessageThreadHead();

			if ('style-1' == $setStyle) {
				self::showMessageThreadCommentsStyle1();
			}

			self::showMessageThreadReply($setStyle);

			echo '</div>';
		}

		public static function showMessageThreadReply( $style = '') {
			echo '
                <div id="uploadedDocumentPreview2" class="wcs-ticket-upload-documents" style="display: none; margin-bottom: 0px;">
                    <div class="wcs-row">
                        <div class="wcs-col-10">
                            <img src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/paperclip-solid.svg" style="width: 14px; margin-left: 10px" /> <span id="uploadedDocumentCount2">1</span> ' . esc_html__('attachment(s) added', 'inbox-for-woocommerce') . '
                        </div>
                        <div class="wcs-col-2 wcs-text-right">
                            <a onClick="dropUploadAttachment2()">' . esc_html__('Drop', 'inbox-for-woocommerce') . '</a>
                        </div>
                    </div>
                </div>
                <div id="wooCommerceSweitoThreadReplySection">
                    <hr class="wcs-message-thread-rule" />
                    <div class="wcs-reply-section wcs-reply-section-' . esc_html($style) . ' wcs-row-padding">
                        <div class="wcs-row ">
                            <div class="wcs-col-10">
                                <textarea id="wooCommerceSweitoReplyInboxMessage" class="wcs-input-textarea" placeholder="' . esc_attr__('Type here to reply ...', 'inbox-for-woocommerce') . '" rows="6"></textarea>
                                <input type="hidden" id="wooCommerceSweitoTicketReference" />
                                <div id="wooCommerceSweitoSendReplyError"></div>
                            </div>
                            <div class="wcs-col-2 wcs-text-left">
                                <button onClick="sendUserReplyToThread()" class="button wcs-button-block" id="wooCommerceSweitoInboxReplyButton">' . esc_html__('Send', 'inbox-for-woocommerce') . '</button>
                                <div class="wcs-margin-top-10 wcs-text-center"><a onClick="addMessageAttachment2()"> <img src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/paperclip-solid.svg" style="width: 14px; margin-left: 10px" /> ' . esc_html__('Attach File', 'inbox-for-woocommerce') . '</a></div> <br/>
                                <input type="file" onChange="handleFileUpload2()" accept=".pdf,.jpg,.png,.jpeg" style="display: none" id="uploadFileField2" />
                                <div id="messageAttachmentContents2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            ';
		}

		public static function showMessageThreadCommentsStyle1() {
			echo '
                <ul id="wcs-chat-style-1"></ul>
            ';
		}

		public static function showMessageThreadHead() {
			echo '
                <div id="wcsPreviewSelectedProduct"">
                    <div class="wcs-preview-selected-container">
                        <div class="wcs-row">
                            <div class="wcs-col-6">
                                ' . esc_html__('Ref:', 'inbox-for-woocommerce') . ' <b id="wooCommerceSweitoThreadRefDisplay"></b>
                            </div>
                            <div class="wcs-col-6">
                                ' . esc_html__('Status:', 'inbox-for-woocommerce') . ' <b id="wooCommerceSweitoThreadStatusDisplay"></b>
                            </div>
                        </div>
                        <div class="wcs-row">
                            <div class="wcs-col-6">
                                ' . esc_html__('TYPE:', 'inbox-for-woocommerce') . ' <b id="wooCommerceSweitoThreadTypeDisplay"></b>
                            </div>
                            <div class="wcs-col-6">
                                ' . esc_html__('Date Created:', 'inbox-for-woocommerce') . ' <b id="wooCommerceSweitoThreadDateDisplay"></b>
                            </div>
                        </div>
                    </div>
                </div>
            ';
		}

		public static function showProductRelatedForm() {
			echo '
                <div class="form-group" id="productRelatedFormSection" style="display: none">
                    <label class="">' . esc_html__('Select Product', 'inbox-for-woocommerce') . '</label>
                    <input type="hidden" id="wcsSelectedProductId" />
                    <div id="wcsPreviewSelectedProduct" style="display: none">
                        <div class="wcs-preview-selected-container">
                            <span class="wcs-close-2" onClick="showWooCommerceSelectionPreview(\'form\')">&times;</span>
                            <div id="previewSelectedBox"></div>
                        </div>
                    </div>
                    <div id="wcsSearchNewProduct">
                        <input placeholder="Type here to search for product" class="wcs-input-field" onFocus="wooCommerceSweitoSearchFocus()" onBlur="wooCommerceSweitoSearchBlur()" onKeyup="wooCommerceSweitoSearchByTyping()" id="wcsSearchField">
                        <div class="wcs-product-dropdown" style="display: none;">
                            <div class="wcs-dropdown-loading" id="wcsDropdownLoading">' . esc_html__('Please wait ...', 'inbox-for-woocommerce') . '</div>
                            <div id="wcsProductDisplayItem"></div>
                        </div>
                        <div id="wcsSelectedProductIdError" style="display: none"><small><small>' . esc_html__('This field is required', 'inbox-for-woocommerce') . '</small></small></div>
                    </div>
                    <br/>
                </div>
            ';
		}

		public static function showNewButton() {
			echo '
                <div class="wcs-text-right">
                <a onClick="showAddNewInbox()" class="button">' . esc_html__('New Message', 'inbox-for-woocommerce') . '</a>
                </div>
            ';
		} 
	}
	
}
