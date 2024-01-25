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

class CAOS_Admin_Settings_Advanced extends CAOS_Admin_Settings_Builder {

	/**
	 * CAOS_Admin_Settings_Advanced constructor.
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
		add_action( 'caos_advanced_settings_content', [ $this, 'do_cache_dir' ], 50 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_cdn_url' ], 60 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_randomize_client_id_promo' ], 110 );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_cloaked_affiliate_links_tracking_promo' ], 120 );
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
			CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE,
			CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE, '' ) != '' ? 'on' : '',
			__( 'Check this option to use CAOS with any other Google Analytics plugin. Any reference to <code>googletagmanager.com/gtag/js</code> in your site\'s HTML will be replaced with the URL pointing to the local copy. <strong>Warning!</strong> Please make sure that CAOS\' <strong>Basic Settings</strong> match your Google Analytics plugin\'s configuration.', 'host-analyticsjs-local' )
		);
	}

	/**
	 * Save .js file to...
	 */
	public function do_cache_dir() {
		$this->do_text(
			__( 'Cache directory for Gtag.js', 'host-analyticsjs-local' ),
			CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR,
			__( 'e.g. /uploads/caos/', 'host-analyticsjs-local' ),
			CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR, '/uploads/caos/' ),
			__( "Change the path where the Gtag.js file is cached inside WordPress' content directory (usually <code>wp-content</code>). Defaults to <code>/uploads/caos/</code>.", 'host-analyticsjs-local' )
		);
	}

	/**
	 * Serve from a CDN?
	 */
	public function do_cdn_url() {
		$this->do_text(
			__( 'Serve from CDN', 'host-analyticsjs-local' ),
			CAOS_Admin_Settings::CAOS_ADV_SETTING_CDN_URL,
			__( 'e.g. cdn.mydomain.com', 'host-analyticsjs-local' ),
			CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_CDN_URL ),
			__( 'If you\'re using a CDN, enter the URL here to serve <code>gtag.js</code> from your CDN.', 'host-analyticsjs-local' )
		);
	}

	/**
	 * Add Randomize Client ID option.
	 *
	 * @return void
	 */
	public function do_randomize_client_id_promo() {
		$description = __( 'Since GA4 only creates <em>first-party</em> (which are GDPR compliant in some countries) cookies, enabling this option for GA4 will generate a random user ID for each visitor of <u>your</u> website to ensure that tracking across different websites/platforms is no longer possible, but it\'ll still be possible to track users on your website. Enabling this option doesn\'t necessarily mean you no longer need a cookie banner.', 'host-analyticsjs-local' ) . ' ' . $this->promo;

		$this->do_checkbox(
			__( 'Enable Randomize Client ID (Pro)', 'host-analyticsjs-local' ),
			'pro_random_cid',
			defined( 'CAOS_PRO_ACTIVE' ) && CAOS::get( 'pro_random_cid' ) ? 'on' : false,
			$description,
			! defined( 'CAOS_PRO_ACTIVE' ) || ( defined( 'CAOS_PRO_ACTIVE' ) && CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE, '' ) ),
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
		$label = __( 'Track Cloaked Affiliate Links (Pro)', 'host-analyticsjs-local' );
		$name  = 'pro_cloaked_affiliate_links';
		?>
		<tr>
			<th><?php echo esc_attr( $label ); ?></th>
			<td>
				<?php
				$disabled = ! defined( 'CAOS_PRO_ACTIVE' ) || ( defined( 'CAOS_PRO_ACTIVE' ) && CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) === 'minimal_ga4' );
				?>
				<?php if ( $disabled && $this->display_reason( true ) ) : ?>
					<p class="description option-disabled">
						<?php echo wp_kses( sprintf( __( 'This option is disabled. %s', 'host-webfonts-local' ), __( 'Enable it by setting <strong>Service Provider</strong> to Google Analytics and/or set <strong>Tracking Code</strong> to Default or Asynchronous.', 'host-webfonts-local' ) ), 'post' ); ?>
					</p>
				<?php else : ?>
					<table class="track-cloaked-affiliate-links">
						<tr>
							<th><?php echo esc_attr( __( 'Path', 'host-analyticsjs-local' ) ); ?></th>
							<th><?php echo esc_attr( __( 'Event Category', 'host-analyticsjs-local' ) ); ?></th>
							<th></th>
						</tr>
						<tr>
							<input type="hidden" name="caos_settings[<?php echo esc_attr( $name ); ?>]" value="0" />
						</tr>
						<?php
						$affiliate_links = defined( 'CAOS_PRO_ACTIVE' ) && CAOS::get( $name ) ? CAOS::get( $name ) : [
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
										<input type="text" <?php echo $disabled ? 'disabled' : ''; ?> class="affiliate-link-<?php echo esc_attr( $prop_key ); ?>" name="caos_settings[<?php echo esc_attr( $name ); ?>][<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $prop_key ); ?>]" value="<?php echo esc_attr( $prop_value ); ?>" />
									</td>
								<?php endforeach; ?>
								<td>
									<span class="dashicons dashicons-remove affiliate-link-remove" data-row="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $disabled ? 'style="opacity: 15%;"' : '' ); ?>></span>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
					<p>
						<input type="button" <?php echo esc_attr( $disabled ); ?> class="button button-secondary" id="affiliate-link-add" value="<?php echo esc_attr( __( 'Add Link Path', 'host-analyticsjs-local' ) ); ?>" />
					</p>
					<p class="description">
						<?php echo wp_kses( apply_filters( $name . '_setting_description', __( 'Send an event to Google Analytics whenever a Cloaked Affiliate Link is clicked. An event with the configured <strong>Event Category</strong> is sent to Google Analytics whenever a link containing the <strong>Path</strong> value is clicked. The <strong>Event Label</strong> will be the URL of the link. Depending on your server\'s capacity, this might not work properly with Stealth Mode enabled.', 'host-analyticsjs-local' ), $label, $name ) . ' ' . $this->promo, 'post' ); ?>
						<?php echo wp_kses( defined( 'CAOS_PRO_ACTIVE' ) && CAOS::get( 'pro_stealth_mode' ) == 'on' ? __( 'If no events are registered in Google Analytics, your server might be too slow to send them in time. Please disable Stealth Mode if that\'s the case.', 'host-analyticsjs-local' ) : '', 'post' ); ?>
					</p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Disable all advertising features functionality
	 */
	public function do_advertising_features() {
		$this->do_checkbox(
			__( 'Disable Advertising Features', 'host-analyticsjs-local' ),
			CAOS_Admin_Settings::CAOS_ADV_SETTING_DISABLE_ADS_FEATURES,
			CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_DISABLE_ADS_FEATURES ),
			sprintf( __( 'Override and disable all advertising reporting and remarketing features established in Google Analytics. <a href="%s" target="_blank">What\'s this?</a>', 'host-analyticsjs-local' ), 'https://support.google.com/analytics/answer/9050852?hl=en' ),
			CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE, '' ),
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
			CAOS_Admin_Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS,
			CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS ),
			'<strong>' . __( 'Warning!', 'host-analytics-local' ) . '</strong> ' . __( 'This will remove the settings from the database upon plugin deletion!', 'host-analyticsjs-local' )
		);
	}
}
