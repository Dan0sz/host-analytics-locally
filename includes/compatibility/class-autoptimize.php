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

class CAOS_Compatibility_Autoptimize {
	/**
	 * Build class.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Action and filter hooks.
	 *
	 * @return void
	 */
	private function init() {
		add_filter( 'caos_gtag_custom_attributes', [ $this, 'exclude_from_autoptimize' ] );
		add_filter( 'caos_ma4_custom_attributes', [ $this, 'exclude_from_autoptimize' ] );
	}

	/**
	 * Add data-no-optimize="1" attribute to script if LiteSpeed Cache is enabled.
	 *
	 * @param $attributes
	 *
	 * @return string
	 */
	public function exclude_from_autoptimize( $attributes ) {
		return 'data-noptimize="true" ' . $attributes;
	}
}