document.addEventListener('DOMContentLoaded', function () {
    activateWooCommerceHelpdeskSweitoCard();

    if (!document.getElementById('wooCommerSweitoDisplayStyle')) return;

    let setStyle = document.getElementById('wooCommerSweitoDisplayStyle').value;
    if (document.getElementById('wcs-chat-' + setStyle)) {
        scrollToBottomOfChat(setStyle);
        document.getElementById('wpfooter').style.position = 'relative';
    }
});

function scrollToBottomOfChat(setStyle) {
    document.getElementById('wcs-chat-' + setStyle).scrollTop = (document.getElementById('wcs-chat-' + setStyle).scrollHeight);
}

function activateWooCommerceHelpdeskSweitoCard() {
    let wcsWizard = document.getElementsByClassName('wcs-helpdesk-cards');

    if (wcsWizard.length > 0) {
        // remove active
        for (let i = 0; i < wcsWizard.length; i++) {
            wcsWizard[i].addEventListener('click', function ($event) {
                console.log($event);
                removeActiveWooCommerceHelpdeskSweitoCard($event);
            })
        }
    }
}

function removeActiveWooCommerceHelpdeskSweitoCard(e) {
    let wcsWizard = document.getElementsByClassName('wcs-helpdesk-cards');

    if (wcsWizard.length > 0) {
        // remove active
        for (let i = 0; i < wcsWizard.length; i++) {
            wcsWizard[i].classList.remove('wcs-helpdesk-active');
        }
    }

    let location = '';
    if (e.target.classList.contains('wcs-helpdesk-cards')) {
        e.target.classList.add('wcs-helpdesk-active');
        location = e.target.dataset.location;
    } else if (e.target.parentElement.classList.contains('wcs-helpdesk-cards')) {
        e.target.parentElement.classList.add('wcs-helpdesk-active');
        location = e.target.parentElement.dataset.location;
    } else {
        e.target.parentElement.parentElement.classList.add('wcs-helpdesk-active');
        location = e.target.parentElement.parentElement.dataset.location;
    }

    document.getElementById('wcsSetupLocation').value = location;
}

