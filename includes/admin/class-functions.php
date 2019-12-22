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
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

defined('ABSPATH') || exit;

class CAOS_Admin_Functions
{
    /**
     * Check if cron is running
     *
     * @return bool
     */
    public function cron_status()
    {
        $fileModTime = filemtime(CAOS_LOCAL_FILE_DIR);

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
}
