jQuery(document).ready(function ($) {
    // show hide on reload
    if ($('#wcs_sub_box_enable_frequency_box').is(':checked'))
        show_attribute_input();
    else
        hide_attribute_input();


    $(document.body).on('change', '#wcs_sub_box_enable_frequency_box', function () {
        if ($(this).is(':checked'))
            show_attribute_input();
        else
            hide_attribute_input();

    });

    function show_attribute_input() {
        $('tr:has(#wcs_sub_box_enable_frequency_attribute_box)').show();

    }

    function hide_attribute_input() {
        $('tr:has(#wcs_sub_box_enable_frequency_attribute_box)').hide();

    }
});
