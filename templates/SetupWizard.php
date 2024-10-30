<?php
/**
 * Setup Wizard for extension
 * 
 * @package Inbox-For-WooCommerce-LTE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'IBXFWL_Inbox_SetupWizard' ) ) {
	class IBXFWL_Inbox_SetupWizard {
		public static function showHomeSection() {
			require_once IBXFWL_SWEITO_INCLUDES_URL . '/SettingController.php';
			// check if API key and credentials are set
			$stage = 1;

			$savedToken = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_TOKEN);
			$savedSite = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);
			$savedEmail = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_APP_SITE);
			$savedLocation = get_option(IBXFWL_Inbox_SettingController::SETTING_GENERAL_TICKET_LOCATION);

			if ($savedToken && $savedSite && $savedEmail) {
				$stage = 3;

				if ( $savedLocation ) {
					$stage = 4;
		
					$savedReference = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_REFERENCE);
					$savedHelpdeskStatus = get_option(IBXFWL_Inbox_SettingController::SETTING_THIRDPARTY_SWEITO_HELPDESK_STATUS);
		
					if ( 'zendesk' == $savedLocation ) {
						$stage = 32;
						if ($savedReference && 'zendesk-active' == $savedHelpdeskStatus) {
							$stage = 4;
						}
					} elseif ( 'freshdesk' == $savedLocation ) {
						$stage = 33;
						if ($savedReference && 'freshdesk-active' == $savedHelpdeskStatus) {
							$stage = 4;
						}
					}
				}
			}

			echo '<h1 class="wcs-text-center">' . esc_html__('Inbox for WooCommerce', 'inbox-for-woocommerce') . '</h1>';
			echo '<p class="wcs-text-center">' . esc_html__('Welcome to Inbox for WooCommerce Setup!', 'inbox-for-woocommerce') . '</p>';
			echo '
            <div id="wcsSetupStage1" style="display: ' . ( 1 == $stage ? 'block' : 'none' ) . '" class="wcs-card wcs-card-center">
                <div class="wcs-row">
                    <div class="wcs-col-6">
                        <div style="position: relative; height: 100%;">
                            <img style="position: absolute; top: 20%; left: 15%; z-index: 2; width: 300px" src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/woocommerce-inbox-logo.png"  />
                            <div style="position: absolute; bottom: 5%; left: 25%;">Powered By</div>
                            <img style="position: absolute; bottom: 2%; left: 45%; z-index: 2; width: 80px" src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/sweito-logo.png"  />
                        </div>
                    </div>
                    <div class="wcs-col-6">
                        <br/>
                        <div id="wcsSignupSection" style="display: block;">
                            <h1>' . esc_html__('Get Started! Signup', 'inbox-for-woocommerce') . '</h1>
                            <br/>
                            <div class="wcs-row">
                                <div class="wcs-col-6">
                                    <div class="form-group">
                                        <label>' . esc_html__('First Name', 'inbox-for-woocommerce') . '</label>
                                        <input type="text" id="wooCommerceSweitoFirstName" class="input wcs-input" />
                                    </div>
                                </div>
                                <div class="wcs-col-6">
                                    <div class="form-group">
                                        <label>' . esc_html__('Last Name', 'inbox-for-woocommerce') . '</label>
                                        <input type="text" id="wooCommerceSweitoLastName" class="input wcs-input" />
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <div class="wcs-row">
                                <div class="wcs-col-6">
                                    <div class="form-group">
                                        <label>' . esc_html__('Store Name', 'inbox-for-woocommerce') . '</label> <br/>
                                        <input type="text" id="wooCommerceSweitoCompanyName" value="' . esc_html(get_bloginfo('name')) . '" class="input wcs-input" />
                                    </div>
                                </div>
                                <div class="wcs-col-6">
                                    <div class="form-group">
                                        <label>' . esc_html__('Store URL', 'inbox-for-woocommerce') . '</label>
                                        <input type="text" value="' . esc_html(get_site_url()) . '" id="wooCommerceSweitoSiteUrl" class="input wcs-input" />
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <div class="wcs-row">
                                <div class="wcs-col-12">
                                    <div class="form-group">
                                        <label>' . esc_html__('Email Address', 'inbox-for-woocommerce') . '</label> <br/>
                                        <input type="email" id="wooCommerceSweitoEmailAddress" value="' . esc_html(get_bloginfo('admin_email')) . '" class="input wcs-input" />
                                    </div>
                                </div>
                            </div>
                            <br/><br/>
                            <div class="wcs-row">
                                <div class="wcs-col-12">
                                    <button id="wooCommerceSweitoSignUpButton" onClick="wcsSetupCreateAccount()" class="button button-primary button-big">' . esc_html__('Get Started!', 'inbox-for-woocommerce') . '</button>
                                    <div id="wooCommerceSweitoSendReplyError"></div>
                                </div>
                            </div>
                            <br/>
                            <p>' . esc_html__('Or', 'inbox-for-woocommerce') . ' <a style="cursor: pointer" onClick="wcsSetupSwitchScreen(\'signin\')">' . esc_html__('I already have a Sweito Account', 'inbox-for-woocommerce') . '</a></p>
                        </div>
                        <div id="wcsSigninSection" style="display: none;">
                            <h1>' . esc_html__('Welcome Back! Signin', 'inbox-for-woocommerce') . '</h1>
                            <br/><br/>
                            <div class="wcs-row">
                                <div class="wcs-col-12">
                                    <div class="form-group">
                                        <label>' . esc_html__('Email Address', 'inbox-for-woocommerce') . '</label> <br/>
                                        <input type="email" id="wooCommerceSweitoEmail" value="' . esc_html(get_bloginfo('admin_email')) . '" class="input wcs-input" />
                                    </div>
                                </div>
                            </div>
                            <br/><br/>
                            <div class="wcs-row">
                                <div class="wcs-col-12">
                                    <button onClick="wcsSetupSigninAccount()" id="wooCommerceSweitoSignInButton" class="button button-primary button-big">' . esc_html__('Sign In!', 'inbox-for-woocommerce') . '</button>
                                    <div id="wooCommerceSweitoSigninError" style="color: #e74747"></div>
                                </div>
                            </div>
                            <br/>
                            <p>' . esc_html__('Or', 'inbox-for-woocommerce') . ' <a style="cursor: pointer" onClick="wcsSetupSwitchScreen(\'signup\')">' . esc_html__('Don\'t have an account? Sign Up', 'inbox-for-woocommerce') . '</a></p>
                        </div>
                        <div id="wcsSigninVerifySection" style="display: none;">
                            <h1>' . esc_html__('Verify your email to continue', 'inbox-for-woocommerce') . '</h1>
                            <p>' . esc_html__('We have sent an email with an access OTP to ', 'inbox-for-woocommerce') . '<span id="wcsOtpSentEmail"></span>,' . esc_html__('if your account exist in out records', 'inbox-for-woocommerce') . '</p>
                            <br/>
                            <div class="wcs-row">
                                <div class="wcs-col-12">
                                    <div class="form-group">
                                        <label>' . esc_html__('Enter OTP', 'inbox-for-woocommerce') . '</label> <br/>
                                        <input type="text" id="wooCommerceSweitoOTP" value="" class="input wcs-input" />
                                    </div>
                                </div>
                            </div>
                            <br/><br/>
                            <div class="wcs-row">
                                <div class="wcs-col-12">
                                    <button onClick="wcsSetupSigninOTPAccount()" id="wooCommerceSweitoSignInOTPButton" class="button button-primary button-big">' . esc_html__('Verify OTP', 'inbox-for-woocommerce') . '</button>
                                    <div id="wooCommerceSweitoSigninOTPError" style="color: #e74747"></div>
                                </div>
                            </div>
                            <br/>
                            <p>' . esc_html__('Or', 'inbox-for-woocommerce') . ' <a style="cursor: pointer" onClick="wcsSetupSwitchScreen(\'signin\')">' . esc_html__('I want to change my email address', 'inbox-for-woocommerce') . '</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="wcsSetupStage2" style="display: ' . ( 2 == $stage ? 'block' : 'none' ) . ';" class="wcs-card wcs-card-center">
                <div class="wcs-row">
                    <div class="wcs-col-12 wcs-text-center">
                        <h1>' . esc_html__('Enter your purchase key', 'inbox-for-woocommerce') . '</h1>
                    </div>
                </div>
                <div class="wcs-row">
                    <div class="wcs-col-12">
                        <br/>
                        <br/>
                        <div class="wcs-row">
                            <div class="wcs-col-12">
                                <div class="form-group">
                                    <label>' . esc_html__('Purchase Key', 'inbox-for-woocommerce') . '</label> <br/>
                                    <input type="text" id="wcsSetupActivationKey" value="" class="input wcs-input" />
                                </div>
                            </div>
                        </div>
                        <br/>
                    </div>
                </div>
                <br/>
                <div class="wcs-row">
                    <div class="wcs-col-12 wcs-text-center">
                        <button id="wooCommerceSweitoActivationKeyButton" onClick="wcsSaveActivationKey()" class="button button-primary">' . esc_html__('Continue', 'inbox-for-woocommerce') . '</button>
                        <div id="wooCommerceSweitoActivationKeySendReplyError"></div>
                    </div>
                </div>
                <br/>
            </div>
            <div id="wcsSetupStage3" style="display: ' . ( 3 == $stage ? 'block' : 'none' ) . ';" class="wcs-card wcs-card-center">
                <div class="wcs-row">
                    <div class="wcs-col-12 wcs-text-center">
                        <h1>' . esc_html__('Select where you wish to manage tickets', 'inbox-for-woocommerce') . '</h1>
                    </div>
                </div>
                <div class="wcs-row">
                    <div class="wcs-col-6">
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <div class="wcs-row">
                            <div class="wcs-col-12 wcs-text-center">
                                <div data-location="wpadmin" class="wcs-helpdesk-cards wcs-helpdesk-active wsc-card-cursor-point" style="margin-left: 50px">
                                    <p class="wsc-helpdesk-text"><small>' . esc_html__('WordPress Admin', 'inbox-for-woocommerce') . '</small></p>
                                    <img class="wcs-helpdesk-wordpress-img" src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/wordpress-logo.png" />
                                </div>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <br/>
                    </div>
                    <div class="wcs-col-6">
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <div class="wcs-row">
                            <div class="wcs-col-6">
                                <div data-location="zendesk" class="wcs-helpdesk-cards wsc-card-cursor-point">
                                    <p class="wsc-helpdesk-text"><small>' . esc_html__('Zendesk', 'inbox-for-woocommerce') . '</small></p>
                                    <img class="wcs-helpdesk-img" src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/zendesk-logo.png" />
                                </div>
                            </div>
                            <div class="wcs-col-6">
                                <div data-location="freshdesk" class="wcs-helpdesk-cards wsc-card-cursor-point">
                                    <p class="wsc-helpdesk-text"><small>' . esc_html__('Freshdesk', 'inbox-for-woocommerce') . '</small></p>
                                    <img class="wcs-helpdesk-freshdesk-img" src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/freshdesk-logo.png" /> 
                                </div>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <br/>
                    </div>
                </div>
                <div class="wcs-row">
                    <div class="wcs-col-12 wcs-text-center">
                        <input type="hidden" id="wcsSetupLocation" />
                        <button id="wooCommerceSweitoSelectLocationButton" onClick="wcsSaveSetupLocation()" class="button button-primary">' . esc_html__('Continue', 'inbox-for-woocommerce') . '</button>
                    </div>
                </div>
                <br/>
            </div>
            <div id="wcsSetupStage3_2" style="display: ' . ( 32 == $stage ? 'block' : 'none' ) . ';" class="wcs-card wcs-card-center">
                <div class="wcs-row" style="margin-top: -30px">
                    <div class="wcs-col-12 wcs-text-right">
                        <div style="position: relative;">
                            <a class="wcs-setup-change-url-button" onClick="document.getElementById(\'wcsZendeskChangeSiteModal\').style.display = \'\';">' . esc_html__('Change Store URL', 'inbox-for-woocommerce') . '</a>
                            <div id="wcsZendeskChangeSiteModal" style="display: none;">
                                <div class="wcs-setup-change-shadow" onClick="document.getElementById(\'wcsZendeskChangeSiteModal\').style.display = \'none\';"></div>
                                <div class="wcs-setup-change-url-popup">
                                    <div class="wcs-row">
                                        <div class="wcs-col-9">
                                            <input id="wcsSiteUpdate" value="' . esc_html($savedSite) . '" class="input wcs-input" style="height: 30px;" /> 
                                        </div>
                                        <div class="wcs-col-3">
                                            <button onClick="wcsUpdateSetupSite()" id="wooCommerceSweitoUpdateSiteButton" class="button">' . esc_html__('Update', 'inbox-for-woocommerce') . '</button> 
                                            <div id="wooCommerceSweitoUpdateSiteError"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcs-row">
                    <div class="wcs-col-12 wcs-text-center">
                        <h1 style="line-height: 30px">' . esc_html__('Authenticate your Zendesk Account', 'inbox-for-woocommerce') . '</h1>
                    </div>
                </div>
                <div class="wcs-row">
                    <div class="wcs-col-3">
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <div class="wcs-row">
                            <div class="wcs-col-12 wcs-text-center">
                                <div data-location="wpadmin" class="wcs-helpdesk-cards wcs-helpdesk-active wsc-card-cursor-point" style="margin-left: 50px">
                                    <p class="wsc-helpdesk-text"><small>' . esc_html__('Zendesk', 'inbox-for-woocommerce') . '</small></p>
                                    <img class="wcs-helpdesk-wordpress-img" src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/zendesk-logo.png" />
                                </div>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <br/>
                    </div>
                    <div class="wcs-col-9">
                        <br/>
                        <br/>
                        <br/>
                        <div class="wcs-row">
                            <div class="wcs-col-6">
                                <p style="font-size: 16px;">
                                ' . esc_html__('We would be redirecting you to Sweito to Authenticate your Zendesk account and 
                                    return back here to continue the process', 'inbox-for-woocommerce') . '
                                </p>
                                <br/>
                                <button id="wooCommerceSweitoZendeskAuthButton" onClick="wcsAuthenticateZendesk()" class="button button-primary">' . esc_html__('Authenticate', 'inbox-for-woocommerce') . '</button>
                                <p style="font-size: 13px; color: #d3d3d3">
                                ' . esc_html__('Please Note: By proceeding, you are agreeing to the terms and conditions governing the use of Sweito application', 'inbox-for-woocommerce') . ' 
                                ' . esc_html__('If you just created an account, your password for Sweito has been sent to your email address. Use this to login in the Sweito', 'inbox-for-woocommerce') . '
                                </p>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <br/>
                    </div>
                </div>
                <div class="wcs-row">
                    <div class="wcs-col-12 wcs-text-center">
                        <p>' . esc_html__('Once you have completed the authentication process, click here to verify and continue', 'inbox-for-woocommerce') . '</p>
                        <button id="wooCommerceSweitoVerifyZendeskAuthButton" onClick="wcsVerifyAuthenticateZendesk()" class="button">' . esc_html__('Verify Auth', 'inbox-for-woocommerce') . '</button>
                        <div id="wooCommerceSweitoZendeskAuthError"></div>
                        <br/><br/>
                        <a onClick="wcsSetupSwitchScreen(\'select-location\')">' . esc_html__('Go Back', 'inbox-for-woocommerce') . '</a>
                    </div>
                </div>
                <br/>
            </div>
            <div id="wcsSetupStage3_3" style="display: ' . ( 33 == $stage ? 'block' : 'none' ) . ';" class="wcs-card wcs-card-center">
                <div class="wcs-row" style="margin-top: -30px">
                    <div class="wcs-col-12 wcs-text-right">
                        <div style="position: relative;">
                            <a class="wcs-setup-change-url-button" onClick="document.getElementById(\'wcsZendeskChangeSiteModal2\').style.display = \'\';">' . esc_html__('Change Store URL', 'inbox-for-woocommerce') . '</a>
                            <div id="wcsZendeskChangeSiteModal2" style="display: none;">
                                <div class="wcs-setup-change-shadow" onClick="document.getElementById(\'wcsZendeskChangeSiteModal2\').style.display = \'none\';"></div>
                                <div class="wcs-setup-change-url-popup">
                                    <div class="wcs-row">
                                        <div class="wcs-col-9">
                                            <input id="wcsSiteUpdate2" value="' . esc_html($savedSite) . '" class="input wcs-input" style="height: 30px;" /> 
                                        </div>
                                        <div class="wcs-col-3">
                                            <button onClick="wcsUpdateSetupSite2()" id="wooCommerceSweitoUpdateSiteButton2" class="button">' . esc_html__('Update', 'inbox-for-woocommerce') . '</button> 
                                            <div id="wooCommerceSweitoUpdateSiteError2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcs-row">
                    <div class="wcs-col-12 wcs-text-center">
                        <h1>' . esc_html__('Authenticate your Freshdesk Account', 'inbox-for-woocommerce') . '</h1>
                    </div>
                </div>
                <div class="wcs-row">
                    <div class="wcs-col-3">
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <div class="wcs-row">
                            <div class="wcs-col-12 wcs-text-center">
                                <div data-location="wpadmin" class="wcs-helpdesk-cards wcs-helpdesk-active wsc-card-cursor-point" style="margin-left: 50px">
                                    <p class="wsc-helpdesk-text"><small>' . esc_html__('Freshdesk', 'inbox-for-woocommerce') . '</small></p>
                                    <img class="wcs-helpdesk-wordpress-img" src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/freshdesk-logo.png" />
                                </div>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <br/>
                    </div>
                    <div class="wcs-col-9">
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <div class="wcs-row">
                            <div class="wcs-col-6">
                                <p>
                                ' . esc_html__('We would be redirecting you to Sweito to Authenticate your Freshdesk account and 
                                    return back here to continue the process', 'inbox-for-woocommerce') . '
                                </p>
                                <br/>
                                <button id="wooCommerceSweitoFreshdeskAuthButton" onClick="wcsAuthenticateFreshdesk()" class="button button-primary">' . esc_html__('Authenticate', 'inbox-for-woocommerce') . '</button>
                                <p style="font-size: 13px; color: #d3d3d3">
                                ' . esc_html__('Please Note: By proceeding, you are agreeing to the terms and conditions governing the use of Sweito application', 'inbox-for-woocommerce') . ' 
                                ' . esc_html__('If you just created an account, your password for Sweito has been sent to your email address. Use this to login in the Sweito', 'inbox-for-woocommerce') . '
                                </p>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <br/>
                    </div>
                </div>
                <div class="wcs-row">
                    <div class="wcs-col-12 wcs-text-center">
                        <p>' . esc_html__('Once you have completed the authentication process, click here to verify and continue', 'inbox-for-woocommerce') . '</p>
                        <button id="wooCommerceSweitoVerifyFreshAuthButton" onClick="wcsVerifyAuthenticateFreshdesk()" class="button button-primary">' . esc_html__('Verify Auth', 'inbox-for-woocommerce') . '</button>
                        <div id="wooCommerceSweitoFreshdeskAuthError"></div>
                        <br/><br/>
                        <a style="cursor: pointer" onClick="wcsSetupSwitchScreen(\'select-location\')">' . esc_html__('Go Back', 'inbox-for-woocommerce') . '</a>
                    </div>
                </div>
                <br/>
            </div>
            <div id="wcsSetupStage4" style="display: ' . ( 4 == $stage ? 'block' : 'none' ) . ';" class="wcs-card wcs-card-center">
                <div class="wcs-row">
                    <div class="wcs-col-12 wcs-text-center">
                        <h1>' . esc_html__('Personalize your settings', 'inbox-for-woocommerce') . '</h1>
                    </div>
                </div>
                <div class="wcs-row">
                    <div class="wcs-col-12">
                        <br/>
                        <br/>
                        <div class="wcs-row">
                            <div class="wcs-col-12">
                                <div class="form-group">
                                    <label>
                                        <input id="wcsAllowMyAccountPage" type="checkbox" checked class="checkbox" />
                                        <strong>' . esc_html__('Allow customer "Inbox" section in MyAccount page for registered users', 'inbox-for-woocommerce') . '</strong>
                                        <img style="width: 100%; margin-top: 10px;" src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/demo-inbox-preview.png" />
                                    </label>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="wcs-row">
                            <div class="wcs-col-12">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" disabled="disabled" class="checkbox" />
                                        <strong>' . esc_html__('Allow inquire box on product display screen', 'inbox-for-woocommerce') . '</strong>
                                        <img style="width: 100%;" src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/demo-inquiry-button.png" />
                                    </label>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="wcs-row">
                            <div class="wcs-col-12">
                                <div class="form-group">
                                    <label>
                                        <input id="wcsAllowCTASection" type="checkbox" checked class="checkbox" />
                                        <strong>' . esc_html__('Allow CTA Banner on product display screen', 'inbox-for-woocommerce') . '</strong>
                                        <img style="width: 100%; margin-top: 10px;" src="' . esc_html(IBXFWL_HELPDESK_ASSETS_URL) . '/images/demo-cta-banner.png" />
                                    </label>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="wcs-row">
                            <div class="wcs-col-12 wcs-text-center">
                                <button id="wooCommerceSweitoPersonalizationButton" onClick="wcsSaveSetupPersonalize()" class="button button-primary">' . esc_html__('Complete Setup', 'inbox-for-woocommerce') . '</button>
                                <div id="wooCommerceSweitoPersonalizeError"></div>
                                <br/><br/>
                                <a id="wcsSkipPersonalizationSection" href="' . esc_html(admin_url('admin.php')) . '?page=woocommerce-inbox-sweito">' . esc_html__('Skip', 'inbox-for-woocommerce') . '</a>
                            </div>
                        </div>
                        <br/>
                    </div>
                </div>
            </div>
            ';
		}
	}

}
