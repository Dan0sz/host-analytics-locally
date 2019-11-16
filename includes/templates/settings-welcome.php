<?php
/**
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 */
$utmTags = '?utm_source=caos&utm_medium=plugin&utm_campaign=support_tab';
?>
<div id="welcome-panel" class="welcome-panel">
    <div class="welcome-panel-content">
        <h2><?php _e('Thank you for using CAOS!', 'host-analyticsjs-local'); ?></h2>
        <p class="about-description">
			<?php _e('CAOS completely optimizes the usage of Google Analytics on your WordPress Website. Host your Google Analytics javascript-file (analytics.js) locally and keep it updated using WordPress\' built-in Cron-scheduler.', 'host-analyticsjs-local'); ?>
        </p>
        <div class="welcome-panel-column-container">
            <div class="welcome-panel-column" style="width: 32%;">
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
            <div class="welcome-panel-column" style="width: 32%;">
                <h3><?php _e('Get a Perfect Score on Pagespeed & Pingdom!', 'host-analyticsjs-local'); ?></h3>
                <p>
                    <strong><?= sprintf(__('Leverage your browser cache%s and %slower pageload times%s by hosting your Google Fonts locally with', 'host-analyticsjs-local'), '</strong>', '<strong>', '</strong>'); ?>
                        <a href="<?= CAOS_SITE_URL; ?>/wordpress/host-google-fonts-locally/<?= $utmTags; ?>" target="_blank">OMGF</a>.
                </p>
                <p>
                    <a target="_blank" href="https://wordpress.org/plugins/host-webfonts-local/"><?php _e('Download now', 'host-analyticsjs-local'); ?></a>
                </p>
                <h3><?php _e('Suggested Reading', 'host-analyticsjs-local'); ?></h3>
                <ul>
                    <li class="welcome-icon dashicons-before dashicons-analytics">
                        <a href="<?= CAOS_SITE_URL; ?>/wordpress/difference-analyics-gtag-ga-js/<?= $utmTags; ?>" target="_blank"><?php _e('What\'s the difference between ga.js, gtag.js and analytics.js?', 'host-analyticsjs-local'); ?></a>
                    </li>
                    <li class="welcome-icon dashicons-before dashicons-welcome-learn-more">
                        <a href="<?= CAOS_SITE_URL; ?>/wordpress/gdpr-compliance-google-analytics/<?= $utmTags; ?>" target="_blank"><?php _e('Complete Guide to GDPR Compliance with CAOS', 'host-analyticsjs-local'); ?></a>
                    </li>
                    <li class="welcome-icon dashicons-before dashicons-dashboard">
                        <a href="<?= CAOS_SITE_URL; ?>/wordpress/quick-guide-pagespeed-insights-pingdom/<?= $utmTags; ?>" target="_blank"><?php _e('Speed-up your site: get 100/100 on Pagespeed Insights in 30 minutes!', 'host-analyticsjs-local'); ?></a>
                    </li>
                </ul>
            </div>
            <div class="welcome-panel-column welcome-panel-last" style="width: 33%;">
                <h3>
                    <?php _e('Need Help?', 'host-analyticsjs-local'); ?>
                </h3>
                <p>
                    <?php _e('Thank you for using CAOS.', 'host-analyticsjs-local'); ?>
                </p>
                <p>
                    <?= sprintf(__('I am convinced that knowledge should be free. That\'s why I will never charge you for the plugins I create and I will help you to succeed in your projects through the %stutorials%s on my blog.', 'host-analyticsjs-local'), '<a href="https://daan.dev/how-to/" target="_blank">', '</a>'); ?>
                </p>
                <p>
                    <?= sprintf(__("However, my time is just as valuable as yours. Consider supporting me by either %sdonating%s or leaving a %s5-star review%s on Wordpress.org.", 'host-analyticsjs-local'), '<a href="' . CAOS_SITE_URL . '/donate" target="_blank">', '</a>', '<a target="_blank" href="https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post">', '</a>'); ?>
                </p>
                <p>
                    <?= sprintf(__('If you\'re running into any issues, please make sure you\'ve read %sthe manual%s thoroughly. Visit the %sFAQ%s and %sSupport Forum%s to see if your question has already been answered. If not, either %scontact%s me or ask a question on the Support Forum.', 'host-analyticsjs-local'), '<a href="' . CAOS_SITE_URL . '/wordpress-plugins/optimize-analytics-wordpress/" target="_blank">' , '</a>', '<a href="https://wordpress.org/plugins/host-analyticsjs-local/#description" target="_blank">', '</a>', '<a href="https://wordpress.org/support/plugin/host-analyticsjs-local">', '</a>', '<a href="' . CAOS_SITE_URL . '/contact" target="_blank">', '</a>'); ?>
                </p>
                <p>
                    <a target="_blank" class="button button-primary button-hero" href="<?= CAOS_SITE_URL; ?>/donate/?utm_source=caos&utm_medium=plugin&utm_campaign=donate_button"><span class="dashicons-before dashicons-heart"> <?php _e('Donate', 'host-analyticsjs-local'); ?></span></a>
                    <a target="_blank" class="button button-secondary button-hero" href="https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post"><span class="dashicons-before dashicons-star-filled"> <?php _e('Review', 'host-analyticsjs-local'); ?></span></a>
                    <a target="_blank" class="button button-secondary button-hero" href="https://twitter.com/Dan0sz"><span class="dashicons-before dashicons-twitter"> <?php _e('Follow', 'host-analyticsjs-local'); ?></span></a>
                </p>
            </div>
        </div>
    </div>
</div>
