/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    function _wsse_register_event(extraData, action, impreseeInViewData) {
        const impreseeRegisterUrl = impreseeInViewData.register_url || '';
        const impreseeCode = impreseeInViewData.impresee_uuid;
        
        let impreseeEvent = impreseeInViewData.impresee_event || '';
        let urlParams = new URLSearchParams(window.location.search || '?' + window.location.hash.split('?')[1]);
        let from_impresee_text = urlParams.get('source_impresee') || '';
        let from_impresee_visual = urlParams.get('seecd') || '';
        let data = 'a=' + encodeURIComponent(action);
        data += '&evt=' + encodeURIComponent(impreseeEvent);
        data += '&fi=' + encodeURIComponent(from_impresee_text);
        data += '&fiv=' + encodeURIComponent(from_impresee_visual);
        data += '&cusid=' + encodeURIComponent(impreseeInViewData.customer.id || '');
        data += extraData;

        if (impreseeCode) {
            var xmlHttp = new XMLHttpRequest();
            xmlHttp.withCredentials = true;
            xmlHttp.open( "GET", impreseeRegisterUrl + impreseeCode + '?' + data, true );
            xmlHttp.send( null );
        }
    }
    function _wsee_parse_data(impreseeInViewData) {
        window._wsee_logged_customer = {
            'id': impreseeInViewData.customer.id || '',
            'name': impreseeInViewData.customer.name || '',
            'email': impreseeInViewData.customer.email || '',
            'customer_group': impreseeInViewData.customer.customer_group || '',
        }

        _wsse_register_event(window._wsse_impresee_data, window._wsse_page_type_event, impreseeInViewData);
    }

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.impreseeInViewEventData = customerData.get('impreseeInViewEventData');
            this.impreseeInViewEventData.subscribe(function(impreseeInViewData){
                _wsee_parse_data(impreseeInViewData)
            }, this);
            let inViewData = this.impreseeInViewEventData();
            if (Object.keys(inViewData).length !== 0) {
                _wsee_parse_data(inViewData);
            }
        }
            
    });
});