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
 * @url      : https://daan.dev/wordpress/caos/
 * @copyright: (c) 2021 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

defined('ABSPATH') || exit;

class CAOS_Admin_Functions
{
    // Transients
    const CAOS_ADMIN_UPDATE_ERROR_MESSAGE_SHOWN   = 'caos_admin_update_error_shown';
    const CAOS_ADMIN_UPDATE_SUCCESS_MESSAGE_SHOWN = 'caos_admin_update_success_shown';
    const CAOS_ADMIN_BLOCKED_PAGES_NOTICE_SHOWN   = 'caos_admin_blocked_pages_notice_shown';
    const CAOS_ADMIN_BLOCKED_PAGES_CURRENT_VALUE  = 'caos_blocked_pages_current_value';

    /** @var string $plugin_text_domain */
    private $plugin_text_domain = 'host-analyticsjs-local';

    /**
     * Display notices and set transients.
     *
     * CAOS_Admin_Functions constructor.
     */
    public function __construct()
    {
        clearstatcache();

        $this->do_update_notice();

        $this->do_adblock_notice();
    }

    /**
     * @return void 
     */
    private function do_update_notice()
    {
        if (CAOS::uses_minimal_analytics()) {
            return;
        }

        $file_updated = $this->file_recently_updated();

        if (!$file_updated) {
            if (!get_transient(self::CAOS_ADMIN_UPDATE_ERROR_MESSAGE_SHOWN)) {
                CAOS_Admin_Notice::set_notice(sprintf(__('%s doesn\'t exist or hasn\'t been updated for more than two days. Try running <strong>Update %s</strong> in <em>Settings > Optimize Analytics</em> to fix this. If this message returns in the next few days, consider <a href="%s" target="_blank">replacing WordPress\' <em>pseudo cron</em> with a real cron</a>.', $this->plugin_text_domain), ucfirst(CAOS::get_current_file_key()), CAOS::get_current_file_key(), 'https://daan.dev/docs/caos-troubleshooting/analytics-js-gtag-js-doesnt-exist/'), 'error');

                set_transient(self::CAOS_ADMIN_UPDATE_ERROR_MESSAGE_SHOWN, true, HOUR_IN_SECONDS * 4);
            }
        }
    }

    /**
     * 
     */
    private function do_adblock_notice()
    {
        $blocked_pages = get_transient(self::CAOS_ADMIN_BLOCKED_PAGES_CURRENT_VALUE);

        // $blocked pages > 1, because the sentence is written in plural form.
        if (!get_transient(self::CAOS_ADMIN_BLOCKED_PAGES_NOTICE_SHOWN) && $blocked_pages > 1) {
            CAOS_Admin_Notice::set_notice(sprintf(__("During the past 7 days, CAOS detected <strong>%s pageviews</strong> on <em>%s</em> with an ad blocker active. CAOS Pro's <strong>Stealth Mode</strong> <em>(starting at € 24,-)</em> bypasses Ad Blockers so you'll no longer miss out on data in Google Analytics. <a href='%s'>Upgrade now</a>!", $this->plugin_text_domain), number_format_i18n(get_option(self::CAOS_ADMIN_BLOCKED_PAGES_CURRENT_VALUE)), get_bloginfo('name'), CAOS_Admin_Settings::FFW_PRESS_WORDPRESS_PLUGINS_CAOS_PRO), 'warning');

            CAOS_Admin_Notice::set_notice(sprintf(__('To disable these messages, disable <em>Track Ad Blockers</em> in <em>Settings > Optimize Google Analytics > <a href="%s">Extensions</a></em>.', $this->plugin_text_domain), admin_url(CAOS_Admin_Settings::CAOS_ADMIN_SETTINGS_EXTENSIONS_TAB_URI)), 'info');

            // Does not expire, but can be safely cleaned by db clean up plugins.
            set_transient(self::CAOS_ADMIN_BLOCKED_PAGES_CURRENT_VALUE, 0);
            set_transient(self::CAOS_ADMIN_BLOCKED_PAGES_NOTICE_SHOWN, true, WEEK_IN_SECONDS);
        }
    }

    /**
     * Check if cron is running
     *
     * @return bool
     */
    public function file_recently_updated()
    {
        $file_path = CAOS::get_file_alias_path(CAOS::get_current_file_key());

        if (!file_exists($file_path)) {
            return false;
        }

        $file_mod_time = @filemtime($file_path);

        if (!$file_mod_time) {
            return false;
        }

        if (time() - $file_mod_time >= 2 * DAY_IN_SECONDS) {
            return false;
        } else {
            return true;
        }
    }
}