function sendTicketThreadReply() {
    let productReference = document.getElementById('wooCommerceSweitoTicketReference').value;
    let description = document.getElementById('wooCommerceSweitoReplyInboxMessage').value;

    document.getElementById('wooCommerceSweitoInboxReplyButton').innerText = 'Please wait ...';
    document.getElementById('wooCommerceSweitoInboxReplyButton').setAttribute('disabled', 'disabled');
    document.getElementById('wooCommerceSweitoSendReplyError').innerText = '';
    let uploadAttachments = [];

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_message_admin_reply_thread_ajax',
            reference: productReference,
            description: description,
            attachments: uploadAttachments,
            attachment_length: uploadAttachments.length,
            nonce: woocommercesweitoscript_object.wcs_thread_nonce
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wooCommerceSweitoReplyInboxMessage').value = '';

                document.getElementById('wooCommerceSweitoInboxReplyButton').innerText = 'Send';
                document.getElementById('wooCommerceSweitoInboxReplyButton').removeAttribute('disabled');

                let setStyle = document.getElementById('wooCommerSweitoDisplayStyle').value;
                if ( setStyle == 'style-1' ) {
                    let li = document.createElement('li');
                    li.classList.add('me');
                    li.innerHTML = `
                        <div class="entete">
                            <h3>Just Now</h3>
                            <h2>You</h2>
                            <span class="status blue"></span>
                        </div>
                        <div class="triangle"></div>
                        <div class="message">
                            ${description}
                        </div>
                    `;
                    document.getElementById('wcs-chat-' + setStyle).appendChild(li);
                } else if ( setStyle == 'style-2' ) {
                    let div = document.createElement('div');
                    div.classList.add('wcs-style-2-message-box-holder');
                    div.innerHTML = `
                        <div class="wcs-style-2-message-box">
                            ${description}
                        </div>
                        <div class="wcs-style-2-message-receiver">
                            Just Now
                        </div>
                    `;
                    document.getElementById('wcs-chat-' + setStyle).appendChild(div);
                } else if ( setStyle == 'style-3' ) {
                    let div = document.createElement('div');
                    div.innerHTML = `
                        <div class="time">
                            Just Now
                        </div>
                        <div class="message parker">
                            ${description}
                        </div>
                    `;
                    document.getElementById('wcs-chat-' + setStyle).appendChild(div);
                }

                if (setAttachments.length > 0) {
                    for (let i = 0; i < setAttachments.length; i++) {

                        let attachmentExt = setAttachments[i].dataset.name.split('.');
                        let displayIcon = '';
                        if ( attachmentExt[1] == 'jpg' || attachmentExt[1] == 'jpeg' || attachmentExt[1] == 'png' ) {
                            displayIcon = setAttachments[i].dataset.url;
                        } else {
                            displayIcon = woocommercesweitoscript_object.pdf_logo; // part to pdf preview
                        }

                        if ( setStyle === 'style-1' ) {
                            let li2 = document.createElement('li');
                            li2.classList.add('me');
                            li2.innerHTML = `
                                <div class="entete">
                                    <h3>Just Now</h3>
                                    <h2>You</h2>
                                    <span class="status blue"></span>
                                </div>
                                <div class="triangle"></div>
                                <div class="message">
                                    <img src="${displayIcon}" style="width: 150px; height: 150px;" /> <br/>
                                    <div><b>${setAttachments[i].dataset.name}</b></div>
                                    <small><a class="wcs-link-text-${setStyle}" href="${setAttachments[i].dataset.url}" target="_blank"><i>View Attachment</i></a></small>
                                </div>
                            `;
                            document.getElementById('wcs-chat-' + setStyle).appendChild(li2);
                        } else if ( setStyle === 'style-2' ) {
                            let div2 = document.createElement('div');
                            div2.classList.add('wcs-style-2-message-box-holder');
                            div2.innerHTML = `
                                <div class="wcs-style-2-message-box">
                                    <div class="wcs-row" style="width: 300px; background-color: #dbdbdb; padding: 5px; padding-bottom: 0px; border-radius: 4px;">
                                        <div class="wcs-col-2">
                                            <img src="${displayIcon}" style="width: 50px; height: 50px;" />
                                        </div>
                                        <div class="wcs-col-10">
                                            <div><b><a class="wcs-link-text-${setStyle}" href="${setAttachments[i].dataset.url}" target="_blank">${setAttachments[i].dataset.name}</a></b></div>
                                            <div style="margin-top: -3px; margin-bottom: -5px; font-size: 9px; color: #333">Attachment</div>
                                            <small><a class="wcs-link-text-${setStyle}" href="${setAttachments[i].dataset.url}" target="_blank"><i>View Attachment</i></a></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="wcs-style-2-message-receiver">
                                    Just Now
                                </div>
                            `;
                            document.getElementById('wcs-chat-' + setStyle).appendChild(div2);
                        } else if ( setStyle === 'style-3' ) {
                            let div2 = document.createElement('div');
                            div2.innerHTML = `
                                <div class="time">
                                    Just Now
                                </div>
                                <div class="message parker">
                                    <div class="wcs-row" style="width: 300px; background-color: #dbdbdb; padding: 5px; padding-bottom: 0px; border-radius: 4px;">
                                        <div class="wcs-col-2">
                                            <img src="${displayIcon}" style="width: 50px; height: 50px;" />
                                        </div>
                                        <div class="wcs-col-10">
                                            <div><b><a class="wcs-link-text-${setStyle}" href="${setAttachments[i].dataset.url}" target="_blank">${setAttachments[i].dataset.name}</a></b></div>
                                            <div style="margin-top: -3px; margin-bottom: -5px; font-size: 9px; color: #333">Attachment</div>
                                            <small><a class="wcs-link-text-${setStyle}" href="${setAttachments[i].dataset.url}" target="_blank"><i>View Attachment</i></a></small>
                                        </div>
                                    </div>
                                </div>
                            `;
                            document.getElementById('wcs-chat-' + setStyle).appendChild(div2);
                        }
                    }
                }

                if (setAttachments.length > 0) {
                    for (let i = 0; i < setAttachments.length; i++) {
                        setAttachments[i].parentElement.removeChild(setAttachments[i]);
                    }
                }
                document.getElementById('uploadedDocumentPreview2').style.display = 'none';
                document.getElementById('wcs-chat-' + setStyle).scrollTop = (document.getElementById('wcs-chat-' + setStyle).scrollHeight);
            } else {
                document.getElementById('wooCommerceSweitoSendReplyError').innerHTML = '<small></small>'+res.data+'</small></small>';
                document.getElementById('wooCommerceSweitoSendReplyError').style.display = '';

                document.getElementById('wooCommerceSweitoInboxReplyButton').innerText = 'Send';
                document.getElementById('wooCommerceSweitoInboxReplyButton').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoInboxReplyButton').innerText = 'Send';
            document.getElementById('wooCommerceSweitoInboxReplyButton').removeAttribute('disabled');
        }
    });
}

