/* * * * * * * * * * * * * * * * * * * *
 *  ██████╗ █████╗  ██████╗ ███████╗
 * ██╔════╝██╔══██╗██╔═══██╗██╔════╝
 * ██║     ███████║██║   ██║███████╗
 * ██║     ██╔══██║██║   ██║╚════██║
 * ╚██████╗██║  ██║╚██████╔╝███████║
 *  ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚══════╝
 *
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

jQuery(document).ready(function ($) {
    var caos_admin = {
        // Buttons
        $manual_download: $('#manual-download'),

        /**
         * Initialize CAOS Admin Functions.
         */
        init: function () {
            $('#manual-download').on('click', this.manual_download);
        },

        /**
         * Triggered when 'Update' button is clicked.
         */
        manual_download: function () {
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'caos_analytics_ajax_manual_download'
                },
                complete: function () {
                    location.reload();
                }
            });
        }
    };

    caos_admin.init();
});

/**
 * @param className
 */
function showOptions(className) {
    if (className) {
        jQuery('.' + className).show();
    }
}

/**
 * @param className
 */
function hideOptions(className) {
    if (className) {
        jQuery('.' + className).hide();
    }
}

/**
 * Toggle sections.
 */
jQuery('.caos-compatibility-mode-input').click(function () {
    settings = 'caos_advanced_settings, .caos_basic_settings';
    if (this.value !== '') {
        hideOptions(settings);
        jQuery('.caos-js-file-input').val('analytics.js');
    } else {
        showOptions(settings);
    }
});

/**
 * Toggle sections.
 */
jQuery('.caos-stealth-mode-input, .caos-capture-outbound-links').click(function () {
    setting = jQuery('.caos-js-file-input');
    if (this.checked === true) {
        setting.val('analytics.js');
    }
    if (this.className === 'caos-stealth-mode-input') {
        jQuery('.caos-capture-outbound-links').attr('checked', false);
    } else {
        jQuery('.caos-stealth-mode-input').attr('checked', false);

    }
});

/**
 * Toggle sections.
 */
jQuery('.caos-js-file-input').click(function () {
    stealth = jQuery('.caos-stealth-mode-input');
    outbound = jQuery('.caos-capture-outbound-links');
    compatibility = jQuery('.caos-compatibility-mode-input');
    if (this.value !== 'analytics.js') {
        stealth.attr('checked', false);
        outbound.attr('checked', false);
        compatibility.val(null);
        // We need to trigger a click to show applicable options again.
        compatibility.click();
    }
});
