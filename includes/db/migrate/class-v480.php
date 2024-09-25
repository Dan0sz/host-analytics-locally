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

class CAOS_DB_Migrate_V480 extends CAOS_DB_Migrate {
	protected $version = '4.8.0';

	/**
	 * Build class
	 *
	 * @return void
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Remove Anonymize IP mode setting from the DB.
	 *
	 * @return void
	 */
	private function init() {
		$new_settings = CAOS::get_settings();

		if ( ! empty( $new_settings[ 'cookie_notice_name' ] ) ) {
			$new_settings[ 'cookie_name' ] = $new_settings[ 'cookie_notice_name' ];

			unset( $new_settings[ 'cookie_notice_name' ] );
		}

		update_option( 'caos_settings', $new_settings );

		$this->update_db_version();
	}
}
