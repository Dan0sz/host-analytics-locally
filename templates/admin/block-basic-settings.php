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
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2020 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

$frontend = new CAOS_Frontend_Tracking();
$utmTags  = '?utm_source=caos&utm_medium=plugin&utm_campaign=settings';
?>
<table class="form-table">
    <tr valign="top">
        <th scope="row"><?php _e('Google Analytics Tracking ID', 'host-analyticsjs-local'); ?></th>
        <td>
            <input type="text" name="<?= CAOS_Admin_Settings::CAOS_SETTING_TRACKING_ID; ?>" value="<?= CAOS_OPT_TRACKING_ID; ?>"/>
        </td>
    </tr>
    <tbody class="caos_basic_settings" <?= empty(CAOS_OPT_COMPATIBILITY_MODE) ? '' : 'style="display: none;"'; ?>>
    <tr valign="top">
        <th scope="row"><?php _e('Allow tracking...', 'host-analyticsjs-local'); ?></th>
        <td>
            <?php
            foreach (CAOS_Admin_Settings::CAOS_ADMIN_ALLOW_TRACKING_OPTIONS as $option => $details): ?>
                <label>
                    <input type="radio" class="caos_allow_tracking_<?= $option; ?>"
                           name="<?= CAOS_Admin_Settings::CAOS_SETTING_ALLOW_TRACKING; ?>" value="<?= $option; ?>"
                        <?= $option == CAOS_OPT_ALLOW_TRACKING ? 'checked="checked"' : ''; ?> onclick="showOptions('<?= $details['show']; ?>'); hideOptions('<?= $details['hide']; ?>');"/>
                    <?= $details['label']; ?>
                </label>
                <br/>
            <?php endforeach; ?>
            <p class="description">
                <?= sprintf(__('Choose \'Always\' to use Google Analytics without a Cookie Notice. Follow %sthis tutorial%s to comply with GDPR Laws.', 'host-analyticsjs-local'), '<a href="' . CAOS_SITE_URL . "/wordpress/analytics-gdpr-anonymize-ip-cookie-notice/$utmTags\" target='_blank'>", '</a>'); ?>
                <?php _e('Choose \'When cookie is set\' or \'When cookie has a value\' to make CAOS compatible with your Cookie Notice plugin.', 'host-analyticsjs-local'); ?>
                <a href="<?= CAOS_SITE_URL; ?>/wordpress/gdpr-compliance-google-analytics/<?= $utmTags; ?>" target="_blank">
                    <?php _e('Read more', 'host-analyticsjs-local'); ?></a>.
            </p>
        </td>
    </tr>
    <tr class="caos_gdpr_setting caos_allow_tracking_name" valign="top" <?= CAOS_OPT_ALLOW_TRACKING ? '' : 'style="display: none;"'; ?>>
        <th scope="row"><?php _e('Cookie name', 'host-analyticsjs-local'); ?></th>
        <td>
            <input type="text" name="<?= CAOS_Admin_Settings::CAOS_SETTING_COOKIE_NOTICE_NAME; ?>"
                   value="<?= CAOS_OPT_COOKIE_NAME; ?>"/>
            <p class="description">
                <?php _e('The cookie name set by your Cookie Notice plugin when user accepts.', 'host-analyticsjs-local'); ?>
            </p>
        </td>
    </tr>
    <tr class="caos_gdpr_setting caos_allow_tracking_name caos_allow_tracking_value" valign="top" <?= CAOS_OPT_ALLOW_TRACKING == 'cookie_has_value' ? '' : 'style="display: none;"'; ?>>
        <th scope="row"><?php _e('Cookie value', 'host-analyticsjs-local'); ?></th>
        <td>
            <input type="text" name="<?= CAOS_Admin_Settings::CAOS_SETTING_COOKIE_VALUE; ?>"
                   value="<?= CAOS_OPT_COOKIE_VALUE; ?>"/>
            <p class="description">
                <?php _e('The value of the above specified cookie set by your Cookie Notice when user accepts.', 'host-analyticsjs-local'); ?>
            </p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Snippet type', 'host-analyticsjs-local'); ?></th>
        <td>
            <?php
            $caos_snippet_type = array(
                ''      => __('Default', 'host-analyticsjs-local'),
                'async' => __('Asynchronous', 'host-analyticsjs-local')
            );
            ?>
            <select name="<?= CAOS_Admin_Settings::CAOS_SETTING_SNIPPET_TYPE; ?>" class="caos_snippet_type">
                <?php foreach ($caos_snippet_type as $option => $label): ?>
                    <option value="<?= $option; ?>" <?= CAOS_OPT_SNIPPET_TYPE == $option ? 'selected' : ''; ?>><?= $label; ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description">
                <?php _e('Should we use the default or the asynchronous tracking snippet? (Only supported for <code>gtag.js</code> and <code>analytics.js</code>)', 'host-analyticsjs-local'); ?>
                <a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/" target="_blank">Read more</a>.
            </p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Position of tracking-code', 'host-analyticsjs-local'); ?></th>
        <td>
            <?php
            foreach (CAOS_Admin_Settings::CAOS_ADMIN_SCRIPT_POSITION_OPTIONS as $option => $details): ?>
                <label>
                    <input class="caos_script_position_<?= $option; ?>" type="radio" name="<?= CAOS_Admin_Settings::CAOS_SETTING_SCRIPT_POSITION; ?>"
                           value="<?= $option; ?>" <?= $option == CAOS_OPT_SCRIPT_POSITION ? 'checked="checked"' : ''; ?> onclick="showOptions('<?= $details['show']; ?>'); hideOptions('<?= $details['hide']; ?>')"/>
                    <?= $details['label']; ?>
                </label>
                <br/>
            <?php endforeach; ?>
            <p class="description">
                <?php _e('Load the Analytics tracking-snippet in the header, footer or manually?', 'host-analyticsjs-local'); ?>
                <?php _e('If e.g. your theme doesn\'t load the wp_head conventionally, choose \'Add manually\'.', 'host-analyticsjs-local'); ?>
            </p>
        </td>
    </tr>
    <tr class="caos_add_manually" valign="top" <?= CAOS_OPT_SCRIPT_POSITION == 'manual' ? '' : 'style="display: none;"'; ?>>
        <th scope="row"><?php _e('Tracking-code', 'host-analyticsjs-local'); ?></th>
        <td>
            <label>
                <textarea style="display: block; width: 100%; height: 250px;"><?php $frontend->render_tracking_code(); ?></textarea>
            </label>
            <p class="description">
                <?php _e('Copy this to the theme or plugin which should handle displaying the snippet.', 'host-analyticsjs-local'); ?>
            </p>
        </td>
    </tr>
    </tbody>
</table>
