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

use CAOS\Admin\Settings;
use CAOS\Plugin as CAOS;

class Advanced extends Builder {

	/**
	 * Settings_Advanced constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->title = __( 'Advanced Settings', 'host-analyticsjs-local' );

		// Open
		add_action( 'caos_advanced_settings_content', [ $this, 'do_title' ], 10 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_description' ], 15 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_before' ], 20 );

		// Settings
		add_action( 'caos_advanced_settings_content', [ $this, 'do_compatibility_mode' ], 30 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_remote_js_file' ], 40 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_cache_dir' ], 50 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_cdn_url' ], 60 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_cookieless_analytics_promo' ], 110 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_cloaked_affiliate_links_tracking_promo' ], 120 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_session_expiry' ], 130 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_site_speed_sample_rate' ], 140 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_advertising_features' ], 150 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_uninstall_settings' ], 220 );

		// Close
		add_action( 'caos_advanced_settings_content', [ $this, 'do_after' ], 250 );

		parent::__construct();
	}

	/**
	 * Description
	 */
	public function do_description() {
		?>
		<p>
		</p>
		<?php
	}

	/**
	 * Enable Compatibility Mode
	 *
	 * @since v4.3.0 Compatibility mode is now a checkbox, because it parses the HTML.
	 */
	public function do_compatibility_mode() {
		$this->do_checkbox(
			__( 'Compatibility Mode', 'host-analyticsjs-local' ),
			Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE,
			CAOS::get( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE, '' ),
			__( 'Check this option to use CAOS with any other Google Analytics plugin. Any reference to <code>google-analytics.com/analytics.js</code> and <code>googletagmanager.com/gtag/js</code> on your site will be replaced with a local copy. <strong>Warning!</strong> Please make sure that CAOS\' <strong>Basic Settings</strong> and <strong>Download File</strong> settings match your Google Analytics plugin\'s configuration.', 'host-analyticsjs-local' ),
			CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) === 'plausible'
		);
	}

	/**
	 * Which file to download?
	 */
	public function do_remote_js_file() {
		$this->do_select(
			__( 'Download File', 'host-analyticsjs-local' ),
			Settings::CAOS_ADV_SETTING_JS_FILE,
			Settings::CAOS_ADMIN_JS_FILE_OPTIONS,
			CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE, 'analytics.js' ),
			sprintf( __( '<code>analytics.js</code> is recommended in most situations. <code>gtag.js</code> is a wrapper for <code>analytics.js</code> and should only be used if you\'re using other Google services or want to enable dual tracking with GA4. Both files are hosted locally when this option is selected! GA v4 (beta) users should choose <code>gtag.js</code> (V4 API). %1$sI don\'t know what to choose%2$s.', 'host-analyticsjs-local' ), '<a href="' . CAOS_SITE_URL . '/wordpress/difference-analyics-gtag-ga-js/' . $this->utm_tags . '" target="_blank">', '</a>' )
		);
	}

	/**
	 * Save .js file to...
	 */
	public function do_cache_dir() {
		$this->do_text(
			sprintf( __( 'Cache directory for %s', 'host-analyticsjs-local' ), CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE ) ),
			Settings::CAOS_ADV_SETTING_CACHE_DIR,
			__( 'e.g. /uploads/caos/', 'host-analyticsjs-local' ),
			CAOS::get( Settings::CAOS_ADV_SETTING_CACHE_DIR, '/uploads/caos/' ),
			__( "Change the path where the Analytics-file is cached inside WordPress' content directory (usually <code>wp-content</code>). Defaults to <code>/uploads/caos/</code>.", 'host-analyticsjs-local' )
		);
	}

	/**
	 * Serve from a CDN?
	 */
	public function do_cdn_url() {
		$this->do_text(
			__( 'Serve from CDN', 'host-analyticsjs-local' ),
			Settings::CAOS_ADV_SETTING_CDN_URL,
			__( 'e.g. cdn.mydomain.com', 'host-analyticsjs-local' ),
			CAOS::get( Settings::CAOS_ADV_SETTING_CDN_URL, '' ),
			sprintf( __( 'If you\'re using a CDN, enter the URL here to serve <code>%s</code> from your CDN.', 'host-analyticsjs-local' ), Settings::CAOS_ADV_SETTING_JS_FILE )
		);
	}

	/**
	 * Add Cookieless Analytics option.
	 *
	 * @return void
	 */
	public function do_cookieless_analytics_promo() {
		$description = __( 'When enabled, Google Analytics (except V4) will not create any (<em>third-party</em>) cookies and the user ID known to Google will be changed with a new, random user ID. This adds a layer of privacy for your visitors, increases GDPR Compliance and effectively removes the necessity for cookie consent. Since GA4 only creates <em>first-party</em> (which are GDPR compliant) cookies, enabling this option for GA4 will generate a random user ID for each visitor of <u>your</u> website to ensure that tracking across different websites/platforms is no longer possible.', 'host-analyticsjs-local' ) . ' ' . $this->promo;

		$this->do_checkbox(
			__( 'Enable Cookieless Analytics (Pro)', 'host-analyticsjs-local' ),
			'cookieless_analytics',
			defined( 'CAOS_PRO_ACTIVE' ) && CAOS::get( 'cookieless_analytics' ),
			$description,
			! defined( 'CAOS_PRO_ACTIVE' ) || ( defined( 'CAOS_PRO_ACTIVE' ) && ( CAOS::get( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE ) || CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) !== 'google_analytics' ) ),
			true,
			true,
			__( 'Disable <strong>Compatibility Mode</strong> to enable it.', 'host-webfonts-local' )
		);
	}

	/**
	 * Add Cloacked Affiliate Links Tracking promo.
	 *
	 * @return void
	 */
	public function do_cloaked_affiliate_links_tracking_promo() {
		?>
		<tr>
			<th><?php echo __( 'Track Cloaked Affiliate Links (Pro)', 'host-analyticsjs-local' ); ?></th>
			<td>
				<?php
				$disabled = ! defined( 'CAOS_PRO_ACTIVE' )
				|| ( defined( 'CAOS_PRO_ACTIVE' ) && ( CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) !== 'google_analytics'
				|| CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) === 'minimal'
				|| CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) === 'minimal_ga4' ) );
				?>
				<?php if ( $disabled && $this->display_reason( true ) ) : ?>
					<p class="description option-disabled">
						<?php echo sprintf( __( 'This option is disabled. %s', 'host-webfonts-local' ), __( 'Enable it by setting <strong>Service Provider</strong> to Google Analytics and/or set <strong>Tracking Code</strong> to Default or Asynchronous.', 'host-webfonts-local' ) ); ?>
					</p>
				<?php else : ?>
					<table class="track-cloaked-affiliate-links">
						<tr>
							<th><?php echo __( 'Path', 'host-analyticsjs-local' ); ?></th>
							<th><?php echo __( 'Event Category', 'host-analyticsjs-local' ); ?></th>
							<th></th>
						</tr>
						<?php
						$affiliate_links = defined( 'CAOS_PRO_ACTIVE' ) && ! empty( CAOS::get( 'cloaked_affiliate_links' ) ) ? CAOS::get( 'cloaked_affiliate_links' ) : [
							0 => [
								'path'     => '',
								'category' => '',
							],
						];

						foreach ( $affiliate_links as $key => $properties ) :
							?>
							<tr id="affiliate-link-row-<?php echo esc_attr( $key ); ?>">
								<?php foreach ( $properties as $prop_key => $prop_value ) : ?>
									<td id="affiliate-link-<?php echo esc_attr( $prop_key ); ?>-<?php echo esc_attr( $key ); ?>">
										<input type="text" <?php echo $disabled ? 'disabled' : ''; ?> class="affiliate-link-<?php echo esc_attr( $prop_key ); ?>" name="cloaked_affiliate_links[<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $prop_key ); ?>]" value="<?php echo esc_attr( $prop_value ); ?>" />
									</td>
								<?php endforeach; ?>
								<td>
									<span class="dashicons dashicons-remove affiliate-link-remove" data-row="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $disabled ) ? 'style="opacity: 15%;"' : ''; ?>></span>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
					<p>
						<input type="button" <?php echo esc_attr( $disabled ); ?> class="button button-secondary" id="affiliate-link-add" value="<?php echo esc_attr__( 'Add Link Path', 'host-analyticsjs-local' ); ?>" />
					</p>
					<p class="description">
						<?php echo defined( 'CAOS_PRO_ACTIVE' ) && CAOS::get( 'stealth_mode' ) ? __( 'If no events are registered in Google Analytics, your server might be too slow to send them in time. Please disable Stealth Mode if that\'s the case.', 'host-analyticsjs-local' ) : ''; ?>
						<?php echo __( 'Send an event to Google Analytics whenever a Cloaked Affiliate Link is clicked. An event with the configured <strong>Event Category</strong> is sent to Google Analytics whenever a link containing the <strong>Path</strong> value is clicked. The <strong>Event Label</strong> will be the URL of the link. Depending on your server\'s capacity, this might not work properly with Stealth Mode enabled.', 'host-analyticsjs-local' ) . ' ' . $this->promo; ?>
					</p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Cookie expiry period (days)
	 */
	public function do_session_expiry() {
		$this->do_number(
			__( 'Session expiry period (days)', 'host-analyticsjs-local' ),
			Settings::CAOS_ADV_SETTING_GA_SESSION_EXPIRY_DAYS,
			CAOS::get( Settings::CAOS_ADV_SETTING_GA_SESSION_EXPIRY_DAYS, 30 ),
			__( 'The number of days when the user session will automatically expire. When using <strong>Cookieless Analytics</strong> the ClientID will be refreshed after this amount of days. (Default: 30)', 'host-analyticsjs-local' ),
			0,
			CAOS::get( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE ),
			__( 'Disable <strong>Compatibility Mode</strong> to enable it.', 'host-webfonts-local' )
		);
	}

	/**
	 * Site Speed Sample Rate (%)
	 *
	 * @return void
	 */
	public function do_site_speed_sample_rate() {
		$this->do_number(
			__( 'Site Speed Sample Rate (%)', 'host-analyticsjs-local' ),
			Settings::CAOS_ADV_SETTING_SITE_SPEED_SAMPLE_RATE,
			CAOS::get( Settings::CAOS_ADV_SETTING_SITE_SPEED_SAMPLE_RATE ),
			__( 'This setting determines how often site speed beacons will be sent. Defaults to 1%. For low-traffic sites it is advised to set this to 50 or higher.', 'host-analyticsjs-local' ),
			0,
			CAOS::get( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE ) || CAOS::uses_ga4(),
			CAOS::uses_ga4() ? __( 'Provide a Google Analytics V3 (UA-) <strong>Tracking ID</strong> to enable it.', 'host-webfonts-local' ) : __( 'Disable <strong>Compatibility Mode</strong> to enable it.', 'host-webfonts-local' )
		);
	}

	/**
	 * Disable all advertising features functionality
	 */
	public function do_advertising_features() {
		$this->do_checkbox(
			__( 'Disable Advertising Features', 'host-analyticsjs-local' ),
			Settings::CAOS_ADV_SETTING_DISABLE_ADS_FEATURES,
			CAOS::get( Settings::CAOS_ADV_SETTING_DISABLE_ADS_FEATURES ),
			sprintf( __( 'Override and disable all advertising reporting and remarketing features established in Google Analytics. <a href="%s" target="_blank">What\'s this?</a>', 'host-analyticsjs-local' ), 'https://support.google.com/analytics/answer/9050852?hl=en' ),
			CAOS::get( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE ),
			true,
			false,
			__( 'Disable <strong>Compatibility Mode</strong> to enable it.', 'host-webfonts-local' )
		);
	}

	/**
	 * Remove settings at uninstall
	 */
	public function do_uninstall_settings() {
		$this->do_checkbox(
			__( 'Remove settings at Uninstall', 'host-analyticsjs-local' ),
			Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS,
			CAOS::get( Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS ),
			'<strong>' . __( 'Warning!', 'host-analytics-local' ) . '</strong> ' . __( 'This will remove the settings from the database upon plugin deletion!', 'host-analyticsjs-local' )
		);
	}
}
