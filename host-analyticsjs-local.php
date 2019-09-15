<?php
/**
 * @formatter:off
 * Plugin Name: CAOS
 * Plugin URI: https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * Description: A plugin that allows you to completely optimize Google Analytics for your Wordpress Website - host analytics.js/gtag.js/ga.js locally and keep it updated using wp_cron(), serve from CDN, place tracking code in footer, and much more!
 * Version: 2.6.5
 * Author: Daan van den Bergh
 * Author URI: https://daan.dev
 * License: GPL2v2 or later
 * Text Domain: host-analyticsjs-local
 * @formatter:on
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

/**
 * Define Constants
 */
define('CAOS_STATIC_VERSION', '2.1.7');
define('CAOS_SITE_URL', 'https://daan.dev');
define('CAOS_BLOG_ID', get_current_blog_id());
define('CAOS_OPT_TRACKING_ID', esc_attr(get_option('sgal_tracking_id')));
define('CAOS_OPT_ALLOW_TRACKING', esc_attr(get_option('caos_allow_tracking')));
define('CAOS_OPT_COOKIE_NAME', esc_attr(get_option('sgal_cookie_notice_name')));
define('CAOS_OPT_COOKIE_VALUE', esc_attr(get_option('caos_cookie_value')));
define('CAOS_OPT_COMPATIBILITY_MODE', esc_attr(get_option('caos_analytics_compatibility_mode', null)));
define('CAOS_OPT_STEALTH_MODE', esc_attr(get_option('caos_stealth_mode')));
define('CAOS_OPT_COOKIE_EXPIRY', esc_attr(get_option('sgal_ga_cookie_expiry_days')));
define('CAOS_OPT_ADJUSTED_BOUNCE_RATE', esc_attr(get_option('sgal_adjusted_bounce_rate')));
define('CAOS_OPT_ENQUEUE_ORDER', esc_attr(get_option('sgal_enqueue_order', 0)));
define('CAOS_OPT_ANONYMIZE_IP', esc_attr(get_option('sgal_anonymize_ip')));
define('CAOS_OPT_TRACK_ADMIN', esc_attr(get_option('sgal_track_admin')));
define('CAOS_OPT_DISABLE_DISPLAY_FEAT', esc_attr(get_option('caos_disable_display_features')));
define('CAOS_OPT_SCRIPT_POSITION', esc_attr(get_option('sgal_script_position')));
define('CAOS_OPT_SNIPPET_TYPE', esc_attr(get_option('caos_snippet_type', 'default')));
define('CAOS_OPT_REMOTE_JS_FILE', esc_attr(get_option('caos_analytics_js_file', 'analytics.js')));
define('CAOS_OPT_CACHE_DIR', esc_attr(get_option('caos_analytics_cache_dir', '/cache/caos-analytics/')));
define('CAOS_OPT_CDN_URL', esc_attr(get_option('caos_analytics_cdn_url')));
define('CAOS_OPT_UNINSTALL_SETTINGS', esc_attr(get_option('caos_analytics_uninstall_settings')));
define('CAOS_COOKIE_EXPIRY_DAYS', CAOS_OPT_COOKIE_EXPIRY ? CAOS_OPT_COOKIE_EXPIRY * 86400 : 0);
define('CAOS_CRON', 'caos_update_analytics_js');
define('CAOS_GA_URL', 'https://www.google-analytics.com');
define('CAOS_GTM_URL', 'https://www.googletagmanager.com');
define('CAOS_REMOTE_URL', CAOS_OPT_REMOTE_JS_FILE == 'gtag.js' ? CAOS_GTM_URL : CAOS_GA_URL);
define('CAOS_LOCAL_DIR', WP_CONTENT_DIR . CAOS_OPT_CACHE_DIR);
define('CAOS_LOCAL_FILE_DIR', CAOS_LOCAL_DIR . CAOS_OPT_REMOTE_JS_FILE);
define('CAOS_LOCAL_FILE_URL', caos_analytics_get_url());
define('CAOS_PROXY_URI', '/wp-json/caos-analytics/v1/proxy');

/**
 * Register Settings
 */
