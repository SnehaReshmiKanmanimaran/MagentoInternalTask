require([
    'jquery',
    'mage/url',
    'Magento_Ui/js/modal/alert'
], function ($, urlBuilder, alert) {
    'use strict';

    $(document).ready(function () {
        $('#address-list input[type="radio"]').change(function () {
            var selectedAddressId = $(this).val();
            if (selectedAddressId) {
                $.ajax({
                    url: urlBuilder.build('checkout/address/getaddress'), // Your custom controller URL
                    type: 'POST',
                    data: { address_id: selectedAddressId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Handle successful response
                            console.log('Address data:', response.address);
                        } else {
                            // Handle error response
                            alert({
                                title: 'Error',
                                content: response.message
                            });
                        }
                    },
                    error: function () {
                        alert({
                            title: 'Error',
                            content: 'An error occurred while fetching the address data.'
                        });
                    }
                });
            }
        });
    });
});
