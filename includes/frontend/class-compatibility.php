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

class CAOS_Frontend_Compatibility {
	/**
	 *
	 */
	public function __construct() {
		add_filter( 'caos_script_custom_attributes', [ $this, 'exclude_from_litespeed' ] );
	}

	/**
	 * Add data-no-optimize="1" attribute to script if LiteSpeed Cache is enabled.
	 */
	public function exclude_from_litespeed( $attributes ) {
		if ( ! defined( 'LSCWP_V' ) ) {
			return $attributes;
		}

		return 'data-no-optimize="1" ' . $attributes;
	}
}