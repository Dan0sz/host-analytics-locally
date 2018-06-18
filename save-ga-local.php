<?php
/**
 * Plugin Name: Complete Analytics Optimization Suite (CAOS) - GDPR Compliant!
 * Plugin URI: https://dev.daanvandenbergh.com/wordpress-plugins/optimize-analytics-wordpress/
 * Description: A plugin that allows you to completely optimize Google Analytics for your Wordpress Website: host analytics.js locally, keep it updated using wp_cron(), anonymize IP, disable tracking of admins, place tracking code in footer, and more!
 * Version: 1.56
 * Author: Daan van den Bergh
 * Author URI: https://dev.daanvandenbergh.com
 * License: GPL2v2 or later
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Create Menu Item
add_action('admin_menu', 'save_ga_locally_create_menu');

// Define Variables
define('CAOS_TRACKING_ID'         , esc_attr(get_option('sgal_tracking_id')));
define('CAOS_ENABLE_GDPR'         , esc_attr(get_option('caos_enable_gdpr')));
define('CAOS_COOKIE_NAME'         , esc_attr(get_option('sgal_cookie_notice_name')));
define('CAOS_COOKIE_EXPIRY'       , esc_attr(get_option('sgal_ga_cookie_expiry_days')));
define('CAOS_COOKIE_EXPIRY_DAYS'  , CAOS_COOKIE_EXPIRY ? CAOS_COOKIE_EXPIRY * 86400 : 0);
define('CAOS_ADVANCED_SETTINGS'   , esc_attr(get_option('caos_advanced_settings')));
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
        'caos_enable_gdpr'
    );
    register_setting('save-ga-locally-basic-settings',
        'sgal_cookie_notice_name'
    );
    register_setting('save-ga-locally-basic-settings',
        'sgal_ga_cookie_expiry_days'
    );
    register_setting('save-ga-locally-basic-settings',
                    'caos_advanced_settings'
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
        <h2><?php _e('CAOS: Complete Analytics Optimization Suite', 'save-ga-locally'); ?></h2>

        <p>
            <?php _e('Developed by: ', 'save-ga-locally'); ?>
            <a title="Buy me a beer!" href="http://dev.daanvandenbergh.com/donate/">Daan van den Bergh</a>.
        </p>

        <p>
            <?php _e('Consider using'); ?> <a href="https://wordpress.org/plugins/cdn-enabler/">CDN Enabler</a> <?php _e('to host your Analytics-script (local-ga.js) from your CDN'); ?>.
        </p>

        <form method="post" action="options.php">
            <?php
            settings_fields('save-ga-locally-basic-settings'
            );
            do_settings_sections('save-ga-locally-basic-settings'
            );
            ?>

            <table class="form-table">
                <tbody class="caos-basic-settings">
                    <tr valign="top">
                        <th scope="row"><?php _e('Google Analytics Tracking ID', 'save-ga-locally'); ?></th>
                        <td><input type="text" name="sgal_tracking_id" value="<?php echo CAOS_TRACKING_ID; ?>"/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Enable GDPR Compliance?', 'save-ga-locally'); ?></th>
                        <td><input type="checkbox" name="caos_enable_gdpr"
                                   onclick="toggleVisibility('.caos_gdpr_setting')" <?php echo CAOS_ENABLE_GDPR ? 'checked' : ''; ?> /></td>
                    </tr>
                    <tr class="caos_gdpr_setting" valign="top" <?php echo CAOS_ENABLE_GDPR ? '' : 'style="display: none;"'; ?>>
                        <th scope="row"><?php _e('Cookie name', 'save-ga-locally'); ?></th>
                        <td><input type="text" name="sgal_cookie_notice_name"
                                   value="<?php echo CAOS_COOKIE_NAME; ?>"/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Position of tracking code', 'save-ga-locally'); ?></th>
                        <td>
                            <?php
                            $sgal_script_position = array('header', 'footer');

                            foreach ($sgal_script_position as $option)
                            {
                                echo "<input type='radio' name='sgal_script_position' value='" . $option . "' ";
                                echo $option == get_option('sgal_script_position') ? ' checked="checked"' : '';
                                echo " />";
                                echo ucfirst($option);
                                echo $option == 'header' ? _e(' (default)', 'save-ga-locally') : '';
                                echo "<br>";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Advanced Settings', 'save-ga-locally'); ?></th>
                        <td><input type="checkbox" name="caos_advanced_settings"
                                   onclick="toggleVisibility('.caos_advanced_settings')" <?php echo CAOS_ADVANCED_SETTINGS ? 'checked' : ''; ?> /></td>
                    </tr>
                </tbody>
                <tbody class="caos_advanced_settings" <?php echo CAOS_ADVANCED_SETTINGS ? '' : 'style="display: none;"'; ?>>
                    <tr valign="top">
                        <th scope="row"><?php _e('Cookie expiry period (days)', 'save-ga-locally'); ?></th>
                        <td><input type="number" name="sgal_ga_cookie_expiry_days" min="0" max="365"
                                   value="<?php echo CAOS_COOKIE_EXPIRY; ?>"/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Use adjusted bounce rate?', 'save-ga-locally'); ?></th>
                        <td>
                            <input type="number" name="sgal_adjusted_bounce_rate" min="0" max="60"
                                   value="<?php echo CAOS_ADJUSTED_BOUNCE_RATE; ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Change enqueue order? (Default = 0)', 'save-ga-locally'); ?></th>
                        <td>
                            <input type="number" name="sgal_enqueue_order" min="0"
                                   value="<?php echo CAOS_ENQUEUE_ORDER; ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Disable all <a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/display-features" target="_blank">display features functionality</a>?', 'save-ga-locally'); ?></th>
                        <td>
                            <input type="checkbox"
                                   name="caos_disable_display_features" <?php if (CAOS_DISABLE_DISPLAY_FEAT == "on") echo 'checked = "checked"'; ?> />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Use <a href="https://support.google.com/analytics/answer/2763052?hl=en" target="_blank">Anonymize IP</a>? (Required by law for some countries)', 'save-ga-locally'); ?></th>
                        <td>
                            <input type="checkbox"
                                   name="sgal_anonymize_ip" <?php if (CAOS_ANONYMIZE_IP == "on") echo 'checked = "checked"'; ?> />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Track logged in Administrators?', 'save-ga-locally'); ?></th>
                        <td>
                            <input type="checkbox"
                                   name="sgal_track_admin" <?php if (CAOS_TRACK_ADMIN == "on") echo 'checked = "checked"'; ?> />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Remove script from wp-cron?', 'save-ga-locally'); ?></th>
                        <td>
                            <input type="checkbox"
                                   name="caos_remove_wp_cron" <?php if (CAOS_REMOVE_WP_CRON == "on") echo 'checked = "checked"'; ?> />
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php do_action('caos_after_form_settings'); ?>

            <?php submit_button(); ?>

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
        wp_schedule_event(time(), 'daily', 'update_local_ga');
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
            wp_schedule_event(time(), 'daily', 'update_local_ga');
        }
        break;
}

// Generate tracking code and add to header/footer (default is header)
function add_ga_header_script()
{
    // If user is admin we don't want to render the tracking code, when option is disabled.
    if (current_user_can('manage_options') && (!CAOS_TRACK_ADMIN)) return;

    echo "<!-- This site is running CAOS: Complete Analytics Optimization Suite for Wordpress -->\n";
    echo "<script>\n";

    if (CAOS_ENABLE_GDPR && CAOS_COOKIE_NAME)
    {
        echo "window.getCookie = function(name) {
        var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        if (match) return match[2]; }\n";
        echo "var cookie_exists = getCookie('" . CAOS_COOKIE_NAME . "');\n\n";
    }

    echo "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','" . plugin_dir_url(__FILE__) . "cache/local-ga.js','ga');\n";

    if (CAOS_ENABLE_GDPR && CAOS_COOKIE_NAME)
    {
        echo "if (cookie_exists) { 
        window['ga-disable-" . CAOS_TRACKING_ID . "'] = false; 
        } else { 
        window['ga-disable-" . CAOS_TRACKING_ID . "'] = true;
        }\n";
    }

    echo "ga('create', '" . CAOS_TRACKING_ID . "', 
    {
        'cookieName':   'caosLocalGa',
  	    'cookieDomain': '{$_SERVER['SERVER_NAME']}',
  	    'cookieExpires':'" . CAOS_COOKIE_EXPIRY_DAYS . "',
	});\n";

    echo CAOS_DISABLE_DISPLAY_FEAT == "on" ? "ga('set', 'displayFeaturesTask', null);\n" : "";
    echo CAOS_ANONYMIZE_IP == "on" ? "ga('set', 'anonymizeIp', true);\n" : "";
    echo "ga('send', 'pageview');\n";
    echo CAOS_ADJUSTED_BOUNCE_RATE ? 'setTimeout("ga(' . "'send','event','adjusted bounce rate','" .  CAOS_ADJUSTED_BOUNCE_RATE . " seconds')" . '"' . "," .  CAOS_ADJUSTED_BOUNCE_RATE * 1000 . ");\n" : "";
    echo "</script>\n";
}

$sgal_enqueue_order   = CAOS_ENQUEUE_ORDER ? CAOS_ENQUEUE_ORDER : 0;

switch (CAOS_SCRIPT_POSITION)
{
    case "footer":
        add_action('wp_footer', 'add_ga_header_script', $sgal_enqueue_order);
        break;
    default:
        add_action('wp_head', 'add_ga_header_script', $sgal_enqueue_order);
        break;
}
