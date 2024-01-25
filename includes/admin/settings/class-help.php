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
 * @url      : https://daan.dev/wordpress/caos/
 * @copyright: © 2021 - 2024 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

class CAOS_Admin_Settings_Help extends CAOS_Admin_Settings_Builder {

	/**
	 * CAOS_Admin_Settings_Help constructor.
	 */
	public function __construct() {
		$this->title = __( 'Help & Documentation', $this->plugin_text_domain );

		// Title
		add_action( 'caos_help_content', [ $this, 'do_title' ], 10 );

		// Content
		add_action( 'caos_help_content', [ $this, 'do_content' ], 20 );
	}

	public function do_content() {
		$utmTags  = '?utm_source=caos&utm_medium=plugin&utm_campaign=support_tab';
		$tweetUrl = 'https://twitter.com/intent/tweet?text=I+am+using+CAOS+to+speed+up+Google+Analytics+for+@WordPress!+Try+it+for+yourself:&via=Dan0sz&hashtags=GoogleAnalytics,WordPress,Pagespeed,Insights&url=https://wordpress.org/plugins/host-analyticsjs-local/';
		?>
		<div class="postbox">
			<div class="content">
				<h2><?php echo sprintf( __( 'Thank you for using %s!', $this->plugin_text_domain ), apply_filters( 'caos_settings_page_title', 'CAOS' ) ); ?></h2>
				<p class="about-description">
					<?php echo sprintf( __( 'Need help configuring %s? Please refer to the links below to get you started.', $this->plugin_text_domain ), apply_filters( 'caos_settings_page_title', 'CAOS' ) ); ?>
				</p>
				<div class="column-container">
					<div class="column">
						<h3>
							<?php _e( 'Need Help?', $this->plugin_text_domain ); ?>
						</h3>
						<ul>
							<li><a target="_blank" href="<?php echo apply_filters( 'caos_settings_help_quick_start', 'https://daan.dev/docs/caos/quick-start-caos/' ); ?>"><i class="dashicons dashicons-controls-play"></i><?php echo __( 'Quick Start Guide', $this->plugin_text_domain ); ?></a></li>
							<li><a target="_blank" href="<?php echo apply_filters( 'caos_settings_help_user_manual', 'https://daan.dev/docs/caos/?collection_id=6171c32112c07c18afddfcce' ); ?>"><i class="dashicons dashicons-text-page"></i><?php echo __( 'User Manual', $this->plugin_text_domain ); ?></a></li>
							<li><a target="_blank" href="<?php echo apply_filters( 'caos_settings_help_faq_link', 'https://daan.dev/docs/caos-troubleshooting/?collection_id=6171c32112c07c18afddfcce' ); ?>"><i class="dashicons dashicons-editor-help"></i><?php echo __( 'FAQ & Troubleshooting', $this->plugin_text_domain ); ?></a></li>
							<li><a target="_blank" href="<?php echo apply_filters( 'caos_settings_help_support_link', 'https://daan.dev/contact/' ); ?>"><i class="dashicons dashicons-bell"></i><?php echo __( 'Get Support', $this->plugin_text_domain ); ?></a></li>
						</ul>
					</div>
					<div class="column">
						<h3><?php echo sprintf( __( 'Support %s & Spread the Word!', $this->plugin_text_domain ), apply_filters( 'caos_settings_page_title', 'CAOS' ) ); ?></h3>
						<ul>
							<li><a target="_blank" href="<?php echo apply_filters( 'caos_settings_help_review_link', 'https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post' ); ?>"><i class="dashicons dashicons-star-filled"></i><?php echo __( 'Write a 5-star Review or,', $this->plugin_text_domain ); ?></a></li>
							<li><a target="_blank" href="<?php echo $tweetUrl; ?>"><i class="dashicons dashicons-twitter"></i><?php echo __( 'Tweet about it!', $this->plugin_text_domain ); ?></a></li>
						</ul>
					</div>
					<div class="column last">
						<h3 class="signature"><?php echo sprintf( __( 'Coded with %s by', $this->plugin_text_domain ), '❤️' ); ?> </h3>
						<p class="signature">
							<a target="_blank" title="<?php echo __( 'Visit Daan.dev', $this->plugin_text_domain ); ?>" href="https://daan.dev/wordpress-plugins/"><img class="signature-image" alt="<?php echo __( 'Visit Daan.dev', $this->plugin_text_domain ); ?>" src="<?php echo plugin_dir_url( CAOS_PLUGIN_FILE ) . 'assets/images/logo.png'; ?>" /></a>
						</p>
					</div>
				</div>
			</div>
		</div>
		</div>
		<?php
	}
}
