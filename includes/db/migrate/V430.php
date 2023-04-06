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
namespace CAOS\DB\Migrate;

use CAOS\Admin\Settings;
use CAOS\DB\Migrate;

defined( 'ABSPATH' ) || exit;

class V430 extends Migrate {

	protected $migrate_option_names = [
		'caos_analytics_compatibility_mode' => Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE,
	];

	protected $version = '4.3.0';

	/**
	 * Build class
	 *
	 * @return void
	 */
	public function __construct() {
		$this->migrate_option_names();

		$compatibility_mode = get_option( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE );

		if ( $compatibility_mode ) {
			update_option( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE, 'on' );
		}

		$this->update_db_version();
	}
}
