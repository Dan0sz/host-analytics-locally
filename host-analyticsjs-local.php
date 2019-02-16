<?php
/**
 * Plugin Name: CAOS for Analytics
 * Plugin URI: https://dev.daanvandenbergh.com/wordpress-plugins/optimize-analytics-wordpress/
 * Description: A plugin that allows you to completely optimize Google Analytics for your Wordpress Website: host analytics.js locally, keep it updated using wp_cron(), anonymize IP, disable tracking of admins, place tracking code in footer, and more!
 * Version: 2.1.2
 * Author: Daan van den Bergh
 * Author URI: https://dev.daanvandenbergh.com
 * License: GPL2v2 or later
 */

if (!defined('ABSPATH')) exit;

global $wpdb;

/**
 * Define Constants
 */
define('CAOS_ANALYTICS_STATIC_VERSION', '2.1.2');
define('CAOS_ANALYTICS_DB_TABLENAME', $wpdb->prefix . 'caos_analytics');
define('CAOS_ANALYTICS_DB_CHARSET', $wpdb->get_charset_collate());
define('CAOS_ANALYTICS_CRON', 'caos_update_analytics_js');
define('CAOS_ANALYTICS_TRACKING_ID', esc_attr(get_option('sgal_tracking_id')));
define('CAOS_ANALYTICS_ALLOW_TRACKING', esc_attr(get_option('caos_allow_tracking')));
define('CAOS_ANALYTICS_COOKIE_NAME', esc_attr(get_option('sgal_cookie_notice_name')));
define('CAOS_ANALYTICS_COOKIE_VALUE', esc_attr(get_option('caos_cookie_value')));
define('CAOS_ANALYTICS_MI_COMPATIBILITY', esc_attr(get_option('caos_mi_compatibility')));
define('CAOS_ANALYTICS_COOKIE_EXPIRY', esc_attr(get_option('sgal_ga_cookie_expiry_days')));
define('CAOS_ANALYTICS_COOKIE_EXPIRY_DAYS', CAOS_ANALYTICS_COOKIE_EXPIRY ? CAOS_ANALYTICS_COOKIE_EXPIRY * 86400 : 0);
define('CAOS_ANALYTICS_ADJUSTED_BOUNCE_RATE', esc_attr(get_option('sgal_adjusted_bounce_rate')));
define('CAOS_ANALYTICS_ENQUEUE_ORDER', esc_attr(get_option('sgal_enqueue_order')));
define('CAOS_ANALYTICS_ANONYMIZE_IP', esc_attr(get_option('sgal_anonymize_ip')));
define('CAOS_ANALYTICS_TRACK_ADMIN', esc_attr(get_option('sgal_track_admin')));
define('CAOS_ANALYTICS_REMOVE_CRON', esc_attr(get_option('caos_remove_wp_cron')));
define('CAOS_ANALYTICS_DISABLE_DISPLAY_FEAT', esc_attr(get_option('caos_disable_display_features')));
define('CAOS_ANALYTICS_SCRIPT_POSITION', esc_attr(get_option('sgal_script_position')));
define('CAOS_ANALYTICS_BLOG_ID', get_current_blog_id());
define('CAOS_ANALYTICS_JS_FILE', 'analytics.js');
define('CAOS_ANALYTICS_CACHE_DIR', esc_attr(get_option('caos_analytics_cache_dir')) ?: '/cache/caos-analytics/');
define('CAOS_ANALYTICS_UPLOAD_PATH', WP_CONTENT_DIR . CAOS_ANALYTICS_CACHE_DIR);
define('CAOS_ANALYTICS_JS_DIR', CAOS_ANALYTICS_UPLOAD_PATH . CAOS_ANALYTICS_JS_FILE);
define('CAOS_ANALYTICS_JS_URL', get_site_url(CAOS_ANALYTICS_BLOG_ID, caos_analytics_get_content_dir_name() . CAOS_ANALYTICS_CACHE_DIR . CAOS_ANALYTICS_JS_FILE));

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
        'caos_mi_compatibility'
    );
    register_setting(
        'save-ga-locally-basic-settings',
        'caos_analytics_cache_dir'
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
        'host-analyticsjs-local',
        'caos_analytics_settings_page'
    );

    add_action(
        'admin_init',
        'caos_analytics_register_settings'
    );
}

