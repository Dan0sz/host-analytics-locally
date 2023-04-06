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
* @copyright: (c) 2021 Daan van den Bergh
* @license  : GPL2v2 or later
* * * * * * * * * * * * * * * * * * * */
namespace CAOS;

use CAOS\Admin\Settings;
use CAOS\Plugin as CAOS;

defined( 'ABSPATH' ) || exit;

class Ajax {

	/**
	 * Build class.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init hooks and filters.
	 *
	 * @return void
	 */
	private function init() {
		add_action( 'wp_ajax_caos_regenerate_alias', [ $this, 'regenerate_alias' ] );
	}

	/**
	 * Regenerate aliases and new files. Cleans up old files.
	 *
	 * @since v4.2.1
	 *
	 * @return void
	 */
	public function regenerate_alias() {
		check_ajax_referer( Settings::CAOS_ADMIN_PAGE, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( "Sorry, you're not allowed to do this.", 'host-analyticsjs-local' ) );
		}

		global $caos_file_aliases;

		if ( empty( $caos_file_aliases ) || ! $caos_file_aliases ) {
			return;
		}

		$path = WP_CONTENT_DIR . CAOS_OPT_CACHE_DIR;

		foreach ( $caos_file_aliases as $file => $alias ) {
			if ( file_exists( $path . $alias ) ) {
				unlink( $path . $alias );
			}

			$caos_file_aliases[ $file ] = bin2hex( random_bytes( 4 ) ) . '.js';
		}

		CAOS::set_file_aliases( $caos_file_aliases, true );
	}
}
