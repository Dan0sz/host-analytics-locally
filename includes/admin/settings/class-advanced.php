<?php
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

class CAOS_Admin_Settings_Advanced extends CAOS_Admin_Settings_Screen
{
    /**
     * CAOS_Admin_Settings_Advanced constructor.
     */
    public function __construct()
    {
        $this->title = __('Advanced Settings', 'host-analyticsjs-local');

        // Open
        add_filter('caos_advanced_settings_content', [$this, 'do_title'], 10);
        add_filter('caos_advanced_settings_content', [$this, 'do_description'], 15);
        add_filter('caos_advanced_settings_content', [$this, 'do_before'], 20);

        // Content
        add_filter('caos_advanced_settings_content', [$this, 'do_compatibility_mode'], 30);
        add_filter('caos_advanced_settings_content', [$this, 'do_stealth_mode'], 40);
        add_filter('caos_advanced_settings_content', [$this, 'do_remote_js_file'], 50);
        add_filter('caos_advanced_settings_content', [$this, 'do_cache_dir'], 60);
        add_filter('caos_advanced_settings_content', [$this, 'do_cdn_url'], 70);
        add_filter('caos_advanced_settings_content', [$this, 'do_preconnect'], 80);
        add_filter('caos_advanced_settings_content', [$this, 'do_capture_outbound_links'], 90);

        // Non Compatibility Mode settings.
        add_filter('caos_advanced_settings_content', [$this, 'do_tbody_advanced_settings_open'], 100);
        add_filter('caos_advanced_settings_content', [$this, 'do_cookie_expiry'], 110);
        add_filter('caos_advanced_settings_content', [$this, 'do_adjusted_bounce_rate'], 120);
        add_filter('caos_advanced_settings_content', [$this, 'do_change_enqueue_order'], 130);
        add_filter('caos_advanced_settings_content', [$this, 'do_disable_display_feat'], 140);
        add_filter('caos_advanced_settings_content', [$this, 'do_anonymize_ip'], 150);
        add_filter('caos_advanced_settings_content', [$this, 'do_track_admin'], 160);
        add_filter('caos_advanced_settings_content', [$this, 'do_tbody_close'], 200);

        // Uninstall Setting
        add_filter('caos_advanced_settings_content', [$this, 'do_uninstall_settings'], 200);

        // Close
        add_filter('caos_advanced_settings_content', [$this, 'do_after'], 250);

    }

    /**
     * Description
     */
    public function do_description()
    {
        ?>
        <p>
            <strong>*</strong> <?php _e('Manual update required after saving changes.', 'host-analyticsjs-local'); ?>
        </p>
        <?php
    }

    /**
     * Enable Compatibility Mode
     */
    public function do_compatibility_mode()
    {
        ?>
        <tr valign="top" class="caos-compatibility-mode">
            <th scope="row">
                <?php _e('Enable Compatibility Mode', 'host-analyticsjs-local'); ?>
            </th>
            <td>
                <select name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE; ?>" class="caos-compatibility-mode-input">
                    <?php foreach (CAOS_Admin_Settings::CAOS_ADMIN_COMPATIBILITY_OPTIONS as $option => $details): ?>
                        <option value="<?= $option; ?>" <?= (CAOS_OPT_COMPATIBILITY_MODE == $option) ? 'selected' : ''; ?>><?= $details['label']; ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description">
                    <?= sprintf(__('Allow another Google Analytics plugin to use <code>%s</code> and manage Google Analytics entirely within the other plugin.', 'host-analyticsjs-local'), CAOS_OPT_CACHE_DIR . CAOS_OPT_REMOTE_JS_FILE); ?>
                </p>
            </td>
        </tr>
        <?php
    }

