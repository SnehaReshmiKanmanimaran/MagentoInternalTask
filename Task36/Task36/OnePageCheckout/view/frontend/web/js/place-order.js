require(['jquery', 'mage/url', 'Magento_Customer/js/customer-data'], function($, urlBuilder, customerData) {
    $(document).ready(function() {
        $('#place-order-button').click(function() {
            var customer = customerData.get('customer')();
            var isLoggedIn = !!customer.firstname;

            var paymentMethod = $("input[name='payment_method']:checked").val();
            var shippingMethod = $("input[name='shipping_method']:checked").val();
            var addressType = $("input[name='address_type']:checked").val(); // Address type may be useful

            if (!paymentMethod || !shippingMethod) {
                alert('Please select payment method and shipping method.');
                return;
            }

            if (isLoggedIn) {
                var selectedAddressId = $("input[name='address_id']:checked").val(); // Get selected address ID
                if (!selectedAddressId) {
                    alert('Please select an address.');
                    return;
                }

                // Fetch selected address details based on selectedAddressId
                $.ajax({
                    url: urlBuilder.build('checkout/address/fetch'), // Endpoint for fetching address details
                    type: 'POST',
                    data: { address_id: selectedAddressId },
                    success: function(response) {
                        if (response.success) {
                            var addressDetails = response.address; // Address details from the response

                            var orderData = {
                                payment_method: paymentMethod,
                                shipping_method: shippingMethod,
                                firstname: addressDetails.firstname,
                                lastname: addressDetails.lastname,
                                street: addressDetails.street.join(','), // Convert array to comma-separated string
                                city: addressDetails.city,
                                country_id: addressDetails.country_id,
                                postcode: addressDetails.postcode,
                                telephone: addressDetails.telephone,
                                region_id: addressDetails.region_id,
                                email: '' // Email is empty for logged-in customers
                            };

                            placeOrder(orderData, isLoggedIn);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        alert('An error occurred while fetching the address details.');
                    }
                });
            } else {
                // Guest user: Collect address details from the form
                var firstname = $('input[name="firstname"]').val();
                var lastname = $('input[name="lastname"]').val();
                var street = $('input[name="street[]"]').map(function() { return $(this).val(); }).get();
                var city = $('input[name="city"]').val();
                var countryId = $('select[name="country"]').val();
                var postcode = $('input[name="postcode"]').val();
                var telephone = $('input[name="telephone"]').val();
                var regionId = $('#region').val();
                var email = $('#email').val();

                if (!firstname || !lastname || !street.length || !city || !countryId || !postcode || !telephone || !regionId) {
                    alert('Please fill in all required fields.');
                    return;
                }

                var orderData = {
                    payment_method: paymentMethod,
                    shipping_method: shippingMethod,
                    firstname: firstname,
                    lastname: lastname,
                    street: street.join(','), // Convert array to comma-separated string
                    city: city,
                    country_id: countryId,
                    postcode: postcode,
                    telephone: telephone,
                    region_id: regionId,
                    email: email
                };

                placeOrder(orderData, isLoggedIn);
            }
        });

        function placeOrder(orderData, isLoggedIn) {
            $.ajax({
                url: urlBuilder.build(isLoggedIn ? 'checkout/checkout/order' : 'checkout/guest/order'),
                type: 'POST',
                data: orderData,
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect_url;
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    alert('An error occurred while placing the order.');
                }
            });
        }
    });
});





 





 











 



 



 