function caos_analytics_register_settings()
{
    register_setting(
        'save-ga-locally-basic-settings',
        'sgal_tracking_id'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'caos_allow_tracking'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'sgal_cookie_notice_name'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'caos_cookie_value'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'caos_analytics_compatibility_mode'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'caos_stealth_mode'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'caos_analytics_js_file'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'caos_analytics_cache_dir'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'caos_analytics_cdn_url'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'sgal_ga_cookie_expiry_days'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'sgal_adjusted_bounce_rate'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'sgal_script_position'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'caos_snippet_type'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'sgal_enqueue_order'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'sgal_anonymize_ip'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'sgal_track_admin'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'caos_remove_wp_cron'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'caos_disable_display_features'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'caos_analytics_uninstall_settings'
    );
}

/**
 * Create WP menu-item
 */
function caos_analytics_create_menu()
{
    add_options_page(
        'Complete Analytics Optimization Suite',
        'Optimize Analytics',
        'manage_options',
        'host_analyticsjs_local',
        'caos_analytics_settings_page'
    );

    add_action(
        'admin_init',
        'caos_analytics_register_settings'
    );
}
add_action('admin_menu', 'caos_analytics_create_menu');

/**
 * @return string
 */
function caos_analytics_get_url()
{
    $url = content_url() . CAOS_OPT_CACHE_DIR . CAOS_OPT_REMOTE_JS_FILE;

    if (CAOS_OPT_CDN_URL) {
        $url = str_replace(get_site_url(CAOS_BLOG_ID), '//' . CAOS_OPT_CDN_URL, $url);
    }

    return $url;
}

/**
 * @return string
 */
function caos_analytics_get_dir()
{
    $dir = WP_CONTENT_DIR . CAOS_OPT_CACHE_DIR . CAOS_OPT_REMOTE_JS_FILE;

    return $dir;
}

/**
 * Format any UNIX timestamp to a date/time in WP's chosen locale.
 *
 * @param null   $dateTime
 * @param string $locale
 *
 * @return string
 */
function caos_analytics_format_time_by_locale($dateTime = null, $locale = 'en_US')
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
 * Format timestamp of analytics.js last updated.
 *
 * @return string
 */
function caos_analytics_file_last_updated()
{
    $fileMtime = filemtime(CAOS_LOCAL_FILE_DIR);

    return caos_analytics_format_time_by_locale($fileMtime, get_locale());
}

/**
 * Get formatted timestamp of next scheduled cronjob.
 *
 * @return string
 */
function caos_analytics_cron_next_scheduled()
{
    $nextScheduled = wp_next_scheduled(CAOS_CRON);

    return caos_analytics_format_time_by_locale($nextScheduled, get_locale());
}

/**
 * Check if cron is running
 *
 * @return bool
 */
function caos_analytics_cron_status()
{
    $fileModTime = filemtime(CAOS_LOCAL_FILE_DIR);

    if (time() - $fileModTime >= 48 * 3600) {
        return false;
    } else {
        return true;
    }
}

/**
 * Add settings link to plugin overview
 *
 * @param $links
 *
 * @return mixed
 */
function caos_analytics_settings_link($links)
{
    $adminUrl     = admin_url() . 'options-general.php?page=host_analyticsjs_local';
    $settingsLink = "<a href='$adminUrl'>" . __('Settings', 'host-analyticsjs-local') . "</a>";
    array_push($links, $settingsLink);

    return $links;
}
$caosLink = plugin_basename(__FILE__);
add_filter("plugin_action_links_$caosLink", 'caos_analytics_settings_link');

/**
 * Create settings page
 */
