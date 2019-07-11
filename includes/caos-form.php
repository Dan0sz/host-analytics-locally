<?php
/**
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 */

$fileStatus = caos_analytics_cron_status();

if ($fileStatus): ?>
    <div class="updated settings-success notice">
        <p>
            <strong><?php _e('Your cron is running healthy.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></strong>
        </p>
        <p>
            <em><?= CAOS_ANALYTICS_JS_FILE; ?></em> <?php _e('last updated at', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>: <?= caos_analytics_file_last_updated(); ?>
        </p>
        <p><?php _e('Next update scheduled at', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>: <?= caos_analytics_cron_next_scheduled(); ?></p>
    </div>
<?php else: ?>
    <div class="notice notice-error">
        <p>
            <strong><?php _e(sprintf('%s hasn\'t been updated for more than two days. Is your cron running?', CAOS_ANALYTICS_JS_FILE), CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></strong>
        </p>
    </div>
<?php endif; ?>

<div class="caos_left_column" style="float:left; width: 50%;">
    <h3><?php _e('Basic Settings'); ?></h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php _e('Google Analytics Tracking ID', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input type="text" name="sgal_tracking_id" value="<?= CAOS_ANALYTICS_TRACKING_ID; ?>"/>
            </td>
        </tr>
        <tbody class="caos_basic_settings" <?= CAOS_ANALYTICS_MI_COMPATIBILITY == 'on' ? 'style="display: none;"' : ''; ?>>
        <tr valign="top">
            <th scope="row"><?php _e('Allow tracking...', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
				<?php
				$caos_allow_tracking_choice = array(
					''                 => 'Always (default)',
					'cookie_is_set'    => 'When cookie is set',
					'cookie_has_value' => 'When cookie has a value'
				);

				foreach ($caos_allow_tracking_choice as $option => $label): ?>
                    <label>
                        <input type="radio" class="caos_allow_tracking_<?= $option; ?>"
                               name="caos_allow_tracking" value="<?= $option; ?>"
							<?= $option == CAOS_ANALYTICS_ALLOW_TRACKING ? 'checked="checked"' : ''; ?>/>
						<?= _e($label, CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                    </label>
                    <br/>
				<?php endforeach; ?>
                <p class="description">
					<?php _e('Choose \'Always\' to use Google Analytics without a Cookie Notice. Follow ', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                    <a href="<?= CAOS_ANALYTICS_SITE_URL; ?>/wordpress/analytics-gdpr-anonymize-ip-cookie-notice/" target="_blank">
						<?php _e('this tutorial', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a> <?php _e('to comply with GDPR Laws.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                    <br/>
					<?php _e('Choose \'When cookie is set\' or \'When cookie has a value\' to make CAOS compatible with your Cookie Notice plugin.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                    <a href="<?= CAOS_ANALYTICS_SITE_URL; ?>/wordpress/gdpr-compliance-google-analytics/" target="_blank">
						<?php _e('Read more', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>.
                </p>
            </td>
        </tr>
        <tr class="caos_gdpr_setting caos_allow_tracking_name"
            valign="top" <?= CAOS_ANALYTICS_ALLOW_TRACKING ? '' : 'style="display: none;"'; ?>>
            <th scope="row"><?php _e('Cookie name', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input type="text" name="sgal_cookie_notice_name"
                       value="<?= CAOS_ANALYTICS_COOKIE_NAME; ?>"/>
                <p class="description">
					<?php _e('The cookie name set by your Cookie Notice plugin when user accepts.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
            </td>
        </tr>
        <tr class="caos_gdpr_setting caos_allow_tracking_name caos_allow_tracking_value"
            valign="top" <?= CAOS_ANALYTICS_ALLOW_TRACKING == 'cookie_has_value' ? '' : 'style="display: none;"'; ?>>
            <th scope="row"><?php _e('Cookie value', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input type="text" name="caos_cookie_value"
                       value="<?= CAOS_ANALYTICS_COOKIE_VALUE; ?>"/>
                <p class="description">
					<?php _e('The value of the above specified cookie set by your Cookie Notice when user accepts.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Position of tracking-code', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
				<?php
				$sgal_script_position = array(
					'header' => 'Header (default)',
					'footer' => 'Footer',
					'manual' => 'Add manually'
				);

				foreach ($sgal_script_position as $option => $label): ?>
                    <label>
                        <input class="caos_script_position_<?= $option; ?>" type="radio" name="sgal_script_position"
                               value="<?= $option; ?>" <?= $option == CAOS_ANALYTICS_SCRIPT_POSITION ? 'checked="checked"' : ''; ?> />
						<?= _e($label, CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                    </label>
                    <br/>
				<?php endforeach; ?>
                <p class="description">
					<?php _e('Load the Analytics tracking-snippet in the header, footer or manually?', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
					<?php _e('If e.g. your theme doesn\'t load the wp_head conventionally, choose \'Add manually\'.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
            </td>
        </tr>
        <tr class="caos_add_manually" valign="top" <?= CAOS_ANALYTICS_SCRIPT_POSITION == 'manual' ? '' : 'style="display: none;"'; ?>>
            <th scope="row"><?php _e('Tracking-code', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <label>
                    <textarea style="display: block; width: 100%; height: 250px;"><?= caos_analytics_render_tracking_code(); ?></textarea>
                </label>
                <p class="description">
					<?php _e('Copy this to the theme or plugin which should handle displaying the snippet.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="caos_column_right"
     style="float:left; width: 50%;">
    <h3><?php _e('Advanced Settings', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></h3>
    <table class="form-table">
        <tbody class="caos_mi_compatibility">
        <tr valign="top">
            <th scope="row"><?php _e('Enable compatibility with Monster Insights?', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input class="caos_mi_compatibility_checkbox" type="checkbox" name="caos_mi_compatibility" <?= CAOS_ANALYTICS_MI_COMPATIBILITY == 'on' ? 'checked = "checked"' : ''; ?> />
                <p class="description">
					<?php _e('The best choice, if you want to use enhanced Analytics features, such as event tracking in e.g. WooCommerce.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
					<?php _e('Allow Monster Insights\' plugin to use the locally hosted analytics.js-file generated and updated by CAOS. Enabling this option means that you\'ll manage Google Analytics entirely within Google Analytics by Monster Insights.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                    <a href="<?= CAOS_ANALYTICS_SITE_URL; ?>/wordpress/leverage-browser-caching-host-analytics-local-monster-insights/" target="_blank"><?php _e('Read more', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>.
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Enable compatibility with Analytify for Wordpress?',
					CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input class="caos_analytics_analytify_compatibility_checkbox" type="checkbox"
                       name="caos_analytics_analytify_compatibility" <?=
				CAOS_ANALYTICS_ANALYTIFY_COMPATIBILITY == 'on' ? 'checked = "checked"' : ''; ?> />
                <p class="description">
					<?php _e('Allow Analytify for Wordpress to use the locally hosted analytics.js-file generated and updated by CAOS. Enabling this option means that you\'ll manage Google Analytics entirely within Analytify for Wordpress.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Which file to download?',
                    CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <?php
                $fileNames = array(
                        "Analytics.js (default)" => "analytics.js",
                        "Gtag.js" => "gtag.js",
                        "Ga.js" => "ga.js"
                );
                ?>
                <select name="caos_analytics_js_file">
                    <?php foreach ($fileNames as $label => $fileName): ?>
                    <option value="<?= $fileName; ?>" <?= (CAOS_ANALYTICS_JS_FILE == $fileName) ? 'selected' : ''; ?>><?= $label; ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description">
                    <?php _e('If you don\'t necessarily need e.g. enhanced e-commerce features, etc. You can choose to download ga.js or gtag.js, instead of the default analytics.js', CAOS_ANALYTICS_TRANSLATE_DOMAIN) ;?>
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Save ' . CAOS_ANALYTICS_JS_FILE . ' to...', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input class="caos_analytics_cache_dir" type="text" name="caos_analytics_cache_dir" placeholder="e.g. /cache/caos-analytics/" value="<?= CAOS_ANALYTICS_CACHE_DIR; ?>"/>
                <p class="description">
					<?php _e("Change the path where the Analytics-file is cached inside WordPress' content directory (usually <code>wp-content</code>). Defaults to <code>/cache/caos-analytics/</code>.", CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Serve from a CDN?', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input class="caos_analytics_cdn_url" type="text" name="caos_analytics_cdn_url" placeholder="e.g. cdn.mydomain.com" value="<?= CAOS_ANALYTICS_CDN_URL ?>" />
                <p class="description">
                    <?php _e(sprintf('If you\'re using a CDN, enter the URL here to serve %s from your CDN.', CAOS_ANALYTICS_JS_FILE), CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
            </td>
        </tr>
        </tbody>
        <tbody class="caos_advanced_settings" <?= CAOS_ANALYTICS_MI_COMPATIBILITY == 'on' ? 'style="display: none;"' : ''; ?>>
        <tr valign="top">
            <th scope="row"><?php _e('Cookie expiry period (days)', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input type="number" name="sgal_ga_cookie_expiry_days" min="0" max="365"
                       value="<?= CAOS_ANALYTICS_COOKIE_EXPIRY; ?>"/>
                <p class="description">
					<?php _e('The number of days when the cookie will automatically expire.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Use adjusted bounce rate?', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input type="number" name="sgal_adjusted_bounce_rate" min="0" max="60"
                       value="<?= CAOS_ANALYTICS_ADJUSTED_BOUNCE_RATE; ?>"/>
                <p class="description">
                    <a href="https://moz.com/blog/adjusted-bounce-rate" target="_blank"><?php _e('More information about adjusted bounce rate', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>.
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Change enqueue order? (Default = 0)', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input type="number" name="sgal_enqueue_order" min="0"
                       value="<?= CAOS_ANALYTICS_ENQUEUE_ORDER; ?>"/>
                <p class="description">
					<?php _e('Leave this alone if you don\'t know what you\'re doing.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Disable all display features functionality?', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input type="checkbox" name="caos_disable_display_features"
					<?= CAOS_ANALYTICS_DISABLE_DISPLAY_FEAT == "on" ? 'checked = "checked"' : ''; ?> />
                <p class="description">
                    <a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/display-features" target="_blank"><?php _e('More information about display features', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>.
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Use Anonymize IP?', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input type="checkbox" name="sgal_anonymize_ip"
					<?= CAOS_ANALYTICS_ANONYMIZE_IP == "on" ? 'checked = "checked"' : ''; ?> />
                <p class="description">
					<?php _e('Required by law in some countries.'); ?>
                    <a href="https://support.google.com/analytics/answer/2763052?hl=en" target="_blank"><?php _e('More information about IP Anonymization', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>.
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Track logged in Administrators?', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input type="checkbox" name="sgal_track_admin"
					<?= CAOS_ANALYTICS_TRACK_ADMIN == "on" ? 'checked = "checked"' : ''; ?> />
                <p class="description">
                    <strong><?php _e('Warning!'); ?></strong> <?php _e('This will track all your traffic as a logged in user.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
            </td>
        </tr>
        </tbody>
        <tbody class="caos_cron_settings">
        <tr valign="top">
            <th scope="row"><?php _e('Remove script from wp-cron?', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></th>
            <td>
                <input type="checkbox" name="caos_remove_wp_cron"
					<?= CAOS_ANALYTICS_REMOVE_CRON == "on" ? 'checked = "checked"' : ''; ?> />
                <p class="description">
					<?php _e('If your local copy of the Analytics-file isn\'t updated automatically or you have WP-Cron disabled, check this option and add the update-analytics.php-script to your crontab manually.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script>
    jQuery('.caos_allow_tracking_').click(function() {
        jQuery('.caos_gdpr_setting').hide()
    })
    jQuery('.caos_allow_tracking_cookie_is_set').click(function() {
        jQuery('.caos_allow_tracking_name').show()
        jQuery('.caos_allow_tracking_value').hide()
    })
    jQuery('.caos_allow_tracking_cookie_has_value').click(function() {
        jQuery('.caos_allow_tracking_name, .caos_allow_tracking_value').show()
    })
    jQuery('.caos_script_position_manual').click(function() {
        jQuery('.caos_add_manually').show()
    })
    jQuery('.caos_script_position_header, .caos_script_position_footer').click(function() {
        jQuery('.caos_add_manually').hide()
    })
    jQuery('.caos_mi_compatibility_checkbox, .caos_analytics_analytify_compatibility_checkbox').click(function() {
        settings = jQuery('.caos_advanced_settings, .caos_basic_settings');
        if (this.checked) {
            jQuery(settings).hide();
        } else {
            jQuery(settings).show();
        }
    })
</script>
