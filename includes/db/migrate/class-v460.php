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
 * @copyright: © 2021 - 2023 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */
class CAOS_DB_Migrate_V460 extends CAOS_DB_Migrate {
	protected $version = '4.6.0';

	protected $rows = [];

	/**
	 * Build class
	 *
	 * @return void
	 */
	public function __construct() {
		$this->rows = apply_filters(
			'caos_db_migration_v460',
			[
				// Basic
				'service_provider',
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACK_ADMIN,
				'domain_name',
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID,
				'dual_tracking',
				'ga4_measurement_id',
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_CODE,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_ANONYMIZE_IP_MODE,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION,
				'adjusted_bounce_rate',
				// Advanced
				CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE,
				'analytics_js_file',
				CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR,
				CAOS_Admin_Settings::CAOS_ADV_SETTING_CDN_URL,
				'ga_cookie_expiry_days',
				'site_speed_sample_rate',
				CAOS_Admin_Settings::CAOS_ADV_SETTING_DISABLE_ADS_FEATURES,
				CAOS_Admin_Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS,
				// Extensions
				'capture_outbound_links',
				'extension_track_ad_blockers',
				'extension_linkid',
			]
		);

		$this->init();
	}

	private function init() {
		$new_settings = CAOS::get_settings();

		foreach ( $this->rows as $row ) {
			$option_value = get_option( "caos_$row" );

			if ( $option_value !== false ) {
				$new_settings[ $row ] = get_option( "caos_$row" );

				delete_option( "caos_$row" );
			}
		}

		update_option( 'caos_settings', $new_settings );

		$this->update_db_version();
	}
}
