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
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

$utmTags = '?utm_source=caos&utm_medium=plugin&utm_campaign=support_tab';
$tweetUrl = "https://twitter.com/intent/tweet?text=I+am+using+CAOS+to+speed+up+Google+Analytics+for+@WordPress!+Try+it+for+yourself:&via=Dan0sz&hashtags=GoogleAnalytics,WordPress,Pagespeed,Insights&url=https://wordpress.org/plugins/host-analyticsjs-local/";
?>
<div id="welcome-panel" class="welcome-panel">
    <div class="welcome-panel-content">
        <p class="about-description">
            <?= get_plugin_data(CAOS_PLUGIN_FILE)['Description']; ?>
        </p>
        <div class="welcome-panel-column-container">
            <div class="welcome-panel-column" style="width: 31%; margin-right: 15px;">
                <h3><?php _e('Quickstart', 'host-analyticsjs-local'); ?></h3>
                <ul>
                    <li class="welcome-icon dashicons-before dashicons-analytics"><?php _e('Enter your Analytics Tracking-ID', 'host-analyticsjs-local'); ?></li>
                    <li class="welcome-icon dashicons-before dashicons-admin-tools"><?php _e('Configure when to allow tracking', 'host-analyticsjs-local'); ?></li>
                    <li class="welcome-icon dashicons-before dashicons-admin-appearance"><?php _e('Set the position of the tracking code', 'host-analyticsjs-local'); ?></li>
                    <li class="welcome-icon dashicons-before dashicons-controls-play"><?php _e('Choose between Async or Default', 'host-analyticsjs-local'); ?></li>
                    <li class="welcome-icon dashicons-before dashicons-smiley"><?php _e("Click 'Save Changes' and you're done!", 'host-analyticsjs-local'); ?></li>
                </ul>
                <p>
                    <a target="_blank" href="<?= CAOS_SITE_URL; ?>/wordpress-plugins/optimize-analytics-wordpress/<?= $utmTags; ?>"><?php _e('Click here', 'host-analyticsjs-local'); ?></a> <?php _e('for a more comprehensive guide.', 'host-analyticsjs-local'); ?>
                </p>
            </div>
            <div class="welcome-panel-column" style="width: 31%; margin-right: 15px;">
                <h3>
                    <?php _e('Need Help?', 'host-analyticsjs-local'); ?>
                </h3>
                <p>
                    <?= sprintf(__('Visit the %sFAQ%s and %sSupport Forum%s to see if your question has already been answered. If not, either %scontact%s me or ask a question on the Support Forum.', 'host-analyticsjs-local'), '<a href="' . CAOS_SITE_URL . '/wordpress-plugins/optimize-analytics-wordpress/' . $utmTags . '" target="_blank">' , '</a>', '<a href="https://wordpress.org/plugins/host-analyticsjs-local/#description" target="_blank">', '</a>', '<a href="https://wordpress.org/support/plugin/host-analyticsjs-local">', '</a>', '<a href="' . CAOS_SITE_URL . '/contact' . $utmTags . '" target="_blank">', '</a>'); ?>
                </p>
                <h3><span class="dashicons dashicons-dashboard"></span> <?php _e('Make WordPress <em>Faster</em> Than Superman', 'host-analyticsjs-local'); ?></h3>
                <p>
                    <?= __('Superman can reach the other side of the world in <strong>3 seconds</strong>. Google wants your website to do it <strong>faster</strong>. Let\'s give Google a run for its money.', 'host-analyticsjs-local'); ?>
                </p>
                <p>
                    <a target="_blank" class="button button-primary button-hero" href="https://woosh.dev/wordpress-services/<?= $utmTags; ?>"><span class="dashicons dashicons-thumbs-up"></span> Hire me</a> <span><em>(<?= __('Starting at € 99,-', 'host-analyticsjs-local'); ?>)</em></span>
                </p>
            </div>
            <div class="welcome-panel-column welcome-panel-last" style="width: 33%;">
                <h3>
                    <?php _e('Support CAOS & Spread the Word!', 'host-analyticsjs-local'); ?>
                </h3>
                <p>
                    <?= sprintf(__('I am convinced that knowledge should be free. That\'s why I will never charge you for the plugins I create and I will help you to succeed in your projects through the %stutorials%s on my blog.', 'host-analyticsjs-local'), '<a href="https://daan.dev/how-to/' . $utmTags . '" target="_blank">', '</a>'); ?>
                </p>
                <p>
                    <?= __('But that doesn\'t mean there\'s nothing you can do to show your support! :)', 'host-analyticsjs-local'); ?>
                </p>
                <p>
                    <?= sprintf(__('Please help me spread the word by leaving a %s5-star review%s on Wordpress.org or sending a %sTweet%s about CAOS.', 'host-analyticsjs-local'), '<a target="_blank" href="https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post">', '</a>', "<a href='$tweetUrl'>", '</a>'); ?>
                </p>
                <p>
                    <a target="_blank" class="button button-secondary button-hero" href="https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post"><span class="dashicons-before dashicons-star-filled"> <?php _e('Review', 'host-analyticsjs-local'); ?></span></a>
                    <a target="_blank" class="button button-secondary button-hero" href="<?= $tweetUrl; ?>"><span class="dashicons-before dashicons-twitter"> <?php _e('Tweet', 'host-analyticsjs-local'); ?></span></a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    h3 > .dashicons {
        line-height: 1.4;
    }
</style>