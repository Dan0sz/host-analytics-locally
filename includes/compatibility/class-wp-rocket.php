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

class CAOS_Compatibility_WPRocket {
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
		add_filter( 'rocket_excluded_inline_js_content', [ $this, 'exclude_minimal_analytics' ] );
		add_filter( 'rocket_delay_js_exclusions', [ $this, 'exclude_minimal_analytics' ] );
	}

	/**
	 * Excludes the minimal analytics script from WP Rocket's optimizations if the minimal analytics feature is enabled.
	 *
	 * @param array $excluded_js An array of JavaScript handles or identifiers that should be excluded.
	 *
	 * @return array The updated array containing the minimal analytics script exclusion, if applicable.
	 */
	public function exclude_minimal_analytics( $excluded_js ) {
		if ( empty( CAOS::uses_minimal_analytics() || isset( $excluded_js[ 'caos-ma' ] ) ) ) {
			return $excluded_js;
		}

		$excluded_js[ 'caos-ma' ] = 'window.minimalAnalytics';

		return $excluded_js;
	}
}
