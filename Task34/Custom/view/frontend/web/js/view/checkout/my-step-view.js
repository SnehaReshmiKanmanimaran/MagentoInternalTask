define(
    [
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator'
    ],
    function (
        ko,
        Component,
        _,
        stepNavigator
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Tychons_Custom/checkout/mystep'
            },

            isVisible: ko.observable(false),

            initialize: function () {
                this._super();
                stepNavigator.registerStep(
                    'mynewstep',
                    'mynewstep',
                    'Shipping Method Section',
                    this.isVisible,
                    _.bind(this.navigate, this),
                    15
                );

                return this;
            },

            navigate: function () {
                this.isVisible(true);
            },

            navigateToNextStep: function () {
                stepNavigator.next();
            }
        });
    }
);
 



 


