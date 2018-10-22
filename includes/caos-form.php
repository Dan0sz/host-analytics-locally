<?php

?>
<div class="caos_left_column" style="float:left; width: 50%;">
    <h3><?php _e('Basic Settings'); ?></h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php _e( 'Google Analytics Tracking ID', 'host-analyticsjs-local' ); ?></th>
            <td>
                <input type="text" name="sgal_tracking_id" value="<?php echo CAOS_TRACKING_ID; ?>"/>
            </td>
        </tr>
        <tbody class="caos_basic_settings" <?php echo CAOS_MI_COMPATIBILITY == 'on' ? 'style="display: none;"' : ''; ?>>
        <tr valign="top">
            <th scope="row"><?php _e( 'Allow tracking...', 'host-analyticsjs-local' ); ?></th>
            <td>
				<?php
				$caos_allow_tracking_choice = array(
					''                 => 'Always (default)',
					'cookie_is_set'    => 'When cookie is set',
					'cookie_has_value' => 'When cookie has a value'
                );

				foreach ( $caos_allow_tracking_choice as $option => $label ): ?>
                    <label>
                        <input type="radio" class="caos_allow_tracking_<?php echo $option; ?>"
                               name="caos_allow_tracking" value="<?php echo $option; ?>"
							<?php echo $option == CAOS_ALLOW_TRACKING ? 'checked="checked"' : ''; ?>/>
						<?php echo _e( $label, 'host-analyticsjs-local'); ?>
                    </label>
                    <br/>
				<?php endforeach; ?>
                <p class="description">
                    <?php _e('Choose \'Always\' to use Google Analytics without a Cookie Notice. Follow ', 'host-analyticsjs-local'); ?>
                    <a href="https://dev.daanvandenbergh.com/wordpress/analytics-gdpr-anonymize-ip-cookie-notice/" target="_blank">
                        <?php _e('this tutorial', 'host-analyticsjs-local'); ?></a> <?php _e('to comply with GDPR Laws.', 'host-analyticsjs-local'); ?><br />
                    <?php _e('Choose \'When cookie is set\' or \'When cookie has a value\' to make CAOS compatible with your Cookie Notice plugin.', 'host-analyticsjs-local'); ?>
                    <a href="https://dev.daanvandenbergh.com/wordpress/gdpr-compliance-google-analytics/" target="_blank">
                        <?php _e('Read more', 'host-analyticsjs-local'); ?></a>.
                </p>
            </td>
        </tr>
        <tr class="caos_gdpr_setting caos_allow_tracking_name"
            valign="top" <?php echo CAOS_ALLOW_TRACKING ? '' : 'style="display: none;"'; ?>>
            <th scope="row"><?php _e( 'Cookie name', 'host-analyticsjs-local'); ?></th>
            <td>
                <input type="text" name="sgal_cookie_notice_name"
                       value="<?php echo CAOS_COOKIE_NAME; ?>"/>
                <p class="description">
                    <?php _e('The cookie name set by your Cookie Notice plugin when user accepts.', 'host-analyticsjs-local'); ?>
                </p>
            </td>
        </tr>
        <tr class="caos_gdpr_setting caos_allow_tracking_name caos_allow_tracking_value"
            valign="top" <?php echo CAOS_ALLOW_TRACKING == 'cookie_has_value' ? '' : 'style="display: none;"'; ?>>
            <th scope="row"><?php _e( 'Cookie value', 'host-analyticsjs-local' ); ?></th>
            <td>
                <input type="text" name="caos_cookie_value"
                       value="<?php echo CAOS_COOKIE_VALUE; ?>"/>
                <p class="description">
                    <?php _e('The value of the above specified cookie set by your Cookie Notice when user accepts.', 'host-analyticsjs-local'); ?>
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e( 'Position of tracking-code', 'host-analyticsjs-local' ); ?></th>
            <td>
				<?php
				$sgal_script_position = array(
					'header' => 'Header (default)',
					'footer' => 'Footer',
                    'manual' => 'Add manually'
                );

				foreach ( $sgal_script_position as $option => $label ): ?>
                    <label>
                        <input class="caos_script_position_<?php echo $option; ?>" type="radio" name="sgal_script_position"
                               value="<?php echo $option; ?>" <?php echo $option == CAOS_SCRIPT_POSITION ? 'checked="checked"' : ''; ?> />
                        <?php echo _e($label, 'host-analyticsjs-local'); ?>
                    </label>
                    <br/>
				<?php endforeach; ?>
                <p class="description">
                    <?php _e('Load the Analytics tracking-snippet in the header, footer or manually?', 'host-analyticsjs-local'); ?>
                    <?php _e('If e.g. your theme doesn\'t load the wp_head conventionally, choose \'Add manually\'.', 'host-analyticsjs-local'); ?>
                </p>
            </td>
        </tr>
        <tr class="caos_add_manually" valign="top" <?php echo CAOS_SCRIPT_POSITION == 'manual' ? '' : 'style="display: none;"'; ?>>
            <th scope="row"><?php _e('Tracking-code', 'host-analyticsjs-local'); ?></th>
            <td>
                <label>
                    <textarea style="display: block; width: 100%; height: 250px;"><?php echo caos_analytics_render_tracking_code(); ?></textarea>
                </label>
                <p class="description">
                    <?php _e('Copy this to the theme or plugin which should handle displaying the snippet.', 'host-analyticsjs-local'); ?>
                </p>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="caos_column_right"
     style="float:left; width: 50%;">
    <h3><?php _e('Advanced Settings', 'host-analyticsjs-local'); ?></h3>
    <table class="form-table">
        <tbody class="caos_mi_compatibility">
            <tr valign="top">
                <th scope="row"><?php _e('Enable compatibility with Monster Insights?', 'host-analyticsjs-local'); ?></th>
                <td>
                    <input class="caos_mi_compatibility_checkbox" type="checkbox" name="caos_mi_compatibility" <?php echo CAOS_MI_COMPATIBILITY == 'on' ? 'checked = "checked"' : ''; ?> />
                    <p class="description">
	                    <?php _e('The best choice, if you want to use enhanced Analytics features, such as event tracking in e.g. WooCommerce.', 'host-analyticsjs-local'); ?>
	                    <?php _e('Allow Monster Insights\' plugin to use the locally hosted analytics.js-file generated and updated by CAOS. Enabling this option means that you\'ll manage Google Analytics entirely within Google Analytics by Monster Insights.', 'host-analyticsjs-local'); ?>
                        <a href="https://dev.daanvandenbergh.com/wordpress/leverage-browser-caching-host-analytics-local-monster-insights/" target="_blank"><?php _e('Read more', 'host-analyticsjs-local'); ?></a>.
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Save analytics.js to...', 'host-analyticsjs-local'); ?></th>
                <td>
                    <input class="caos_analytics_cache_dir" type="text" name="caos_analytics_cache_dir" placeholder="e.g. /cache/caos-analytics/" value="<?php echo CAOS_ANALYTICS_CACHE_DIR; ?>" />
                    <p class="description">
                        <?php _e("Change the path where analytics.js is cached inside WordPress' content directory (usually <code>wp-content</code>). Defaults to <code>/cache/caos-analytics/</code>.", 'host-analyticsjs-local'); ?>
                    </p>
                </td>
            </tr>
        </tbody>
        <tbody class="caos_advanced_settings" <?php echo CAOS_MI_COMPATIBILITY == 'on' ? 'style="display: none;"' : ''; ?>>
        <tr valign="top">
            <th scope="row"><?php _e( 'Cookie expiry period (days)', 'host-analyticsjs-local' ); ?></th>
            <td>
                <input type="number" name="sgal_ga_cookie_expiry_days" min="0" max="365"
                       value="<?php echo CAOS_COOKIE_EXPIRY; ?>" />
                <p class="description">
                    <?php _e('The number of days when the cookie will automatically expire.', 'host-analyticsjs-local'); ?>
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e( 'Use adjusted bounce rate?', 'host-analyticsjs-local' ); ?></th>
            <td>
                <input type="number" name="sgal_adjusted_bounce_rate" min="0" max="60"
                       value="<?php echo CAOS_ADJUSTED_BOUNCE_RATE; ?>" />
                <p class="description">
                    <a href="https://moz.com/blog/adjusted-bounce-rate" target="_blank"><?php _e('More information about adjusted bounce rate', 'host-analyticsjs-local'); ?></a>.
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e( 'Change enqueue order? (Default = 0)', 'host-analyticsjs-local' ); ?></th>
            <td>
                <input type="number" name="sgal_enqueue_order" min="0"
                       value="<?php echo CAOS_ENQUEUE_ORDER; ?>" />
                <p class="description">
                    <?php _e('Leave this alone if you don\'t know what you\'re doing.', 'host-analyticsjs-local'); ?>
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e( 'Disable all display features functionality?', 'host-analyticsjs-local' ); ?></th>
            <td>
                <input type="checkbox" name="caos_disable_display_features"
                        <?php echo CAOS_DISABLE_DISPLAY_FEAT == "on" ? 'checked = "checked"' : ''; ?> />
                <p class="description">
                    <a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/display-features" target="_blank"><?php _e('More information about display features', 'host-analyticsjs-local'); ?></a>.
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e( 'Use Anonymize IP?', 'host-analyticsjs-local' ); ?></th>
            <td>
                <input type="checkbox" name="sgal_anonymize_ip"
                    <?php echo CAOS_ANONYMIZE_IP == "on" ? 'checked = "checked"' : ''; ?> />
                <p class="description">
                    <?php _e('Required by law in some countries.'); ?>
                    <a href="https://support.google.com/analytics/answer/2763052?hl=en" target="_blank"><?php _e('More information about IP Anonymization'); ?></a>.
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e( 'Track logged in Administrators?', 'host-analyticsjs-local' ); ?></th>
            <td>
                <input type="checkbox" name="sgal_track_admin"
                    <?php echo CAOS_TRACK_ADMIN == "on" ? 'checked = "checked"' : ''; ?> />
                <p class="description">
                    <strong><?php _e('Warning!'); ?></strong> <?php _e('This will track all your traffic as a logged in user.'); ?>
                </p>
            </td>
        </tr>
        </tbody>
        <tbody class="caos_cron_settings">
        <tr valign="top">
            <th scope="row"><?php _e( 'Remove script from wp-cron?', 'host-analyticsjs-local' ); ?></th>
            <td>
                <input type="checkbox" name="caos_remove_wp_cron"
                    <?php echo CAOS_REMOVE_WP_CRON == "on" ? 'checked = "checked"' : ''; ?> />
                <p class="description">
	                <?php _e('If your local-ga.js isn\'t updated automatically or you have WP-Cron disabled, check this option and add the update-analytics.php-script to your crontab manually.', 'host-analyticsjs-local'); ?>
                </p>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script>
    jQuery('.caos_allow_tracking_').click(function () {
        jQuery('.caos_gdpr_setting').hide();
    });
    jQuery('.caos_allow_tracking_cookie_is_set').click(function () {
        jQuery('.caos_allow_tracking_name').show();
        jQuery('.caos_allow_tracking_value').hide();
    });
    jQuery('.caos_allow_tracking_cookie_has_value').click(function () {
        jQuery('.caos_allow_tracking_name, .caos_allow_tracking_value').show();
    });
    jQuery('.caos_script_position_manual').click(function () {
        jQuery('.caos_add_manually').show();
    });
    jQuery('.caos_script_position_header, .caos_script_position_footer').click(function() {
        jQuery('.caos_add_manually').hide();
    });
    jQuery('.caos_mi_compatibility_checkbox').click(function() {
        jQuery('.caos_advanced_settings, .caos_basic_settings').toggle();
    });
</script>
