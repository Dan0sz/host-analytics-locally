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
 * @url      : https://daan.dev/wordpress/caoss/
 * @copyright: © 2021 - 2024 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

defined( 'ABSPATH' ) || exit;

class CAOS_Setup {
	const CRON_LABEL = 'caos_update_analytics_js';

	/** @var string $plugin_text_domain */
	protected $plugin_text_domain = 'host-analyticsjs-local';

	/**
	 * CAOS_Setup constructor.
	 */
	public function __construct() {
		register_activation_hook( CAOS_PLUGIN_FILE, [ $this, 'create_cache_dir' ] );
		register_activation_hook( CAOS_PLUGIN_FILE, [ $this, 'activate_cron' ] );
		register_deactivation_hook( CAOS_PLUGIN_FILE, [ $this, 'deactivate_cron' ] );
		add_action( self::CRON_LABEL, [ $this, 'load_cron_script' ] );
	}

	/**
	 * Create Cache-dir upon reactivation.
	 */
	public function create_cache_dir() {
		$upload_dir = CAOS::get_local_dir();
		if ( ! is_dir( $upload_dir ) ) {
			wp_mkdir_p( $upload_dir );
		}
	}

	/**
	 * Register hook to schedule script in wp_cron()
	 */
	public function activate_cron() {
		if ( ! wp_next_scheduled( self::CRON_LABEL ) ) {
			wp_schedule_event( time(), 'twicedaily', self::CRON_LABEL );
		}
	}

	/**
	 *
	 */
	public function deactivate_cron() {
		if ( wp_next_scheduled( self::CRON_LABEL ) ) {
			wp_clear_scheduled_hook( self::CRON_LABEL );
		}
	}

	/**
	 *
	 */
	public function load_cron_script() {
		if ( CAOS::uses_minimal_analytics() ) {
			return;
		}

		new CAOS_Cron();
	}
}