function caos_analytics_settings_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(__("You're not cool enough to access this page.", 'host-analyticsjs-local'));
    }
    ?>

    <div class="wrap">
        <h1><?php _e('CAOS | Complete Analytics Optimization Suite', 'host-analyticsjs-local'); ?></h1>

        <div id="caos-notices"></div>

        <p>
            <?php _e('Developed by: ', 'host-analyticsjs-local'); ?>
            <a title="Buy me a beer!" href="<?= CAOS_SITE_URL; ?>/donate/">Daan van den Bergh</a>.
        </p>

        <?php include(plugin_dir_path(__FILE__) . 'includes/welcome-panel.php'); ?>

        <form method="post" action="options.php">
            <?php
            settings_fields(
                'save-ga-locally-basic-settings'
            );
            do_settings_sections(
                'save-ga-locally-basic-settings'
            );
            ?>

            <?php include(plugin_dir_path(__FILE__) . 'includes/caos-form.php'); ?>

            <?php do_action('caos_after_form_settings'); ?>

            <div style="clear: left; display: inline-block;">
                <?php submit_button(); ?>
            </div>

            <div style="display: inline-block;">
                <p class="submit">
                    <input id="manual-download" class="button button-secondary" name="caos-download" value="Update <?= CAOS_OPT_REMOTE_JS_FILE; ?>" type="button"
                           onclick="caosDownloadManually();"/>
                </p>
            </div>
        </form>
    </div>
    <?php
}

/**
 * Create Cache-dir upon reactivation.
 */
function caos_analytics_create_cache_dir()
{
    $uploadDir = CAOS_LOCAL_DIR;
    if (!is_dir($uploadDir)) {
        wp_mkdir_p($uploadDir);
    }
}
register_activation_hook(__FILE__, 'caos_analytics_create_cache_dir');

/**
 * Enqueue JS scripts for Administrator Area.
 * s
 *
 * @param $hook
 */
function caos_analytics_enqueue_js_scripts($hook)
{
    if ($hook == 'settings_page_host_analyticsjs_local') {
        wp_enqueue_script('caos_admin_script', plugins_url('js/caos-admin.js', __FILE__), ['jquery'], CAOS_STATIC_VERSION, true);
    }
}
add_action('admin_enqueue_scripts', 'caos_analytics_enqueue_js_scripts');

/**
 * Register hook to schedule script in wp_cron()
 */
function caos_analytics_activate_cron()
{
    if (!wp_next_scheduled(CAOS_CRON)) {
        wp_schedule_event(time(), 'daily', CAOS_CRON);
    }
}
register_activation_hook(__FILE__, 'caos_analytics_activate_cron');

/**
 * Load update script to schedule in wp_cron()
 */
function caos_analytics_load_cron_script()
{
    include(plugin_dir_path(__FILE__) . 'includes/update-analytics.php');
}
add_action(CAOS_CRON, 'caos_analytics_load_cron_script');

/**
 * Manually Update Local Analytics.js Script
 */
add_action('wp_ajax_caos_analytics_ajax_manual_download', 'caos_analytics_load_cron_script');

/**
 * Remove script from wp_cron upon plugin deactivation
 */
function caos_analytics_deactivate_cron()
{
    if (wp_next_scheduled(CAOS_CRON)) {
        wp_clear_scheduled_hook(CAOS_CRON);
    }
}
register_deactivation_hook(__FILE__, 'caos_analytics_deactivate_cron');

/**
 * Register CAOS Proxy so endpoint can be used.
 * For using Stealth mode, SSL is required.
 */
function caos_register_proxy()
{
    if (!CAOS_OPT_STEALTH_MODE) {
        return;
    }

    include(plugin_dir_path(__FILE__) . 'includes/class-caos-proxy.php');

    $controller = new CAOS_Proxy();
    $controller->register_routes();
}
add_action('rest_api_init', 'caos_register_proxy');

/**
 * Generate tracking code and add to header/footer (default is header)
 */
