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
            $('#manual-download, #notice-manual-download').on('click', this.manual_download);

            // Radio's
            $('input[class^="caos-allow-tracking"]').on('click', this.toggle_allow_tracking);
            $('input[class^="sgal-script-position"]').on('click', this.toggle_script_position);

            // Options
            $('.caos-analytics-compatibility-mode').on('click', this.toggle_compatibility_mode);
            $('.caos-stealth-mode, .caos-capture-outbound-links').on('click', this.toggle_stealth_mode);
            $('.caos-analytics-js-file').on('click', this.toggle_js_file_input);
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
         * Toggle Allow tracking options.
         */
        toggle_allow_tracking: function() {
            option        = this.className.replace('caos-allow-tracking-', '');
            $cookie_name  = $('.sgal-cookie-notice-name-row');
            $cookie_value = $('.caos-cookie-value-row');

            switch (option) {
                case 'cookie-is-set':
                case 'cookie-is-not-set':
                    $cookie_name.show();
                    $cookie_value.hide();
                    break;
                case 'cookie-has-value':
                    $cookie_name.show();
                    $cookie_value.show();
                    break;
                default:
                    $cookie_name.hide();
                    $cookie_value.hide();
                    break;
            }
        },

        /**
         * Toggle 'Add Manual' window.
         */
        toggle_script_position: function() {
            option        = this.className.replace('sgal-script-position-', '');
            $add_manually = $('.caos_add_manually');

            if (option === 'manual') {
                $add_manually.show();
            } else {
                $add_manually.hide();
            }
        },

        /**
         * Toggle Compatibility Mode options
         */
        toggle_compatibility_mode: function () {
            settings = '.caos_advanced_settings, .caos_basic_settings';
            if (this.value !== '') {
                $(settings).hide();
                $('.caos-analytics-js-file').val('analytics.js');
            } else {
                $(settings).show();
            }
        },

        /**
         * Toggle Stealth Mode options
         */
        toggle_stealth_mode: function () {
            setting = $('.caos-analytics-js-file');
            if (this.checked === true) {
                setting.val('analytics.js');
            }
            if (this.className === 'caos-stealth-mode') {
                $('.caos-capture-outbound-links').attr('checked', false);
            } else {
                $('.caos-stealth-mode').attr('checked', false);

            }
        },

        /**
         * Toggle JS File Input options
         */
        toggle_js_file_input: function () {
            stealth = $('.caos-stealth-mode');
            outbound = $('.caos-capture-outbound-links');
            compatibility = $('.caos-analytics-compatibility-mode');
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
