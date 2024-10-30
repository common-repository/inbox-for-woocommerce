<?php
/**
 * Admin Ticket preview
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_Admin_TicketThreadPreview' ) ) {
	class IBXFWL_Inbox_Admin_TicketThreadPreview {
		public static function showTicketThread() {
			self::importDirectories();

			$currentUser = wp_get_current_user();
			$roles = ( array ) $currentUser->roles;

			$setStyle = IBXFWL_Inbox_SettingController::defaultTicketThreadStyle();

			$allTickets = self::getActiveTickets();
			$ticket = self::getTicketThread();

			$defaultInbox = 'woocommerce-inbox-sweito';
			if ( !in_array('administrator', $roles) && in_array('inbox-for-woocommerce-agent', $roles) ) {
				if ( $ticket['agent_wp_user_id'] != $currentUser->ID ) {
					echo '<div class="error">
                        <p>' . esc_html__('You do not have permission to view this ticket, contact the Administrator to assign this ticket to you', 'inbox-for-woocommerce') . '</p>
                    </div>';
					return;
				}

				$allowedTickets = [];
				foreach ($allTickets as $allTicket) {
					if ( $allTicket['agent_wp_user_id'] == $currentUser->ID ) {
						$allowedTickets[] = $allTicket;
					}
				}

				$allTickets = $allowedTickets;
				$defaultInbox = 'woocommerce-inbox-sweito-agent';
			}

			$selectedStyle = $setStyle;

			// get current ticket information
			echo '<h1 class="wcs-header-title"><a style="text-decoration: none;" href="' . esc_html(admin_url('admin.php?page=' . $defaultInbox)) . '">' . esc_html__('WooCommerce Inbox', 'inbox-for-woocommerce') . '</a> > #' . esc_html(strtoupper($ticket['ref'])) . ' ' . esc_html__('Thread', 'inbox-for-woocommerce') . '</h1>';

			// process submission
			self::processFormSubmission(); 

			echo '<br/>';
			echo '<input type="hidden" id="wooCommerSweitoDisplayStyle" value="' . esc_html($selectedStyle) . '"';
			echo '<div>';
			echo '<div class="wcs-row">';
				echo '<div class="wcs-col-2">';
					self::showActiveTickets($allTickets);
				echo '</div>';

				echo '<div class="wcs-col-8">';
					self::showTicketThreadHeader($ticket);
			if ('style-1' == $selectedStyle) {
				self::showTicketThreadStyle1($ticket);
			} elseif ('style-2' == $selectedStyle) {
				self::showTicketThreadStyle2($ticket);
			} elseif ('style-3' == $selectedStyle) {
				self::showTicketThreadStyle3($ticket);
			}
					self::showTicketThreadReply($ticket);
				echo '</div>';

				echo '<div class="wcs-col-2">';
					self::showTicketUserDetails($ticket);
                echo '
                    <div style="position: relative;">
                        <div class="wcs-faded-section">
                            <div class="wcs-faded-inner-section">
                                <div class="wcs-row">
                                    <div class="wcs-col-12 wcs-text-right">
                                        <button class="button wcs-margin-top-12">' . esc_html__('Update', 'inbox-for-woocommerce') . '</button>
                                    </div>
                                </div>
                            </div>
                            <div class="wcs-faded-inner-section">
                                <div class="wcs-row">
                                    <div class="wcs-col-12 wcs-text-right">
                                        <button class="button wcs-margin-top-12">' . esc_html__('Update', 'inbox-for-woocommerce') . '</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wcs-faded-overlay-section">
                            <div class="wcs-faded-button" onClick="(document.getElementsByClassName(\'wcs-upgrade-background\')[0]).style.display = \'block\'">Unlock This Section</div>
                        </div>
                    </div>
                ';
				echo '<br/>';
				echo '</div>';
			echo '</div>';
			echo '</div>';

            self::upgradePopUp();

			// mark as read
			if (( null == $ticket['read_at'] ) && ( !$ticket['agent_wp_user_id'] || ( $ticket['agent_wp_user_id'] == $currentUser->ID ) ) ) {
				IBXFWL_Inbox_TicketController::adminUpdateTicketReadAt([$ticket['ref']]);
			}
		}

		private static function processFormSubmission() {
			echo '<div class="updated">
                    <p>' . esc_html__('You are currently using the free version (light-version). UPGRADE to the full version to access more features like different chat display themes, different inbox categories and others.', 'inbox-for-woocommerce') . ' <a target="_blank" href="' . esc_html(IBXFWL_SWEITO_PRODUCT_URL) . '">' . esc_html__('Click here to purchase full version now', 'inbox-for-woocommerce') . '</a></p></div>';
		}

        private static function upgradePopUp()
        {
            echo '
                <div class="wcs-upgrade-background">
                    <div class="wcs-upgrade-card">
                        <div class="wcs-upgrae-close-box" onClick="(document.getElementsByClassName(\'wcs-upgrade-background\')[0]).style.display = \'none\'">
                            <div class="wcs-upgrade-close">&times;</div>
                        </div>
                        <div class="wcs-row">
                            <div class="wcs-col-4 wcs-text-right">
                                <img src="' . IBXFWL_HELPDESK_ASSETS_URL . '/images/upgrade.png" class="wcs-upgrade-img" /> &nbsp;
                            </div>
                            <div class="wcs-col-8">
                                <div class="wcs-upgrade-title">' . esc_html__('UPGRADE TO FULL VERSION', 'inbox-for-woocommerce') . '</div>
                            </div>
                        </div>
                        <div class="wcs-row">
                            <div class="wcs-col-4">
                                <div class="wcs-row">
                                    <div class="wcs-col-2 wcs-text-right"><img src="' . IBXFWL_HELPDESK_ASSETS_URL . '/images/upgrade-check.png" class="wcs-upgrade-check" /></div>
                                    <div class="wcs-col-10">
                                        <div class="wcs-upgrade-check-list">' . esc_html__('Multiple Chat Themes', 'inbox-for-woocommerce') . '</div>
                                    </div>
                                </div>
                                <div class="wcs-row">
                                    <div class="wcs-col-2 wcs-text-right"><img src="' . IBXFWL_HELPDESK_ASSETS_URL . '/images/upgrade-check.png" class="wcs-upgrade-check" /></div>
                                    <div class="wcs-col-10">
                                        <div class="wcs-upgrade-check-list">' . esc_html__('Additional Message Type', 'inbox-for-woocommerce') . '</div>
                                    </div>
                                </div>
                                <div class="wcs-row">
                                    <div class="wcs-col-2 wcs-text-right"><img src="' . IBXFWL_HELPDESK_ASSETS_URL . '/images/upgrade-check.png" class="wcs-upgrade-check" /></div>
                                    <div class="wcs-col-10">
                                        <div class="wcs-upgrade-check-list">' . esc_html__('Unlocked Preview Sidebar', 'inbox-for-woocommerce') . '</div>
                                    </div>
                                </div>
                                <div class="wcs-row">
                                    <div class="wcs-col-2 wcs-text-right"><img src="' . IBXFWL_HELPDESK_ASSETS_URL . '/images/upgrade-check.png" class="wcs-upgrade-check" /></div>
                                    <div class="wcs-col-10">
                                        <div class="wcs-upgrade-check-list">' . esc_html__('Ticket Archives', 'inbox-for-woocommerce') . '</div>
                                    </div>
                                </div>
                                <div class="wcs-row">
                                    <div class="wcs-col-12 wcs-text-center">
                                        <div class="wcs-upgrade-more">' . esc_html__('+ more features', 'inbox-for-woocommerce') . '</div>
                                    </div>
                                </div>
                                 &nbsp; 
                            </div>
                            <div class="wcs-col-8 wcs-text-center">

                            <div class="wcs-upgrade-text">' . esc_html__('You can get the full package now by clicking on the button below', 'inbox-for-woocommerce') . '</div>
                            <a class="wcs-purchase-link" href="' . esc_html(IBXFWL_SWEITO_PRODUCT_URL) . '" target="_blank"><div class="wcs-upgrade-purchase-button">' . esc_html__('Purchase Now', 'inbox-for-woocommerce') . '</div></a>
                            
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }

		private static function showTicketUserDetails( $ticket) {
			$activeTheme = ( rand(10, 20) % 2 ) == 1 ? '#2271b1' : '#a36597';

			echo '
                <div class="wcs-ticket-thread-user-profile">
                    <div style="background-color: ' . esc_html($activeTheme) . '; height: 80px"></div>
                    <div class="wcs-ticket-thread-user-profile-inner">
                        <div class="wcs-ticket-thread-user-profile-img-container">
                        ';

			/**
			 * Accepted Plugins:
			 * 1. https://wordpress.org/plugins/metronet-profile-picture/
			 * 2. https://wordpress.org/plugins/wp-user-profile-avatar/
			 */
			if (function_exists('mt_profile_img') && $ticket['is_customer'] && $ticket['user_wp_user_id']) {
				mt_profile_img($ticket['user_wp_user_id'], array(
					'size' => 'thumbnail',
					'attr' => array( 'class' => 'wcs-ticket-thread-user-profile-img-preview' ),
					'echo' => true )
				);
			} elseif (function_exists('get_wpupa_url') && $ticket['is_customer'] && $ticket['user_wp_user_id']) { 
				echo '<img src="' . esc_html(get_wpupa_url($ticket['user_wp_user_id'], ['size' => 'thumbnail'])) . '" class="wcs-ticket-thread-user-profile-img-preview" />';
			} else {
				echo '<img src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/a0.png" class="wcs-ticket-thread-user-profile-img-preview" />';
			}
							
			echo '</div>
                        <div class="wcs-text-center"><b>' . esc_html($ticket['user']) . '</b></div>';
			
			if (!$ticket['is_customer'] || !$ticket['user_wp_user_id']) {
				echo '<div class="wcs-text-center">' . esc_html__('Guest User', 'inbox-for-woocommerce') . '</div>';
			} else {
				echo '<div class="wcs-text-center"><i>' . esc_html__('User', 'inbox-for-woocommerce') . '</i></div>';
				echo '<a href="/wp-admin/user-edit.php?user_id=' . esc_html($ticket['user_wp_user_id']) . '"><button class="button wcs-button-block">' . esc_html__('Visit User Profile', 'inbox-for-woocommerce') . '</button></a>';
			}

			echo '
                    </div>
                </div>
            ';
		}

		private static function showTicketThreadReply( $ticket) {
			echo '
                <div id="wooCommerceSweitoThreadReplySection">
                    <hr class="wcs-message-thread-rule" />
                    <div class="wcs-reply-section wcs-row-padding">
                        <div class="wcs-row ">
                            <div class="wcs-col-10">
                                <textarea id="wooCommerceSweitoReplyInboxMessage" class="wcs-input-textarea" placeholder="' . esc_attr__('Type here to reply ...', 'inbox-for-woocommerce') . '" rows="8"></textarea>
                                <input type="hidden" id="wooCommerceSweitoTicketReference" value="' . esc_html($ticket['ref']) . '" />
                                <div id="wooCommerceSweitoSendReplyError"></div>
                            </div>
                            <div class="wcs-col-2 wcs-text-left">
                                <button onClick="sendTicketThreadReply()" class="button button-primary wcs-button-block" id="wooCommerceSweitoInboxReplyButton">' . esc_html__('Send', 'inbox-for-woocommerce') . '</button>
                                <div class="wcs-margin-top-10 wcs-text-center" style="width: 100%;"><button class="button wcs-button-block" onClick="(document.getElementsByClassName(\'wcs-upgrade-background\')[0]).style.display = \'block\'"> <img src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/paperclip-solid.svg" style="width: 14px; margin-left: 10px" /> ' . esc_html__('Attach File', 'inbox-for-woocommerce') . '</button></div> <br/>
                            </div>
                        </div>
                    </div>
                </div>
            ';
		}

		private static function showTicketThreadStyle3( $ticket) {
			echo '<div class="wcs-style-3-chat">';
			echo '<div class="messages" id="wcs-chat-style-3">';
			
			foreach ($ticket['threads'] as $indx => $thread) {
				if ( 0 === $indx && ( 0 < count($ticket['products']) ) ) {
					foreach ($ticket['products'] as $product) {
						if ( $thread['is_you'] ) {
							echo '<div>
                                <div class="time">
                                ' . esc_html($thread['created_at']) . '
                                </div>
                                <div class="message parker">
                                    <div class="wcs-row" style="width: 300px; background-color: #dbdbdb; padding: 5px; padding-bottom: 0px; border-radius: 4px;">
                                        <div class="wcs-col-2">
                                            <img src="' . esc_html($product['img'][0]) . '" style="width: 50px; height: 50px;" />
                                        </div>
                                        <div class="wcs-col-10">
                                            <div><b><a class="wcs-link-text-style-2" href="' . esc_html($product['link']) . '" target="_blank">' . esc_html($product['title']) . '</a></b></div>
                                            <div style="margin-top: -1px; margin-bottom: -2px; font-size: 9px;">' . esc_html(substr($product['description'], 0, 50)) . ( 50 < strlen($product['description']) ? '...' : '' ) . '</div>
                                            <small><a class="wcs-link-text-style-2" href="' . esc_html($product['link']) . '" target="_blank"><i>View Product Page</i></a></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ';
						} else {
							echo '<div>
                                <div class="time">
                                ' . esc_html($thread['sender']) . ' <span>' . esc_html($thread['created_at']) . '</span>
                                </div>
                                <div class="message">
                                    <div class="wcs-row" style="width: 300px; background-color: #dbdbdb; padding: 5px; padding-bottom: 0px; border-radius: 4px;">
                                        <div class="wcs-col-2">
                                            <img src="' . esc_html($product['img'][0]) . '" style="width: 50px; height: 50px;" />
                                        </div>
                                        <div class="wcs-col-10">
                                            <div><b><a class="wcs-link-text-style-2" href="' . esc_html($product['link']) . '" target="_blank">' . esc_html($product['title']) . '</a></b></div>
                                            <div style="margin-top: -1px; margin-bottom: -2px; font-size: 9px;">' . esc_html(substr($product['description'], 0, 50)) . ( 50 < strlen($product['description']) ? '...' : '' ) . '</div>
                                            <small><a class="wcs-link-text-style-2" href="' . esc_html($product['link']) . '" target="_blank"><i>View Product Page</i></a></small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
						}
					}
				}

				if ( true === $thread['is_you'] ) {
					echo '<div>
                        <div class="time">
                        ' . esc_html($thread['created_at']) . '
                        </div>
                        <div class="message parker">
                        ' . wp_kses($thread['comment'], array( 'br' => array())) . '
                        </div>
                    </div>';
				} else {
					echo '<div>
                        <div class="time">
                        ' . esc_html($thread['sender']) . ' . <span>' . esc_html($thread['created_at']) . '</span>
                        </div>
                        <div class="message">
                        ' . wp_kses($thread['comment'], array( 'br' => array())) . '
                        </div>
                    </div>';
				}

				if ( count($thread['attachments']) ) {
					foreach ($thread['attachments'] as $attachment) {
						$attachmentExt = explode('.', $attachment['name']);

						$displayIcon = '';
						if ( 'jpg' == $attachmentExt[1] || 'jpeg' == $attachmentExt[1] || 'png' == $attachmentExt[1] ) {
							$displayIcon = $attachment['url'];
						} else {
							$displayIcon = IBXFWL_HELPDESK_ASSETS_URL . '/images/pdf-icon.png'; // part to pdf preview
						}

						if ( $thread['is_you'] ) {
							echo '<div>
                                <div class="time">
                                ' . esc_html($thread['created_at']) . '
                                </div>
                                <div class="message parker">
                                    <div class="wcs-row" style="width: 300px; background-color: #dbdbdb; padding: 5px; padding-bottom: 0px; border-radius: 4px;">
                                        <div class="wcs-col-2">
                                            <img src="' . esc_html($displayIcon) . '" style="width: 50px; height: 50px;" />
                                        </div>
                                        <div class="wcs-col-10">
                                            <div><b><a class="wcs-link-text-style-2" style="color: #333" href="' . esc_html($attachment['url']) . '" target="_blank">' . esc_html($attachment['name']) . '</a></b></div>
                                            <div style="margin-top: -3px; margin-bottom: -5px; font-size: 9px; color: #333">Attachment</div>
                                            <small><a class="wcs-link-text-style-2" style="color: #333" href="' . esc_html($attachment['url']) . '" target="_blank"><i>View Attachment</i></a></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ';
						} else {
							echo '<div>
                                <div class="time">
                                ' . esc_html($thread['sender']) . ' <span>' . esc_html($thread['created_at']) . '</span>
                                </div>
                                <div class="message">
                                    <div class="wcs-row" style="width: 300px; background-color: #dbdbdb; padding: 5px; padding-bottom: 0px; border-radius: 4px;">
                                        <div class="wcs-col-2">
                                            <img src="' . esc_html($displayIcon) . '" style="width: 50px; height: 50px;" />
                                        </div>
                                        <div class="wcs-col-10">
                                            <div><b><a class="wcs-link-text-style-2" style="color: #333" href="' . esc_html($attachment['url']) . '" target="_blank">' . esc_html($attachment['name']) . '</a></b></div>
                                            <div style="margin-top: -3px; margin-bottom: -5px; font-size: 9px; color: #333">Attachment</div>
                                            <small><a class="wcs-link-text-style-2" style="color: #333" href="' . esc_html($attachment['url']) . '" target="_blank"><i>View Attachment</i></a></small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
						}
					}
				}
			}
			
			echo '</div>';
			echo '</div>';
		}

		private static function showTicketThreadStyle2( $ticket) {
			echo '<div class="wcs-style-2-chat-messages" id="wcs-chat-style-2">';
			
			foreach ($ticket['threads'] as $indx => $thread) {
				if ( 0 === $indx && ( count($ticket['products']) > 0 ) ) {
					foreach ($ticket['products'] as $product) {
						if ( $thread['is_you'] ) {
							echo '<div class="wcs-style-2-message-box-holder">
                                <div class="wcs-style-2-message-box">
                                    <div class="wcs-row" style="width: 300px; background-color: #dbdbdb; padding: 5px; padding-bottom: 0px; border-radius: 4px;">
                                        <div class="wcs-col-2">
                                            <img src="' . esc_html($product['img'][0]) . '" style="width: 50px; height: 50px;" />
                                        </div>
                                        <div class="wcs-col-10">
                                            <div><b><a class="wcs-link-text-style-2" href="' . esc_html($product['link']) . '" target="_blank">' . esc_html($product['title']) . '</a></b></div>
                                            <div style="margin-top: -1px; margin-bottom: -2px; font-size: 9px;">' . esc_html(substr($product['description'], 0, 50)) . ( strlen($product['description']) > 50 ? '...' : '' ) . '</div>
                                            <small><a class="wcs-link-text-style-2" href="' . esc_html($product['link']) . '" target="_blank"><i>View Product Page</i></a></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="wcs-style-2-message-receiver">
                                ' . esc_html($thread['created_at']) . '
                                </div>
                            </div>';
						} else {
							echo '<div class="wcs-style-2-message-box-holder">
                                <div class="wcs-style-2-message-sender">
                                ' . esc_html($thread['sender']) . ' <span>' . esc_html($thread['created_at']) . '</span>
                                </div>
                                <div class="wcs-style-2-message-box wcs-style-2-message-partner">
                                    <div class="wcs-row" style="width: 300px; background-color: #dbdbdb; padding: 5px; padding-bottom: 0px; border-radius: 4px;">
                                        <div class="wcs-col-2">
                                            <img src="' . esc_html($product['img'][0]) . '" style="width: 50px; height: 50px;" />
                                        </div>
                                        <div class="wcs-col-10">
                                            <div><b><a class="wcs-link-text-style-2" href="' . esc_html($product['link']) . '" target="_blank">' . esc_html($product['title']) . '</a></b></div>
                                            <div style="margin-top: -1px; margin-bottom: -2px; font-size: 9px;">' . esc_html(substr($product['description'], 0, 50)) . ( strlen($product['description']) > 50 ? '...' : '' ) . '</div>
                                            <small><a class="wcs-link-text-style-2" href="' . esc_html($product['link']) . '" target="_blank"><i>View Product Page</i></a></small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
						}
					}
				}

				if ( true === $thread['is_you'] ) {
					echo '<div class="wcs-style-2-message-box-holder">
                        <div class="wcs-style-2-message-box">
                        ' . wp_kses($thread['comment'], array( 'br' => array())) . '
                        </div>
                        <div class="wcs-style-2-message-receiver">
                        ' . esc_html($thread['created_at']) . '
                        </div>
                    </div>';
				} else {
					echo '<div class="wcs-style-2-message-box-holder">
                        <div class="wcs-style-2-message-sender">
                        ' . esc_html($thread['sender']) . ' <span>' . esc_html($thread['created_at']) . '</span>
                        </div>
                        <div class="wcs-style-2-message-box wcs-style-2-message-partner">
                        ' . wp_kses($thread['comment'], array( 'br' => array())) . '
                        </div>
                    </div>';
				}

				if ( count($thread['attachments']) ) {
					foreach ($thread['attachments'] as $attachment) {
						$attachmentExt = explode('.', $attachment['name']);

						$displayIcon = '';
						if ( 'jpg' == $attachmentExt[1] || 'jpeg' == $attachmentExt[1] || 'png' == $attachmentExt[1] ) {
							$displayIcon = $attachment['url'];
						} else {
							$displayIcon = IBXFWL_HELPDESK_ASSETS_URL . '/images/pdf-icon.png'; // part to pdf preview
						}

						if ( $thread['is_you'] ) {
							echo '<div class="wcs-style-2-message-box-holder">
                                <div class="wcs-style-2-message-box">
                                    <div class="wcs-row" style="width: 300px; background-color: #dbdbdb; padding: 5px; padding-bottom: 0px; border-radius: 4px;">
                                        <div class="wcs-col-2">
                                            <img src="' . esc_html($displayIcon) . '" style="width: 50px; height: 50px;" />
                                        </div>
                                        <div class="wcs-col-10">
                                            <div><b><a class="wcs-link-text-style-2" href="' . esc_html($attachment['url']) . '" target="_blank">' . esc_html($attachment['name']) . '</a></b></div>
                                            <div style="margin-top: -3px; margin-bottom: -5px; font-size: 9px;">Attachment</div>
                                            <small><a class="wcs-link-text-style-2" href="' . esc_html($attachment['url']) . '" target="_blank"><i>View Attachment</i></a></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="wcs-style-2-message-receiver">
                                ' . esc_html($thread['created_at']) . '
                                </div>
                            </div>';
						} else {
							echo '<div class="wcs-style-2-message-box-holder">
                                <div class="wcs-style-2-message-sender">
                                ' . esc_html($thread['sender']) . ' <span>' . esc_html($thread['created_at']) . '</span>
                                </div>
                                <div class="wcs-style-2-message-box wcs-style-2-message-partner">
                                    <div class="wcs-row" style="width: 300px; background-color: #dbdbdb; padding: 5px; padding-bottom: 0px; border-radius: 4px;">
                                        <div class="wcs-col-2">
                                            <img src="' . esc_html($displayIcon) . '" style="width: 50px; height: 50px;" />
                                        </div>
                                        <div class="wcs-col-10">
                                            <div><b><a class="wcs-link-text-style-2" href="' . esc_html($attachment['url']) . '" target="_blank">' . esc_html($attachment['name']) . '</a></b></div>
                                            <div style="margin-top: -3px; margin-bottom: -5px; font-size: 9px;">Attachment</div>
                                            <small><a class="wcs-link-text-style-2" href="' . esc_html($attachment['url']) . '" target="_blank"><i>View Attachment</i></a></small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
						}
					}
				}
			}

			echo '</div>';
		}

		private static function showTicketThreadStyle1( $ticket) {
			echo '<ul id="wcs-chat-style-1">';
			
			foreach ($ticket['threads'] as $indx => $thread) {
				if ( 0 === $indx && ( 0 < count($ticket['products']) ) ) {
					foreach ($ticket['products'] as $product) {
						if ( $thread['is_you'] ) {
							echo '<li class="me">
                                <div class="entete">
                                    <h3>' . esc_html($thread['created_at']) . '</h3>
                                    <h2>' . esc_html($thread['sender']) . '</h2>
                                    <span class="status blue"></span>
                                </div>
                                <div class="triangle"></div>
                                <div class="message">
                                    <img src="' . esc_html($product['img'][0]) . '" style="width: 150px; height: 150px;" /> <br/>
                                    <div><b>' . esc_html($product['title']) . '</b></div>
                                    <small><a class="wcs-link-text-${style}" href="' . esc_html($product['link']) . '" target="_blank"><i>View Product Page</i></a></small>
                                </div>
                            </li>';
						} else {
							echo '<li class="you">
                                <div class="entete">
                                    <h2>' . esc_html($thread['sender']) . '</h2>
                                    <h3>' . esc_html($thread['created_at']) . '</h3>
                                    <span class="status blue"></span>
                                </div>
                                <div class="triangle"></div>
                                <div class="message">
                                    <img src="' . esc_html($product['img'][0]) . '" style="width: 150px; height: 150px;" /> <br/>
                                    <div><b>' . esc_html($product['title']) . '</b></div>
                                    <small><a class="wcs-link-text-${style}" href="' . esc_html($product['link']) . '" target="_blank"><i>View Product Page</i></a></small>
                                </div>
                            </li>';
						}
					}
				}

				if ( true === $thread['is_you'] ) {
					echo '<li class="me">
                        <div class="entete">
                            <h3>' . esc_html($thread['created_at']) . '</h3>
                            <h2>' . esc_html($thread['sender']) . '</h2>
                            <span class="status blue"></span>
                        </div>
                        <div class="triangle"></div>
                        <div class="message">
                            ' . wp_kses($thread['comment'], array( 'br' => array())) . '
                        </div>
                    </li>';
				} else {
					echo '<li class="you">
                        <div class="entete">
                            <h2>' . esc_html($thread['sender']) . '</h2>
                            <h3>' . esc_html($thread['created_at']) . '</h3>
                            <span class="status blue"></span>
                        </div>
                        <div class="triangle"></div>
                        <div class="message">
                            ' . wp_kses($thread['comment'], array( 'br' => array())) . '
                        </div>
                    </li>';
				}

				if ( count($thread['attachments']) ) {
					foreach ($thread['attachments'] as $attachment) {
						$attachmentExt = explode('.', $attachment['name']);

						$displayIcon = '';
						if ( 'jpg' == $attachmentExt[1] || 'jpeg' == $attachmentExt[1] || 'png' == $attachmentExt[1] ) {
							$displayIcon = $attachment['url'];
						} else {
							$displayIcon = IBXFWL_HELPDESK_ASSETS_URL . '/images/pdf-icon.png'; // part to pdf preview
						}

						if ( $thread['is_you'] ) {
							echo '<li class="me">
                                <div class="entete">
                                    <h3>' . esc_html($thread['created_at']) . '</h3>
                                    <h2>' . esc_html($thread['sender']) . '</h2>
                                    <span class="status blue"></span>
                                </div>
                                <div class="triangle"></div>
                                <div class="message">
                                    <img src="' . esc_html($displayIcon) . '" style="width: 150px; max-height: 150px;" /> <br/>
                                    <div><b>' . esc_html($attachment['name']) . '</b></div>
                                    <small><a class="wcs-link-text-${style}" href="' . esc_html($attachment['url']) . '" target="_blank"><i>View Product Page</i></a></small>
                                </div>
                            </li>';
						} else {
							echo '<li class="you">
                                <div class="entete">
                                    <h2>' . esc_html($thread['sender']) . '</h2>
                                    <h3>' . esc_html($thread['created_at']) . '</h3>
                                    <span class="status blue"></span>
                                </div>
                                <div class="triangle"></div>
                                <div class="message">
                                    <img src="' . esc_html($displayIcon) . '" style="width: 150px; max-height: 150px;" /> <br/>
                                    <div><b>' . esc_html($attachment['name']) . '</b></div>
                                    <small><a class="wcs-link-text-${style}" href="' . esc_html($attachment['url']) . '" target="_blank"><i>View Product Page</i></a></small>
                                </div>
                            </li>';
						}
					}
				}
			}



			echo '</ul>';
		}

		private static function showTicketThreadHeader( $ticket) {
			echo '
                <div id="wcsPreviewSelectedProduct">
                    <div class="wcs-preview-selected-container">
                        <div class="wcs-row">
                            <div class="wcs-col-6">
                                ' . esc_html__('Ref:', 'inbox-for-woocommerce') . ' <b>#' . esc_html(strtoupper($ticket['ref'])) . '</b>
                            </div>
                            <div class="wcs-col-6">
                                ' . esc_html__('Status:', 'inbox-for-woocommerce') . ' <b>' . esc_html(strtoupper($ticket['status'])) . '</b>
                            </div>
                        </div>
                        <div class="wcs-row">
                            <div class="wcs-col-6">
                                ' . esc_html__('Type:', 'inbox-for-woocommerce') . ' <b>' . esc_html(strtoupper($ticket['type'])) . '</b>
                            </div>
                            <div class="wcs-col-6">
                                ' . esc_html__('Date Created:', 'inbox-for-woocommerce') . ' <b>' . esc_html( $ticket['created_at'] ) . '</b>
                            </div>
                        </div>
                    </div>
                </div>
            ';
		}

		private static function showActiveTickets( $tickets) {
			echo '
                <div class="wcs-ticket-thread-active-list">
                    <div class="wcs-ticket-thread-active-list-inner">
                    <div class="wcs-row">
                        <div class="wcs-col-12">
                            <div class="wcs-ticket-thread-active-list-header">' . esc_html__('Active Tickets', 'inbox-for-woocommerce') . '</div>
                        </div>
                    </div>
                ';
			
			foreach ($tickets as $ticket) {
				echo '
                    <a class="wcs-ticket-list-link" href="/wp-admin/admin.php?page=woocommerce-inbox-sweito-preview&reference=' . esc_html($ticket['ref']) . '">
                    <div class="wcs-ticket-thread-active-item-container">
                        <div class="wcs-row">
                            <div class="wcs-col-12">
                                <div class="wcs-ticket-thread-active-list-username">&#128100; ' . esc_html($ticket['user']) . '</div>
                            </div>
                        </div>

                        <div class="wcs-row">
                            <div class="wcs-col-12">
                                <div class="wcs-ticket-thread-active-list-subject">' . esc_html(substr($ticket['subject'], 0, 30)) . ( strlen($ticket['subject']) > 30 ? '...' : '' ) . '</div>
                            </div>
                        </div>

                        <div class="wcs-row">
                            <div class="wcs-col-6">
                                <div class="wcs-ticket-thread-active-list-reference">#' . esc_html(strtoupper($ticket['ref'])) . '</div>
                            </div>
                            <div class="wcs-col-6">
                            <div class="wcs-ticket-thread-active-list-reference">' . esc_html($ticket['created_at']) . '</div>
                            </div>
                        </div>
                    </div>
                    </a>
                ';
			}
				
			echo '
                    </div>
                </div>
            ';
		}

		private static function importDirectories() {
			require_once IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php';
			require_once IBXFWL_SWEITO_INCLUDES_URL . '/TicketController.php';
		}

		private static function getActiveTickets() {
			return IBXFWL_Inbox_TicketController::getAdminUserActiveTickets();
		}

		private static function getTicketThread() { 
			$reference = isset($_GET['reference']) ? sanitize_text_field($_GET['reference']) : '';

			$currentUser = wp_get_current_user();
			$ticket = IBXFWL_Inbox_TicketController::getAdminUserTicketThreads($currentUser->data->user_email, $currentUser->ID, $reference);

			return $ticket;
		}
	}

}
