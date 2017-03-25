<?php
/**
 * Plugin Name: Complete Analytics Optimization Suite (CAOS)
 * Plugin URI: http://dev.daanvandenbergh.com/wordpress-plugins/host-analytics-js-local
 * Description: A plugin that allows you to completely optimize Google Analytics for your Wordpress Website: host analytics.js locally, keep it updated using wp_cron(), anonymize IP, disable tracking of admins, place tracking code in footer, and more!
 * Version: 1.44
 * Author: Daan van den Bergh
 * Author URI: http://dev.daanvandenbergh.com
 * License: GPL2v2 or later
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Create Menu Item
add_action('admin_menu', 'save_ga_locally_create_menu');

function save_ga_locally_create_menu() {
    add_options_page	(	'Complete Analytics Optimization Suite',
        'Optimize Analytics',
        'manage_options',
        'save-ga-locally',
        'save_ga_locally_settings_page'
    );

    add_action			(	'admin_init',
        'register_save_ga_locally_settings'
    );
}

// Register Settings
function register_save_ga_locally_settings() {
    register_setting	(	'save-ga-locally-basic-settings',
        'sgal_tracking_id'
    );
    register_setting	(	'save-ga-locally-basic-settings',
        'sgal_adjusted_bounce_rate'
    );
    register_setting	(	'save-ga-locally-basic-settings',
        'sgal_script_position'
    );
    register_setting	(	'save-ga-locally-basic-settings',
        'sgal_enqueue_order'
    );
    register_setting	(	'save-ga-locally-basic-settings',
        'sgal_anonymize_ip'
    );
    register_setting	(	'save-ga-locally-basic-settings',
        'sgal_track_admin'
    );
    register_setting	(	'save-ga-locally-basic-settings',
        'caos_remove_wp_cron'
    );
    register_setting	(	'save-ga-locally-basic-settings',
        'caos_disable_display_features'
    );
}

// Create Settings Page
function save_ga_locally_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die( __("You're not cool enough to access this page."));
    }
    ?>

    <div class="wrap">
        <h2><?php _e('CAOS: Complete Analytics Optimization Suite', 'save-ga-locally'); ?></h2>
        <?php _e('Developed by: ', 'save-ga-locally'); ?><p><a title="Buy me a beer!" href="http://dev.daanvandenbergh.com/donate/">Daan van den Bergh</a>.</p>

        <p><?php _e('Consider using'); ?> <a href="https://wordpress.org/plugins/cdn-enabler/">CDN Enabler</a> <?php _e('to host your Analytics-script (local-ga.js) from your CDN'); ?>.</p>

        <form method="post" action="options.php">
            <?php
            settings_fields		(	'save-ga-locally-basic-settings'
            );
            do_settings_sections(	'save-ga-locally-basic-settings'
            );

            $sgal_tracking_id = esc_attr(get_option('sgal_tracking_id'));
            $sgal_adjusted_bounce_rate = esc_attr(get_option('sgal_adjusted_bounce_rate'));
            $sgal_script_position = esc_attr(get_option('sgal_script_position'));
            $sgal_enqueue_order = esc_attr(get_option('sgal_enqueue_order'));
            $sgal_anonymize_ip = esc_attr(get_option('sgal_anonymize_ip'));
            $sgal_track_admin = esc_attr(get_option('sgal_track_admin'));
            $caos_remove_wp_cron = esc_attr(get_option('caos_remove_wp_cron'));
            $caos_disable_display_features = esc_attr(get_option('caos_disable_display_features'));
            ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Google Analytics Tracking ID', 'save-ga-locally'); ?></th>
                    <td><input type="text" name="sgal_tracking_id" value="<?php echo $sgal_tracking_id; ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Position of tracking code', 'save-ga-locally'); ?></th>
                    <td>
                        <?php
                        $sgal_script_position = array('header', 'footer');

                        foreach ($sgal_script_position as $option) {
                            echo "<input type='radio' name='sgal_script_position' value='" . $option . "' ";
                            echo $sgal_checked = ($option == get_option('sgal_script_position')) ? ' checked="checked"' : '';
                            echo " />";
                            echo ucfirst($option);
                            echo $sgal_script_default = ($option == 'header') ? _e(' (default)', 'save-ga-locally') : '';
                            echo "<br>";
                        }
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Use adjusted bounce rate?', 'save-ga-locally'); ?></th>
                    <td><input type="number" name="sgal_adjusted_bounce_rate" min="0" max="60" value="<?php echo $sgal_adjusted_bounce_rate; ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Change enqueue order? (Default = 0)', 'save-ga-locally'); ?></th>
                    <td><input type="number" name="sgal_enqueue_order" min="0" value="<?php echo $sgal_enqueue_order; ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Disable all <a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/display-features" target="_blank">display features functionality</a>?', 'save-ga-locally'); ?></th>
                    <td><input type="checkbox" name="caos_disable_display_features" <?php if ($caos_disable_display_features == "on") echo 'checked = "checked"'; ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Use <a href="https://support.google.com/analytics/answer/2763052?hl=en" target="_blank">Anomymize IP</a>? (Required by law for some countries)', 'save-ga-locally'); ?></th>
                    <td><input type="checkbox" name="sgal_anonymize_ip" <?php if ($sgal_anonymize_ip == "on") echo 'checked = "checked"'; ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Track logged in Administrators?', 'save-ga-locally'); ?></th>
                    <td><input type="checkbox" name="sgal_track_admin" <?php if($sgal_track_admin == "on") echo 'checked = "checked"'; ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Remove script from wp-cron?', 'save-ga-locally'); ?></th>
                    <td><input type="checkbox" name="caos_remove_wp_cron" <?php if($caos_remove_wp_cron == "on") echo 'checked = "checked"'; ?> /></td>
                </tr>
            </table>

            <?php do_action('caos_after_form_settings'); ?>

            <?php submit_button() ;?>

        </form>
    </div>
    <?php
} // End: save_ga_locally_settings_page()

// Register hook to schedule script in wp_cron()
register_activation_hook(__FILE__, 'activate_update_local_ga');

function activate_update_local_ga() {
    if	(!wp_next_scheduled('update_local_ga')) {
        wp_schedule_event(time(), 'daily', 'update_local_ga');
    }
}

// Load update script to schedule in wp_cron()
add_action('update_local_ga', 'update_local_ga_script');

function update_local_ga_script() {
    include('includes/update_local_ga.php');
}

// Remove script from wp_cron upon plugin deactivation
register_deactivation_hook(__FILE__, 'deactivate_update_local_ga');

function deactivate_update_local_ga() {
    if	(wp_next_scheduled('update_local_ga')) {
        wp_clear_scheduled_hook('update_local_ga');
    }
}

// Remove script from wp_cron if option is selected
$caos_remove_wp_cron = esc_attr(get_option('caos_remove_wp_cron'));

switch ($caos_remove_wp_cron) {
    case "on":
        if (wp_next_scheduled('update_local_ga')) {
            wp_clear_scheduled_hook('update_local_ga');
        }
        break;
    default:
        if (!wp_next_scheduled('update_local_ga')) {
            wp_schedule_event(time(), 'daily', 'update_local_ga');
        }
        break;
}

// Generate tracking code and add to header/footer (default is header)
function add_ga_header_script() {
    $sgal_track_admin = esc_attr(get_option('sgal_track_admin'));
    // If user is admin we don't want to render the tracking code, when option is disabled.
    if (current_user_can('manage_options') && (!$sgal_track_admin)) return;

    $sgal_tracking_id = esc_attr(get_option('sgal_tracking_id'));
    $sgal_adjusted_bounce_rate = esc_attr(get_option('sgal_adjusted_bounce_rate'));
    $sgal_anonymize_ip = esc_attr(get_option('sgal_anonymize_ip'));
    $caos_disable_display_features = esc_attr(get_option('caos_disable_display_features'));

    echo "<!-- This site is running CAOS: Complete Analytics Optimization Suite for Wordpress -->";

    echo "<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','" . plugin_dir_url(__FILE__) . "cache/local-ga.js','ga');";

    echo "ga('create', '" . $sgal_tracking_id . "', 'auto');";

    echo $caos_disable_display_features_code = ($caos_disable_display_features == "on") ? "ga('set', 'displayFeaturesTask', null);
" : "";

    echo $sgal_anonymize_ip_code = ($sgal_anonymize_ip == "on") ? "ga('set', 'anonymizeIp', true);" : "";

    echo "ga('send', 'pageview');";

    echo $sgal_abr_code = ($sgal_adjusted_bounce_rate) ? 'setTimeout("ga(' . "'send','event','adjusted bounce rate','" . $sgal_adjusted_bounce_rate . " seconds')" . '"' . "," . $sgal_adjusted_bounce_rate * 1000 . ");" : "";

    echo "</script>";
}

$sgal_script_position = esc_attr(get_option('sgal_script_position'));
$sgal_enqueue_order = (esc_attr(get_option('sgal_enqueue_order'))) ? esc_attr(get_option('sgal_enqueue_order')) : 0;

switch ($sgal_script_position) {
    case "footer":
        add_action('wp_footer', 'add_ga_header_script', $sgal_enqueue_order);
        break;
    default:
        add_action('wp_head', 'add_ga_header_script', $sgal_enqueue_order);
        break;
}
?>