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
 * @copyright: (c) 2021 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

class CAOS_Admin_Settings_Help extends CAOS_Admin_Settings_Builder
{
    /**
     * CAOS_Admin_Settings_Help constructor.
     */
    public function __construct()
    {
        $this->title = __('Help & Documentation', $this->plugin_text_domain);

        // Title
        add_filter('caos_help_content', [$this, 'do_title'], 10);

        // Content
        add_filter('caos_help_content', [$this, 'do_content'], 20);
    }

    public function do_content()
    {
        $plugin_text_domain = 'host-analyticsjs-local';
        $utmTags = '?utm_source=caos&utm_medium=plugin&utm_campaign=support_tab';
        $tweetUrl = "https://twitter.com/intent/tweet?text=I+am+using+CAOS+to+speed+up+Google+Analytics+for+@WordPress!+Try+it+for+yourself:&via=Dan0sz&hashtags=GoogleAnalytics,WordPress,Pagespeed,Insights&url=https://wordpress.org/plugins/host-analyticsjs-local/";
?>
        <div class="welcome-panel">
            <div class="welcome-panel-content">
                <h2><?= __('Thank you for using CAOS!'); ?></h2>
                <p class="about-description">
                    <?= __('Need help configuring CAOS? Please refer to the links below to get you started.', $this->plugin_text_domain); ?>
                </p>
                <div class="welcome-panel-column-container">
                    <div class="welcome-panel-column">
                        <h3>
                            <?php _e('Need Help?', $plugin_text_domain); ?>
                        </h3>
                        <ul>
                            <li><a class="welcome-icon dashicons-controls-play" target="_blank" href="<?= apply_filters('caos_settings_sidebar_quick_start', 'https://ffw.press/docs/caos/quick-start/'); ?>"><?= __('Quick Start Guide', $plugin_text_domain); ?></a></li>
                            <li><a class="welcome-icon dashicons-text-page" target="_blank" href="<?= apply_filters('caos_settings_sidebar_user_manual', 'https://ffw.press/docs/caos/user-manual/'); ?>"><?= __('User Manual', $plugin_text_domain); ?></a></li>
                            <li><a class="welcome-icon dashicons-editor-help" target="_blank" href="<?= apply_filters('caos_settings_sidebar_faq_link', 'https://wordpress.org/plugins/host-analyticjs-local/#description'); ?>"><?= __('FAQ', $plugin_text_domain); ?></a></li>
                            <li><a class="welcome-icon dashicons-bell" target="_blank" href="<?= apply_filters('caos_settings_sidebar_get_support_link', 'https://wordpress.org/support/plugin/host-analyticsjs-local/#new-post'); ?>"><?= __('Get Support', $plugin_text_domain); ?></a></li>
                        </ul>
                    </div>
                    <div class="welcome-panel-column">
                        <h3><?= __('Support CAOS & Spread the Word!', $plugin_text_domain); ?></h3>
                        <ul>
                            <li><a class="welcome-icon dashicons-star-filled" target="_blank" href="https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post"><?= __('Write a 5-star Review or,', $plugin_text_domain); ?></a></li>
                            <li><a class="welcome-icon dashicons-twitter" target="_blank" href="<?= $tweetUrl; ?>"><?= __('Tweet about it!', $plugin_text_domain); ?></a></li>
                        </ul>
                    </div>
                    <div class="welcome-panel-column welcome-panel-last">
                        <h3 class="signature"><?= __('Check out my other plugins', $plugin_text_domain); ?> @</h3>
                        <p class="signature">
                            <a target="_blank" title="<?= __('Visit FFW Press', $plugin_text_domain); ?>" href="https://ffw.press/wordpress-plugins/"><img class="signature-image" alt="<?= __('Visit FFW Press', $plugin_text_domain); ?>" src="https://ffw.press/wp-content/uploads/2021/01/logo-color-full@05x.png" /></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        </div>
<?php
    }
}
