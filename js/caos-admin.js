/**
 * CAOS for Analytics
 * @author: Daan van den Bergh
 * @package: CAOS for Analytics
 */

jQuery(function ($) {
    $(document).on('click', '.caos-dismissible .notice-dismiss', function () {
        var type = $(this).closest('.caos-dismissible').data('notice');
        // Make an AJAX call
        // Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.ajax(
            ajaxurl,
            {
                type: 'POST',
                data: {
                    action: 'caos_notice_handler',
                    type: type
                }
            }
        );
    })
});
