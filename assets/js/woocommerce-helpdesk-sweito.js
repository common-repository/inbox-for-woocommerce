/**
 * Single Product
 */

 document.addEventListener('DOMContentLoaded', function($) {
    var modal = document.getElementById('wooCommerceSweitoModal');
    var closeButtons = document.getElementsByClassName("wcs-close");
    
    for (let i = 0; i < closeButtons.length; i++) {
        closeButtons[i].addEventListener('click', function() {
            modal.style.display = "none";
        });
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    
    var wcsInquiryButton = document.getElementsByClassName('wcs-inquiry-button');
    
    if (wcsInquiryButton.length > 0) wcsSetupGoogleRecaptcha();

    for (let i = 0; i < wcsInquiryButton.length; i++) {
        wcsInquiryButton[i].addEventListener('click', function ($event) {
            $event.stopImmediatePropagation();
            $event.preventDefault();

            // get url
            var productId = $event.target.dataset.productId;
            document.getElementById('wooCommerceSweitoPreviewLoading').style.display = 'block';
            document.getElementById('wooCommerceSweitoPreviewForm').style.display = 'none';
            document.getElementById('wooCommerceSweitoSentSuccess').style.display = 'none';

            modal.style.display = "block";
            
            jQuery.ajax({
                type:"POST",
                dataType: 'json',
                url: woocommercesweitoscript_object.ajax_url,
                data: {
                    action: 'woocommerce_sweito_product_ajax',
                    id: productId,
                    nonce: woocommercesweitoscript_object.wcs_thread_nonce
                },
                success:function(res){
                    document.getElementById('wooCommerceSweitoProductId').value = productId
                    handleWooCommerceSweitoProductPreview(res);
                },
                error:function(xhr, status, error) {

                }
            });
        });
    }
    
    // $(document).on("click","#myBtn",function(e) {
        
    // });
});

function wcsSetupGoogleRecaptcha() {
    if ( ! document.getElementById('recaptcha_import') && woocommercesweitoscript_object.recaptcha_key && !window.grecaptcha ) {
        let script = document.createElement('script');
        script.setAttribute('src', 'https://www.google.com/recaptcha/api.js?render=' + woocommercesweitoscript_object.recaptcha_key);
        script.setAttribute('id', 'recaptcha_import');
        document.body.appendChild(script);
    }
}

function handleWooCommerceSweitoProductPreview(res) {
    if (res.success) {
        document.getElementById('wooCommerceSweitoPreviewImg').setAttribute('src', res.data.img[0]);
        document.getElementById('wooCommerceSweitoPreviewTitle').innerHTML = res.data.title;
        // document.getElementById('wooCommerceSweitoPreviewDescription').innerHTML = res.data.description.slice(0, 60);
        document.getElementById('wooCommerceSweitoPreviewPrice').innerHTML = res.data.price;

        document.getElementById('wooCommerceSweitoProductAccess').value = res.data.is_logged_in == true ? '1' : '0';

        document.getElementById('wooCommerceSweitoPreviewLoading').style.display = 'none';
        document.getElementById('wooCommerceSweitoPreviewForm').style.display = '';
        document.getElementById('wooCommerceSweitoSentSuccess').style.display = 'none';

        if ( res.data.is_logged_in ) {
            document.getElementById('wooCommerceSweitoEmailFieldSection').style.display = 'none';
            document.getElementById('wooCommerceSweitoInquiryField').setAttribute('rows', '10');
            document.getElementById('wooCommerceSweitoInquiryFieldSection').style.marginTop = '-30px';
        } else {
            document.getElementById('wooCommerceSweitoEmailFieldSection').style.display = '';
            document.getElementById('wooCommerceSweitoInquiryField').setAttribute('rows', '8');
            document.getElementById('wooCommerceSweitoInquiryFieldSection').style.marginTop = '0px';
        }
    }
}

function sendWooCommerceSweitoInquiryForm() {
    let senderEmail = document.getElementById('wooCommerceSweitoEmailField').value;
    let senderInquiry = document.getElementById('wooCommerceSweitoInquiryField').value;
    let senderOnline = document.getElementById('wooCommerceSweitoProductAccess').value;
    document.getElementById('wooCommerceSweitoEmailError').style.display = 'none';
    document.getElementById('wooCommerceSweitoInquiryError').style.display = 'none';

    if ( ! senderEmail && (senderOnline !== '1') ) {
        document.getElementById('wooCommerceSweitoEmailError').style.display = '';
        return;
    }

    if ( ! senderInquiry ) {
        document.getElementById('wooCommerceSweitoInquiryError').style.display = '';
        return;
    }

    document.getElementById('wooCommerceSweitoInquiryButton').innerText = woocommercesweitoscript_object.wcs_please_wait;
    document.getElementById('wooCommerceSweitoInquiryButton').setAttribute('disabled', 'disabled');

    document.getElementById('wooCommerceSweitoSendError').style.display = 'none';

    if (woocommercesweitoscript_object.recaptcha_key) {
        (window).grecaptcha.ready(() => {
            (window).grecaptcha.execute(woocommercesweitoscript_object.recaptcha_key, {action: 'create_comment'}).then((token) => {

                jQuery.ajax({
                    type:"POST",
                    dataType: 'json',
                    url: woocommercesweitoscript_object.ajax_url,
                    data: {
                        action: 'woocommerce_sweito_submit_inquiry_form',
                        id: document.getElementById('wooCommerceSweitoProductId').value,
                        email: senderEmail,
                        description: senderInquiry,
                        token: token,
                        nonce: woocommercesweitoscript_object.wcs_thread_nonce
                    },
                    success:function(res){
                        if (res.success) {
                            document.getElementById('wooCommerceSweitoEmailField').value = '';
                            document.getElementById('wooCommerceSweitoInquiryField').value = '';

                            document.getElementById('wooCommerceSentResponse').innerText = res.data;
                            document.getElementById('wooCommerceSweitoPreviewLoading').style.display = 'none';
                            document.getElementById('wooCommerceSweitoPreviewForm').style.display = 'none';
                            document.getElementById('wooCommerceSweitoSentSuccess').style.display = '';

                            document.getElementById('wooCommerceSweitoInquiryButton').innerText = 'Send';
                            document.getElementById('wooCommerceSweitoInquiryButton').removeAttribute('disabled');
                        } else {
                            document.getElementById('wooCommerceSweitoSendError').innerHTML = '<small></small>'+res.data+'</small></small>';
                            document.getElementById('wooCommerceSweitoSendError').style.display = '';

                            document.getElementById('wooCommerceSweitoInquiryButton').innerText = 'Send';
                            document.getElementById('wooCommerceSweitoInquiryButton').removeAttribute('disabled');
                        }
                    },
                    error:function(xhr, status, error) {
                        document.getElementById('wooCommerceSweitoInquiryButton').innerText = 'Send';
                        document.getElementById('wooCommerceSweitoInquiryButton').removeAttribute('disabled');
                    }
                });
            });
        });
    } else {
        jQuery.ajax({
            type:"POST",
            dataType: 'json',
            url: woocommercesweitoscript_object.ajax_url,
            data: {
                action: 'woocommerce_sweito_submit_inquiry_form',
                id: document.getElementById('wooCommerceSweitoProductId').value,
                email: senderEmail,
                description: senderInquiry,
                nonce: woocommercesweitoscript_object.wcs_thread_nonce
            },
            success:function(res){
                if (res.success) {
                    document.getElementById('wooCommerceSweitoEmailField').value = '';
                    document.getElementById('wooCommerceSweitoInquiryField').value = '';

                    document.getElementById('wooCommerceSentResponse').innerText = res.data;
                    document.getElementById('wooCommerceSweitoPreviewLoading').style.display = 'none';
                    document.getElementById('wooCommerceSweitoPreviewForm').style.display = 'none';
                    document.getElementById('wooCommerceSweitoSentSuccess').style.display = '';

                    document.getElementById('wooCommerceSweitoInquiryButton').innerText = 'Send';
                    document.getElementById('wooCommerceSweitoInquiryButton').removeAttribute('disabled');
                } else {
                    document.getElementById('wooCommerceSweitoSendError').innerHTML = '<small></small>'+res.data+'</small></small>';
                    document.getElementById('wooCommerceSweitoSendError').style.display = '';

                    document.getElementById('wooCommerceSweitoInquiryButton').innerText = 'Send';
                    document.getElementById('wooCommerceSweitoInquiryButton').removeAttribute('disabled');
                }
            },
            error:function(xhr, status, error) {
                document.getElementById('wooCommerceSweitoInquiryButton').innerText = 'Send';
                document.getElementById('wooCommerceSweitoInquiryButton').removeAttribute('disabled');
            }
        });
    }
    
}


/**
 * My Accounts
 */

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('mainInboxPage') && document.getElementById('newInboxPage')) {
        getUserInboxMessages();
    }
})

