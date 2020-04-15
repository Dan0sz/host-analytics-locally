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

class CAOS_Admin_Settings_Basic extends CAOS_Admin_Settings_Builder
{
    /**
     * CAOS_Admin_Settings_Basic constructor.
     */
    public function __construct()
    {
        $this->title = __('Basic Settings', 'host-analyticsjs-local');

        // Open
        add_filter('caos_basic_settings_content', [$this, 'do_title'], 10);
        add_filter('caos_basic_settings_content', [$this, 'do_before'], 20);

        // Settings
        add_filter('caos_basic_settings_content', [$this, 'do_tracking_id'], 30);

        // Non-compatibility mode settings
        add_filter('caos_basic_settings_content', [$this, 'do_tbody_basic_settings_open'], 40);
        add_filter('caos_basic_settings_content', [$this, 'do_allow_tracking'], 50);
        add_filter('caos_basic_settings_content', [$this, 'do_cookie_name'], 60);
        add_filter('caos_basic_settings_content', [$this, 'do_cookie_value'], 70);
        add_filter('caos_basic_settings_content', [$this, 'do_snippet_type'], 80);
        add_filter('caos_basic_settings_content', [$this, 'do_script_position'], 90);
        add_filter('caos_basic_settings_content', [$this, 'do_add_manually'], 100);
        add_filter('caos_basic_settings_content', [$this, 'do_tbody_close'], 100);

        // Close
        add_filter('caos_basic_settings_content', [$this, 'do_after'], 100);
    }

    /**
     * Google Analytics Tracking ID
     */
    public function do_tracking_id()
    {
        $this->do_text(
            __('Google Analytics Tracking ID', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_ID,
            __('e.g. UA-1234567-12', 'host-analyticsjs-local'),
            CAOS_OPT_TRACKING_ID
        );
    }

    /**
     *
     */
    public function do_tbody_basic_settings_open()
    {
        $this->do_tbody_open('caos_basic_settings');
    }

    /**
     * Allow tracking...
     */
    public function do_allow_tracking()
    {
        ?>
        <tr valign="top">
            <th scope="row"><?php _e('Allow tracking...', 'host-analyticsjs-local'); ?></th>
            <td>
                <?php
                foreach (CAOS_Admin_Settings::CAOS_ADMIN_ALLOW_TRACKING_OPTIONS as $option => $details): ?>
                    <label>
                        <input type="radio" class="caos_allow_tracking_<?= $option; ?>"
                               name="<?= CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING; ?>" value="<?= $option; ?>"
                            <?= $option == CAOS_OPT_ALLOW_TRACKING ? 'checked="checked"' : ''; ?> onclick="showOptions('<?= $details['show']; ?>'); hideOptions('<?= $details['hide']; ?>');"/>
                        <?= $details['label']; ?>
                    </label>
                    <br/>
                <?php endforeach; ?>
                <p class="description">
                    <?= sprintf(__('Choose \'Always\' to use Google Analytics without a Cookie Notice. Follow %sthis tutorial%s to comply with GDPR Laws.', 'host-analyticsjs-local'), '<a href="' . CAOS_SITE_URL . "/wordpress/analytics-gdpr-anonymize-ip-cookie-notice/$this->utm_tags\" target='_blank'>", '</a>'); ?>
                    <?php _e('Choose \'When cookie is set\' or \'When cookie has a value\' to make CAOS compatible with your Cookie Notice plugin.', 'host-analyticsjs-local'); ?>
                    <a href="<?= CAOS_SITE_URL; ?>/wordpress/gdpr-compliance-google-analytics/<?= $this->utm_tags; ?>" target="_blank">
                        <?php _e('Read more', 'host-analyticsjs-local'); ?></a>.
                </p>
            </td>
        </tr>
        <?php
    }

    /**
     * Cookie name
     */
    public function do_cookie_name()
    {
        ?>
        <tr class="caos_gdpr_setting caos_allow_tracking_name" valign="top" <?= CAOS_OPT_ALLOW_TRACKING ? '' : 'style="display: none;"'; ?>>
            <th scope="row"><?php _e('Cookie name', 'host-analyticsjs-local'); ?></th>
            <td>
                <input type="text" name="<?= CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME; ?>"
                       value="<?= CAOS_OPT_COOKIE_NAME; ?>"/>
                <p class="description">
                    <?php _e('The cookie name set by your Cookie Notice plugin when user accepts.', 'host-analyticsjs-local'); ?>
                </p>
            </td>
        </tr>
        <?php
    }

    /**
     * Cookie value
     */
    public function do_cookie_value()
    {
        ?>
        <tr class="caos_gdpr_setting caos_allow_tracking_name caos_allow_tracking_value" valign="top" <?= CAOS_OPT_ALLOW_TRACKING == 'cookie_has_value' ? '' : 'style="display: none;"'; ?>>
            <th scope="row"><?php _e('Cookie value', 'host-analyticsjs-local'); ?></th>
            <td>
                <input type="text" name="<?= CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_VALUE; ?>"
                       value="<?= CAOS_OPT_COOKIE_VALUE; ?>"/>
                <p class="description">
                    <?php _e('The value of the above specified cookie set by your Cookie Notice when user accepts.', 'host-analyticsjs-local'); ?>
                </p>
            </td>
        </tr>
        <?php
    }

    /**
     * Snippet type
     */
    public function do_snippet_type()
    {
        $this->do_select(
            __('Snippet type', 'host-analyticsjs-local'),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_SNIPPET_TYPE,
            CAOS_Admin_Settings::CAOS_ADMIN_SNIPPET_TYPE_OPTIONS,
            CAOS_OPT_SNIPPET_TYPE,
            __('Should we use the default or the asynchronous tracking snippet? (Only supported for <code>gtag.js</code> and <code>analytics.js</code>)', 'host-analyticsjs-local') . '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/" target="_blank">' . __('Read more', 'host-analyticsjs-local') . '</a>'
        );
    }

    /**
     * Position of tracking-code
     */
    public function do_script_position()
    {
        ?>
        <tr valign="top">
            <th scope="row"><?php _e('Position of tracking-code', 'host-analyticsjs-local'); ?></th>
            <td>
                <?php
                foreach (CAOS_Admin_Settings::CAOS_ADMIN_SCRIPT_POSITION_OPTIONS as $option => $details): ?>
                    <label>
                        <input class="caos_script_position_<?= $option; ?>" type="radio" name="<?= CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION; ?>"
                               value="<?= $option; ?>" <?= $option == CAOS_OPT_SCRIPT_POSITION ? 'checked="checked"' : ''; ?> onclick="showOptions('<?= $details['show']; ?>'); hideOptions('<?= $details['hide']; ?>')"/>
                        <?= $details['label']; ?>
                    </label>
                    <br/>
                <?php endforeach; ?>
                <p class="description">
                    <?php _e('Load the Analytics tracking-snippet in the header, footer or manually?', 'host-analyticsjs-local'); ?>
                    <?php _e('If e.g. your theme doesn\'t load the <code>wp_head()</code> conventionally, choose \'Add manually\'.', 'host-analyticsjs-local'); ?>
                </p>
            </td>
        </tr>
        <?php
    }

    /**
     * Tracking-code
     */
    public function do_add_manually()
    {
        $frontend = new CAOS_Frontend_Tracking();

        ?>
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
        <?php
    }
}
