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

$plugin_text_domain = 'host-analyticsjs-local';
$utmTags  = '?utm_source=caos&utm_medium=plugin&utm_campaign=support_tab';
$tweetUrl = "https://twitter.com/intent/tweet?text=I+am+using+CAOS+to+speed+up+Google+Analytics+for+@WordPress!+Try+it+for+yourself:&via=Dan0sz&hashtags=GoogleAnalytics,WordPress,Pagespeed,Insights&url=https://wordpress.org/plugins/host-analyticsjs-local/";
?>
<div class="welcome-panel-content">
    <div class="welcome-panel-column-container">
        <div class="welcome-panel-column" style="width: 100%;">
            <h2>
                <?php _e('Support CAOS & Spread the Word!', $plugin_text_domain); ?>
            </h2>
            <p>
                <?= __('Just because this plugin\'s free, doesn\'t mean there\'s nothing you can do to support me!', $plugin_text_domain); ?>
            </p>
            <p>
                <?= sprintf(__('Please help me spread the word by leaving a %s5-star review%s on Wordpress.org or sending a %sTweet%s about CAOS.', $plugin_text_domain), '<a target="_blank" href="https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post">', '</a>', "<a href='$tweetUrl'>", '</a>'); ?>
            </p>
            <p>
                <a target="_blank" class="button button-secondary button-hero" href="https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post"><span class="dashicons-before dashicons-star-filled"> <?php _e('Review', $plugin_text_domain); ?></span></a>
                <a target="_blank" class="button button-secondary button-hero" href="<?= $tweetUrl; ?>"><span class="dashicons-before dashicons-twitter"> <?php _e('Tweet', $plugin_text_domain); ?></span></a>
            </p>
            <h3>
                <?php _e('Need Help?', $plugin_text_domain); ?>
            </h3>
            <ul>
                <li><a target="_blank" href="<?= apply_filters('caos_settings_sidebar_quick_start', 'https://ffw.press/docs/caos/quick-start/'); ?>"><?= __('Quick Start Guide', $plugin_text_domain); ?></a></li>
                <li><a target="_blank" href="<?= apply_filters('caos_settings_sidebar_user_manual', 'https://ffw.press/docs/omgf-pro/user-manual/'); ?>"><?= __('User Manual', $plugin_text_domain); ?></a></li>
                <li><a target="_blank" href="<?= apply_filters('caos_settings_sidebar_faq_link', 'https://wordpress.org/plugins/host-webfonts-local/#description'); ?>"><?= __('FAQ', $plugin_text_domain); ?></a></li>
                <li><a target="_blank" href="<?= apply_filters('caos_settings_sidebar_get_support_link', 'https://wordpress.org/support/plugin/host-webfonts-local/#new-post'); ?>"><?= __('Get Support', $plugin_text_domain); ?></a></li>
            </ul>
            <hr />
            <h4 class="signature"><?= sprintf(__('Coded with %s in The Netherlands.', $plugin_text_domain), '<span class="dashicons dashicons-heart ffwp-heart"></span>'); ?></h4>
            <p class="signature">
                <a target="_blank" title="<?= __('Visit FFW Press', $plugin_text_domain); ?>" href="https://ffw.press/wordpress-plugins/"><img class="signature-image" alt="<?= __('Visit FFW Press', $plugin_text_domain); ?>" src="https://ffw.press/wp-content/uploads/2021/01/logo-color-full@05x.png" /></a>
            </p>
        </div>
    </div>
</div>