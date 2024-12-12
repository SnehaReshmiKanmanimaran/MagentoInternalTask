require(['jquery'], function($) {
    $(document).ready(function() {
        $('#guest-address-form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var newAddressHtml = 
                        '<div class="address-box">' +
                                '<input type="radio" name="address" id="address_' + response.address.id + '" value="' + response.address.id + '" required>' +
                                '<label for="address_' + response.address.id + '">' +
                                    '<p><strong>' + response.address.firstname + ' ' + response.address.lastname + '</strong></p>' +
                                    '<p>' + response.address.street.join(', ') + '</p>' +
                                    '<p>' + response.address.city + ', ' + response.address.region + ' (ID: ' + response.address.region_id + ')</p>' + // Show region name and ID
                                    '<p>' + response.address.postcode + '</p>' +
                                    '<p>' + response.address.country + '</p>' +
                                    '<p>' + response.address.telephone + '</p>' +
                                    '<p>' + response.address.email + '</p>' +
                                '</label>' +
                            '</div>';

                        // Append new address to the address details container
                        $('#guest-address-details').append(newAddressHtml);
                        // Hide the address form
                        $('#guest-address-form').hide();
                        // Display success message
                        $('#guest-message').text(response.message).css('color', 'green');
                    } else {
                        $('#guest-message').text(response.message).css('color', 'red');
                    }
                },
                error: function() {
                    $('#guest-message').text('An error occurred.').css('color', 'red');
                }
            });
        });
    });
});

