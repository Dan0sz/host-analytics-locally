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

class CAOS_Compatibility {
	/**
	 * Build class.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Trigger compatibility fixes.
	 *
	 * @return void
	 */
	private function init() {
		if ( defined( 'AUTOPTIMIZE_PLUGIN_VERSION' ) ) {
			new CAOS_Compatibility_Autoptimize();
		}

		/**
		 * Always run Cloudflare compatibility, because it doesn't do any harm.
		 */
		new CAOS_Compatibility_Cloudflare();

		if ( defined( 'LSCWP_V' ) ) {
			new CAOS_Compatibility_Litespeed();
		}

		if ( defined( 'WPFC_MAIN_PATH' ) ) {
			new CAOS_Compatibility_WpFastestCache();
		}

		if ( defined( 'WP_ROCKET_VERSION' ) ) {
			new CAOS_Compatibility_WpRocket();
		}
	}
}