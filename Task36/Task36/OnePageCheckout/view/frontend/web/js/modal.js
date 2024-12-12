require([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function($, modal) {
    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        title: 'New Address',
        buttons: []
    };

    var popup = modal(options, $('#address-modal'));

    $('#add-new-address-button').on('click', function() {
        $('#address-modal').modal('openModal');
    });

    $('#modal-cancel').on('click', function() {
        $('#address-modal').modal('closeModal');
    });

    // Handle form submission with AJAX
    $('#new-address-form').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        var form = $(this);
        var formData = form.serialize(); // Serialize form data

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#message').html('<p class="success-message">' + response.message + '</p>');
                    $('#address-modal').modal('closeModal');
                    location.reload(true);
                    
                    // Optionally refresh parts of the page or update other elements
                } else {
                    $('#message').html('<p class="error-message">' + response.message + '</p>');
                }
            },
            error: function(xhr, status, error) {
                $('#message').html('<p class="error-message">An error occurred while processing your request. Please try again.</p>');
            }
        });
    });
});
 

 

 

 