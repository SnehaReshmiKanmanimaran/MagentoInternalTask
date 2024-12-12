define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-list',
    'Magento_Checkout/js/model/quote',
    './custom-checkout',
], function ($, ko, Component, paymentService, methodList, quote, customCheckout, quantity) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'tychons_onepagecheckout/checkout'
        },

        initialize: function () {
            this._super();
            this.selectedPaymentMethod = ko.observable(null);  
            customCheckout(this);  
            quantity(this);  
        },

        getPaymentMethods: function () {
            return methodList();
        },

        selectPaymentMethod: function (method) {
            this.selectedPaymentMethod(method);
        },

        getMethodTitle: function (method) {
            return method.title;
        }
    });
});
