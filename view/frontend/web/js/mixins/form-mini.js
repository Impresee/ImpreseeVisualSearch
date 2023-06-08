define([
    'jquery',
    'underscore',
    'mage/template',
    'matchMedia',
    'jquery/ui',
    'mage/translate'
], function ($, _, mageTemplate, mediaCheck) {

    return function (widget) {
        $.widget('mage.quickSearch', widget, {
            _create: function () {
                this.searchForm = this.element.parents(this.options.formSelector);
                this.element.on('keyup', this._onKeyUpImpresee.bind(this));
                this.searchForm.on('submit', this._onSubmitImpresee.bind(this));
                this._super();
                this.responseList = new Proxy(this.responseList, {
                    set: function (target, key, value) {
                        if (key === 'indexList' && value !== null) {
                            let results = [];
                            for(let element of Array.from(value)) {
                                let text = element.querySelector('.qs-option-name');
                                results.push(text.innerHTML.trim());
                            }
                            const onQuickSearch = 'QUICK_SEARCH';
                            _wsse_register_event('&r='+encodeURIComponent(results.join('|')), onQuickSearch);
                        }
                        target[key] = value;
                        return true;
                    }
                });
            },
            _onKeyUpImpresee: function(e) {
                if(e.which == 13) return;
                var value = this.element.val().trim();
                const onKeyDown = 'ON_KEY_UP';
                _wsse_register_event('&q='+encodeURIComponent(value), onKeyDown);

            },
            _onSubmitImpresee: function (e) {
                var value = this.element.val().trim();
                const onSubmit = 'SEARCH';
                _wsse_register_event('&q='+encodeURIComponent(value), onSubmit);
            },
        });
        return $.mage.quickSearch;
    };
});