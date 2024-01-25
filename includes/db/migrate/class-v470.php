<?php
defined( 'ABSPATH' ) || exit;

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
class CAOS_DB_Migrate_V470 extends CAOS_DB_Migrate {
	protected $version = '4.7.0';

	protected $rows = [];

	/**
	 * Build class
	 *
	 * @return void
	 */
	public function __construct() {
		$this->rows = apply_filters(
			'caos_db_migration_v470',
			[
				// Basic
				'service_provider',
				'tracking_id',
				'domain_name',
				'dual_tracking',
				'ga4_measurement_id',
				'snippet_type',
				'adjusted_bounce_rate',
				// Advanced
				'analytics_js_file',
				'ga_cookie_expiry_days',
				'site_speed_sample_rate',
				// Extensions
				'extension_track_ad_blockers',
				'extension_linkid',
				'capture_outbound_links',
			]
		);

		$this->init();
	}

	/**
	 * Remove settings belonging to removed options and migrate a few options to their new destination.
	 *
	 * @return void
	 */
	private function init() {
		$new_settings = CAOS::get_settings();

		// Migrate Tracking ID option if it's already a GA4 measurement ID.
		if ( ! empty( $new_settings['tracking_id'] ) && strpos( $new_settings['tracking_id'], 'G-' ) === 0 ) {
			$new_settings['measurement_id'] = $new_settings['tracking_id'];
		}

		// Migrate GA4 Measurement ID to new Measurement ID setting if it's set.
		if ( empty( $new_settings['measurement_id'] ) && ! empty( $new_settings['ga4_measurement_id'] ) ) {
			$new_settings['measurement_id'] = $new_settings['ga4_measurement_id'];
		}

		if ( ! empty( $new_settings['snippet_type'] ) ) {
			$new_settings['tracking_code'] = $new_settings['snippet_type'];

			if ( $new_settings['tracking_code'] === 'async' ) {
				// Async is the new default.
				$new_settings['tracking_code'] = '';
			}
		}

		foreach ( $this->rows as $row ) {
			if ( isset( $new_settings[ $row ] ) ) {
				unset( $new_settings[ $row ] );
			}
		}

		update_option( 'caos_settings', $new_settings );

		/**
		 * Refresh the aliases.
		 */
		delete_option( CAOS_Admin_Settings::CAOS_CRON_FILE_ALIASES );

		if ( ! empty( $new_settings['measurement_id'] ) ) {
			new CAOS_Cron();
		}

		CAOS_Admin_Notice::set_notice(
			sprintf(
				__( 'Universal Analytics (i.e. Google Analytics V3) and some of its features have been removed in this version of CAOS. Please check <a href="%1$s" target="_blank">your settings</a> and refer to <a href="%2$s" target="_blank">this article</a> for a list of the changes.', 'host-analyticsjs-local' ),
				admin_url( 'options-general.php?page=host_analyticsjs_local' ),
				'https://daan.dev/blog/wordpress/rip-universal-analytics/'
			),
			'info',
			'all',
			'caos-update-message-v470'
		);

		$this->update_db_version();
	}
}
