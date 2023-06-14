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
 * @copyright: (c) 2021 Daan van den Bergh
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
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACK_ADMIN,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_DOMAIN_NAME,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_ID,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_DUAL_TRACKING,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_CODE,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_ANONYMIZE_IP_MODE,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION,
				CAOS_Admin_Settings::CAOS_BASIC_SETTING_ADJUSTED_BOUNCE_RATE,
				// Advanced
				CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE,
				CAOS_Admin_Settings::CAOS_ADV_SETTING_JS_FILE,
				CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR,
				CAOS_Admin_Settings::CAOS_ADV_SETTING_CDN_URL,
				CAOS_Admin_Settings::CAOS_ADV_SETTING_GA_SESSION_EXPIRY_DAYS,
				CAOS_Admin_Settings::CAOS_ADV_SETTING_SITE_SPEED_SAMPLE_RATE,
				CAOS_Admin_Settings::CAOS_ADV_SETTING_DISABLE_ADS_FEATURES,
				CAOS_Admin_Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS,
				// Extensions
				CAOS_Admin_Settings::CAOS_EXT_SETTING_CAPTURE_OUTBOUND_LINKS,
				CAOS_Admin_Settings::CAOS_EXT_SETTING_TRACK_AD_BLOCKERS,
				CAOS_Admin_Settings::CAOS_EXT_SETTING_LINKID,
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

		// $this->update_db_version();
	}
}
