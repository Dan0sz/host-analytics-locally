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
    const CAOS_ADMIN_TIME_LAST_ERROR          = 'caos_time_last_error';
    const CAOS_ADMIN_TIME_LAST_SUCCESS        = 'caos_time_last_success';
    const CAOS_ADMIN_ERROR_MESSAGE_INTERVAL   = (3600 * 4);
    const CAOS_ADMIN_SUCCESS_MESSAGE_INTERVAL = (3600 * 24 * 7);


    public function __construct()
    {
        clearstatcache();

        $fileStatus  = $this->cron_status();
        $lastSuccess = (int) get_option(self::CAOS_ADMIN_TIME_LAST_SUCCESS);
        $lastError   = (int) get_option(self::CAOS_ADMIN_TIME_LAST_ERROR);

        if ($fileStatus) {
            if ($lastSuccess + self::CAOS_ADMIN_SUCCESS_MESSAGE_INTERVAL < time()) {
                CAOS_Admin_Notice::set_notice(__('CAOS is running healthy.', 'host-analyticsjs-local') . ' <strong>' . CAOS_OPT_REMOTE_JS_FILE . '</strong> ' . __('was last updated on', 'host-analyticsjs-local') . ' <em>' . $this->file_last_updated() . '</em> ' . __('and the next update is scheduled on', 'host-analyticsjs-local') . ' <em>' . $this->cron_next_scheduled() . '</em>.', false);

                update_option(self::CAOS_ADMIN_TIME_LAST_SUCCESS, time());
            }
        } else {
            if ($lastError + self::CAOS_ADMIN_ERROR_MESSAGE_INTERVAL < time()) {
                CAOS_Admin_Notice::set_notice(sprintf(__('%s doesn\'t exist or hasn\'t been updated for more than two days. Try running <strong>Update %s</strong> in <em>Settings > Optimize Analytics</em> to fix this. If this message returns in the next few days, make sure your cron is running healthy.', 'host-analyticsjs-local'), CAOS_OPT_REMOTE_JS_FILE, CAOS_OPT_REMOTE_JS_FILE), false, 'error');

                update_option(self::CAOS_ADMIN_TIME_LAST_ERROR, time());
            }
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
            return __('Date/Time cannot be set', 'host-analyticsjs-local') . ': ' . $e->getMessage();
        }

        $intlLoaded = extension_loaded('intl');

        if (!$intlLoaded) {
            return $dateObj->format('Y-m-d H:i:s');
        }

        try {
            $format = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::LONG);
        } catch (\Exception $e) {
            return __('Date/Time cannot be formatted to locale', 'host-analyticsjs-local') . ': ' . $e->getMessage();
        }

        return $format->format($dateTime);
    }
}
