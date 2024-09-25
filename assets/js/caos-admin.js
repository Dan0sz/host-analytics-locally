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

            // Radio's
            $('input[class^="allow-tracking"]').on('click', this.toggle_cookie_fields);
            $('input[class^="allow-tracking"]').on('click', this.toggle_tracking_code);
            $('.tracking-code').on('change', this.maybe_disable_consent_mode);
            $('input[class^="script-position"]').on('click', this.toggle_script_position);

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
        toggle_cookie_fields: function () {
            option = this.className.replace('allow-tracking-', '');
            $cookie_name = $('.cookie-name-row');
            $cookie_value = $('.cookie-value-row');

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
         * Disables Consent Mode when Minimal Analytics is selected.
         */
        toggle_tracking_code: function () {
            let option = this.className.replace('allow-tracking-', '');
            let tracking_code_option = $('.tracking-code');

            if (option === 'consent-mode') {
                tracking_code_option.val('');
            }
        },

        /**
         *
         */
        maybe_disable_consent_mode: function () {
            let value = this.value;
            let allow_tracking_option = $('input[name="caos_settings[allow_tracking]"]:checked');

            if (value === 'minimal_ga4' && allow_tracking_option.val() === 'consent_mode') {
                $('.allow-tracking-')[0].checked = true;
            }
        },

        /**
         * Toggle 'Add Manual' window.
         */
        toggle_script_position: function () {
            option = this.className.replace('script-position-', '');
            $add_manually = $('.caos_add_manually');

            if (option === 'manual') {
                $add_manually.show();
            } else {
                $add_manually.hide();
            }
        },

        /**
         *
         */
        show_loader: function () {
            $('#wpwrap').append('<div class="caos-loading"><span class="spinner is-active"></span></div>');
        }
    };

    caos_admin.init();
});
