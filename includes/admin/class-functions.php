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

class CAOS_Admin_Functions {

	// Transients
	const CAOS_ADMIN_UPDATE_ERROR_MESSAGE_SHOWN   = 'caos_admin_update_error_shown';
	const CAOS_ADMIN_UPDATE_SUCCESS_MESSAGE_SHOWN = 'caos_admin_update_success_shown';
	const CAOS_ADMIN_BLOCKED_PAGES_NOTICE_SHOWN   = 'caos_admin_blocked_pages_notice_shown';
	const CAOS_ADMIN_BLOCKED_PAGES_CURRENT_VALUE  = 'caos_blocked_pages_current_value';

	/** @var string $plugin_text_domain */
	private $plugin_text_domain = 'host-analyticsjs-local';

	/**
	 * Display notices and set transients.
	 *
	 * CAOS_Admin_Functions constructor.
	 */
	public function __construct() {
		clearstatcache();

		$this->do_update_notice();
	}

	/**
	 * @return void
	 */
	private function do_update_notice() {
		if ( CAOS::uses_minimal_analytics() ) {
			return;
		}

		$file_updated = $this->file_recently_updated();

		if ( ! $file_updated ) {
			if ( ! get_transient( self::CAOS_ADMIN_UPDATE_ERROR_MESSAGE_SHOWN ) ) {
				CAOS_Admin_Notice::set_notice(
					sprintf(
						__( 'Gtag.js doesn\'t exist or hasn\'t been updated for more than two days. Try running Update gtag.js in <em>Settings > Optimize Analytics</em> to fix this. If this message returns in the next few days, consider <a href="%s" target="_blank">replacing WordPress\' <em>pseudo cron</em> with a real cron</a>.', $this->plugin_text_domain ),
						'https://daan.dev/docs/caos-troubleshooting/analytics-js-gtag-js-doesnt-exist/'
					),
					'error'
				);

				set_transient( self::CAOS_ADMIN_UPDATE_ERROR_MESSAGE_SHOWN, true, HOUR_IN_SECONDS * 4 );
			}
		}
	}

	/**
	 * Check if cron is running
	 *
	 * @return bool
	 */
	public function file_recently_updated() {
		$file_path = CAOS::get_file_alias_path();

		if ( ! file_exists( $file_path ) ) {
			return false;
		}

		$file_mod_time = @filemtime( $file_path );

		if ( ! $file_mod_time ) {
			return false;
		}

		if ( time() - $file_mod_time >= 2 * DAY_IN_SECONDS ) {
			return false;
		} else {
			return true;
		}
	}
}