function caos_analytics_render_tracking_code()
{
    if (!CAOS_OPT_TRACKING_ID) {
        return;
    } ?>

    <!-- This site is running CAOS: Complete Analytics Optimization Suite for Wordpress -->
    <?php if (
    CAOS_OPT_REMOTE_JS_FILE == 'gtag.js'
    || (CAOS_OPT_SNIPPET_TYPE == 'async' && CAOS_OPT_REMOTE_JS_FILE != 'ga.js')):
    $urlId = CAOS_OPT_REMOTE_JS_FILE == 'gtag.js' ? "?id=" . CAOS_OPT_TRACKING_ID : '';
    ?>
    <script <?= CAOS_OPT_SNIPPET_TYPE == 'async' ? 'async' : ''; ?> src="<?= CAOS_LOCAL_FILE_URL . $urlId; ?>"></script>
<?php endif; ?>

    <script>
        <?php
        if (CAOS_OPT_ALLOW_TRACKING && CAOS_OPT_COOKIE_NAME
            && CAOS_OPT_COOKIE_VALUE): ?>
        function getCookieValue(name)
        {
            cookies = document.cookie;
            cookiesArray = cookies.split('; ');
            cookieValue = null;
            cookiesArray.forEach(function (cookie) {
                cookieArray = cookie.split('=');
                if (cookieArray[0] !== name) {
                    return;
                }
                cookieValue = cookieArray[1];
            });
            return cookieValue;
        }

        cookieValue = getCookieValue('<?= CAOS_OPT_COOKIE_NAME; ?>');
        <?php endif; ?>

        <?php if (CAOS_OPT_REMOTE_JS_FILE == 'gtag.js'): ?>
        window.dataLayer = window.dataLayer || [];
        <?php if (CAOS_OPT_ALLOW_TRACKING == 'cookie_is_set' && CAOS_OPT_COOKIE_NAME): ?>
        if (document.cookie.indexOf('<?= CAOS_OPT_COOKIE_NAME; ?>=')) {
            window['ga-disable-<?= CAOS_OPT_TRACKING_ID; ?>'] = false;
        } else {
            window['ga-disable-<?= CAOS_OPT_TRACKING_ID; ?>'] = true;
        }
        <?php elseif (CAOS_OPT_ALLOW_TRACKING == 'cookie_has_value' && CAOS_OPT_COOKIE_NAME && CAOS_OPT_COOKIE_VALUE): ?>
        if (cookieValue === '<?= CAOS_OPT_COOKIE_VALUE; ?>') {
            window['ga-disable-<?= CAOS_OPT_TRACKING_ID; ?>'] = false;
        } else {
            window['ga-disable-<?= CAOS_OPT_TRACKING_ID; ?>'] = true;
        }
        <?php else: ?>
        window['ga-disable-<?= CAOS_OPT_TRACKING_ID; ?>'] = false;
        <?php endif; ?>

        function gtag() {dataLayer.push(arguments);}

        gtag('js', new Date());
        gtag('config', '<?= CAOS_OPT_TRACKING_ID; ?>', {
                'cookie_prefix': 'CaosGtag',
                'cookie_domain': '<?= $_SERVER['SERVER_NAME']; ?>',
                'cookie_expires': '<?= CAOS_COOKIE_EXPIRY_DAYS; ?>',
                'anonymize_ip': '<?= CAOS_OPT_ANONYMIZE_IP ? 'true' : 'false'; ?>'
            }
        );

        <?php if (CAOS_OPT_ADJUSTED_BOUNCE_RATE): ?>
        setTimeout(
            gtag('event', 'Adjusted Bounce Rate', {
                    'event_category': 'ABR'
                }
            ), <?= CAOS_OPT_ADJUSTED_BOUNCE_RATE * 1000; ?>
        );
        <?php endif; ?>

        <?php else: ?>
        <?php if (CAOS_OPT_SNIPPET_TYPE == 'default'): ?>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments);
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m);
        })(window, document, 'script', '<?= CAOS_LOCAL_FILE_URL; ?>', 'ga');
        <?php elseif (CAOS_OPT_SNIPPET_TYPE == 'async'): ?>
        window.ga = window.ga || function () {(ga.q = ga.q || []).push(arguments);};
        ga.l = +new Date;
        <?php endif; ?>

        <?php if (CAOS_OPT_ALLOW_TRACKING == 'cookie_is_set' && CAOS_OPT_COOKIE_NAME): ?>
        if (document.cookie.indexOf('<?= CAOS_OPT_COOKIE_NAME; ?>=')) {
            window['ga-disable-<?= CAOS_OPT_TRACKING_ID; ?>'] = false;
        } else {
            window['ga-disable-<?= CAOS_OPT_TRACKING_ID; ?>'] = true;
        }
        <?php elseif (CAOS_OPT_ALLOW_TRACKING == 'cookie_has_value' && CAOS_OPT_COOKIE_NAME && CAOS_OPT_COOKIE_VALUE): ?>
        if (cookieValue === '<?= CAOS_OPT_COOKIE_VALUE; ?>') {
            window['ga-disable-<?= CAOS_OPT_TRACKING_ID; ?>'] = false;
        } else {
            window['ga-disable-<?= CAOS_OPT_TRACKING_ID; ?>'] = true;
        }
        <?php else: ?>
        window['ga-disable-<?= CAOS_OPT_TRACKING_ID; ?>'] = false;
        <?php endif; ?>

        ga('create', '<?= CAOS_OPT_TRACKING_ID; ?>',
            {
                'cookieName': 'caosLocalGa',
                'cookieDomain': '<?= $_SERVER['SERVER_NAME']; ?>',
                'cookieExpires': '<?= CAOS_COOKIE_EXPIRY_DAYS; ?>'
            }
        );

        <?php if (CAOS_OPT_DISABLE_DISPLAY_FEAT == 'on'): ?>
        ga('set', 'displayFeaturesTask', null);
        <?php endif; ?>

        <?php if (CAOS_OPT_ANONYMIZE_IP == 'on'): ?>
        ga('set', 'anonymizeIp', true);
        <?php endif; ?>
        ga('send', 'pageview');

        <?php if (CAOS_OPT_ADJUSTED_BOUNCE_RATE): ?>
        setTimeout(
            "ga('send', 'event', 'adjusted bounce rate', '<?= CAOS_OPT_ADJUSTED_BOUNCE_RATE . " seconds"; ?>')", <?= CAOS_OPT_ADJUSTED_BOUNCE_RATE * 1000; ?>);
        <?php endif; ?>

        <?php endif; ?>
    </script>
    <?php
}