function wcsSetupCreateAccount() {
    let firstname = document.getElementById('wooCommerceSweitoFirstName').value;
    let lastname = document.getElementById('wooCommerceSweitoLastName').value;
    let companyname = document.getElementById('wooCommerceSweitoCompanyName').value;
    let emailAddress = document.getElementById('wooCommerceSweitoEmailAddress').value;
    let site = document.getElementById('wooCommerceSweitoSiteUrl').value;

    document.getElementById('wooCommerceSweitoSignUpButton').innerText = 'Please wait ...';
    document.getElementById('wooCommerceSweitoSignUpButton').setAttribute('disabled', 'disabled');
    document.getElementById('wooCommerceSweitoSendReplyError').innerText = '';

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_setup_create_account',
            first_name: firstname,
            last_name: lastname,
            company_name: companyname,
            email_address: emailAddress,
            site: site,
            nonce: woocommercesweitoscript_object.wcs_setup_nonce
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wooCommerceSweitoSignUpButton').innerText = 'Get Started';
                document.getElementById('wooCommerceSweitoSignUpButton').removeAttribute('disabled');

                wcsSetupSwitchScreen(res.data);
            } else {
                document.getElementById('wooCommerceSweitoSendReplyError').innerHTML = '<small></small>'+res.data+'</small></small>';
                document.getElementById('wooCommerceSweitoSendReplyError').style.display = '';

                // if (res.data == 'Seems you already have an account with user, you should login instead') {
                //     wcsSetupSwitchScreen('signin');
                // }

                document.getElementById('wooCommerceSweitoSignUpButton').innerText = 'Get Started';
                document.getElementById('wooCommerceSweitoSignUpButton').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoSignUpButton').innerText = 'Get Started';
            document.getElementById('wooCommerceSweitoSignUpButton').removeAttribute('disabled');
        }
    });
}

function wcsSetupSigninAccount() {
    let email = document.getElementById('wooCommerceSweitoEmail').value;

    document.getElementById('wooCommerceSweitoSignInButton').innerText = 'Please wait ...';
    document.getElementById('wooCommerceSweitoSignInButton').setAttribute('disabled', 'disabled');
    document.getElementById('wooCommerceSweitoSigninError').innerText = '';

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_setup_signin_account',
            email: email,
            nonce: woocommercesweitoscript_object.wcs_setup_nonce
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wooCommerceSweitoSignInButton').innerText = 'Get Started';
                document.getElementById('wooCommerceSweitoSignInButton').removeAttribute('disabled');

                wcsSetupSwitchScreen(res.data);
                document.getElementById('wcsOtpSentEmail').innerText = email;
            } else {
                document.getElementById('wooCommerceSweitoSigninError').innerHTML = '<small></small>'+res.data+'</small></small>';
                document.getElementById('wooCommerceSweitoSigninError').style.display = '';

                document.getElementById('wooCommerceSweitoSignInButton').innerText = 'Get Started';
                document.getElementById('wooCommerceSweitoSignInButton').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoSignInButton').innerText = 'Get Started';
            document.getElementById('wooCommerceSweitoSignInButton').removeAttribute('disabled');
        }
    });
}

