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

class CAOS_Compatibility_MonsterInsights {
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
		// MonsterInsights adds its tests at the default priority (10).
		add_filter( 'site_status_tests', [ $this, 'replace_tracking_code_test' ], 11 );
	}

	/**
	 * MonsterInsights' "Tracking Code" Site Health test fetches the homepage and counts the occurrences of the
	 * Measurement ID. It only discounts the gtag.js <script> if it's loaded from googletagmanager.com. In
	 * Compatibility Mode, CAOS rewrites that URL to the locally hosted file, so the test always reports
	 * "multiple tracking codes" (critical), which is a false positive.
	 *
	 * Replace it with an informational notice explaining why. Note that this notice doesn't verify the tracking code itself.
	 *
	 * @filter site_status_tests
	 *
	 * @param array $tests
	 *
	 * @return array
	 */
	public function replace_tracking_code_test( $tests ) {
		if ( ! isset( $tests[ 'async' ][ 'monsterinsights_tracking_code' ] ) ) {
			return $tests;
		}

		/**
		 * Allow WP devs to keep MonsterInsights' original test.
		 *
		 * @filter caos_monsterinsights_replace_site_health_test
		 */
		if ( ! apply_filters( 'caos_monsterinsights_replace_site_health_test', true ) ) {
			return $tests;
		}

		unset( $tests[ 'async' ][ 'monsterinsights_tracking_code' ] );

		$tests[ 'direct' ][ 'caos_monsterinsights_tracking_code' ] = [
			'label' => __( 'MonsterInsights Compatibility Notice (CAOS)', 'host-analyticsjs-local' ),
			'test'  => [ $this, 'test_tracking_code' ],
		];

		return $tests;
	}

	/**
	 * @return array
	 */
	public function test_tracking_code() {
		return [
			'label'       => __( 'CAOS local gtag.js compatibility notice', 'host-analyticsjs-local' ),
			'status'      => 'good',
			'badge'       => [
				'label' => __( 'MonsterInsights', 'host-analyticsjs-local' ),
				'color' => 'blue',
			],
			'description' => sprintf(
				'<p>%s</p>',
				__(
					'CAOS (Compatibility Mode) loads gtag.js from your own server instead of googletagmanager.com. MonsterInsights\' own tracking code check doesn\'t recognize this and would falsely report multiple tracking codes, so CAOS has replaced it with this notice. For the same reason, Google Tag Assistant may report that no tag was found. This is expected when gtag.js is served locally.',
					'host-analyticsjs-local'
				)
			),
			'test'        => 'caos_monsterinsights_tracking_code',
		];
	}
}
