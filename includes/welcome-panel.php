<?php
/**
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 */
?>
<div id="welcome-panel" class="welcome-panel">
    <div class="welcome-panel-content">
        <h2><?php _e('Thank you for using CAOS for Analytics!', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></h2>
        <p class="about-description">
			<?php _e('CAOS for Analytics completely optimizes the usage of Google Analytics on your WordPress Website. Host your Google Analytics javascript-file (analytics.js) locally and keep it updated using WordPress\' built-in Cron-scheduler.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
        </p>
        <div class="welcome-panel-column-container">
            <div class="welcome-panel-column" style="width: 32%;">
                <h3><?php _e('Quickstart', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></h3>
                <ul>
                    <li class="welcome-icon dashicons-before dashicons-analytics"><?php _e('Enter your Analytics Tracking-ID', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></li>
                    <li class="welcome-icon dashicons-before dashicons-admin-tools"><?php _e('Configure when to allow tracking', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></li>
                    <li class="welcome-icon dashicons-before dashicons-admin-appearance"><?php _e('Set the position of the tracking code', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></li>
                    <li class="welcome-icon dashicons-before dashicons-smiley"><?php _e("Click 'Save Changes' and you're done!", CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></li>
                </ul>
                <p>
                    <a target="_blank" href="<?= CAOS_ANALYTICS_SITE_URL; ?>/wordpress-plugins/optimize-analytics-wordpress/"><?php _e('Click here', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a> <?php _e('for a more comprehensive guide.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
            </div>
            <div class="welcome-panel-column" style="width: 32%;">
                <h3><?php _e('Get a Perfect Score on Pagespeed & Pingdom!', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></h3>
                <p>
                    <strong><?php _e('Leverage your browser cache</strong> and <strong>lower pageload times</strong> by hosting your Google Fonts locally with', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                        <a href="<?= CAOS_ANALYTICS_SITE_URL; ?>/wordpress/host-google-fonts-locally/" target="_blank"><?php _e('CAOS for Webfonts', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>.
                </p>
                <p>
                    <a target="_blank" href="https://wordpress.org/plugins/host-webfonts-local/"><?php _e('Download now', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>
                </p>
                <h3><?php _e('Suggested Reading', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></h3>
                <ul>
                    <li class="welcome-icon dashicons-before dashicons-analytics">
                        <a href="<?= CAOS_ANALYTICS_SITE_URL; ?>/wordpress/leverage-browser-caching-host-analytics-local-monster-insights/" target="_blank"><?php _e('Configure CAOS with Monster Insights and WooCommerce', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>
                    </li>
                    <li class="welcome-icon dashicons-before dashicons-welcome-learn-more">
                        <a href="<?= CAOS_ANALYTICS_SITE_URL; ?>/wordpress/gdpr-compliance-google-analytics/" target="_blank"><?php _e('Complete Guide to GDPR Compliance with CAOS', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>
                    </li>
                    <li class="welcome-icon dashicons-before dashicons-dashboard">
                        <a href="<?= CAOS_ANALYTICS_SITE_URL; ?>/wordpress/quick-guide-pagespeed-insights-pingdom/" target="_blank"><?php _e('Speed-up your site: get 100/100 on Pagespeed Insights in 30 minutes!', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>
                    </li>
                </ul>
            </div>
            <div class="welcome-panel-column welcome-panel-last" style="width: 33%;">
                <h3>
                    <?php _e('Need Help?', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </h3>
                <p>
                    <?php _e('Thank you for using CAOS for Analytics.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
                <p>
                    <?php _e('I am convinced that knowledge should be free. That\'s why I will never charge you for the plugins I create and I will help you to succeed in your projects through the <a href="https://daan.dev/how-to/" target="_blank">tutorials</a> on my blog.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
                <p>
                    <?php _e("However, my time is just as valuable as yours. Consider supporting me by either <a href='" . CAOS_ANALYTICS_SITE_URL . "/donate' target='_blank'>donating</a> or leaving a <a target='_blank' href='https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post'>5-star review</a> on Wordpress.org.", CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
                <p>
                    <?php _e('If you\'re running into any issues, please make sure you\'ve read <a href="' . CAOS_ANALYTICS_SITE_URL . '/wordpress-plugins/optimize-analytics-wordpress/" target="_blank">the manual</a> thoroughly. Visit the <a href="https://wordpress.org/plugins/host-analyticsjs-local/#description" target="_blank">FAQ</a> and <a href="https://wordpress.org/support/plugin/host-analyticsjs-local">Support Forum</a> to see if your question has already been answered. If not, ask a question on the Support Forum.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
                <p>
                    <a target="_blank" class="button button-primary button-hero" href="<?= CAOS_ANALYTICS_SITE_URL; ?>/donate/"><span class="dashicons-before dashicons-heart"> <?php _e('Donate', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></span></a>
                    <a target="_blank" class="button button-secondary button-hero" href="https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post"><span class="dashicons-before dashicons-star-filled"> <?php _e('Review', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></span></a>
                    <a target="_blank" class="button button-secondary button-hero" href="https://twitter.com/Dan0sz"><span class="dashicons-before dashicons-twitter"> <?php _e('Follow', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></span></a>
                </p>
            </div>
        </div>
    </div>
</div>
