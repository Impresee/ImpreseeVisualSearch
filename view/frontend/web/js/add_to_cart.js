require([
   'jquery',
   'mage/url',
   'Magento_Customer/js/customer-data'
], function ($, urlBuilder, customerData) {
    window._wsee_impresee_add_to_cart = function(sku, qty, onComplete, onSuccess, onError) {
        $.ajax({
            type: 'POST',
            data: {
                sku: sku,
                qty: qty
            },
            url: urlBuilder.build('impresee/product/addToCart'),
            complete: onComplete,
            success: onSuccess,
            error: onError
        });
    }
});