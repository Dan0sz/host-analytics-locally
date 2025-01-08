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

class CAOS_Compatibility_Cloudflare {
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
		add_filter( 'script_loader_tag', [ $this, 'gtag_exclude_from_cloudflare' ], 10 );
		add_filter( 'caos_ma4_custom_attributes', [ $this, 'exclude_from_cloudflare' ] );
	}

	/**
	 * Add data-no-optimize="1" attribute to script if LiteSpeed Cache is enabled.
	 *
	 * @param $attributes
	 *
	 * @return string
	 */
	public function exclude_from_cloudflare( $attributes ) {
		return 'data-cfasync="false" ' . $attributes;
	}

	public function gtag_exclude_from_cloudflare( $tag ) {
		if ( strpos( $tag, 'gtag' ) !== false ) {
			return str_replace( '<script ', '<script data-cfasync="false" ', $tag );
		}

		return $tag;
	}
}