function wcsSetupSigninOTPAccount() {
    let email = document.getElementById('wooCommerceSweitoEmail').value;
    let otp = document.getElementById('wooCommerceSweitoOTP').value;

    document.getElementById('wooCommerceSweitoSignInOTPButton').innerText = 'Please wait ...';
    document.getElementById('wooCommerceSweitoSignInOTPButton').setAttribute('disabled', 'disabled');
    document.getElementById('wooCommerceSweitoSigninOTPError').innerText = '';

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_setup_signin_verify_otp_account',
            email: email,
            otp: otp,
            nonce: woocommercesweitoscript_object.wcs_setup_nonce
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wooCommerceSweitoSignInOTPButton').innerText = 'Get Started';
                document.getElementById('wooCommerceSweitoSignInOTPButton').removeAttribute('disabled');

                wcsSetupSwitchScreen(res.data);
            } else {
                let errorText = res.data;

                if (res.data == 'The provided OTP does not match') {
                    errorText = woocommercesweitoscript_object.wcs_setup_otp_mismatch;
                } else if (res.data == 'account_exists') {
                    errorText = woocommercesweitoscript_object.wcs_setup_otp_failed;
                }

                document.getElementById('wooCommerceSweitoSigninOTPError').innerHTML = '<small></small>'+errorText+'</small></small>';
                document.getElementById('wooCommerceSweitoSigninOTPError').style.display = '';

                document.getElementById('wooCommerceSweitoSignInOTPButton').innerText = 'Get Started';
                document.getElementById('wooCommerceSweitoSignInOTPButton').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoSignInOTPButton').innerText = 'Get Started';
            document.getElementById('wooCommerceSweitoSignInOTPButton').removeAttribute('disabled');
        }
    });
}

function wcsSaveSetupLocation() {
    let location = document.getElementById('wcsSetupLocation').value;

    document.getElementById('wooCommerceSweitoSelectLocationButton').innerText = 'Please wait ...';
    document.getElementById('wooCommerceSweitoSelectLocationButton').setAttribute('disabled', 'disabled');
    document.getElementById('wooCommerceSweitoSendReplyError').innerText = '';

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_setup_save_ticket_location',
            location: location ? location : 'wpadmin',
            nonce: woocommercesweitoscript_object.wcs_setup_nonce
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wooCommerceSweitoSelectLocationButton').innerText = 'Continue';
                document.getElementById('wooCommerceSweitoSelectLocationButton').removeAttribute('disabled');

                wcsSetupSwitchScreen(res.data);
            } else {
                document.getElementById('wooCommerceSweitoSendReplyError').innerHTML = '<small></small>'+res.data+'</small></small>';
                document.getElementById('wooCommerceSweitoSendReplyError').style.display = '';

                document.getElementById('wooCommerceSweitoSelectLocationButton').innerText = 'Continue';
                document.getElementById('wooCommerceSweitoSelectLocationButton').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoSelectLocationButton').innerText = 'Continue';
            document.getElementById('wooCommerceSweitoSelectLocationButton').removeAttribute('disabled');
        }
    });
}

function wcsAuthenticateZendesk() {
    document.getElementById('wooCommerceSweitoZendeskAuthButton').innerText = 'Please wait ...';
    document.getElementById('wooCommerceSweitoZendeskAuthButton').setAttribute('disabled', 'disabled');
    document.getElementById('wooCommerceSweitoZendeskAuthError').innerText = '';

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_setup_zendesk_auth_token',
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wooCommerceSweitoZendeskAuthButton').innerText = 'Authenticate';
                document.getElementById('wooCommerceSweitoZendeskAuthButton').removeAttribute('disabled');

                // window.location.href = res.data;
                window.location.href = res.data;
            } else {
                document.getElementById('wooCommerceSweitoZendeskAuthError').innerHTML = '<small></small>'+res.data+'</small></small>';
                document.getElementById('wooCommerceSweitoZendeskAuthError').style.display = '';

                document.getElementById('wooCommerceSweitoZendeskAuthButton').innerText = 'Authenticate';
                document.getElementById('wooCommerceSweitoZendeskAuthButton').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoZendeskAuthButton').innerText = 'Authenticate';
            document.getElementById('wooCommerceSweitoZendeskAuthButton').removeAttribute('disabled');
        }
    });
}

function wcsAuthenticateFreshdesk() {
    document.getElementById('wooCommerceSweitoFreshdeskAuthButton').innerText = 'Please wait ...';
    document.getElementById('wooCommerceSweitoFreshdeskAuthButton').setAttribute('disabled', 'disabled');
    document.getElementById('wooCommerceSweitoFreshdeskAuthError').innerText = '';

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_setup_freshdesk_auth_token',
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wooCommerceSweitoFreshdeskAuthButton').innerText = 'Authenticate';
                document.getElementById('wooCommerceSweitoFreshdeskAuthButton').removeAttribute('disabled');

                // window.location.href = res.data;
                window.location.href = res.data;
            } else {
                document.getElementById('wooCommerceSweitoFreshdeskAuthError').innerHTML = '<small></small>'+res.data+'</small></small>';
                document.getElementById('wooCommerceSweitoFreshdeskAuthError').style.display = '';

                document.getElementById('wooCommerceSweitoFreshdeskAuthButton').innerText = 'Authenticate';
                document.getElementById('wooCommerceSweitoFreshdeskAuthButton').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoFreshdeskAuthButton').innerText = 'Authenticate';
            document.getElementById('wooCommerceSweitoFreshdeskAuthButton').removeAttribute('disabled');
        }
    });
}

