<?php
/**
* Plugin Name: Host Analytics.js Locally
* Plugin URI: http://dev.daanvandenbergh.com/wordpress-plugins/host-analytics-js-local
* Description: A plugin that inserts the Analytics tracking code into the header, saves the analytics.js file locally and keeps it updated using wp_cron().
* Version: 1.2
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

        ?>
        
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Google Analytics Tracking ID</th>
                    <td><input type="text" name="sgal_tracking_id" value="<?php echo $sgal_tracking_id; ?>" /></td>
                </tr>
            </table>
            
            <?php submit_button() ;?>
            
        </form>	
	</div>
<?php
}

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

// Add Analytics script to header
function add_ga_header_script() {
	
		$sgal_tracking_id = esc_attr(get_option('sgal_tracking_id'));

		echo "<script>
				  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				  })(window,document,'script','" . plugin_dir_url(__FILE__) . "cache/local-ga.js','ga');
				
				  ga('create', '" . $sgal_tracking_id . "', 'auto');
				  ga('send', 'pageview');
			</script>";
}
add_action('wp_head', 'add_ga_header_script');

// Remove script from scheduler upon plugin deactivation
register_deactivation_hook(__FILE__, 'deactivate_update_local_ga');

function deactivate_update_local_ga() {
	wp_clear_scheduled_hook('update_local_ga');	
}

?>