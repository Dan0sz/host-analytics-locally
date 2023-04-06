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

use CAOS\Admin\Settings;

defined( 'ABSPATH' ) || exit;

class DB {

	/** @var string */
	private $current_version = '';

	/**
	 * DB Migration constructor.
	 */
	public function __construct() {
		$this->current_version = get_option( Settings::CAOS_DB_VERSION );

		if ( $this->should_run_migration( '4.2.2' ) ) {
			new \CAOS\DB\Migrate\V422();
		}

		if ( $this->should_run_migration( '4.3.0' ) ) {
			new \CAOS\DB\Migrate\V430();
		}
	}

	/**
	 * Checks whether migration script has been run.
	 *
	 * @param mixed $version
	 * @return bool
	 */
	private function should_run_migration( $version ) {
		return version_compare( $this->current_version, $version ) < 0;
	}
}
