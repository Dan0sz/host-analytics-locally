<?php
/**
 * If file is accessed directly or 'uninstall settings' option is not set, do nothing.
 */
if (!defined('WP_UNINSTALL_PLUGIN') || get_option('caos_analytics_uninstall_settings') !== 'on')
    return;

// If user has not set the 'uninstall settings' option, do nothing.

$options = array(
    'caos_allow_tracking',
    'caos_analytics_analytify_compatibility',
    'caos_analytics_cache_dir',
    'caos_analytics_cdn_url',
    'caos_analytics_js_file',
    'caos_analytics_uninstall_settings',
    'caos_cookie_value',
    'caos_disable_display_features',
    'caos_mi_compatibility',
    'caos_remove_wp_cron',
    'sgal_adjusted_bounce_rate',
    'sgal_anonymize_ip',
    'sgal_cookie_notice_name',
    'sgal_enqueue_order',
    'sgal_ga_cookie_expiry_days',
    'sgal_script_position',
    'sgal_tracking_id',
    'sgal_track_admin'
);

// Loop through array and delete each option.
foreach ($options as $option) {
    delete_option($option);
}

// Delete cached file
$cacheDir  = get_option('caos_analytics_cache_dir', '/cache/caos-analytics/');
$jsFile    = get_option('caos_analytics_js_file', 'analytics.js');
$fullPath  = WP_CONTENT_DIR . $cacheDir;
$cacheFile = $fullPath . $jsFile;

if (file_exists($cacheFile)) {
    wp_delete_file($cacheFile);
}

if (file_exists($fullPath)) {
    rmdir($fullPath);
}
