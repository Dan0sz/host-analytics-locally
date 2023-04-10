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
 * @copyright: (c) 2021 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */
namespace CAOS;

use CAOS\Plugin as CAOS;
use CAOS\Admin\Functions;
use CAOS\Admin\Notice;
use CAOS\Admin\Settings;

defined( 'ABSPATH' ) || exit;

class Setup {

	/** @var string $plugin_text_domain */
	protected $plugin_text_domain = 'host-analyticsjs-local';

	/**
	 * CAOS_Setup constructor.
	 */
	public function __construct() {
		register_activation_hook( CAOS_PLUGIN_FILE, [ $this, 'create_cache_dir' ] );
		register_activation_hook( CAOS_PLUGIN_FILE, [ $this, 'activate_cron' ] );
		register_activation_hook( CAOS_PLUGIN_FILE, [ $this, 'show_ad_block_message' ] );
		register_deactivation_hook( CAOS_PLUGIN_FILE, [ $this, 'deactivate_cron' ] );
		add_action( CAOS_CRON, [ $this, 'load_cron_script' ] );
	}

	/**
	 * Create Cache-dir upon reactivation.
	 */
	public function create_cache_dir() {
		$upload_dir = CAOS_LOCAL_DIR;
		if ( ! is_dir( $upload_dir ) ) {
			wp_mkdir_p( $upload_dir );
		}
	}

	/**
	 * Register hook to schedule script in wp_cron()
	 */
	public function activate_cron() {
		if ( ! wp_next_scheduled( CAOS_CRON ) ) {
			wp_schedule_event( time(), 'twicedaily', CAOS_CRON );
		}
	}

	/**
	 *
	 */
	public function show_ad_block_message() {
		$admin_url = admin_url( Settings::CAOS_SETTINGS_EXTENSIONS_TAB_URI );
		$message   = __( "Did you know <strong>~30%% of your visitors use Ad Blockers</strong>? CAOS now offers insights into the Ad Blocker usage of your visitors, i.e. the stats that're currently missing in your Google Analytics dashboard. Enable this option in <em>Settings > Optimize Google Analytics > <a href='%s'>Extensions</a></em>.", 'host-analyticsjs-local' );

		Notice::set_notice( sprintf( $message, $admin_url ), 'info' );
		set_transient( Functions::CAOS_ADMIN_BLOCKED_PAGES_NOTICE_SHOWN, true, WEEK_IN_SECONDS );
	}

	/**
	 *
	 */
	public function deactivate_cron() {
		if ( wp_next_scheduled( CAOS_CRON ) ) {
			wp_clear_scheduled_hook( CAOS_CRON );
		}
	}

	/**
	 *
	 */
	public function load_cron_script() {
		if ( CAOS::uses_minimal_analytics() ) {
			return;
		}

		new \CAOS\Cron();
	}
}
