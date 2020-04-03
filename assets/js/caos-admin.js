/* * * * * * * * * * * * * * * * * * * *
 *  ██████╗ █████╗  ██████╗ ███████╗
 * ██╔════╝██╔══██╗██╔═══██╗██╔════╝
 * ██║     ███████║██║   ██║███████╗
 * ██║     ██╔══██║██║   ██║╚════██║
 * ╚██████╗██║  ██║╚██████╔╝███████║
 *  ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚══════╝
 *
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/caos/
 * @copyright: (c) 2020 Daan van den Bergh
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
            // Buttons
            $('#manual-download').on('click', this.manual_download);

            // Options
            $('.caos-compatibility-mode-input').on('click', this.toggle_compatibility_mode);
            $('.caos-stealth-mode-input, .caos-capture-outbound-links').on('click', this.toggle_stealth_mode);
            $('.caos-js-file-input').on('click', this.toggle_js_file_input);
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
        },

        /**
         * Toggle Compatibility Mode options
         */
        toggle_compatibility_mode: function () {
            settings = 'caos_advanced_settings, .caos_basic_settings';
            if (this.value !== '') {
                hideOptions(settings);
                jQuery('.caos-js-file-input').val('analytics.js');
            } else {
                showOptions(settings);
            }
        },

        /**
         * Toggle Stealth Mode options
         */
        toggle_stealth_mode: function () {
            setting = jQuery('.caos-js-file-input');
            if (this.checked === true) {
                setting.val('analytics.js');
            }
            if (this.className === 'caos-stealth-mode-input') {
                jQuery('.caos-capture-outbound-links').attr('checked', false);
            } else {
                jQuery('.caos-stealth-mode-input').attr('checked', false);

            }
        },

        /**
         * Toggle JS File Input options
         */
        toggle_js_file_input: function () {
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