/**
 * Render a HTML comment for logged in Administrators in the source code.
 */
function caos_analytics_show_admin_message()
{
    echo "<!-- This site is using CAOS, but you\'re an Administrator. So we\'re not loading the tracking code. -->\n";
}

/**
 * Render the URL of the cached local-ga.js file
 *
 * @return string
 */
function caos_analytics_return_analytics_js_url()
{
    return CAOS_LOCAL_FILE_URL;
}

/**
 * Render the tracking code in it's selected locations
 */
function caos_analytics_insert_tracking_code()
{
    if (CAOS_OPT_COMPATIBILITY_MODE == 'woocommerce') {
        add_filter('woocommerce_google_analytics_script_src', 'caos_analytics_return_analytics_js_url', PHP_INT_MAX);
    } elseif (CAOS_OPT_COMPATIBILITY_MODE == 'monster_insights') {
        add_filter('monsterinsights_frontend_output_analytics_src', 'caos_analytics_return_analytics_js_url', PHP_INT_MAX);
    } elseif (CAOS_OPT_COMPATIBILITY_MODE == 'analytify') {
        add_filter('analytify_output_ga_js_src', 'caos_analytics_return_analytics_js_url', PHP_INT_MAX);
    } elseif (CAOS_OPT_COMPATIBILITY_MODE == 'exact_metrics') {
        add_filter('gadwp_analytics_script_path', 'caos_analytics_return_analytics_js_url', PHP_INT_MAX);
    } elseif (current_user_can('manage_options') && !CAOS_OPT_TRACK_ADMIN) {
        switch (CAOS_OPT_SCRIPT_POSITION) {
            case "footer":
                add_action('wp_footer', 'caos_analytics_show_admin_message', CAOS_OPT_ENQUEUE_ORDER);
                break;
            case "manual":
                break;
            default:
                add_action('wp_head', 'caos_analytics_show_admin_message', CAOS_OPT_ENQUEUE_ORDER);
                break;
        }
    } else {
        switch (CAOS_OPT_SCRIPT_POSITION) {
            case "footer":
                add_action('wp_footer', 'caos_analytics_render_tracking_code', CAOS_OPT_ENQUEUE_ORDER);
                break;
            case "manual":
                break;
            default:
                add_action('wp_head', 'caos_analytics_render_tracking_code', CAOS_OPT_ENQUEUE_ORDER);
                break;
        }
    }
}
add_action('init', 'caos_analytics_insert_tracking_code');
