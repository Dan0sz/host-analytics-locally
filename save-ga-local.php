<?php
/**
* Plugin Name: Host Analytics.js Locally
* Plugin URI: http://dev.daanvandenbergh.com/wordpress-plugins/host-analytics-js-local
* Description: A plugin that inserts the Analytics tracking code into the header, saves the analytics.js file locally and keeps it updated using wp_cron().
* Version: 1.36
* Author: Daan van den Bergh
* Author URI: http://dev.daanvandenbergh.com
* License: GPL2v2 or later
*/

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Create Menu Item
add_action('admin_menu', 'save_ga_locally_create_menu');

function save_ga_locally_create_menu() {
	add_options_page	(	'Host Google Analytics Locally Settings', 
							'Host GA Locally',	
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
}

// Create Settings Page
function save_ga_locally_settings_page() {
	if (!current_user_can('manage_options')) {
		wp_die( __("You're not cool enough to access this page."));
	}
	?>
	
    <div class="wrap">
        <h2>Host Google Analytics Locally</h2>
        <?php _e('Created by: ', 'save-ga-locally'); ?><a title="Buy me a beer!" href="http://dev.daanvandenbergh.com/donate/">Daan van den Bergh</a>.
        
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
				
        ?>
        
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Google Analytics Tracking ID</th>
                    <td><input type="text" name="sgal_tracking_id" value="<?php echo $sgal_tracking_id; ?>" /></td>
                </tr>
                <tr valign="top">
                	<th scope="row">Position of tracking code</th>
                    <td>
                	<?php
						$sgal_script_position = array("header", "footer");
										
						foreach ($sgal_script_position as $option) {
							echo "<input type='radio' name='sgal_script_position' value='" . $option . "' ";
							echo $sgal_checked = ($option == get_option('sgal_script_position')) ? $sgal_checked = ' checked="checked"' : $sgal_checked = '';
							echo " />";
							echo ucfirst($option);
							echo $sgal_script_default = ($option == "header") ? $sgal_script_default = " (default) " : $sgal_script_default = "";
							echo "<br>";
						}
					?>
                    </td>
                </tr>
                <tr valign="top">
                	<th scope="row">Use adjusted bounce rate?</th>
                    <td><input type="number" name="sgal_adjusted_bounce_rate" min="0" max="60" value="<?php echo $sgal_adjusted_bounce_rate; ?>" /></td>
                </tr>
                <tr valign="top">
                	<th scope="row">Change enqueue order? (Default = 0)</th>
                    <td><input type="number" name="sgal_enqueue_order" min="0" value="<?php echo $sgal_enqueue_order; ?>" /></td>
                </tr>
                <tr valign="top">
                	<th scope="row">Use Anomymize IP? (Required by law for some countries)</th>
                    <td><input type="checkbox" name="sgal_anonymize_ip" <?php echo $sgal_anon_checked = ($sgal_anonymize_ip == "on") ? 'checked = "checked"' : ""; ?> /></td>
            </table>
            
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

// Add Analytics script to header/footer (default is header)
function add_ga_header_script() {
	
		$sgal_tracking_id = esc_attr(get_option('sgal_tracking_id'));
		$sgal_adjusted_bounce_rate = esc_attr(get_option('sgal_adjusted_bounce_rate'));
		$sgal_anonymize_ip = esc_attr(get_option('sgal_anonymize_ip'));

		echo "<script>
				(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				})(window,document,'script','" . plugin_dir_url(__FILE__) . "cache/local-ga.js','ga');";

		echo "ga('create', '" . $sgal_tracking_id . "', 'auto');";
		
		echo $sgal_anonymize_ip_code = ($sgal_anonymize_ip == "on") ? "ga('set', 'anonymizeIp', true);" : "";
				
		echo "ga('send', 'pageview');";

		echo $sgal_abr_code = ($sgal_adjusted_bounce_rate) ? 'setTimeout("ga(' . "'send','event','adjusted bounce rate','" . $sgal_adjusted_bounce_rate . " seconds')" . '"' . "," . $sgal_adjusted_bounce_rate * 1000 . ");" : "";
		
		echo "</script>";
}

$sgal_script_position = esc_attr(get_option('sgal_script_position'));
$sgal_enqueue_order = (esc_attr(get_option('sgal_enqueue_order'))) ? esc_attr(get_option('sgal_enqueue_order')) : 0;

switch ($sgal_script_position) {
	case "header":
		add_action('wp_head', 'add_ga_header_script', $sgal_enqueue_order);
		break;
	case "footer":
		add_action('wp_footer', 'add_ga_header_script', $sgal_enqueue_order);
		break;
	default:
		add_action('wp_head', 'add_ga_header_script', $sgal_enqueue_order);
		break;
}

// Remove script from wp_cron upon plugin deactivation
register_deactivation_hook(__FILE__, 'deactivate_update_local_ga');

function deactivate_update_local_ga() {
	wp_clear_scheduled_hook('update_local_ga');	
}

?>