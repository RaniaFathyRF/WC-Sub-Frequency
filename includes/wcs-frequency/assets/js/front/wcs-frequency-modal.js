jQuery(document).ready(function ($) {

    $(document.body).on('click', 'a.change-frequency', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        MicroModal.init();
        MicroModal.show('wcs_sub_box_frequency_modal');
    });


    $(document.body).on("click", '.modal__close', function (event) {
        MicroModal.close('wcs_sub_box_frequency_modal')
    });

    $(document.body).on('click','#wcs-sub-box-frequency-continue-button', function (e) {
        e.preventDefault();
        let product_id = $('#product_id').val();
        let variation_id = $('#variation').val();
        let new_variation_name = $('#variation :selected').text();
        // Show loader before making the AJAX call
        show_loader();
        $.ajax({
            url: wcs_frequency_object.ajax_url,
            type: 'POST',
            data: {
                action: 'wcs_switch_subscription',
                'item_id': $('#wcs_sub_box_frequency_modal').attr('data-item_id'),
                'subscription_id': $('#wcs_sub_box_frequency_modal').attr('data-subscription_id'),
                'product_id': product_id,
                'variation_id': variation_id,
                'new_variation_name': new_variation_name,
                'old_variation_name': $('#wcs_sub_box_frequency_modal').attr('data-old_variation_name'),
            },

            success: function (response) {
                if (response.status) {
                    window.location.reload();
                }
            },
            error: function (error) {
                console.log(error);
                hide_loader();
            }
        });
    });

    function show_loader() {
        // Show the loader by remove display none attribute
        $('.wc-sub-box-extra-action-loader-wrapper').show();
    }

    function hide_loader() {
        // Hide the loader by set display none class
        $('.wc-sub-box-extra-action-loader-wrapper').hide();
    }
});