add_action('admin_menu', 'caos_analytics_create_menu');

/**
 * Returns the configured name of WordPress' content directory.
 *
 * @return mixed
 */
function caos_analytics_get_content_dir_name()
{
    preg_match('/[^\/]+$/u', WP_CONTENT_DIR, $match);

    return $match[0];
}

/**
 * Format any UNIX timestamp to a date/time in WP's chosen locale.
 *
 * @param null $dateTime
 * @param      $locale
 *
 * @return string
 */
function caos_analytics_format_time_by_locale($dateTime = null, $locale)
{
    $dateObj = new DateTime();
    $dateObj->setTimestamp($dateTime);
    $format = new IntlDateFormatter($locale, IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM);

    return $format->format($dateTime);
}

/**
 * Format timestamp of analytics.js last updated.
 *
 * @return string
 */
function caos_analytics_file_last_updated()
{
    $fileMtime = filemtime(CAOS_ANALYTICS_JS_DIR);
    return caos_analytics_format_time_by_locale($fileMtime, get_locale()) ?: date('Y-m-d H:i:s', $fileMtime);
}

/**
 * Get formatted timestamp of next scheduled cronjob.
 *
 * @return string
 */
function caos_analytics_cron_next_scheduled()
{
    $nextScheduled = wp_next_scheduled(CAOS_ANALYTICS_CRON);
    return caos_analytics_format_time_by_locale($nextScheduled, get_locale()) ?: date('Y-m-d H:i:s', $nextScheduled);
}

/**
 * Check if cron is running
 *
 * @return bool
 */
