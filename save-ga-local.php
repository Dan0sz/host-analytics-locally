<?php
/**
 * Plugin Name: CAOS for Analytics | compatible with MonsterInsights & WooCommerce!
 * Plugin URI: https://dev.daanvandenbergh.com/wordpress-plugins/optimize-analytics-wordpress/
 * Description: A plugin that allows you to completely optimize Google Analytics for your Wordpress Website: host analytics.js locally, keep it updated using wp_cron(), anonymize IP, disable tracking of admins, place tracking code in footer, and more!
 * Version: 1.81
 * Author: Daan van den Bergh
 * Author URI: https://dev.daanvandenbergh.com
 * License: GPL2v2 or later
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Create Menu Item
add_action('admin_menu', 'save_ga_locally_create_menu');

// Define Variables
define('CAOS_PLUGIN_PATH'         , plugin_dir_path(__FILE__));
define('CAOS_TRACKING_ID'         , esc_attr(get_option('sgal_tracking_id')));
define('CAOS_ALLOW_TRACKING'      , esc_attr(get_option('caos_allow_tracking')));
define('CAOS_COOKIE_NAME'         , esc_attr(get_option('sgal_cookie_notice_name')));
define('CAOS_COOKIE_VALUE'        , esc_attr(get_option('caos_cookie_value')));
define('CAOS_MI_COMPATIBILITY'    , esc_attr(get_option('caos_mi_compatibility')));
define('CAOS_COOKIE_EXPIRY'       , esc_attr(get_option('sgal_ga_cookie_expiry_days')));
define('CAOS_COOKIE_EXPIRY_DAYS'  , CAOS_COOKIE_EXPIRY ? CAOS_COOKIE_EXPIRY * 86400 : 0);
define('CAOS_ADJUSTED_BOUNCE_RATE', esc_attr(get_option('sgal_adjusted_bounce_rate')));
define('CAOS_ENQUEUE_ORDER'       , esc_attr(get_option('sgal_enqueue_order')));
define('CAOS_ANONYMIZE_IP'        , esc_attr(get_option('sgal_anonymize_ip')));
define('CAOS_TRACK_ADMIN'         , esc_attr(get_option('sgal_track_admin')));
define('CAOS_REMOVE_WP_CRON'      , esc_attr(get_option('caos_remove_wp_cron')));
define('CAOS_DISABLE_DISPLAY_FEAT', esc_attr(get_option('caos_disable_display_features')));
define('CAOS_SCRIPT_POSITION'     , esc_attr(get_option('sgal_script_position')));

function save_ga_locally_create_menu()
{
    add_options_page('Complete Analytics Optimization Suite',
        'Optimize Analytics',
        'manage_options',
        'save-ga-locally',
        'save_ga_locally_settings_page'
    );

    add_action('admin_init',
        'register_save_ga_locally_settings'
    );
}

// Register Settings
function register_save_ga_locally_settings()
{
    register_setting('save-ga-locally-basic-settings',
        'sgal_tracking_id'
    );
    register_setting('save-ga-locally-basic-settings',
        'caos_allow_tracking'
    );
    register_setting('save-ga-locally-basic-settings',
        'caos_allow_tracking'
    );
    register_setting('save-ga-locally-basic-settings',
        'sgal_cookie_notice_name'
    );
    register_setting('save-ga-locally-basic-settings',
        'caos_cookie_value'
    );
    register_setting('save-ga-locally-basic-settings',
        'caos_mi_compatibility'
    );
    register_setting('save-ga-locally-basic-settings',
        'sgal_ga_cookie_expiry_days'
    );
    register_setting('save-ga-locally-basic-settings',
        'sgal_adjusted_bounce_rate'
    );
    register_setting('save-ga-locally-basic-settings',
        'sgal_script_position'
    );
    register_setting('save-ga-locally-basic-settings',
        'sgal_enqueue_order'
    );
    register_setting('save-ga-locally-basic-settings',
        'sgal_anonymize_ip'
    );
    register_setting('save-ga-locally-basic-settings',
        'sgal_track_admin'
    );
    register_setting('save-ga-locally-basic-settings',
        'caos_remove_wp_cron'
    );
    register_setting('save-ga-locally-basic-settings',
        'caos_disable_display_features'
    );
}



// Create Settings Page
function save_ga_locally_settings_page()
{
    if (!current_user_can('manage_options'))
    {
        wp_die(__("You're not cool enough to access this page."));
    }
    ?>

    <div class="wrap">
        <h1><?php _e('CAOS for Analytics', 'save-ga-locally'); ?></h1>

        <p>
            <?php _e('Developed by: ', 'save-ga-locally'); ?>
            <a title="Buy me a beer!" href="http://dev.daanvandenbergh.com/donate/">Daan van den Bergh</a>.
        </p>

        <p>
            <?php _e('Consider using'); ?> <a href="https://wordpress.org/plugins/cdn-enabler/">CDN Enabler</a> <?php _e('to host your Analytics-script (local-ga.js) from your CDN'); ?>.
        </p>

        <?php require_once(CAOS_PLUGIN_PATH . 'includes/welcome-panel.php'); ?>

        <form method="post" action="options.php">
            <?php
            settings_fields('save-ga-locally-basic-settings'
            );
            do_settings_sections('save-ga-locally-basic-settings'
            );
            ?>

            <?php require_once(CAOS_PLUGIN_PATH . 'includes/caos-form.php'); ?>

            <?php do_action('caos_after_form_settings'); ?>

            <div style="clear: both; display: block;">
	            <?php submit_button(); ?>
            </div>
        </form>
    </div>
    <script>
        function toggleVisibility(id)
        {
            jQuery(id).toggle(this.checked);
        }
    </script>
    <?php
} // End: save_ga_locally_settings_page()

// Register hook to schedule script in wp_cron()
register_activation_hook(__FILE__, 'activate_update_local_ga');

function activate_update_local_ga()
{
    if (!wp_next_scheduled('update_local_ga'))
    {
        wp_schedule_event(time(), 'hourly', 'update_local_ga');
    }
}

// Load update script to schedule in wp_cron()
add_action('update_local_ga', 'update_local_ga_script');

function update_local_ga_script()
{
    include('includes/update_local_ga.php');
}

// Remove script from wp_cron upon plugin deactivation
register_deactivation_hook(__FILE__, 'deactivate_update_local_ga');

function deactivate_update_local_ga()
{
    if (wp_next_scheduled('update_local_ga'))
    {
        wp_clear_scheduled_hook('update_local_ga');
    }
}

switch (CAOS_REMOVE_WP_CRON)
{
    case "on":
        if (wp_next_scheduled('update_local_ga'))
        {
            wp_clear_scheduled_hook('update_local_ga');
        }
        break;
    default:
        if (!wp_next_scheduled('update_local_ga'))
        {
            wp_schedule_event(time(), 'hourly', 'update_local_ga');
        }
        break;
}

// Generate tracking code and add to header/footer (default is header)
function add_ga_header_script()
{
    // If user is admin we don't want to render the tracking code, when option is disabled.
    if (current_user_can('manage_options') && (!CAOS_TRACK_ADMIN)) return;

    if (!CAOS_TRACKING_ID) return; ?>

    <!-- This site is running CAOS: Complete Analytics Optimization Suite for Wordpress -->
    <script>
    <?php
    if (CAOS_COOKIE_NAME && CAOS_COOKIE_VALUE): ?>
    function getCookieValue(name) {
            cookies = document.cookie;
            cookiesArray = cookies.split('; ');
            cookieValue = null;

            cookiesArray.forEach(function(cookie) {
                cookieArray = cookie.split('=');
                if (cookieArray[0] !== name) {
                    return;
                }
                cookieValue = cookieArray[1];
            });
            return cookieValue;
        }
        cookieValue = getCookieValue('<?php echo CAOS_COOKIE_NAME; ?>');
    <?php endif; ?>

        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','<?php echo plugin_dir_url(__FILE__) . 'cache/local-ga.js'; ?>','ga');
    <?php if (CAOS_ALLOW_TRACKING == 'cookie_is_set' && CAOS_COOKIE_NAME): ?>
        if (document.cookie.indexOf('<?php echo CAOS_COOKIE_NAME; ?>=')) {
            window[ 'ga-disable-<?php echo CAOS_TRACKING_ID; ?>' ] = false;
        } else {
            window[ 'ga-disable-<?php echo CAOS_TRACKING_ID; ?>' ] = true;
        }
    <?php elseif (CAOS_ALLOW_TRACKING == 'cookie_has_value' && CAOS_COOKIE_NAME && CAOS_COOKIE_VALUE): ?>
        if (cookieValue === '<?php echo CAOS_COOKIE_VALUE; ?>') {
            window[ 'ga-disable-<?php echo CAOS_TRACKING_ID; ?>' ] = false;
        } else {
            window[ 'ga-disable-<?php echo CAOS_TRACKING_ID; ?>' ] = true;
        }
    <?php else: ?>
        window[ 'ga-disable-<?php echo CAOS_TRACKING_ID; ?>' ] = false;
    <?php endif; ?>
    ga('create', '<?php echo CAOS_TRACKING_ID; ?>',
        {
            'cookieName':   'caosLocalGa',
            'cookieDomain': '<?php echo $_SERVER['SERVER_NAME']; ?>',
            'cookieExpires':'<?php echo CAOS_COOKIE_EXPIRY_DAYS; ?>',
        });
    <?php if (CAOS_DISABLE_DISPLAY_FEAT == 'on'): ?>
    ga('set', 'displayFeaturesTask', null);
    <?php endif; ?>
    <?php if (CAOS_ANONYMIZE_IP == 'on'): ?>
    ga('set', 'anonymizeIp', true);
    <?php endif; ?>
    ga('send', 'pageview');
    <?php if (CAOS_ADJUSTED_BOUNCE_RATE): ?>
    setTimeout("ga('send', 'event', 'adjusted bounce rate', '<?php echo CAOS_ADJUSTED_BOUNCE_RATE . " seconds"; ?>')", <?php echo CAOS_ADJUSTED_BOUNCE_RATE * 1000; ?>);
    <?php endif; ?>
</script>
<?php
}

function caos_host_mi_locally($url) {
    $url = plugin_dir_url(__FILE__) . 'cache/local-ga.js';

    return $url;
}

$sgal_enqueue_order   = CAOS_ENQUEUE_ORDER ? CAOS_ENQUEUE_ORDER : 0;

if(CAOS_MI_COMPATIBILITY == 'on') {
    add_filter('monsterinsights_frontend_output_analytics_src', 'caos_host_mi_locally', 1000);
} else {
	switch (CAOS_SCRIPT_POSITION)
	{
		case "footer":
			add_action('wp_footer', 'add_ga_header_script', $sgal_enqueue_order);
			break;
		case "manual":
			break;
		default:
			add_action('wp_head', 'add_ga_header_script', $sgal_enqueue_order);
			break;
	}
}
