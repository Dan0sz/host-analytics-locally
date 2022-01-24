/* * * * * * * * * * * * * * * * * * * *
 * This file is loaded inline to avoid ad blockers while maintaining the least amount of overhead. 
 * 
 * @author   : Daan van den Bergh
 * @url      : https://ffw.press/wordpress/caos/
 * @copyright: (c) 2021 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

jQuery(document).ready(function ($) {
    var caos_admin = {
        ticker_items: document.querySelectorAll('.ticker-item'),
        ticker_index: 0,
        nonce: $('#caos-regenerate-alias').data('nonce'),

        /**
         * Initialize CAOS Admin Functions.
         */
        init: function () {
            // Buttons
            $('#caos-regenerate-alias').on('click', this.regenerate_alias);

            // Text Fields
            $('.sgal-tracking-id').on('input', this.toggle_dual_tracking_visibility);

            // Checkboxes
            $('.caos-dual-tracking',).on('change', this.toggle_ga4_measurement_id);

            // Radio's
            $('input[class^="caos-allow-tracking"]').on('click', this.toggle_allow_tracking);
            $('input[class^="caos-anonymize-ip-mode"]').on('click', this.update_aip_example);
            $('input[class^="sgal-script-position"]').on('click', this.toggle_script_position);
            $('.caos-stealth-mode, .caos-extension-optimize').on('click', this.toggle_stealth_mode);
            $('.caos-extension-optimize').on('click', this.toggle_optimize_id);

            // Ticker
            setInterval(this.loop_ticker_items, 4000);
        },

        regenerate_alias: function () {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'caos_regenerate_alias',
                    nonce: caos_admin.nonce
                },
                complete: function () {
                    // Submit the form, to force refresh the files.
                    $('#submit').click();
                }
            });
        },

        /**
         * 
         */
        toggle_dual_tracking_visibility: function () {
            current_value = this.value;
            $option = $('.caos-dual-tracking');
            $option_row = $('.caos-dual-tracking-row');
            $2nd_option_row = $('.caos-ga4-measurement-id-row');

            if (current_value.startsWith('UA-')) {
                $option_row.show();

                if ($option.is(':checked')) {
                    $2nd_option_row.show();
                }
            } else {
                $option_row.hide();
                $2nd_option_row.hide();
            }
        },

        /**
         * 
         */
        toggle_ga4_measurement_id: function () {
            $option = $('.caos-ga4-measurement-id-row');

            if (this.checked) {
                $option.show();
            } else {
                $option.hide();
            }
        },

        /**
         * 
         */
        loop_ticker_items: function () {
            caos_admin.ticker_items.forEach(function (item, index) {
                if (index == caos_admin.ticker_index) {
                    $(item).fadeIn(500);
                } else {
                    $(item).hide(0);
                }
            });

            caos_admin.ticker_index++;

            if (caos_admin.ticker_index == caos_admin.ticker_items.length) {
                caos_admin.ticker_index = 0;
            }
        },

        /**
         * Toggle Allow tracking options.
         */
        toggle_allow_tracking: function () {
            option = this.className.replace('caos-allow-tracking-', '');
            $cookie_name = $('.sgal-cookie-notice-name-row');
            $cookie_value = $('.caos-cookie-value-row');

            switch (option) {
                case 'cookie-is-set':
                case 'cookie-is-not-set':
                    $cookie_name.show();
                    $cookie_value.hide();
                    break;
                case 'cookie-has-value':
                case 'cookie-value-contains':
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
         * Update Anonymize IP mode example, based on selection.
         */
        update_aip_example: function () {
            mask = '0';
            default_3 = '178';
            default_4 = '123';
            $octet_3 = $('.caos-aip-example .third-octet');
            $octet_4 = $('.caos-aip-example .fourth-octet');

            switch (this.value) {
                case '':
                    $octet_3.text(default_3);
                    $octet_4.text(default_4);
                    break;
                case 'one':
                    $octet_3.text(default_3);
                    $octet_4.text(mask);
                    break;
                case 'two':
                    $octet_3.text(mask);
                    $octet_4.text(mask);
                    break;
            }
        },

        /**
         * Toggle 'Add Manual' window.
         */
        toggle_script_position: function () {
            option = this.className.replace('sgal-script-position-', '');
            $add_manually = $('.caos_add_manually');

            if (option === 'manual') {
                $add_manually.show();
            } else {
                $add_manually.hide();
            }
        },

        /**
         * Toggle Stealth Mode options
         */
        toggle_stealth_mode: function () {
            if (this.className === 'caos-stealth-mode') {
                $('.caos-extension-optimize').attr('checked', false);
                $('.caos-extension-optimize-id-row').hide();
            } else {
                $('.caos-stealth-mode').attr('checked', false);

            }
        },

        /**
         * Toggle Optimize ID field.
         */
        toggle_optimize_id: function () {
            $optimize_id = $('.caos-extension-optimize-id-row');

            if (this.checked) {
                $optimize_id.show();
            } else {
                $optimize_id.hide();
            }
        }
    };

    caos_admin.init();
});
