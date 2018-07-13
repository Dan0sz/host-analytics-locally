<?php

?>
<table class="form-table">
    <tbody class="caos-basic-settings">
    <tr valign="top">
        <th scope="row"><?php _e( 'Google Analytics Tracking ID', 'save-ga-locally' ); ?></th>
        <td>
            <input type="text"
                   name="sgal_tracking_id"
                   value="<?php echo CAOS_TRACKING_ID; ?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Allow tracking...', 'save-ga-locally'); ?></th>
        <td>
            <?php
            $caos_allow_tracking_choice = [
                    ''                 => 'Always (default)',
                    'cookie_is_set'    => 'When cookie is set',
                    'cookie_has_value' => 'When cookie has a value'
            ];

            foreach ($caos_allow_tracking_choice as $option => $label): ?>
                <input type="radio" class="caos_allow_tracking_<?php echo $option; ?>" name="caos_allow_tracking" value="<?php echo $option; ?>"
                       <?php echo $option == CAOS_ALLOW_TRACKING ? 'checked="checked"' : ''; ?>/>
                <label><?php echo _e($label); ?></label><br/>
            <?php endforeach; ?>
        </td>
    </tr>
    <tr class="caos_gdpr_setting caos_allow_tracking_name"
        valign="top" <?php echo CAOS_ALLOW_TRACKING ? '' : 'style="display: none;"'; ?>>
        <th scope="row"><?php _e( 'Cookie name', 'save-ga-locally' ); ?></th>
        <td>
            <input type="text"
                   name="sgal_cookie_notice_name"
                   value="<?php echo CAOS_COOKIE_NAME; ?>"/>
        </td>
    </tr>
    <tr class="caos_gdpr_setting caos_allow_tracking_name caos_allow_tracking_value"
        valign="top" <?php echo CAOS_ALLOW_TRACKING == 'cookie_has_value' ? '': 'style="display: none;"'; ?>>
        <th scope="row"><?php _e('Cookie value', 'save-ga-locally' ); ?></th>
        <td>
            <input type="text"
                   name="caos_cookie_value"
                   value="<?php echo CAOS_COOKIE_VALUE; ?>" />
        </td>
    </tr>
    <script>
        jQuery('.caos_allow_tracking_').click(function() {
            jQuery('.caos_gdpr_setting').hide();
        });
        jQuery('.caos_allow_tracking_cookie_is_set').click(function() {
            jQuery('.caos_allow_tracking_name').show();
            jQuery('.caos_allow_tracking_value').hide();
        });
        jQuery('.caos_allow_tracking_cookie_has_value').click(function() {

            jQuery('.caos_allow_tracking_name, .caos_allow_tracking_value').show();
        });
    </script>
    <tr valign="top">
        <th scope="row"><?php _e( 'Position of tracking code', 'save-ga-locally' ); ?></th>
        <td>
			<?php
			$sgal_script_position = [
				'header' => 'Header (default)',
				'footer' => 'Footer'
            ];

			foreach ($sgal_script_position as $option => $label): ?>
                <input type="radio" name="sgal_script_position" value="<?php echo $option; ?>"
                       <?php echo $option == CAOS_SCRIPT_POSITION ? 'checked="checked"' : ''; ?> />
                <label><?php echo _e($label); ?></label><br />
			<?php endforeach; ?>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e( 'Advanced Settings', 'save-ga-locally' ); ?></th>
        <td>
            <input type="checkbox"
                   name="caos_advanced_settings"
                   onclick="toggleVisibility('.caos_advanced_settings')" <?php echo CAOS_ADVANCED_SETTINGS ? 'checked' : ''; ?> />
        </td>
    </tr>
    </tbody>
    <tbody class="caos_advanced_settings" <?php echo CAOS_ADVANCED_SETTINGS ? '' : 'style="display: none;"'; ?>>
    <tr valign="top">
        <th scope="row"><?php _e( 'Cookie expiry period (days)', 'save-ga-locally' ); ?></th>
        <td>
            <input type="number"
                   name="sgal_ga_cookie_expiry_days"
                   min="0"
                   max="365"
                   value="<?php echo CAOS_COOKIE_EXPIRY; ?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e( 'Use adjusted bounce rate?', 'save-ga-locally' ); ?></th>
        <td>
            <input type="number"
                   name="sgal_adjusted_bounce_rate"
                   min="0"
                   max="60"
                   value="<?php echo CAOS_ADJUSTED_BOUNCE_RATE; ?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e( 'Change enqueue order? (Default = 0)', 'save-ga-locally' ); ?></th>
        <td>
            <input type="number"
                   name="sgal_enqueue_order"
                   min="0"
                   value="<?php echo CAOS_ENQUEUE_ORDER; ?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e( 'Disable all <a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/display-features" target="_blank">display features functionality</a>?', 'save-ga-locally' ); ?></th>
        <td>
            <input type="checkbox"
                   name="caos_disable_display_features" <?php if ( CAOS_DISABLE_DISPLAY_FEAT == "on" ) {
				echo 'checked = "checked"';
			} ?> />
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e( 'Use <a href="https://support.google.com/analytics/answer/2763052?hl=en" target="_blank">Anonymize IP</a>? (Required by law for some countries)', 'save-ga-locally' ); ?></th>
        <td>
            <input type="checkbox"
                   name="sgal_anonymize_ip" <?php if ( CAOS_ANONYMIZE_IP == "on" ) {
				echo 'checked = "checked"';
			} ?> />
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e( 'Track logged in Administrators?', 'save-ga-locally' ); ?></th>
        <td>
            <input type="checkbox"
                   name="sgal_track_admin" <?php if ( CAOS_TRACK_ADMIN == "on" ) {
				echo 'checked = "checked"';
			} ?> />
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e( 'Remove script from wp-cron?', 'save-ga-locally' ); ?></th>
        <td>
            <input type="checkbox"
                   name="caos_remove_wp_cron" <?php if ( CAOS_REMOVE_WP_CRON == "on" ) {
				echo 'checked = "checked"';
			} ?> />
        </td>
    </tr>
    </tbody>
</table>
