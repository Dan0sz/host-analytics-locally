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
 * @copyright: (c) 2020 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

jQuery(document).ready(function ($) {
    var caos_admin = {
        // Buttons
        $manual_download: $('#manual-download'),

        // Settings screen elements
        $nav: $('.caos-nav span'),
        $nav_basic_settings: $('.basic-settings'),
        $nav_advanced_settings: $('.advanced-settings'),
        $basic_settings_form: $('#caos-basic-settings-form'),
        $advanced_settings_form: $('#caos-advanced-settings-form'),

        /**
         * Initialize CAOS Admin Functions.
         */
        init: function () {
            // Nav
            this.$nav.on('click', this.toggle_section);

            // Buttons
            $('#manual-download').on('click', this.manual_download);
        },

        toggle_section: function () {
            caos_admin.$nav.removeClass('selected');
            $(this).addClass('selected');

            if (this.classList.contains('basic-settings')) {
                caos_admin.$basic_settings_form.fadeIn();
                caos_admin.$advanced_settings_form.fadeOut(100);
            } else {
                caos_admin.$advanced_settings_form.fadeIn();
                caos_admin.$basic_settings_form.fadeOut(100);
            }
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