function caos_analytics_cron_status()
{
    $fileModTime = filemtime(CAOS_ANALYTICS_JS_DIR);

    if (time() - $fileModTime >= 48 * 3600)
    {
        return false;
    } else
    {
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
    $adminUrl = admin_url() . 'options-general.php?page=host-analyticsjs-local';
    $settingsLink = "<a href='$adminUrl'>" . __('Settings') . "</a>";
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
    if (!current_user_can('manage_options'))
    {
        wp_die(__("You're not cool enough to access this page."));
    }
    ?>

    <div class="wrap">
        <h1><?php _e('CAOS for Analytics', 'host-analyticsjs-local'); ?></h1>

        <div id="caos-notices"></div>

        <p>
            <?php _e('Developed by: ', 'host-analyticsjs-local'); ?>
            <a title="Buy me a beer!" href="http://dev.daanvandenbergh.com/donate/">Daan van den Bergh</a>.
        </p>

        <p>
            <?php _e('Consider using'); ?> <a href="https://wordpress.org/plugins/cdn-enabler/">CDN
                Enabler</a> <?php _e('to host your Analytics-script (analytics.js) from your CDN'); ?>.
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
                    <input id="manual-download" class="button button-secondary" name="caos-download" value="Update analytics.js" type="button"
                           onclick="caosDownloadManually();"/>
                </p>
            </div>
        </form>
    </div>
    <?php
}

// Create Cache Dir upon plugin (re-)activation.
function caos_analytics_create_cache_dir()
{
    $uploadDir = CAOS_ANALYTICS_UPLOAD_PATH;
    if (!is_dir($uploadDir))
    {
        wp_mkdir_p($uploadDir);
    }
}

register_activation_hook(__FILE__, 'caos_analytics_create_cache_dir');

// Enqueue JS scripts for Administrator Area.
function caos_analytics_enqueue_js_scripts($hook)
{
    if ($hook == 'settings_page_host-analyticsjs-local')
    {
        wp_enqueue_script('caos_admin_script', plugins_url('js/caos-admin.js', __FILE__), ['jquery'], CAOS_ANALYTICS_STATIC_VERSION, true);
    }
}

add_action('admin_enqueue_scripts', 'caos_analytics_enqueue_js_scripts');

// Register hook to schedule script in wp_cron()
function caos_analytics_activate_cron()
{
    if (!wp_next_scheduled(CAOS_ANALYTICS_CRON))
    {
        wp_schedule_event(time(), 'daily', CAOS_ANALYTICS_CRON);
    }
}

register_activation_hook(__FILE__, 'caos_analytics_activate_cron');

// Load update script to schedule in wp_cron()
function caos_analytics_load_cron_script()
{
    include(plugin_dir_path(__FILE__) . 'includes/update-analytics.php');
}

add_action(CAOS_ANALYTICS_CRON, 'caos_analytics_load_cron_script');

// Manually Update Local Analytics.js Script
add_action('wp_ajax_caos_analytics_ajax_manual_download', 'caos_analytics_load_cron_script');

// Remove script from wp_cron upon plugin deactivation
function caos_analytics_deactivate_cron()
{
    if (wp_next_scheduled(CAOS_ANALYTICS_CRON))
    {
        wp_clear_scheduled_hook(CAOS_ANALYTICS_CRON);
    }
}

register_deactivation_hook(__FILE__, 'caos_analytics_deactivate_cron');

// Deactivate cron is option is checked
function caos_analytics_deactivate_wp_cron()
{
    switch (CAOS_ANALYTICS_REMOVE_CRON)
    {
        case "on":
            if (wp_next_scheduled(CAOS_ANALYTICS_CRON))
            {
                wp_clear_scheduled_hook(CAOS_ANALYTICS_CRON);
            }
            break;
        default:
            if (!wp_next_scheduled(CAOS_ANALYTICS_CRON))
            {
                wp_schedule_event(time(), 'hourly', CAOS_ANALYTICS_CRON);
            }
    }
}

add_action('init', 'caos_analytics_deactivate_wp_cron');

// Generate tracking code and add to header/footer (default is header)
function caos_analytics_render_tracking_code()
{
    if (!CAOS_ANALYTICS_TRACKING_ID) return; ?>

    <!-- This site is running CAOS: Complete Analytics Optimization Suite for Wordpress -->
    <script>
        <?php
        if (CAOS_ANALYTICS_COOKIE_NAME && CAOS_ANALYTICS_COOKIE_VALUE): ?>
        function getCookieValue(name)
        {
            cookies = document.cookie;
            cookiesArray = cookies.split('; ');
            cookieValue = null;
            cookiesArray.forEach(function(cookie) {
                cookieArray = cookie.split('=');
                if(cookieArray[ 0 ] !== name) {
                    return;
                }
                cookieValue = cookieArray[ 1 ];
            });
            return cookieValue;
        }

        cookieValue = getCookieValue('<?php echo CAOS_ANALYTICS_COOKIE_NAME; ?>');
        <?php endif; ?>

        (function(i, s, o, g, r, a, m) {
            i[ 'GoogleAnalyticsObject' ] = r;
            i[ r ] = i[ r ] || function() {
                (i[ r ].q = i[ r ].q || []).push(arguments);
            }, i[ r ].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[ 0 ];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m);
        })(window, document, 'script', '<?php echo CAOS_ANALYTICS_JS_URL; ?>', 'ga');
        <?php if (CAOS_ANALYTICS_ALLOW_TRACKING == 'cookie_is_set' && CAOS_ANALYTICS_COOKIE_NAME): ?>
        if(document.cookie.indexOf('<?php echo CAOS_ANALYTICS_COOKIE_NAME; ?>=')) {
            window[ 'ga-disable-<?php echo CAOS_ANALYTICS_TRACKING_ID; ?>' ] = false;
        } else {
            window[ 'ga-disable-<?php echo CAOS_ANALYTICS_TRACKING_ID; ?>' ] = true;
        }
        <?php elseif (CAOS_ANALYTICS_ALLOW_TRACKING == 'cookie_has_value' && CAOS_ANALYTICS_COOKIE_NAME && CAOS_ANALYTICS_COOKIE_VALUE): ?>
        if(cookieValue === '<?php echo CAOS_ANALYTICS_COOKIE_VALUE; ?>') {
            window[ 'ga-disable-<?php echo CAOS_ANALYTICS_TRACKING_ID; ?>' ] = false;
        } else {
            window[ 'ga-disable-<?php echo CAOS_ANALYTICS_TRACKING_ID; ?>' ] = true;
        }
        <?php else: ?>
        window[ 'ga-disable-<?php echo CAOS_ANALYTICS_TRACKING_ID; ?>' ] = false;
        <?php endif; ?>
        ga('create', '<?php echo CAOS_ANALYTICS_TRACKING_ID; ?>',
            {
                'cookieName': 'caosLocalGa',
                'cookieDomain': '<?php echo $_SERVER['SERVER_NAME']; ?>',
                'cookieExpires': '<?php echo CAOS_ANALYTICS_COOKIE_EXPIRY_DAYS; ?>'
            }
        );
        <?php if (CAOS_ANALYTICS_DISABLE_DISPLAY_FEAT == 'on'): ?>
        ga('set', 'displayFeaturesTask', null);
        <?php endif; ?>
        <?php if (CAOS_ANALYTICS_ANONYMIZE_IP == 'on'): ?>
        ga('set', 'anonymizeIp', true);
        <?php endif; ?>
        ga('send', 'pageview');
        <?php if (CAOS_ANALYTICS_ADJUSTED_BOUNCE_RATE): ?>
        setTimeout(
            "ga('send', 'event', 'adjusted bounce rate', '<?php echo CAOS_ANALYTICS_ADJUSTED_BOUNCE_RATE . " seconds"; ?>')", <?php echo CAOS_ANALYTICS_ADJUSTED_BOUNCE_RATE * 1000; ?>);
        <?php endif; ?>
    </script>
    <?php
}

