<?php
/**
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

include(dirname(__FILE__) . '/class-caos-update.php');
$update = new CAOS_Update();

// Remote file to download
$remoteFile = CAOS_REMOTE_URL . '/' . CAOS_OPT_REMOTE_JS_FILE;
$localFile  = CAOS_LOCAL_FILE_DIR;

// Check if directory exists, otherwise create it.
$update->create_dir_recursive(CAOS_LOCAL_DIR);

if (CAOS_OPT_REMOTE_JS_FILE == 'gtag.js') {
    $remoteFile = array(
        'analytics' => array(
            'remote' => CAOS_GA_URL . '/analytics.js',
            'local'  => CAOS_LOCAL_DIR . 'analytics.js'
        ),
        'gtag' => array(
            'remote' => CAOS_GTM_URL . '/' . CAOS_OPT_REMOTE_JS_FILE,
            'local'  => CAOS_LOCAL_FILE_DIR
        )
    );
}

if (is_array($remoteFile)) {
    foreach ($remoteFile as $file => $location) {
        $update->update_file($location['local'], $location['remote']);

        if ($file == 'gtag') {
            $caosGaUrl = str_replace('gtag.js', 'analytics.js', caos_analytics_get_url());
            $gaUrl = CAOS_GA_URL . '/analytics.js';
            $update->update_gtag_js($location['local'], $gaUrl, $caosGaUrl);
        }
    }
} else {
    $update->update_file($localFile, $remoteFile);

    if (CAOS_OPT_STEALTH_MODE && (CAOS_OPT_REMOTE_JS_FILE == 'analytics.js')) {
        $update->insert_proxy(CAOS_LOCAL_FILE_DIR);

        $pluginDir = CAOS_LOCAL_DIR . '/plugins/ua';
        $update->create_dir_recursive($pluginDir);

        $plugins = [
            '/plugins/ua/ec.js',
            '/plugins/ua/linkid.js'
        ];

        foreach ($plugins as $plugin) {
            $localFile = rtrim(CAOS_LOCAL_DIR, '/') . $plugin;
            $remoteFile = CAOS_GA_URL . $plugin;
            $update->update_file($localFile, $remoteFile);
        }
    }
}
