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

class CAOS_Admin_Settings_Extensions extends CAOS_Admin_Settings_Builder {
	/**
	 * @return void
	 */
	public function __construct() {
		// Stealth Mode Panel
		add_action( 'caos_extensions_settings_content', [ $this, 'do_before' ], 13 );
		add_action( 'caos_extensions_settings_content', [ $this, 'do_stealth_mode_promo' ], 14 );
		add_action( 'caos_extensions_settings_content', [ $this, 'do_cloudflare_compatibility' ], 17 );
		add_action( 'caos_extensions_settings_content', [ $this, 'do_after' ], 18 );

		parent::__construct();
	}

	/**
	 * @return void
	 */
	public function do_stealth_mode_promo() {
		$this->do_checkbox(
			__( 'Stealth Mode (Pro)', 'host-analyticsjs-local' ),
			'pro_stealth_mode',
			defined( 'CAOS_PRO_ACTIVE' ) ? CAOS::get( 'pro_stealth_mode', '' ) : false,
			sprintf(
				__(
					'Stealth Mode routes all Google Analytics requests (e.g. <code>google-analytics.com/g/collect</code>) through a custom-built API before sending it to Google Analytics, making it undetectable by Ad Blockers. <a href="%s" target="_blank">Read More</a>',
					'host-analyticsjs-local'
				),
				CAOS_SITE_URL . '/how-to/bypass-ad-blockers-caos/' . $this->utm_tags
			) . ' ' . $this->promo, ! defined( 'CAOS_PRO_ACTIVE' ),
			true,
			true
		);
	}

	/**
	 * @return void
	 */
	public function do_cloudflare_compatibility() {
		$this->do_checkbox(
			__( 'Cloudflare Compatibility (Pro)', 'host-analyticsjs-local' ),
			'pro_cf_compatibility',
			defined( 'CAOS_PRO_ACTIVE' ) ? CAOS::get( 'pro_cf_compatibility', '' ) : false,
			__(
				'When your site is proxied through Cloudflare and your Google Analytics data is incomplete (e.g. location data is missing) when suing Stealth Mode, enable this option.',
				'host-analyticsjs-local'
			) . ' ' . $this->promo, ! defined( 'CAOS_PRO_ACTIVE' ),
			true,
			true
		);
	}
}
