/* * * * * * * * * * * * * * * * * * * *
 * This file is loaded inline to avoid ad blockers while maintaining the least amount of overhead. 
 * 
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/caos/
 * @copyright: (c) 2021 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

jQuery(document).ready(function ($) {
    var caos_admin = {
        ticker_items: document.querySelectorAll('.ticker-item'),
        ticker_index: 0,

        /**
         * Initialize CAOS Admin Functions.
         */
        init: function () {
            // Radio's
            $('input[class^="caos-allow-tracking"]').on('click', this.toggle_allow_tracking);
            $('input[class^="sgal-script-position"]').on('click', this.toggle_script_position);
            $('.caos-stealth-mode, .caos-extension-optimize').on('click', this.toggle_stealth_mode);
            $('.caos-extension-optimize').on('click', this.toggle_optimize_id);

            // Ticker
            setInterval(this.loop_ticker_items, 4000);
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
