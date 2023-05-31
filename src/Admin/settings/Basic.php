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

class Basic extends Builder {

	/**
	 * Basic constructor.
	 */
	public function __construct() {
		$this->title = __( 'Basic Settings', 'host-analyticsjs-local' );

		// Open
		add_action( 'caos_basic_settings_content', [ $this, 'do_title' ], 1 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_before' ], 2 );

		// Settings
		add_action( 'caos_basic_settings_content', [ $this, 'do_service_provider' ], 10 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_track_admin' ], 12 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_domain_name' ], 22 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_tracking_id' ], 32 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_dual_tracking' ], 34 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_ga4_measurement_id' ], 36 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_gdpr_compliance_promo' ], 51 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_allow_tracking' ], 52 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_cookie_name' ], 54 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_cookie_value' ], 56 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_tracking_code' ], 58 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_anonymize_ip_mode' ], 60 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_script_position' ], 61 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_add_manually' ], 62 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_adjusted_bounce_rate' ], 65 );

		// Close
		add_action( 'caos_basic_settings_content', [ $this, 'do_after' ], 100 );

		parent::__construct();
	}

	/**
	 * Service Provider
	 *
	 * @return void
	 */
	public function do_service_provider() {
		$this->do_radio(
			__( 'Service Provider', 'host-analyticsjs-local' ),
			Settings::CAOS_ADMIN_SERVICE_PROVIDER_OPTION,
			Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER,
			CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER, 'google-analytics' ),
			sprintf( __( 'Looking for a simple, privacy and GDPR friendly alternative to Google Analytics? <a href="%s" target="_blank">Try Plausible Analytics free for 30 days</a>!', 'host-analyticsjs-local' ), 'https://plausible.io/register' )
		);
	}

	/**
	 * Do Domain Name
	 *
	 * @return void
	 */
	public function do_domain_name() {
		$this->do_text(
			__( 'Domain Name', 'host-analyticsjs-local' ),
			Settings::CAOS_BASIC_SETTING_DOMAIN_NAME,
			'',
			CAOS::get( Settings::CAOS_BASIC_SETTING_DOMAIN_NAME, str_replace( [ 'https://', 'http://' ], '', get_home_url() ) ),
			__( '', 'host-analyticsjs-local' ),
			true,
			CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) === 'google_analytics',
			__( 'Enable it by setting <strong>Service Provider</strong> to Plausible Analytics.', 'host-webfonts-local' )
		);
	}

	/**
	 * Google Analytics Tracking ID
	 */
	public function do_tracking_id() {
		$translated_tracking_id = _x( 'UA-123456789', 'Define a different Tracking ID for this site.', 'host-analyticsjs-local' );

		$this->do_text(
			__( 'Google Analytics Tracking ID', 'host-analyticsjs-local' ),
			Settings::CAOS_BASIC_SETTING_TRACKING_ID,
			__( 'e.g. UA-1234567-12', 'host-analyticsjs-local' ),
			CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_ID, $translated_tracking_id !== 'UA-123456789' ? $translated_tracking_id : '' ),
			__( 'Enter your Tracking ID, e.g. UA-1234567-89 (v3 API) or G-123ABC789 (v4 API). Enter a V3 Tracking ID if you\'d like to enable Dual Tracking with GA V4.', 'host-analyticsjs-local' ),
			true,
			CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) === 'plausible',
			__( 'Enable it by setting <strong>Service Provider</strong> to Google Analytics.', 'host-webfonts-local' )
		);
	}

	/**
	 * Enable Dual Tracking
	 *
	 * @return void
	 */
	public function do_dual_tracking() {
		$this->do_checkbox(
			__( 'Enable Dual Tracking', 'host-analyticsjs-local' ),
			Settings::CAOS_BASIC_SETTING_DUAL_TRACKING,
			CAOS::get( Settings::CAOS_BASIC_SETTING_DUAL_TRACKING ),
			'Enable dual tracking to send hits and events to both your UA and GA4 properties.',
			false,
			strpos( CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_ID ), 'UA-' ) === 0
		);
	}

	/**
	 * Google Analytics Dual Tracking ID
	 *
	 * @return void
	 */
	public function do_ga4_measurement_id() {
		$this->do_text(
			__( 'GA4 Measurement ID', 'host-analyticsjs-local' ),
			Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID,
			__( 'e.g. G-123ABC456', 'host-analyticsjs-local' ),
			CAOS::get( Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID ),
			__( 'Enter a GA4 Measurement ID to enable dual tracking, e.g. G-123ABC789.', 'host-analyticsjs-local' ),
			CAOS::get( Settings::CAOS_BASIC_SETTING_DUAL_TRACKING ) === 'on' && strpos( CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_ID ), 'UA-' ) === 0
		);
	}

	/**
	 * Track logged in Administrators
	 */
	public function do_track_admin() {
		$this->do_checkbox(
			__( 'Track logged in Administrators', 'host-analyticsjs-local' ),
			Settings::CAOS_BASIC_SETTING_TRACK_ADMIN,
			CAOS::get( Settings::CAOS_BASIC_SETTING_TRACK_ADMIN ),
			'<strong>' . __( 'Warning!', 'host-analyticsjs-local' ) . '</strong> ' . __( 'This will track all your traffic as a logged in user. (For testing/development purposes.)', 'host-analyticsjs-local' ),
			false
		);
	}

	/**
	 * GDPR Compliance
	 */
	public function do_gdpr_compliance_promo() {
		$this->do_checkbox(
			__( 'Increase GDPR Compliance (Pro)', 'host-analyticsjs-local' ),
			'gdpr',
			defined( 'CAOS_PRO_ACTIVE' ) ? CAOS::get( 'gdpr', false ) : false,
			sprintf( __( 'Remove any data that can be used to identify a person (i.e. personal data, e.g. IP address, User Agent, Location, etc.) to use Google Analytics in compliance with the GDPR. Be warned that enabling this setting <u>doesn\'t</u> guarantee GDPR compliance of your site, e.g. any parameters that enable (internal) routing (e.g. UTM tags) must be removed from any URLs on your site. <A href="%s" target="_blank">Read more</a>', 'host-analyticsjs-local' ), 'https://www.cnil.fr/en/google-analytics-and-data-transfers-how-make-your-analytics-tool-compliant-gdpr' ) . ' ' . $this->promo,
			! defined( 'CAOS_PRO_ACTIVE' ) || CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) === 'plausible' || CAOS::get( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE ),
			true,
			true,
			__( 'Enable it by setting <strong>Service Provider</strong> to Google Analytics and/or disable <strong>Compatibility Mode</strong>.', 'host-webfonts-local' )
		);
	}

	/**
	 * Allow tracking...
	 */
	public function do_allow_tracking() {
		$this->do_radio(
			__( 'Allow tracking...', 'host-analyticsjs-local' ),
			Settings::CAOS_ADMIN_ALLOW_TRACKING_OPTIONS,
			Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING,
			CAOS::get( Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ),
			__( 'Configure CAOS to "listen" to your Cookie Notice plugin.', 'host-analyticsjs-local' ) . ' ' . __( 'Choose \'Always\' to use Google Analytics without a Cookie Notice.', 'host-analyticsjs-local' ) . ' ' . sprintf( __( '<a href="%s" target="_blank">Consent Mode</a> is used when <strong>Consent mode</strong> is selected or a Google Analytics 4 (starting with G-) Measurement ID is configured in the <strong>Google Analytics Tracking ID</strong> field.', 'host-analyticsjs-local' ), 'https://support.google.com/analytics/answer/9976101?hl=en' ),
			CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) === 'plausible' || CAOS::get( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE ),
			false,
			__( 'Enable it by setting <strong>Service Provider</strong> to Google Analytics and/or disable <strong>Compatibility Mode</strong>.', 'host-webfonts-local' )
		);
	}

	/**
	 * Cookie name
	 */
	public function do_cookie_name() {
		$this->do_text(
			__( 'Cookie Name', 'host-analyticsjs-local' ),
			Settings::CAOS_BASIC_SETTING_COOKIE_NAME,
			__( 'e.g. cookie_accepted', 'host-analyticsjs-local' ),
			CAOS::get( Settings::CAOS_BASIC_SETTING_COOKIE_NAME ),
			__( 'The cookie name set by your Cookie Notice plugin when user accepts.', 'host-analyticsjs-local' ),
			CAOS::get( Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING )
		);
	}

	/**
	 * Cookie value
	 */
	public function do_cookie_value() {
		$this->do_text(
			__( 'Cookie Value', 'host-analyticsjs-local' ),
			Settings::CAOS_BASIC_SETTING_COOKIE_VALUE,
			__( 'e.g. true', 'host-analyticsjs-local' ),
			CAOS::get( Settings::CAOS_BASIC_SETTING_COOKIE_VALUE ),
			__( 'The value of the above specified cookie set by your Cookie Notice when user accepts.', 'host-analyticsjs-local' ),
			CAOS::get( Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) === 'cookie_has_value'
		);
	}

	/**
	 * Snippet type
	 */
	public function do_tracking_code() {
		$this->do_select(
			__( 'Tracking Code', 'host-analyticsjs-local' ),
			Settings::CAOS_BASIC_SETTING_TRACKING_CODE,
			Settings::CAOS_ADMIN_TRACKING_CODE_OPTIONS,
			CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_CODE ),
			__( 'Should we use the default or the asynchronous tracking code? Minimal Analytics is fastest, but supports only basic features i.e. pageviews and events.', 'host-analyticsjs-local' ) . ' ' . sprintf( '<a href="%s" target="_blank">', 'https://daan.dev/docs/caos/basic-settings/' . $this->utm_tags ) . __( 'Read more', 'host-analyticsjs-local' ) . '</a>',
			CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) === 'plausible' || CAOS::get( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE ),
			__( 'Enable it by setting <strong>Service Provider</strong> to Google Analytics and/or disable <strong>Compatibility Mode</strong>.', 'host-webfonts-local' )
		);
	}

	/**
	 * Render Anonymize IP Mode option and example.
	 */
	public function do_anonymize_ip_mode() {
		$aip_mode     = CAOS::get( Settings::CAOS_BASIC_SETTING_ANONYMIZE_IP_MODE );
		$aip_template = '<span class="caos-aip-example"><span class="octet">%s</span>.<span class="octet">%s</span>.<span class="octet">%s</span>.<span class="octet">%s</span></span>';

		switch ( $aip_mode ) {
			case 'one':
				$aip_example = sprintf( $aip_template, '192', '168', '178', '0' );
				break;
			case 'two':
				$aip_example = sprintf( $aip_template, '192', '168', '0', '0' );
				break;
			case 'all':
				$aip_example = sprintf( $aip_template, '1', '0', '0', '0' );
				break;
			default:
				$aip_example = sprintf( $aip_template, '192', '168', '178', '1' );
		}

		$this->do_radio(
			__( 'Anonymize IP Mode', 'host-analyticsjs-local' ),
			Settings::CAOS_ADMIN_ANONYMIZE_IP_MODE_OPTIONS,
			Settings::CAOS_BASIC_SETTING_ANONYMIZE_IP_MODE,
			$aip_mode,
			sprintf( __( '<strong>One octet</strong> enables the <code>anonymize_ip</code> parameter, provided by Google. <strong>Important:</strong> Due to <a href="%1$s">recent rulings</a>, anonymizing the last octet of the IP address is no longer sufficient according to the GDPR. If you have IP anonymization set to \'off\' or \'one\', your website will not comply with GDPR as personal data is still stored on Google\'s servers. Anonymize <strong>two octets</strong> and enable <a href="%2$s">Stealth Mode</a> to properly anonymize IP addresses before sending the data over to Google, however location data might not be accurate.', 'host-analyticsjs-local' ), CAOS_SITE_URL . '/gdpr/google-analytics-illegal-austria/' . $this->utm_tags, admin_url( 'options-general.php?page=host_analyticsjs_local&tab=caos-extensions-settings' ) ) . sprintf( ' <span class="caos-aip">Example: %s', $aip_example ) . ' ' . $this->promo,
			CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) === 'plausible' ? true : [ false, false, ! defined( 'CAOS_PRO_ACTIVE' ) ],
			false,
			__( 'Enable it by setting <strong>Service Provider</strong> to Google Analytics.', 'host-webfonts-local' )
		);
	}

	/**
	 * Position of tracking-code
	 */
	public function do_script_position() {
		$this->do_radio(
			__( 'Tracking Code Position', 'host-analyticsjs-local' ),
			Settings::CAOS_ADMIN_SCRIPT_POSITION_OPTIONS,
			Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION,
			CAOS::get( Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION, 'header' ),
			__( 'Load the Analytics tracking-snippet in the header, footer or manually? If e.g. your theme doesn\'t load the <code>wp_head()</code> conventionally, choose \'Add manually\'.', 'host-analyticsjs-local' ),
			CAOS::get( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE ) || CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) === 'plausible',
			false,
			__( 'Enable it by setting <strong>Service Provider</strong> to Google Analytics and/or disable <strong>Compatibility Mode</strong>.', 'host-webfonts-local' )
		);
	}

	/**
	 * Use adjusted bounce rate?
	 */
	public function do_adjusted_bounce_rate() {
		$this->do_number(
			__( 'Adjusted Bounce Rate (seconds)', 'host-analyticsjs-local' ),
			Settings::CAOS_BASIC_SETTING_ADJUSTED_BOUNCE_RATE,
			CAOS::get( Settings::CAOS_BASIC_SETTING_ADJUSTED_BOUNCE_RATE ),
			sprintf( __( 'Create a more realistic view of your website\'s Bounce Rate. This option creates an event which is triggered after a user spends X seconds on a page. <a target="_blank" href="%s">Read more</a>.', 'host-analyticsjs-local' ), CAOS_SITE_URL . '/how-to/adjusted-bounce-rate-caos/' . $this->utm_tags ),
			0,
			CAOS::get( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE ),
			__( 'Disable <strong>Compatibility Mode</strong> to use it.', 'host-webfonts-local' )
		);
	}

	/**
	 * Render Tracking-code when 'Add Manually' is selected.
	 */
	public function do_add_manually() {
		?>
		<tr class="caos_add_manually" valign="top" <?php echo CAOS::get( Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION ) === 'manual' ? '' : 'style="display: none;"'; ?>>
			<th scope="row"><?php _e( 'Tracking-code', 'host-analyticsjs-local' ); ?></th>
			<td>
				<label>
					<textarea style="display: block; width: 100%; height: 250px;"><?php echo $this->render_tracking_code(); ?></textarea>
				</label>
				<p class="description">
					<?php _e( 'Copy this to the theme or plugin which should handle displaying the snippet.', 'host-analyticsjs-local' ); ?>
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render Tracking Code for Manual placement.
	 *
	 * @return string
	 */
	private function render_tracking_code() {
		$tracking_code = "\n";
		$tracking_id   = CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_ID );

		if ( ! $tracking_id ) {
			return $tracking_code;
		}

		$tracking_code .= '<!-- ' . __( 'This site is running CAOS for WordPress.', 'host-analyticsjs-local' ) . " -->\n";
		$snippet_type   = CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_CODE );

		if ( $snippet_type === 'minimal' ) {
			return $tracking_code . $this->get_tracking_code_template( 'minimal' );
		}

		if ( $snippet_type === 'minimal_ga4' ) {
			return $tracking_code . $this->get_tracking_code_template( 'minimal-ga4' );
		}

		$url_id         = CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE ) === 'gtag.js' ? '?id=' . $tracking_id : '';
		$local_file_url = CAOS::get_local_file_url() . $url_id;
		$tracking_code .= "<script $snippet_type src='$local_file_url'></script>\n";

		if ( CAOS::get( Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) === 'cookie_has_value' && CAOS::get( Settings::CAOS_BASIC_SETTING_COOKIE_NAME ) && CAOS::get( Settings::CAOS_BASIC_SETTING_COOKIE_VALUE ) ) {
			$tracking_code .= $this->get_tracking_code_template( 'cookie-value' );
		}

		if ( CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE ) === 'gtag.js' ) {
			return $tracking_code . $this->get_tracking_code_template( 'gtag' );
		} else {
			return $tracking_code . $this->get_tracking_code_template( 'analytics' );
		}
	}

	/**
	 * @param $name
	 *
	 * @return false|string
	 */
	private function get_tracking_code_template( $name ) {
		ob_start();

		include CAOS_PLUGIN_DIR . 'templates/frontend-tracking-code-' . $name . '.phtml';

		return ob_get_clean();
	}
}