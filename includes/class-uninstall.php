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

defined( 'ABSPATH' ) || exit;

class CAOS_Uninstall {

	/** @var array $options */
	private $options;

	/** @var string $cache_dir */
	private $cache_dir;

	/**
	 * CAOS_Uninstall constructor.
	 * @throws ReflectionException
	 */
	public function __construct() {
		if ( CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS ) !== 'on' ) {
			return;
		}

		$settings        = new CAOS_Admin_Settings();
		$this->options   = $settings->get_settings();
		$this->cache_dir = CAOS::get_local_dir();

		$this->remove_db_entries();
		$this->delete_files();
		$this->delete_dir();
	}

	/**
	 * Remove all options from the database.
	 */
	private function remove_db_entries() {
		foreach ( $this->options as $constant => $option ) {
			delete_option( $option );
		}
	}

	/**
	 * Delete all files in the cache directory.
	 *
	 * @return array
	 */
	private function delete_files() {
		return array_map( 'unlink', glob( $this->cache_dir . '*.*' ) );
	}

	/**
	 * Delete the cache directory.
	 *
	 * @return bool
	 */
	private function delete_dir() {
		return rmdir( $this->cache_dir );
	}
}
