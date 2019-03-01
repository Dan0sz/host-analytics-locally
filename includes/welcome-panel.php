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
            <div class="welcome-panel-column">
                <h3><?php _e('Quickstart', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></h3>
                <ul>
                    <li class="welcome-icon dashicons-before dashicons-analytics"><?php _e('Enter your Analytics Tracking-ID', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></li>
                    <li class="welcome-icon dashicons-before dashicons-admin-tools"><?php _e('Load the script in header/footer', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></li>
                    <li class="welcome-icon dashicons-before dashicons-smiley"><?php _e("Click 'Save Changes' and you're done!", CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></li>
                </ul>
                <p>
                    <a target="_blank" href="<?php echo CAOS_ANALYTICS_SITE_URL; ?>/wordpress-plugins/optimize-analytics-wordpress/"><?php _e('Click here', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a> <?php _e('for a more comprehensive guide.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                </p>
                <h3><?php _e('Suggested Reading', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></h3>
                <ul>
                    <li class="welcome-icon dashicons-before dashicons-analytics">
                        <a href="<?php echo CAOS_ANALYTICS_SITE_URL; ?>/wordpress/leverage-browser-caching-host-analytics-local-monster-insights/" target="_blank"><?php _e('Configure CAOS with Monster Insights and WooCommerce', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>
                    </li>
                    <li class="welcome-icon dashicons-before dashicons-welcome-learn-more">
                        <a href="<?php echo CAOS_ANALYTICS_SITE_URL; ?>/wordpress/gdpr-compliance-google-analytics/" target="_blank"><?php _e('Complete Guide to GDPR Compliance with CAOS', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>
                    </li>
                    <li class="welcome-icon dashicons-before dashicons-dashboard">
                        <a href="<?php echo CAOS_ANALYTICS_SITE_URL; ?>/wordpress/quick-guide-pagespeed-insights-pingdom/" target="_blank"><?php _e('Speed-up your site: get 100/100 on Pagespeed Insights in 30 minutes!', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>
                    </li>
                </ul>
            </div>
            <div class="welcome-panel-column">
                <h3><?php _e('Get a Perfect Score on Pagespeed & Pingdom!', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></h3>
                <p>
                    <strong><?php _e('Leverage your browser cache</strong> and <strong>lower pageload times</strong> by hosting your Google Fonts locally with', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
                        <a href="<?php echo CAOS_ANALYTICS_SITE_URL; ?>/wordpress/host-google-fonts-locally/" target="_blank"><?php _e('CAOS for Webfonts', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>.
                </p>
                <p>
                    <a target="_blank" href="https://wordpress.org/plugins/host-webfonts-local/"><?php _e('Download now', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>
                </p>
                <h3><?php _e('Need help?', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></h3>
                <p><?php _e('First make sure you read the manual thoroughly.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></p>
                <p><?php _e('Still running into issues? Visit the <a href="https://wordpress.org/plugins/host-analyticsjs-local/#description" target="_blank">FAQ</a> and <a href="https://wordpress.org/support/plugin/host-analyticsjs-local">Support Forum</a> to see if your question has already been answered.</p>', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?>
					<?php _e('<p>If not, ask a question on the Support Forum or', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?> <a href="<?php echo CAOS_ANALYTICS_SITE_URL; ?>/contact/" target="_blank"><?php _e('contact me', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></a>.</p>
            </div>
            <div class="welcome-panel-column welcome-panel-last">
                <h3><?php _e('Support CAOS!', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></h3>
                <p><?php _e('I create simple, but useful solutions that easily optimize your website for Pagespeed Insights and Pingdom.', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></p>
                <p><?php _e('I believe these solutions should be free, but if you appreciate my work, please consider donating!', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></p>
                <a target="_blank" class="button button-primary button-hero" href="<?php echo CAOS_ANALYTICS_SITE_URL; ?>/donate/"><span class="dashicons-before dashicons-heart"> <?php _e('Donate', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></span></a>
                <a target="_blank" class="button button-primary button-hero" style="background-color: #00aced; box-shadow: 0 2px 0 #0084b4; border-color: #0084b4 #0084b4 #0084b4;" href="https://twitter.com/Dan0sz"><span class="dashicons-before dashicons-twitter"> <?php _e('Follow me!', CAOS_ANALYTICS_TRANSLATE_DOMAIN); ?></span></a>
            </div>
        </div>
    </div>
</div>