function wcsVerifyAuthenticateZendesk() {
    document.getElementById('wooCommerceSweitoVerifyZendeskAuthButton').innerText = 'Please wait ...';
    document.getElementById('wooCommerceSweitoVerifyZendeskAuthButton').setAttribute('disabled', 'disabled');

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_setup_verify_helpdesk_auth',
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wooCommerceSweitoVerifyZendeskAuthButton').innerText = 'Verify Auth';
                document.getElementById('wooCommerceSweitoVerifyZendeskAuthButton').removeAttribute('disabled');

                // window.location.href = res.data;
                wcsSetupSwitchScreen(res.data);
            } else {
                document.getElementById('wooCommerceSweitoVerifyZendeskAuthButton').innerText = 'Verify Auth';
                document.getElementById('wooCommerceSweitoVerifyZendeskAuthButton').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoVerifyZendeskAuthButton').innerText = 'Verify Auth';
            document.getElementById('wooCommerceSweitoVerifyZendeskAuthButton').removeAttribute('disabled');
        }
    });
}

function wcsVerifyAuthenticateFreshdesk() {
    document.getElementById('wooCommerceSweitoVerifyFreshAuthButton').innerText = 'Please wait ...';
    document.getElementById('wooCommerceSweitoVerifyFreshAuthButton').setAttribute('disabled', 'disabled');

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_setup_verify_helpdesk_auth',
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wooCommerceSweitoVerifyFreshAuthButton').innerText = 'Verify Auth';
                document.getElementById('wooCommerceSweitoVerifyFreshAuthButton').removeAttribute('disabled');

                // window.location.href = res.data;
                wcsSetupSwitchScreen(res.data);
            } else {
                document.getElementById('wooCommerceSweitoVerifyFreshAuthButton').innerText = 'Verify Auth';
                document.getElementById('wooCommerceSweitoVerifyFreshAuthButton').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoVerifyFreshAuthButton').innerText = 'Verify Auth';
            document.getElementById('wooCommerceSweitoVerifyFreshAuthButton').removeAttribute('disabled');
        }
    });
}

function wcsUpdateSetupSite() {
    let newSite = document.getElementById('wcsSiteUpdate').value;

    document.getElementById('wooCommerceSweitoUpdateSiteButton').innerText = 'Please wait ...';
    document.getElementById('wooCommerceSweitoUpdateSiteButton').setAttribute('disabled', 'disabled');
    document.getElementById('wooCommerceSweitoUpdateSiteError').innerText = '';

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_setup_update_site',
            site: newSite,
            nonce: woocommercesweitoscript_object.wcs_setup_nonce
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wooCommerceSweitoUpdateSiteButton').innerText = 'Continue';
                document.getElementById('wooCommerceSweitoUpdateSiteButton').removeAttribute('disabled');

                // window.location.href = res.data;
                // window.open(res.data, '_blank').focus();
                document.getElementById('wooCommerceSweitoUpdateSiteError').innerHTML = '<small></small>Updated successfully </small></small>';
                document.getElementById('wooCommerceSweitoUpdateSiteError').style.display = '';

                setTimeout(()=>{
                    document.getElementById('wooCommerceSweitoUpdateSiteError').style.display = 'none';
                },2000);
            } else {
                document.getElementById('wooCommerceSweitoUpdateSiteError').innerHTML = '<small></small>'+res.data+'</small></small>';
                document.getElementById('wooCommerceSweitoUpdateSiteError').style.display = '';

                document.getElementById('wooCommerceSweitoUpdateSiteButton').innerText = 'Continue';
                document.getElementById('wooCommerceSweitoUpdateSiteButton').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoUpdateSiteButton').innerText = 'Continue';
            document.getElementById('wooCommerceSweitoUpdateSiteButton').removeAttribute('disabled');
        }
    });
}

