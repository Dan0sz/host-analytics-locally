<?php
/* * * * * * * * * * * * * * * * * * * *
 *  ██████╗ █████╗  ██████╗ ███████╗
 * ██╔════╝██╔══██╗██╔═══██╗██╔════╝
 * ██║     ███████║██║   ██║███████╗
 * ██║     ██╔══██║██║   ██║╚════██║
 * ╚██████╗██║  ██║╚██████╔╝███████║
 *  ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚══════╝
 *
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/caos/
 * @copyright: (c) 2020 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

$utmTags = '?utm_source=caos&utm_medium=plugin&utm_campaign=settings';
?>
<h3><?= __('Advanced Settings', 'host-analyticsjs-local'); ?></h3>
<p>
    <strong>*</strong> <?php _e('Manual update required after saving changes.', 'host-analyticsjs-local'); ?>
</p>
<table class="form-table">
    <tr valign="top" class="caos-compatibility-mode">
        <th scope="row">
            <?php _e('Enable compatibility mode', 'host-analyticsjs-local'); ?>
        </th>
        <td>
            <select name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE; ?>" class="caos-compatibility-mode-input">
                <?php foreach (CAOS_Admin_Settings::CAOS_ADMIN_COMPATIBILITY_OPTIONS as $option => $details): ?>
                    <option value="<?= $option; ?>" <?= (CAOS_OPT_COMPATIBILITY_MODE == $option) ? 'selected' : ''; ?>><?= $details['label']; ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description">
                <?php _e('Allow another Google Analytics plugin to use the js-file created and updated by CAOS. Enabling this option means that you\'ll manage Google Analytics entirely within the other plugin.', 'host-analyticsjs-local'); ?>
            </p>
        </td>
    </tr>
    <tr valign="top" class="caos-stealth-mode">
        <th scope="row"><?php _e('Enable stealth mode', 'host-analyticsjs-local'); ?> *</th>
        <td>
            <input type="checkbox" class="caos-stealth-mode-input" name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_STEALTH_MODE; ?>"
                <?= CAOS_OPT_STEALTH_MODE == "on" ? 'checked = "checked"' : ''; ?> />
            <p class="description">
                <strong><?php _e('Experimental', 'host-analyticsjs-local'); ?></strong>: <?= sprintf(__('Use at your own risk! This setting allows you to bypass most Ad Blockers. Make sure your blog/account respects any relevant privacy laws. (SSL [https] required! / Does not work (yet) for Google Analytics Remarketing features. %sRead more%s)', 'host-analyticsjs-local'), '<a target="_blank" href="https://developers.google.com/analytics/resources/concepts/gaConceptsTrackingOverview">', '</a>'); ?>
            </p>
        </td>
    </tr>
    <tr valign="top" class="caos-js-file">
        <th scope="row"><?php _e('Which file to download?', 'host-analyticsjs-local'); ?> *
        </th>
        <td>
            <select name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_JS_FILE; ?>" class="caos-js-file-input">
                <?php foreach (CAOS_Admin_Settings::CAOS_ADMIN_JS_FILE_OPTIONS as $label => $fileName): ?>
                    <option value="<?= $fileName; ?>" <?= (CAOS_OPT_REMOTE_JS_FILE == $fileName) ? 'selected' : ''; ?>><?= $label; ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description">
                <?= sprintf(__('<code>analytics.js</code> is recommended in most situations. When using <code>gtag.js</code>, <code>analytics.js</code> is also cached and updated! Need help choosing? %sRead this%s', 'host-analyticsjs-local'), '<a href="' . CAOS_SITE_URL . '/wordpress/difference-analyics-gtag-ga-js/' . $utmTags . '" target="_blank">', '</a>'); ?>
            </p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?= sprintf(__('Save %s to...', 'host-analyticsjs-local'), CAOS_OPT_REMOTE_JS_FILE); ?> *</th>
        <td>
            <input class="caos_analytics_cache_dir" type="text" name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR; ?>" placeholder="e.g. /cache/caos/" value="<?= CAOS_OPT_CACHE_DIR; ?>"/>
            <p class="description">
                <?php _e("Change the path where the Analytics-file is cached inside WordPress' content directory (usually <code>wp-content</code>). Defaults to <code>/cache/caos/</code>.", 'host-analyticsjs-local'); ?>
            </p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Serve from a CDN?', 'host-analyticsjs-local'); ?></th>
        <td>
            <input class="caos_analytics_cdn_url" type="text" name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_CDN_URL; ?>" placeholder="e.g. cdn.mydomain.com" value="<?= CAOS_OPT_CDN_URL ?>"/>
            <p class="description">
                <?= sprintf(__('If you\'re using a CDN, enter the URL here to serve <code>%s</code> from your CDN.', 'host-analyticsjs-local'), CAOS_OPT_REMOTE_JS_FILE); ?>
            </p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?= __('Enable Preconnect? (Recommended)', 'host-analyticsjs-local'); ?></th>
        <td>
            <input type="checkbox" class="caos-preconnect" name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_PRECONNECT; ?>" <?= CAOS_OPT_PRECONNECT == 'on' ? 'checked = "checked"' : ''; ?> />
            <p class="description">
                <?= __('Preconnect to google-analytics.com and CDN URL (if set) to reduce latency and speed up requests to these servers.', 'host-analyticsjs-local'); ?>
            </p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Capture outbound links?', 'host-analyticsjs-local'); ?></th>
        <td>
            <input type="checkbox" class="caos-capture-outbound-links" name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_CAPTURE_OUTBOUND_LINKS; ?>" <?= CAOS_OPT_CAPTURE_OUTBOUND_LINKS == "on" ? 'checked = "checked"' : ''; ?> />
            <p class="description">
                <?= sprintf(__('Find out when users click a link to leave your site. Only works with <code>analytics.js</code> and when Stealth Mode is disabled.  %sRead more%s', 'host-analyticsjs-local'), '<a target="_blank" href="https://support.google.com/analytics/answer/1136920">', '</a>'); ?>
            </p>
        </td>
    </tr>
    <tbody class="caos_advanced_settings" <?= empty(CAOS_OPT_COMPATIBILITY_MODE) ? '' : 'style="display: none;"'; ?>>
    <tr valign="top">
        <th scope="row"><?php _e('Cookie expiry period (days)', 'host-analyticsjs-local'); ?></th>
        <td>
            <input type="number" name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_GA_COOKIE_EXPIRY_DAYS; ?>" min="0" max="365"
                   value="<?= CAOS_OPT_COOKIE_EXPIRY; ?>"/>
            <p class="description">
                <?php _e('The number of days when the cookie will automatically expire.', 'host-analyticsjs-local'); ?>
            </p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Use adjusted bounce rate? (seconds)', 'host-analyticsjs-local'); ?></th>
        <td>
            <input type="number" name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_ADJUSTED_BOUNCE_RATE; ?>" min="0" max="60"
                   value="<?= CAOS_OPT_ADJUSTED_BOUNCE_RATE; ?>"/>
            <p class="description">
                <a href="<?= CAOS_SITE_URL; ?>/how-to/adjusted-bounce-rate-caos/<?= $utmTags; ?>" target="_blank"><?php _e('More information about adjusted bounce rate', 'host-analyticsjs-local'); ?></a>.
            </p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Change enqueue order? (Default = 0)', 'host-analyticsjs-local'); ?></th>
        <td>
            <input type="number" name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_ENQUEUE_ORDER; ?>" min="0"
                   value="<?= CAOS_OPT_ENQUEUE_ORDER; ?>"/>
            <p class="description">
                <?php _e('Leave this alone if you don\'t know what you\'re doing.', 'host-analyticsjs-local'); ?>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Disable all display features functionality?', 'host-analyticsjs-local'); ?></th>
        <td>
            <input type="checkbox" name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_DISABLE_DISPLAY_FEATURES; ?>"
                <?= CAOS_OPT_DISABLE_DISPLAY_FEAT == "on" ? 'checked = "checked"' : ''; ?> />
            <p class="description">
                <a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/display-features" target="_blank"><?php _e('More information about display features', 'host-analyticsjs-local'); ?></a>.
            </p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Anonymize IP?', 'host-analyticsjs-local'); ?></th>
        <td>
            <input type="checkbox" name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_ANONYMIZE_IP; ?>"
                <?= CAOS_OPT_ANONYMIZE_IP == "on" ? 'checked = "checked"' : ''; ?> />
            <p class="description">
                <?php _e('Required by law in some countries.', 'host-analyticsjs-local'); ?>
                <a href="https://support.google.com/analytics/answer/2763052?hl=en" target="_blank"><?php _e('More information about IP Anonymization', 'host-analyticsjs-local'); ?></a>.
            </p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Track logged in Administrators?', 'host-analyticsjs-local'); ?></th>
        <td>
            <input type="checkbox" name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_TRACK_ADMIN; ?>"
                <?= CAOS_OPT_TRACK_ADMIN == "on" ? 'checked = "checked"' : ''; ?> />
            <p class="description">
                <strong><?php _e('Warning!', 'host-analyticsjs-local'); ?></strong> <?php _e('This will track all your traffic as a logged in user.', 'host-analyticsjs-local'); ?>
            </p>
        </td>
    </tr>
    </tbody>
    <tr valign="top">
        <th scope="row"><?php _e('Remove settings at uninstall?', 'host-analyticsjs-local'); ?></th>
        <td>
            <input type="checkbox" name="<?= CAOS_Admin_Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS; ?>"
                <?= CAOS_OPT_UNINSTALL_SETTINGS == "on" ? 'checked = "checked"' : ''; ?> />
            <p class="description">
                <strong><?php _e('Warning!', 'host-analyticsjs-local'); ?></strong> <?php _e('This will remove the settings from the database upon plugin deletion!.', 'host-analyticsjs-local'); ?>
            </p>
        </td>
    </tr>
</table>