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
namespace CAOS;

use CAOS\Plugin as CAOS;
use CAOS\Admin\Settings;

defined( 'ABSPATH' ) || exit;

class Uninstall {

	/** @var array $options */
	private $options;

	/** @var string $cache_dir */
	private $cache_dir;

	/**
	 * CAOS_Uninstall constructor.
	 * @throws ReflectionException
	 */
	public function __construct() {
		if ( CAOS::get( Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS ) !== 'on' ) {
			return;
		}

		$settings        = new Settings();
		$this->options   = $settings->get_settings();
		$this->cache_dir = CAOS_OPT_CACHE_DIR;

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
		return array_map( 'unlink', glob( $this->cache_dir . '/*.*' ) );
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
