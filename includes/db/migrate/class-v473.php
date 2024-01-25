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
class CAOS_DB_Migrate_V473 extends CAOS_DB_Migrate {
	protected $version = '4.7.3';

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

		if ( ! empty( $new_settings['anonymize_ip_mode'] ) ) {
			unset( $new_settings['anonymize_ip_mode'] );
		}

		update_option( 'caos_settings', $new_settings );

		$this->update_db_version();
	}
}