    /**
     * Enable Stealth Mode Lite
     */
    public function do_stealth_mode()
    {
        $this->do_checkbox(
            __('Enable Stealth Mode Lite', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_STEALTH_MODE,
            CAOS_OPT_STEALTH_MODE,
            sprintf(__('Bypass some Ad Blockers. To bypass <u>all</u> Ad Blockers and track incognito browser sessions, upgrade to <a target="_blank" href="%s">Super Stealth</a>! (SSL required) <a target="_blank" href="%s">How does it work?</a>', 'host-analyticsjs-local'), 'https://woosh.dev/wordpress-plugins/caos-upgrades/super-stealth/', CAOS_SITE_URL . '/how-to/bypass-ad-blockers-caos/')
        );
    }

    /**
     * Which file to download?
     */
    public function do_remote_js_file()
    {
        ?>
        <tr valign="top" class="caos-js-file">
            <th scope="row"><?php _e('Which file to download?', 'host-analyticsjs-local'); ?> *
            </th>
            <td>
                <select name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_JS_FILE; ?>" class="caos-js-file-input">
                    <?php foreach (CAOS_Admin_Settings::CAOS_ADMIN_JS_FILE_OPTIONS as $label => $fileName): ?>
                        <option value="<?= $fileName; ?>" <?= (CAOS_OPT_REMOTE_JS_FILE == $fileName) ? 'selected' : ''; ?>><?= $label; ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description">
                    <?= sprintf(__('<code>analytics.js</code> is recommended in most situations. When using <code>gtag.js</code>, <code>analytics.js</code> is also cached and updated! Need help choosing? %sRead this%s', 'host-analyticsjs-local'), '<a href="' . CAOS_SITE_URL . '/wordpress/difference-analyics-gtag-ga-js/' . $this->utm_tags . '" target="_blank">', '</a>'); ?>
                </p>
            </td>
        </tr>
        <?php
    }

    /**
     * Save .js file to...
     */
    public function do_cache_dir()
    {
        $this->do_text(
            sprintf(__('Save %s to...', 'host-analyticsjs-local'), CAOS_OPT_REMOTE_JS_FILE),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR,
            __('e.g. /cache/caos/', 'host-analyticsjs-local'),
            CAOS_OPT_CACHE_DIR,
            __("Change the path where the Analytics-file is cached inside WordPress' content directory (usually <code>wp-content</code>). Defaults to <code>/cache/caos/</code>.", 'host-analyticsjs-local'),
            true
        );
    }

    /**
     * Serve from a CDN?
     */
    public function do_cdn_url()
    {
        $this->do_text(
            __('Serve from a CDN?', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_CDN_URL,
            __('e.g. cdn.mydomain.com', 'host-analyticsjs-local'),
            CAOS_OPT_CDN_URL,
            sprintf(__('If you\'re using a CDN, enter the URL here to serve <code>%s</code> from your CDN.', 'host-analyticsjs-local'), CAOS_OPT_REMOTE_JS_FILE)
        );
    }

    /**
     * Enable Preconnect?
     */
    public function do_preconnect()
    {
        $this->do_checkbox(
            __('Enable Preconnect? (Recommended)', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_PRECONNECT,
            CAOS_OPT_PRECONNECT,
            __('Preconnect to google-analytics.com and CDN URL (if set) to reduce latency and speed up requests to these servers.', 'host-analyticsjs-local')
        );
    }

    /**
     * Capture outbound links?
     */
    public function do_capture_outbound_links()
    {
        $this->do_checkbox(
            __('Capture outbound links?', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_CAPTURE_OUTBOUND_LINKS,
            CAOS_OPT_CAPTURE_OUTBOUND_LINKS,
            sprintf(__('Find out when users click a link to leave your site. Only works with <code>analytics.js</code> and when Stealth Mode is disabled.  %sRead more%s', 'host-analyticsjs-local'), '<a target="_blank" href="https://support.google.com/analytics/answer/1136920">', '</a>')
        );
    }

    /**
     * Tbody open
     */
    public function do_tbody_advanced_settings_open()
    {
        $this->do_tbody_open('caos_advanced_settings');
    }

    /**
     * Cookie expiry period (days)
     */
    public function do_cookie_expiry()
    {
        $this->do_number(
            __('Cookie expiry period (days)', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_GA_COOKIE_EXPIRY_DAYS,
            CAOS_OPT_COOKIE_EXPIRY,
            __('The number of days when the cookie will automatically expire.', 'host-analyticsjs-local')
        );
    }

    /**
     * Use adjusted bounce rate?
     */
    public function do_adjusted_bounce_rate()
    {
        $this->do_number(
            __('Use adjusted bounce rate? (seconds)', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_ADJUSTED_BOUNCE_RATE,
            CAOS_OPT_ADJUSTED_BOUNCE_RATE,
            '<a href="' . CAOS_SITE_URL . '/how-to/adjusted-bounce-rate-caos/' . $this->utm_tags . '" target="_blank">' . __('More information about adjusted bounce rate', 'host-analyticsjs-local') . '</a>.'
        );
    }

    /**
     * Change enqueue order
     */
    public function do_change_enqueue_order()
    {
        $this->do_number(
            __('Change enqueue order? (Default = 0)', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_ENQUEUE_ORDER,
            CAOS_OPT_ENQUEUE_ORDER,
            __('Do not change this unless you know, what you\'re doing.', 'host-analyticsjs-local')
        );
    }

    /**
     * Disable all display features functionality
     */
    public function do_disable_display_feat()
    {
        $this->do_checkbox(
            __('Disable all display features functionality?', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_DISABLE_DISPLAY_FEATURES,
            CAOS_OPT_DISABLE_DISPLAY_FEAT,
            '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/display-features" target="_blank">' . __('What\'s this?', 'host-analyticsjs-local') . '</a>'
        );
    }

    /**
     * Anonymize IP
     */
    public function do_anonymize_ip()
    {
        $this->do_checkbox(
            __('Anonymize IP?', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_ANONYMIZE_IP,
            CAOS_OPT_ANONYMIZE_IP,
            __('Required by law in some countries. Replaces the last digits of a visitor\'s IP-address with \'000\'.', 'host-analyticsjs-local')
        );
    }

    /**
     * Track logged in Administrators
     */
    public function do_track_admin()
    {
        $this->do_checkbox(
            __('Track logged in Administrators?', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_TRACK_ADMIN,
            CAOS_OPT_TRACK_ADMIN,
            '<strong>' . __('Warning!', 'host-analyticsjs-local') . '</strong> ' . __('This will track all your traffic as a logged in user.', 'host-analyticsjs-local')
        );
    }

    /**
     * Remove settings at uninstall
     */
    public function do_uninstall_settings()
    {
        $this->do_checkbox(
            __('Remove settings at uninstall?', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS,
            CAOS_OPT_UNINSTALL_SETTINGS,
            '<strong>' . __('Warning!', 'host-analytics-local') . '</strong> ' . __('This will remove the settings from the database upon plugin deletion!', 'host-analyticsjs-local')
        );
    }
}
