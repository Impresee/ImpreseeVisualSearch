define([
    'jquery',
    'underscore',
    'mage/template',
    'matchMedia',
    'jquery/ui',
    'mage/translate'
], function ($, _, mageTemplate, mediaCheck) {

    return function (widget) {
        $.widget('impresee.quickSearch', widget, {
            _create: function () {
                this.searchForm = this.element.parents(this.options.formSelector);
                this.element.on('keyup', this._onKeyUp.bind(this));
                this.searchForm.on('submit', this._onSubmit.bind(this));
            },
            _onKeyUp: function(e) {
                if(e.which == 13) return;
                var value = this.element.val().trim();
                const onKeyDown = 'ON_KEY_UP';
                _wsse_register_event('&q='+encodeURIComponent(value), onKeyDown);

            },
            _onSubmit: function (e) {
                var value = this.element.val().trim();
                const onSubmit = 'SEARCH';
                _wsse_register_event('&q='+encodeURIComponent(value), onSubmit);
            },
        });
        return $.impresee.quickSearch;
    };
});