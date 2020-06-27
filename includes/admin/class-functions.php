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

defined('ABSPATH') || exit;

class CAOS_Admin_Functions
{
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

        $fileStatus  = $this->cron_status();

        if ($fileStatus) {
            if (!get_transient(self::CAOS_ADMIN_UPDATE_SUCCESS_MESSAGE_SHOWN)) {
                CAOS_Admin_Notice::set_notice(__('CAOS is running healthy.', $this->plugin_text_domain) . ' <strong>' . CAOS_OPT_REMOTE_JS_FILE . '</strong> ' . __('was last updated on', $this->plugin_text_domain) . ' <em>' . $this->file_last_updated() . '</em> ' . __('and the next update is scheduled on', $this->plugin_text_domain) . ' <em>' . $this->cron_next_scheduled() . '</em>.', false);

                set_transient(self::CAOS_ADMIN_UPDATE_SUCCESS_MESSAGE_SHOWN, true, WEEK_IN_SECONDS);
            }
        } else {
            if (!get_transient(self::CAOS_ADMIN_UPDATE_ERROR_MESSAGE_SHOWN)) {
                CAOS_Admin_Notice::set_notice(sprintf(__('%s doesn\'t exist or hasn\'t been updated for more than two days. Try running <strong>Update %s</strong> in <em>Settings > Optimize Analytics</em> to fix this. If this message returns in the next few days, consider <a href="%s" target="_blank">replacing WordPress\' <em>pseudo cron</em> with a real cron</a>.', $this->plugin_text_domain), CAOS_OPT_REMOTE_JS_FILE, CAOS_OPT_REMOTE_JS_FILE, 'https://daan.dev/wordpress-plugins/caos/#not-updated-for-more-than-two-days'), false, 'error');

                set_transient(self::CAOS_ADMIN_UPDATE_ERROR_MESSAGE_SHOWN, true, HOUR_IN_SECONDS * 4);
            }
        }

        $blocked_pages = get_transient(self::CAOS_ADMIN_BLOCKED_PAGES_CURRENT_VALUE);

        // $blocked pages > 1, because the sentence is written in plural form.
        if (!get_transient(self::CAOS_ADMIN_BLOCKED_PAGES_NOTICE_SHOWN) && $blocked_pages > 1) {
            CAOS_Admin_Notice::set_notice(sprintf(__("During the past 7 days, CAOS detected <strong>%s pageviews</strong> on <em>%s</em> with an ad blocker active. CAOS' <strong>Super Stealth Upgrade</strong> <em>(starting at € 29,-)</em> bypasses Ad Blockers so you'll no longer miss out on data in Google Analytics. <a href='%s'>Upgrade now</a>!", $this->plugin_text_domain), number_format_i18n(get_option(self::CAOS_ADMIN_BLOCKED_PAGES_CURRENT_VALUE)), get_bloginfo('name'), CAOS_Admin_Settings::WOOSH_DEV_WORDPRESS_PLUGINS_SUPER_STEALTH), false, 'warning');

            CAOS_Admin_Notice::set_notice(sprintf(__('To disable these messages, disable <em>Track Ad Blockers</em> in <em>Settings > Optimize Google Analytics > <a href="%s">Extensions</a></em>.', $this->plugin_text_domain), admin_url(CAOS_Admin_Settings::CAOS_ADMIN_SETTINGS_EXTENSIONS_TAB_URI)), false, 'info');

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
    public function cron_status()
    {
        $fileModTime = @filemtime(CAOS_LOCAL_FILE_DIR);

        if (!$fileModTime) {
            return false;
        }

        if (time() - $fileModTime >= 48 * 3600) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Format timestamp of analytics.js last updated.
     *
     * @return string
     */
    public function file_last_updated()
    {
        $fileMtime = filemtime(CAOS_LOCAL_FILE_DIR);

        return $this->format_time_by_locale($fileMtime, get_locale());
    }

    /**
     * Get formatted timestamp of next scheduled cronjob.
     *
     * @return string
     */
    public function cron_next_scheduled()
    {
        $nextScheduled = wp_next_scheduled(CAOS_CRON);

        return $this->format_time_by_locale($nextScheduled, get_locale());
    }

    /**
     * Format any UNIX timestamp to a date/time in WP's chosen locale.
     *
     * @param null   $dateTime
     * @param string $locale
     *
     * @return string
     */
    public function format_time_by_locale($dateTime = null, $locale = 'en_US')
    {
        try {
            $dateObj = new DateTime;
            $dateObj->setTimestamp($dateTime);
        } catch (\Exception $e) {
            return __('Date/Time cannot be set', $this->plugin_text_domain) . ': ' . $e->getMessage();
        }

        $intlLoaded = extension_loaded('intl');

        if (!$intlLoaded) {
            return $dateObj->format('Y-m-d H:i:s');
        }

        try {
            $format = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::LONG);
        } catch (\Exception $e) {
            return __('Date/Time cannot be formatted to locale', $this->plugin_text_domain) . ': ' . $e->getMessage();
        }

        return $format->format($dateTime);
    }
}
