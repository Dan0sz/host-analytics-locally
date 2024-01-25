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

class CAOS_Admin_Settings_Basic extends CAOS_Admin_Settings_Builder {

	/**
	 * CAOS_Admin_Settings_Basic constructor.
	 */
	public function __construct() {
		$this->title = __( 'Basic Settings', 'host-analyticsjs-local' );

		// Open
		add_action( 'caos_basic_settings_content', [ $this, 'do_title' ], 1 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_before' ], 2 );

		// Settings
		add_action( 'caos_basic_settings_content', [ $this, 'do_track_admin' ], 12 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_measurement_id' ], 32 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_gdpr_compliance_promo' ], 51 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_allow_tracking' ], 52 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_cookie_name' ], 54 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_cookie_value' ], 56 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_tracking_code' ], 58 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_anonymize_ip_mode' ], 60 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_script_position' ], 61 );
		add_action( 'caos_basic_settings_content', [ $this, 'do_add_manually' ], 62 );

		// Close
		add_action( 'caos_basic_settings_content', [ $this, 'do_after' ], 100 );

		parent::__construct();
	}

	/**
	 * Google Analytics Measurement ID
	 */
	public function do_measurement_id() {
		$this->do_text(
			__( 'Data Stream Measurement ID', 'host-analyticsjs-local' ),
			CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID,
			__( 'e.g. G-123ABC789', 'host-analyticsjs-local' ),
			CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID ),
			__( 'Enter your Measurement ID, e.g. G-123ABC789.', 'host-analyticsjs-local' ),
			true,
			null
		);
	}

	/**
	 * Track logged in Administrators
	 */
	public function do_track_admin() {
		$this->do_checkbox(
			__( 'Track logged in Administrators', 'host-analyticsjs-local' ),
			CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACK_ADMIN,
			CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACK_ADMIN ),
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
			'pro_gdpr',
			defined( 'CAOS_PRO_ACTIVE' ) ? CAOS::get( 'pro_gdpr', '' ) : false,
			sprintf( __( 'Remove any data that can be used to identify a person (i.e. personal data, e.g. IP address, User Agent, Location, etc.) to use Google Analytics in compliance with the GDPR. Be warned that enabling this setting <u>doesn\'t</u> guarantee GDPR compliance of your site, e.g. any parameters that enable (internal) routing (e.g. UTM tags) must be removed from any URLs on your site. <A href="%s" target="_blank">Read more</a>', 'host-analyticsjs-local' ), 'https://daan.dev/blog/wordpress/google-analytics-gdpr-compliance/' ) . ' ' . $this->promo,
			! defined( 'CAOS_PRO_ACTIVE' ) || CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE, '' ),
			true,
			true
		);
	}

	/**
	 * Allow tracking...
	 */
	public function do_allow_tracking() {
		$this->do_radio(
			__( 'Allow tracking...', 'host-analyticsjs-local' ),
			CAOS_Admin_Settings::CAOS_ADMIN_ALLOW_TRACKING_OPTIONS,
			CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING,
			CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ),
			__( 'Configure CAOS to "listen" to your Cookie Banner plugin.', 'host-analyticsjs-local' ) . ' ' . __( 'Choose \'Always\' to enable tracking without prior consent.', 'host-analyticsjs-local' ) . ' ' . sprintf( __( 'CAOS uses the Google Analytics 4 <a href="%s" target="_blank">Consent Mode API</a>.', 'host-analyticsjs-local' ), 'https://support.google.com/analytics/answer/9976101?hl=en' ),
			CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE, '' ),
			false,
			__( 'Enable it by disabling <strong>Compatibility Mode</strong>.', 'host-webfonts-local' )
		);
	}

	/**
	 * Cookie name
	 */
	public function do_cookie_name() {
		$this->do_text(
			__( 'Cookie Name', 'host-analyticsjs-local' ),
			CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME,
			__( 'e.g. cookie_accepted', 'host-analyticsjs-local' ),
			CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME ),
			__( 'The cookie name set by your Cookie Notice plugin when user accepts.', 'host-analyticsjs-local' ),
			CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING )
		);
	}

	/**
	 * Cookie value
	 */
	public function do_cookie_value() {
		$this->do_text(
			__( 'Cookie Value', 'host-analyticsjs-local' ),
			CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_VALUE,
			__( 'e.g. true', 'host-analyticsjs-local' ),
			CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_VALUE ),
			__( 'The value of the above specified cookie set by your Cookie Notice when user accepts.', 'host-analyticsjs-local' ),
			CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) === 'cookie_has_value' || CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) === 'cookie_value_contains'
		);
	}

	/**
	 * Snippet type
	 */
	public function do_tracking_code() {
		$this->do_select(
			__( 'Tracking Code', 'host-analyticsjs-local' ),
			CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_CODE,
			CAOS_Admin_Settings::CAOS_ADMIN_TRACKING_CODE_OPTIONS,
			CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_CODE ),
			__( 'Should we use the Asynchronous or Minimal tracking code? Minimal Analytics is fastest, but supports only basic features i.e. pageviews and events.', 'host-analyticsjs-local' ) . ' ' . sprintf( '<a href="%s" target="_blank">', 'https://daan.dev/docs/caos/basic-settings/' . $this->utm_tags ) . __( 'Read more', 'host-analyticsjs-local' ) . '</a>',
			CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE, '' ),
			__( 'Enable it by disabling <strong>Compatibility Mode</strong>.', 'host-webfonts-local' )
		);
	}

	/**
	 * Render Anonymize IP Mode option and example.
	 */
	public function do_anonymize_ip_mode() {
		?>
		<tr>
			<th scope="row">
				<?php echo esc_attr( __( 'Anonymize IP (deprecated)', 'host-analyticsjs-local' ) ); ?>
			</th>
			<td>
				<p class="description">
					<?php echo wp_kses( sprintf( __( 'As of July 1st, Google retired Universal Analytics (GA3). According to Google, IP masking is <a target="_blank" href="%s">no longer required</a>, because IP addresses are no longer logged or stored. The <code>aip</code> parameter and CAOS Pro\'s custom technologies are no longer supported by Google.', 'host-analyticsjs-local' ), 'https://support.google.com/analytics/answer/2763052?hl=en' ), 'post' ); ?>
				</p>
			</td>	
		</tr>
		<?php
	}

	/**
	 * Position of tracking-code
	 */
	public function do_script_position() {
		$this->do_radio(
			__( 'Tracking Code Position', 'host-analyticsjs-local' ),
			CAOS_Admin_Settings::CAOS_ADMIN_SCRIPT_POSITION_OPTIONS,
			CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION,
			CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION, 'header' ),
			__( 'Load the Analytics tracking-snippet in the header, footer or manually? If e.g. your theme doesn\'t load the <code>wp_head()</code> conventionally, choose \'Add manually\'.', 'host-analyticsjs-local' ),
			CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE, '' ),
			false,
			__( 'Enable it by disabling <strong>Compatibility Mode</strong>.', 'host-webfonts-local' )
		);
	}

	/**
	 * Render Tracking-code when 'Add Manually' is selected.
	 */
	public function do_add_manually() {
		?>
		<tr class="caos_add_manually" valign="top" <?php echo CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION, 'header' ) === 'manual' ? '' : 'style="display: none;"'; ?>>
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

		if ( ! CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID ) ) {
			return $tracking_code;
		}

		if ( apply_filters( 'caos_frontend_tracking_promo_message', true ) ) {
			$tracking_code .= '<!-- ' . __( 'This site is running CAOS for WordPress.', 'host-analyticsjs-local' ) . " -->\n";
		}

		if ( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) === 'minimal_ga4' ) {
			return $tracking_code . $this->get_tracking_code_template( 'minimal-ga4' );
		}

		$url_id         = '?id=' . CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID );
		$local_file_url = CAOS::get_local_file_url() . $url_id;

		$tracking_code .= "<script async src='$local_file_url'></script>\n";

		return $tracking_code . $this->get_tracking_code_template();
	}

	/**
	 * @param $name
	 *
	 * @return false|string
	 */
	private function get_tracking_code_template() {
		ob_start();

		include CAOS_PLUGIN_DIR . 'templates/frontend-tracking-code-gtag.phtml';

		return ob_get_clean();
	}
}
