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
            var successMessage = '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>Analytic.js successfully downloaded and saved.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
            
            jQuery('html, body').animate({scrollTop: 0}, 800);
            
            jQuery(successMessage).insertAfter('.wrap h1');
            
            return false;
        }
    });
}