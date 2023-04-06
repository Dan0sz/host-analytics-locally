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
 * @copyright: (c) 2021 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */
namespace CAOS\Admin\Settings;

class Help extends Builder {
	/**
	 * Settings_Help constructor.
	 */
	public function __construct() {
		$this->title = __( 'Help & Documentation', 'host-analyticsjs-local' );

		// Title
		add_filter( 'caos_help_content', [ $this, 'do_title' ], 10 );

		// Content
		add_filter( 'caos_help_content', [ $this, 'do_content' ], 20 );
	}

	public function do_content() {
		$utm_tags  = '?utm_source=caos&utm_medium=plugin&utm_campaign=support_tab';
		$tweet_url = 'https://twitter.com/intent/tweet?text=I+am+using+CAOS+to+speed+up+Google+Analytics+for+@WordPress!+Try+it+for+yourself:&via=Dan0sz&hashtags=GoogleAnalytics,WordPress,Pagespeed,Insights&url=https://wordpress.org/plugins/host-analyticsjs-local/';
		?>
		<div class="postbox">
			<div class="content">
				<h2><?php echo sprintf( __( 'Thank you for using %s!', 'host-analyticsjs-local' ), apply_filters( 'caos_settings_page_title', 'CAOS' ) ); ?></h2>
				<p class="about-description">
					<?php echo sprintf( __( 'Need help configuring %s? Please refer to the links below to get you started.', 'host-analyticsjs-local' ), apply_filters( 'caos_settings_page_title', 'CAOS' ) ); ?>
				</p>
				<div class="column-container">
					<div class="column">
						<h3>
							<?php _e( 'Need Help?', 'host-analyticsjs-local' ); ?>
						</h3>
						<ul>
							<li><a target="_blank" href="<?php echo apply_filters( 'caos_settings_help_quick_start', 'https://daan.dev/docs/caos/quick-start-caos/' ); ?>"><i class="dashicons dashicons-controls-play"></i><?php echo __( 'Quick Start Guide', 'host-analyticsjs-local' ); ?></a></li>
							<li><a target="_blank" href="<?php echo apply_filters( 'caos_settings_help_user_manual', 'https://daan.dev/docs/caos/?collection_id=6171c32112c07c18afddfcce' ); ?>"><i class="dashicons dashicons-text-page"></i><?php echo __( 'User Manual', 'host-analyticsjs-local' ); ?></a></li>
							<li><a target="_blank" href="<?php echo apply_filters( 'caos_settings_help_faq_link', 'https://daan.dev/docs/caos-troubleshooting/?collection_id=6171c32112c07c18afddfcce' ); ?>"><i class="dashicons dashicons-editor-help"></i><?php echo __( 'FAQ & Troubleshooting', 'host-analyticsjs-local' ); ?></a></li>
							<li><a target="_blank" href="<?php echo apply_filters( 'caos_settings_help_support_link', 'https://daan.dev/contact/' ); ?>"><i class="dashicons dashicons-bell"></i><?php echo __( 'Get Support', 'host-analyticsjs-local' ); ?></a></li>
						</ul>
					</div>
					<div class="column">
						<h3><?php echo sprintf( __( 'Support %s & Spread the Word!', 'host-analyticsjs-local' ), apply_filters( 'caos_settings_page_title', 'CAOS' ) ); ?></h3>
						<ul>
							<li><a target="_blank" href="<?php echo apply_filters( 'caos_settings_help_review_link', 'https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post' ); ?>"><i class="dashicons dashicons-star-filled"></i><?php echo __( 'Write a 5-star Review or,', 'host-analyticsjs-local' ); ?></a></li>
							<li><a target="_blank" href="<?php echo $tweet_url; ?>"><i class="dashicons dashicons-twitter"></i><?php echo __( 'Tweet about it!', 'host-analyticsjs-local' ); ?></a></li>
						</ul>
					</div>
					<div class="column last">
						<h3 class="signature"><?php echo sprintf( __( 'Coded with %s by', 'host-analyticsjs-local' ), '❤️' ); ?> </h3>
						<p class="signature">
							<a target="_blank" title="<?php echo __( 'Visit Daan.dev', 'host-analyticsjs-local' ); ?>" href="https://daan.dev/wordpress-plugins/"><img class="signature-image" alt="<?php echo __( 'Visit Daan.dev', 'host-analyticsjs-local' ); ?>" src="<?php echo plugin_dir_url( CAOS_PLUGIN_FILE ) . 'assets/images/logo.png'; ?>" /></a>
						</p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