function wcsUpdateSetupSite2() {
    let newSite = document.getElementById('wcsSiteUpdate2').value;

    document.getElementById('wooCommerceSweitoUpdateSiteButton2').innerText = 'Please wait ...';
    document.getElementById('wooCommerceSweitoUpdateSiteButton2').setAttribute('disabled', 'disabled');
    document.getElementById('wooCommerceSweitoUpdateSiteError2').innerText = '';

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_setup_update_site',
            site: newSite,
            nonce: woocommercesweitoscript_object.wcs_setup_nonce
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wooCommerceSweitoUpdateSiteButton2').innerText = 'Continue';
                document.getElementById('wooCommerceSweitoUpdateSiteButton2').removeAttribute('disabled');

                // window.location.href = res.data;
                // window.open(res.data, '_blank').focus();
                document.getElementById('wooCommerceSweitoUpdateSiteError2').innerHTML = '<small></small>Updated successfully </small></small>';
                document.getElementById('wooCommerceSweitoUpdateSiteError2').style.display = '';
            } else {
                document.getElementById('wooCommerceSweitoUpdateSiteError2').innerHTML = '<small></small>'+res.data+'</small></small>';
                document.getElementById('wooCommerceSweitoUpdateSiteError2').style.display = '';

                document.getElementById('wooCommerceSweitoUpdateSiteButton2').innerText = 'Continue';
                document.getElementById('wooCommerceSweitoUpdateSiteButton2').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoUpdateSiteButton2').innerText = 'Continue';
            document.getElementById('wooCommerceSweitoUpdateSiteButton2').removeAttribute('disabled');
        }
    });
}

function wcsSaveSetupPersonalize() {
    let allowCustomerInbox = document.getElementById('wcsAllowMyAccountPage').checked ? 'yes' : 'no';
    let allowCTASection = document.getElementById('wcsAllowCTASection').checked ? 'yes' : 'no';

    document.getElementById('wooCommerceSweitoPersonalizationButton').innerText = 'Please wait ...';
    document.getElementById('wooCommerceSweitoPersonalizationButton').setAttribute('disabled', 'disabled');
    document.getElementById('wooCommerceSweitoPersonalizeError').innerText = '';

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_setup_personalization',
            allow_customer_inbox: allowCustomerInbox,
            allow_cta_section: allowCTASection,
            nonce: woocommercesweitoscript_object.wcs_setup_nonce
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wooCommerceSweitoPersonalizationButton').innerText = 'Complete Setup';
                document.getElementById('wooCommerceSweitoPersonalizationButton').removeAttribute('disabled');

                // window.location.href = res.data;
                // window.open(res.data, '_blank').focus();
                document.getElementById('wcsSkipPersonalizationSection').click();
            } else {
                document.getElementById('wooCommerceSweitoPersonalizeError').innerHTML = '<small></small>'+res.data+'</small></small>';
                document.getElementById('wooCommerceSweitoPersonalizeError').style.display = '';

                document.getElementById('wooCommerceSweitoPersonalizationButton').innerText = 'Complete Setup';
                document.getElementById('wooCommerceSweitoPersonalizationButton').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoPersonalizationButton').innerText = 'Complete Setup';
            document.getElementById('wooCommerceSweitoPersonalizationButton').removeAttribute('disabled');
        }
    });
}

function wcsSetupSwitchScreen($state) {
    if ($state == 'signup' || $state == 'signin' || $state == 'account-otp') {
        document.getElementById('wcsSetupStage1').style.display = '';
    } else {
        document.getElementById('wcsSetupStage1').style.display = 'none';
    }

    document.getElementById('wcsSignupSection').style.display = ($state == 'signup') ? '' : 'none';
    document.getElementById('wcsSigninSection').style.display = ($state == 'signin') ? '' : 'none';
    document.getElementById('wcsSigninVerifySection').style.display = ($state == 'account-otp') ? '' : 'none';
    document.getElementById('wcsSetupStage2').style.display = ($state == 'api-key') ? '' : 'none';
    document.getElementById('wcsSetupStage3').style.display = ($state == 'select-location') ? '' : 'none';
    document.getElementById('wcsSetupStage4').style.display = ($state == 'personalize') ? '' : 'none';
    document.getElementById('wcsSetupStage3_2').style.display = ($state == 'zendesk-auth') ? '' : 'none';
    document.getElementById('wcsSetupStage3_3').style.display = ($state == 'freshdesk-auth') ? '' : 'none';
}