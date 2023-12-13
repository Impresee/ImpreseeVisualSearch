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
        window._wsse_page_type_event = impreseeInViewData.page_type_event || '';
        let impreseeScreen = impreseeInViewData.screen || '';
        let impreseeData = '';
        if (impreseeInViewData.page_type_event === 'VIEW_PRODUCT') {
            const product = impreseeInViewData.product;
            const _wsee_productSku = product.sku || '';
            const _wsee_productId = product.id || '';
            const _wsee_productName = product.name || '';
            impreseeData += '&pid=' + encodeURIComponent(_wsee_productId);
            impreseeData += '&sku=' + encodeURIComponent(_wsee_productSku);
            impreseeData += '&pna=' + encodeURIComponent(_wsee_productName);
        }
        else if (impreseeInViewData.page_type_event === 'VIEW_CATEGORY') {
            const category = impreseeInViewData.category;
            const _wsee_category = category.name || '';
            const _wsee_categoryId = category.id || '';
            const _wsee_categoryUrl = category.url || '';
            impreseeData += '&cat=' + encodeURIComponent(_wsee_category);
            impreseeData += '&catid=' + encodeURIComponent(_wsee_categoryId);
            impreseeData += '&caturl=' + encodeURIComponent(_wsee_categoryUrl);
        }
        else if (impreseeScreen) {
            impreseeData += '&scr=' + encodeURIComponent(impreseeScreen);
        }
        impreseeData += impreseeInViewData.base_url || '';

        _wsse_register_event(impreseeData, window._wsse_page_type_event, impreseeInViewData);
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