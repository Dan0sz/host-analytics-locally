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
class CAOS_DB_Migrate_V430 extends CAOS_DB_Migrate {

	protected $migrate_option_names = [
		'caos_analytics_compatibility_mode' => CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE,
	];

	protected $version = '4.3.0';

	/**
	 * Build class
	 *
	 * @return void
	 */
	public function __construct() {
		$this->migrate_option_names();

		$compatibility_mode = get_option( CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE );

		if ( $compatibility_mode ) {
			update_option( CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE, 'on' );
		}

		$this->update_db_version();
	}
}