function showAddNewInbox() {
    showAccountInboxSection('new');
}

function getUserInboxMessages() {
    showAccountInboxSection('loading');

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_message_list_ajax',
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wcsTicketListSection').innerHTML = '';
                if (res.data.length > 0) {

                    res.data.map(item => {
                        let tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><a href="#" onClick="viewTicketThread('${item.ref}')">#${item.ref.toUpperCase()} `+(!item.read_at ? '<span class="wcs-ticket-list-status">new</span>' : '')+`</a></td>
                            <td><span class="wcs-table-status-${item.status}">${item.status.toUpperCase()}</span></td>
                            <td><span class="wcs-ticket-type-${item.status}">${item.type.toUpperCase()}</span></td>
                            <td>${item.updated_at}</td>
                            <td><a class="button" onClick="viewTicketThread('${item.ref}')">View</a></td>
                        `;

                        document.getElementById('wcsTicketListSection').appendChild(tr);
                    })


                    document.getElementById('wcsNoMessageFoundInInbox').style.display = 'none';
                } else {
                    document.getElementById('wcsNoMessageFoundInInbox').style.display = '';
                }

                showAccountInboxSection('tickets');
            }
        },
        error:function(xhr, status, error) {

        }
    });
}

function viewTicketThread(reference) {
    showAccountInboxSection('loading');

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_message_threads_ajax',
            reference: reference,
            nonce: woocommercesweitoscript_object.wcs_thread_nonce
        },
        success:function(res){
            if (res.success) {
                showAccountInboxSection('threads');
                document.getElementById('wooCommerceSweitoTicketReference').value = reference;

                // check style in use
                let setStyle = document.getElementById('wooCommerSweitoDisplayStyle').value;

                document.getElementById('wooCommerceSweitoThreadRefDisplay').innerText = '#' + res.data.ref.toUpperCase();
                document.getElementById('wooCommerceSweitoThreadStatusDisplay').innerText = res.data.status.toUpperCase();
                document.getElementById('wooCommerceSweitoThreadTypeDisplay').innerText = res.data.type.toUpperCase();
                document.getElementById('wooCommerceSweitoThreadDateDisplay').innerText = res.data.created_at;

                res.data.threads.map((item, indx) => {
                    if ( setStyle === 'style-1' ) {
                        setupStyle1Item(item, indx, res.data.products, setStyle);
                    }
                });

                setTimeout(() => {
                    document.getElementById('wcs-chat-' + setStyle).scrollTop = (document.getElementById('wcs-chat-' + setStyle).scrollHeight);
                    document.getElementById('wooCommerceSweitoThreadReplySection').scrollIntoView({behavior: "smooth", block: "end"});
                },500);
                
            } else {
                showAccountInboxSection('tickets');
            }
        },
        error:function(xhr, status, error) {
            showAccountInboxSection('tickets')
        }
    });
}

function showAccountInboxSection(section) {
    document.getElementById('mainInboxLoading').style.display = (section == 'loading') ? '' : 'none';
    document.getElementById('mainInboxPage').style.display = (section == 'tickets') ? '' : 'none';
    document.getElementById('newInboxPage').style.display = (section == 'new') ? '' : 'none';
    document.getElementById('mainInboxThreadPreview').style.display = (section == 'threads') ? '' : 'none';
}

function setupStyle1Item(item, indx, products, style) {
    if ((indx === 0) && (products.length > 0)) {
        products.map((item2) => {
            let li = document.createElement('li');
            if ( item.is_you ) {
                li.classList.add('me');
                li.innerHTML = `
                    <div class="entete">
                        <h3>${item.created_at}</h3>
                        <h2>${item.sender}</h2>
                        <span class="status blue"></span>
                    </div>
                    <div class="triangle"></div>
                    <div class="message">
                        <img src="${item2.img[0]}" style="width: 150px; height: 150px;" /> <br/>
                        <div><b>${item2.title}</b></div>
                        <small><a class="wcs-link-text-${style}" href="${item2.link}" target="_blank"><i>View Product Page</i></a></small>
                    </div>
                `;
            } else {
                li.classList.add('you');
                li.innerHTML = `
                    <div class="entete">
                        <h2>${item.sender}</h2>
                        <h3>${item.created_at}</h3>
                        <span class="status blue"></span>
                    </div>
                    <div class="triangle"></div>
                    <div class="message">
                        <img src="${item2.img[0]}" style="width: 150px; height: 150px;" /> <br/>
                        <div><b>${item2.title}</b></div>
                        <small><a class="wcs-link-text-${style}" href="${item2.link}" target="_blank"><i>View Product Page</i></a></small>
                    </div>
                `;  
            }
            

            document.getElementById('wcs-chat-' + style).appendChild(li);
        });
    } 

    let li = document.createElement('li');
    if ( item.is_you ) {
        li.classList.add('me');
        li.innerHTML = `
            <div class="entete">
                <h3>${item.created_at}</h3>
                <h2>${item.sender}</h2>
                <span class="status blue"></span>
            </div>
            <div class="triangle"></div>
            <div class="message">
                ${item.comment}
            </div>
        `;

    } else {
        li.classList.add('you');

        li.innerHTML = `
            <div class="entete">
                <h2>${item.sender}</h2>
                <h3>${item.created_at}</h3>
                <span class="status blue"></span>
            </div>
            <div class="triangle"></div>
            <div class="message">
                ${item.comment}
            </div>
        `;
    }
    
    document.getElementById('wcs-chat-' + style).appendChild(li);

    if (item.attachments.length > 0) {
        item.attachments.map(attachment => {
            let li2 = document.createElement('li');
            let attachmentExt = attachment.name.split('.');
            let displayIcon = '';
            if ( attachmentExt[1] == 'jpg' || attachmentExt[1] == 'jpeg' || attachmentExt[1] == 'png' ) {
                displayIcon = attachment.url;
            } else {
                displayIcon = woocommercesweitoscript_object.pdf_logo; // part to pdf preview
            }

            if ( item.is_you ) {
                li2.classList.add('me');
                li2.innerHTML = `
                    <div class="entete">
                        <h3>${item.created_at}</h3>
                        <h2>${item.sender}</h2>
                        <span class="status blue"></span>
                    </div>
                    <div class="triangle"></div>
                    <div class="message">
                        <img src="${displayIcon}" style="width: 150px; height: 150px;" /> <br/>
                        <div><b>${attachment.name}</b></div>
                        <small><a class="wcs-link-text-${style}" href="${attachment.url}" target="_blank"><i>View Attachment</i></a></small>
                    </div>
                `;
            } else {
                li2.classList.add('you');
                li2.innerHTML = `
                    <div class="entete">
                        <h2>${item.sender}</h2>
                        <h3>${item.created_at}</h3>
                        <span class="status blue"></span>
                    </div>
                    <div class="triangle"></div>
                    <div class="message">
                        <img src="${displayIcon}" style="width: 150px; height: 150px;" /> <br/>
                        <div><b>${attachment.name}</b></div>
                        <small><a class="wcs-link-text-${style}" href="${attachment.url}" target="_blank"><i>View Attachment</i></a></small>
                    </div>
                `;
            }

            document.getElementById('wcs-chat-' + style).appendChild(li2);
        });        
    }
}

function showWooCommerceSweitoProductList() {
    let selectedType = document.getElementById('wcsMessageType');
    if (  selectedType && selectedType.value !== 'product-related' ) {
        document.getElementById('productRelatedFormSection').style.display = 'none';
        return;
    };
    
    let searchField = document.getElementById('wcsSearchField');
    document.getElementById('productRelatedFormSection').style.display = '';
    document.getElementById('wcsDropdownLoading').style.display = '';
    document.getElementById('wcsProductDisplayItem').innerHTML = '';
    document.getElementById('wcsSearchField').focus();

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_product_list_ajax',
            search: searchField ? searchField.value : '',
            nonce: woocommercesweitoscript_object.wcs_thread_nonce
        },
        success:function(res){
            console.log(res);
            if (res.success) {
                res.data.map(item => {
                    let div = document.createElement('div');
                    div.innerHTML = `
                        <div class="wcs-col-2 wcs-text-center" data-product-id="${item.id}">
                            <img class="wcs-dropdown-img" src="${item.img[0]}" data-product-id="${item.id}" />
                        </div>
                        <div class="wcs-col-10" data-product-id="${item.id}">
                            <strong data-product-id="${item.id}">${item.title}</strong>
                            <div class="wcs-product-description" data-product-id="${item.id}">${item.description}</div>
                            <div class="wcs-product-price" data-product-id="${item.id}">${item.price}</div>
                        </div>
                    `;
                    div.classList.add('wcs-row');
                    div.classList.add('wcs-dropdown-option');
                    div.setAttribute('data-product-id', item.id);
                    div.addEventListener('click', ($event) => {
                        selectWooCommerceSweitoProduct($event, item);
                    });
                    document.getElementById('wcsProductDisplayItem').appendChild(div);
                });

                document.getElementById('wcsDropdownLoading').style.display = 'none';
            }
        },
        error:function(xhr, status, error) {

        }
    });
}

function wooCommerceSweitoSearchByTyping() {
    // show loading 
    document.getElementById('wcsDropdownLoading').style.display = '';
    document.getElementById('wcsProductDisplayItem').innerHTML = '';
    
    let timeout = 3;
    let interval = setInterval(() => {
        if (timeout == 0) {
            showWooCommerceSweitoProductList();
            clearInterval(interval);
        }
        timeout--;
    }, 1000);
}

function wooCommerceSweitoSearchBlur() {
    setTimeout(() => {
        document.getElementsByClassName('wcs-product-dropdown')[0].style.display = 'none';
    }, 1000);
}

function wooCommerceSweitoSearchFocus() {
    document.getElementsByClassName('wcs-product-dropdown')[0].style.display = '';
}

function selectWooCommerceSweitoProduct(event, product) {
    document.getElementById('wcsSelectedProductId').value = event.target.dataset.productId;
    document.getElementById('previewSelectedBox').innerHTML = '';
    let div = document.createElement('div');
    div.innerHTML = `
        <div class="wcs-col-2 wcs-text-center" data-product-id="${product.id}">
            <img class="wcs-dropdown-img" src="${product.img[0]}" data-product-id="${product.id}" />
        </div>
        <div class="wcs-col-10" data-product-id="${product.id}">
            <strong data-product-id="${product.id}">${product.title}</strong>
            <div class="wcs-product-description" data-product-id="${product.id}">${product.description}</div>
            <div class="wcs-product-price" data-product-id="${product.id}">${product.price}</div>
        </div>
    `;
    div.classList.add('wcs-row');
    document.getElementById('previewSelectedBox').appendChild(div);

    showWooCommerceSelectionPreview();
}

function showWooCommerceSelectionPreview(state = 'preview') {
    if ( state == 'preview' ) {
        document.getElementById('wcsPreviewSelectedProduct').style.display = '';
        document.getElementById('wcsSearchNewProduct').style.display = 'none';
    } else {
        document.getElementById('wcsPreviewSelectedProduct').style.display = 'none';
        document.getElementById('wcsSearchNewProduct').style.display = '';
        document.getElementById('wcsSelectedProductId').value = '';
    }
}

function sendNewInboxMessage() {
    let type = document.getElementById('wcsMessageType').value;
    let productId = document.getElementById('wcsSelectedProductId').value;
    let description = document.getElementById('wcsMessageContent').value;

    document.getElementById('wcsMessageTypeError').style.display = 'none';
    document.getElementById('wcsSelectedProductIdError').style.display = 'none';
    document.getElementById('wcsMessageContentError').style.display = 'none';

    // validate
    if ( ! type ) {
        document.getElementById('wcsMessageTypeError').style.display = '';
        return;
    }

    if ( ! productId && type == 'product-related' ) {
        document.getElementById('wcsSelectedProductIdError').style.display = '';
        return;
    }

    if ( ! description ) {
        document.getElementById('wcsMessageContentError').style.display = '';
        return;
    }

    document.getElementById('wooCommerceSweitoInboxButton').innerText = woocommercesweitoscript_object.wcs_please_wait;
    document.getElementById('wooCommerceSweitoInboxButton').setAttribute('disabled', 'disabled');
    document.getElementById('wooCommerceSweitoSendError').style.display = 'none';

    let setAttachments = document.getElementsByClassName('wcs-uploaded-files');
    let uploadAttachments = [];
    if (setAttachments.length > 0) {
        for (let i = 0; i < setAttachments.length; i++) {
            if ( !setAttachments[i].dataset.name || !setAttachments[i].dataset.url ) {
                uploadAttachedDocuments(setAttachments[i], sendNewInboxMessage);
                return;
            } else {
                uploadAttachments.push({
                    name: setAttachments[i].dataset.name,
                    url: setAttachments[i].dataset.url,
                });
            }
        }
    }

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_message_submission_ajax',
            type: type,
            id: productId,
            description: description,
            attachments: uploadAttachments,
            attachment_length: uploadAttachments.length,
            nonce: woocommercesweitoscript_object.wcs_thread_nonce
        },
        success:function(res){
            if (res.success) {
                document.getElementById('wcsMessageType').value = '';
                document.getElementById('wcsSelectedProductId').value = '';
                document.getElementById('wcsMessageContent').value = '';

                document.getElementById('wooCommerceSweitoInboxButton').innerText = 'Send';
                document.getElementById('wooCommerceSweitoInboxButton').removeAttribute('disabled');

                showAccountInboxSection('tickets');
                getUserInboxMessages();

                document.getElementById('uploadedDocumentPreview').style.display = 'none';
                if (setAttachments.length > 0) {
                    for (let i = 0; i < setAttachments.length; i++) {
                        setAttachments[i].parentElement.removeChild(setAttachments[i]);
                    }
                }
                document.getElementById('uploadedDocumentPreview').style.display = 'none';
            } else {
                document.getElementById('wooCommerceSweitoSendError').innerHTML = '<small></small>'+res.data+'</small></small>';
                document.getElementById('wooCommerceSweitoSendError').style.display = '';

                document.getElementById('wooCommerceSweitoInboxButton').innerText = 'Send';
                document.getElementById('wooCommerceSweitoInboxButton').removeAttribute('disabled');
            }
        },
        error:function(xhr, status, error) {
            document.getElementById('wooCommerceSweitoInboxButton').innerText = 'Send';
            document.getElementById('wooCommerceSweitoInboxButton').removeAttribute('disabled');
        }
    });
}

function sendUserReplyToThread() {
    let productReference = document.getElementById('wooCommerceSweitoTicketReference').value;
    let description = document.getElementById('wooCommerceSweitoReplyInboxMessage').value;

    document.getElementById('wooCommerceSweitoInboxReplyButton').innerText = woocommercesweitoscript_object.wcs_please_wait;
    document.getElementById('wooCommerceSweitoInboxReplyButton').setAttribute('disabled', 'disabled');
    document.getElementById('wooCommerceSweitoSendReplyError').innerText = '';

    let setAttachments = document.getElementsByClassName('wcs-uploaded-files2');
    let uploadAttachments = [];
    if (setAttachments.length > 0) {
        for (let i = 0; i < setAttachments.length; i++) {
            if ( !setAttachments[i].dataset.name || !setAttachments[i].dataset.url ) {
                uploadAttachedDocuments(setAttachments[i], sendUserReplyToThread);
                return;
            } else {
                uploadAttachments.push({
                    name: setAttachments[i].dataset.name,
                    url: setAttachments[i].dataset.url,
                });
            }
        }
    }

    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_message_reply_thread_ajax',
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

                if ( setStyle === 'style-1' ) {
                    let li = document.createElement('li');
                    li.classList.add('me');
                    li.innerHTML = `
                        <div class="entete">
                            <h3>${woocommercesweitoscript_object.wcs_just_now}</h3>
                            <h2>You</h2>
                            <span class="status blue"></span>
                        </div>
                        <div class="triangle"></div>
                        <div class="message">
                            ${description}
                        </div>
                    `;
                    document.getElementById('wcs-chat-' + setStyle).appendChild(li);
                } else if ( setStyle === 'style-2' ) {
                    let div = document.createElement('div');
                    div.classList.add('wcs-style-2-message-box-holder');
                    div.innerHTML = `
                        <div class="wcs-style-2-message-box">
                            ${description}
                        </div>
                        <div class="wcs-style-2-message-receiver">
                            ${woocommercesweitoscript_object.wcs_just_now}
                        </div>
                    `;
                    document.getElementById('wcs-chat-' + setStyle).appendChild(div);
                } else if ( setStyle === 'style-3' ) {
                    let div = document.createElement('div');
                    div.innerHTML = `
                        <div class="time">
                            ${woocommercesweitoscript_object.wcs_just_now}
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
                                    <h3>${woocommercesweitoscript_object.wcs_just_now}</h3>
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
                                    ${woocommercesweitoscript_object.wcs_just_now}
                                </div>
                            `;
                            document.getElementById('wcs-chat-' + setStyle).appendChild(div2);
                        } else if ( setStyle === 'style-3' ) {
                            let div2 = document.createElement('div');
                            div2.innerHTML = `
                                <div class="time">
                                    ${woocommercesweitoscript_object.wcs_just_now}
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

function uploadAttachedDocuments(element, callback) {
    jQuery.ajax({
        type:"POST",
        dataType: 'json',
        url: woocommercesweitoscript_object.ajax_url,
        data: {
            action: 'woocommerce_inbox_message_upload_document',
            upload: element.value,
            nonce: woocommercesweitoscript_object.wcs_upload_nonce
        },
        success:function(res){
            if (res.success) {
                element.setAttribute('data-name', res.data.name);
                element.setAttribute('data-url', res.data.url);

                callback();
            } else {
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

function addMessageAttachment() {
    document.getElementById('uploadFileField').click();
}

function handleFileUpload() {
    var file2 = document.getElementById('uploadFileField');
    document.getElementById('wooCommerceSweitoSendError').innerText = '';

    if (!file2.files[0]) { return; }

    if (
        (file2.files[0].type != 'image/jpeg') &&
        (file2.files[0].type != 'image/jpg') &&
        (file2.files[0].type != 'image/png') &&
        (file2.files[0].type != 'application/pdf') 
    ) {
        let error = 'File Type not Supported. File must be jpeg, jpg, png or pdf';
        document.getElementById('wooCommerceSweitoSendError').innerText = error;
        document.getElementById('uploadFileField').value = '';
        return;
    }

    if (file2.files[0].size > 5000000) {
        let error = 'Max upload size of 5MB allowed';
        document.getElementById('wooCommerceSweitoSendError').innerText = error;
        document.getElementById('uploadFileField').value = '';
        return;
    }

    let filex = file2.files[0];
    var reader2 = new FileReader();
    reader2.readAsDataURL(filex);
    reader2.onload = performConvert;
}

function performConvert(imgsrcs) {
    let input = document.createElement('input');
    input.setAttribute('type', 'hidden');
    input.classList.add('wcs-uploaded-files');
    input.setAttribute('value', imgsrcs.target.result);

    document.getElementById('messageAttachmentContents').appendChild(input);
    document.getElementById('uploadedDocumentPreview').style.display = '';
    document.getElementById('uploadedDocumentCount').innerText = (document.getElementsByClassName('wcs-uploaded-files')).length;

    document.getElementById('uploadFileField').value = '';
}

function dropUploadAttachment() {
    let setAttachments = document.getElementsByClassName('wcs-uploaded-files');
    if (setAttachments.length > 0) {
        for (let i = 0; i < setAttachments.length; i++) {
            setAttachments[i].parentElement.removeChild(setAttachments[i]);
        }
    }

    document.getElementById('uploadedDocumentPreview').style.display = 'none';
    document.getElementById('uploadedDocumentCount').innerText = (document.getElementsByClassName('wcs-uploaded-files')).length;
    document.getElementById('uploadFileField').value = '';
}

function addMessageAttachment2() {
    document.getElementById('uploadFileField2').click();
}

function handleFileUpload2() {
    var file2 = document.getElementById('uploadFileField2');
    document.getElementById('wooCommerceSweitoSendReplyError').innerText = '';

    if (!file2.files[0]) { return; }

    if (
        (file2.files[0].type != 'image/jpeg') &&
        (file2.files[0].type != 'image/jpg') &&
        (file2.files[0].type != 'image/png') &&
        (file2.files[0].type != 'application/pdf') 
    ) {
        let error = 'File Type not Supported. File must be jpeg, jpg, png or pdf';
        document.getElementById('wooCommerceSweitoSendReplyError').innerText = error;
        document.getElementById('uploadFileField2').value = '';
        return;
    }

    if (file2.files[0].size > 5000000) {
        let error = 'Max upload size of 5MB allowed';
        document.getElementById('wooCommerceSweitoSendReplyError').innerText = error;
        document.getElementById('uploadFileField2').value = '';
        return;
    }

    let filex = file2.files[0];
    var reader2 = new FileReader();
    reader2.readAsDataURL(filex);
    reader2.onload = performConvert2;
}

function performConvert2(imgsrcs) {
    let input = document.createElement('input');
    input.setAttribute('type', 'hidden');
    input.classList.add('wcs-uploaded-files2');
    input.setAttribute('value', imgsrcs.target.result);

    document.getElementById('messageAttachmentContents2').appendChild(input);
    document.getElementById('uploadedDocumentPreview2').style.display = '';
    document.getElementById('uploadedDocumentCount2').innerText = (document.getElementsByClassName('wcs-uploaded-files2')).length;

    document.getElementById('uploadFileField2').value = '';
}

function dropUploadAttachment2() {
    let setAttachments = document.getElementsByClassName('wcs-uploaded-files2');
    if (setAttachments.length > 0) {
        for (let i = 0; i < setAttachments.length; i++) {
            setAttachments[i].parentElement.removeChild(setAttachments[i]);
        }
    }

    document.getElementById('uploadedDocumentPreview2').style.display = 'none';
    document.getElementById('uploadedDocumentCount2').innerText = (document.getElementsByClassName('wcs-uploaded-files')).length;
    document.getElementById('uploadFileField2').value = '';
}