define([
    'jquery',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/url'
], function ($, fullScreenLoader, urlBuilder) {
    'use strict';

    return function (Component) {
        return Component.extend({
            initialize: function () {
                this._super();
                $('#proceed-to-checkout').on('click', function (e) {
                    e.preventDefault();
                    window.location.href = urlBuilder.build('checkout/index/index');
                });
            }
        });
    };
});
