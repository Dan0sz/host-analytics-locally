/**
 * @author: Daan van den Bergh
 * @url: https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license: GPL2v2 or later
 */

function caosDownloadManually() {
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            action: 'caos_analytics_ajax_manual_download'
        },
        success: function (response) {
            var successMessage = '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>File successfully downloaded and saved.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
            
            jQuery('html, body').animate({scrollTop: 0}, 800);
            
            jQuery(successMessage).insertAfter('.wrap h1');
            
            return false;
        }
    });
}

jQuery('.caos_allow_tracking_').click(function () {
    jQuery('.caos_gdpr_setting').hide();
});

jQuery('.caos_allow_tracking_cookie_is_set, .caos_allow_tracking_cookie_is_not_set').click(function () {
    jQuery('.caos_allow_tracking_name').show();
    jQuery('.caos_allow_tracking_value').hide();
});

jQuery('.caos_allow_tracking_cookie_has_value').click(function () {
    jQuery('.caos_allow_tracking_name, .caos_allow_tracking_value').show();
});

jQuery('.caos_script_position_manual').click(function () {
    jQuery('.caos_add_manually').show();
});

jQuery('.caos_script_position_header, .caos_script_position_footer').click(function () {
    jQuery('.caos_add_manually').hide();
});

jQuery('.caos-compatibility-mode-input').click(function () {
    settings = jQuery('.caos_advanced_settings, .caos_basic_settings');
    if (this.value !== '') {
        jQuery(settings).hide();
        jQuery('.caos-js-file-input').val('analytics.js');
    } else {
        jQuery(settings).show();
    }
});

jQuery('.caos-stealth-mode-input').click(function () {
    setting = jQuery('.caos-js-file-input');
    if (this.checked === true) {
        setting.val('analytics.js');
    }
});

jQuery('.caos-js-file-input').click(function () {
    stealth = jQuery('.caos-stealth-mode-input');
    compatibility = jQuery('.caos-compatibility-mode-input');
    if (this.value !== 'analytics.js') {
        stealth.attr('checked', false);
        compatibility.val(null);
        // We need to trigger a click to show applicable options again.
        compatibility.click();
    }
});