// Render a HTML comment for logged in Administrators in the source code.
function caos_analytics_show_admin_message()
{
    echo "<!-- This site is using CAOS, but you\'re an Administrator. So we\'re not loading the tracking code. -->\n";
}

// Render the URL of the cached local-ga.js file
function caos_analytics_host_mi_locally($url)
{
    return CAOS_ANALYTICS_JS_URL;
}

// Render the tracking code in it's selected locations
function caos_analytics_insert_tracking_code()
{
    $sgal_enqueue_order = CAOS_ANALYTICS_ENQUEUE_ORDER ? CAOS_ANALYTICS_ENQUEUE_ORDER : 0;

    if (CAOS_ANALYTICS_MI_COMPATIBILITY == 'on')
    {
        add_filter('monsterinsights_frontend_output_analytics_src', 'caos_analytics_host_mi_locally', 1000);
    } elseif (current_user_can('manage_options') && !CAOS_ANALYTICS_TRACK_ADMIN)
    {
        switch (CAOS_ANALYTICS_SCRIPT_POSITION)
        {
            case "footer":
                add_action('wp_footer', 'caos_analytics_show_admin_message', $sgal_enqueue_order);
                break;
            case "manual":
                break;
            default:
                add_action('wp_head', 'caos_analytics_show_admin_message', $sgal_enqueue_order);
                break;
        }
    } else
    {
        switch (CAOS_ANALYTICS_SCRIPT_POSITION)
        {
            case "footer":
                add_action('wp_footer', 'caos_analytics_render_tracking_code', $sgal_enqueue_order);
                break;
            case "manual":
                break;
            default:
                add_action('wp_head', 'caos_analytics_render_tracking_code', $sgal_enqueue_order);
                break;
        }
    }
}

add_action('init', 'caos_analytics_insert_tracking_code');
