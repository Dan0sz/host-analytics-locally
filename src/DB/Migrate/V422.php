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

use CAOS\DB\Migrate;

defined( 'ABSPATH' ) || exit;

class V422 extends Migrate {

	protected $migrate_option_names = [
		'sgal_anonymize_ip' => 'caos_anonymize_ip_mode',
	];

	protected $update_option_values = [
		'caos_anonymize_ip_mode' => [ 'one', 'two' ],
	];

	protected $version = '4.2.2';

	/**
	 * Build class
	 *
	 * @return void
	 */
	public function __construct() {
		$this->migrate_option_names();
		$this->update_option_values();
		$this->update_db_version();
	}
}
