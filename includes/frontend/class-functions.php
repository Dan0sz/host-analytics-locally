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
 * @copyright: © 2021 - 2023 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

defined( 'ABSPATH' ) || exit;

class CAOS_Frontend_Functions {

	/**
	 * CAOS_Frontend_Functions constructor.
	 */
	public function __construct() {
		// Needs to be added after Google Analytics library is requested.
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_js_scripts' ], 11 );
		add_filter( 'caos_frontend_add_dns_prefetch', [ $this, 'maybe_add_dns_prefetch' ] );
		add_filter( 'wp_resource_hints', [ $this, 'add_dns_prefetch' ], 10, 2 );
	}

	/**
	 * Enqueue JS scripts for frontend.
	 */
	function enqueue_js_scripts() {
		if ( current_user_can( 'manage_options' ) && ! CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACK_ADMIN ) ) {
			return;
		}

		if ( CAOS::get( CAOS_Admin_Settings::CAOS_EXT_SETTING_CAPTURE_OUTBOUND_LINKS ) === 'on' ) {
			$tracking = new CAOS_Frontend_Tracking();
			wp_add_inline_script( $tracking->handle, $this->get_frontend_template( 'outbound-link-tracking' ) );
		}
	}

	/**
	 * @param $name
	 *
	 * @return false|string
	 */
	public function get_frontend_template( $name ) {
		ob_start();

		include CAOS_PLUGIN_DIR . 'templates/frontend-' . $name . '.phtml';

		return str_replace( [ '<script>', '</script>' ], '', ob_get_clean() );
	}

	/**
	 * Don't add DNS prefetch if compatibility mode is enabled.
	 *
	 * @param mixed $result
	 * @return bool
	 */
	public function maybe_add_dns_prefetch() {
		return CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE, '' ) !== 'on';
	}

	/**
	 * Add Preconnect to google-analytics.com and CDN URL (if set) in wp_head().
	 *
	 * @filter caos_frontend_add_dns_prefetch Allows disabling the prefetch, if already added by another plugin.
	 */
	public function add_dns_prefetch( $hints, $type ) {
		if ( ! apply_filters( 'caos_frontend_add_dns_prefetch', true ) ) {
			return $hints;
		}

		if ( $type == 'preconnect' ) {
			$hints[] = '//www.google-analytics.com';
		}

		return $hints;
	}
}
