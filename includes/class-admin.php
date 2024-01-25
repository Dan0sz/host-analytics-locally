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

use WpOrg\Requests\Exception\InvalidArgument;

defined( 'ABSPATH' ) || exit;

class CAOS_Admin {

	const CAOS_ADMIN_JS_HANDLE          = 'caos-admin-js';
	const CAOS_ADMIN_CSS_HANDLE         = 'caos-admin-css';
	const CAOS_ADMIN_UTM_PARAMS_NOTICES = '?utm_source=caos&utm_medium=plugin&utm_campaign=notices';

	/**
	 * CAOS_Admin constructor.
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this, 'add_notice' ] );

		// Settings
		$this->do_basic_settings();
		$this->do_advanced_settings();
		$this->do_extensions_settings();
		$this->do_help_section();

		// Plugin Updates
		$this->handle_premium_plugin_updates();

		// Notices
		add_action( 'update_option_' . CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID, [ $this, 'add_tracking_code_notice' ], 10, 2 );
		add_action( 'update_option_' . CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION, [ $this, 'add_script_position_notice' ], 10, 2 );
		add_action( 'update_option_' . CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR, [ $this, 'set_cache_dir_notice' ], 10, 2 );
		add_action( 'pre_update_option_' . CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR, [ $this, 'validate_cache_dir' ], 10, 2 );
	}

	/**
	 * Add notice to admin screen.
	 */
	public function add_notice() {
		CAOS_Admin_Notice::print_notice();
	}

	/**
	 * @return CAOS_Admin_Settings_Basic
	 */
	private function do_basic_settings() {
		return new CAOS_Admin_Settings_Basic();
	}

	/**
	 * @return CAOS_Admin_Settings_Advanced
	 */
	private function do_advanced_settings() {
		return new CAOS_Admin_Settings_Advanced();
	}

	/**
	 * @return CAOS_Admin_Settings_Extensions
	 */
	private function do_extensions_settings() {
		return new CAOS_Admin_Settings_Extensions();
	}

	/**
	 * @return CAOS_Admin_Settings_Help
	 */
	private function do_help_section() {
		return new CAOS_Admin_Settings_Help();
	}

	/**
	 * @return CAOS_Admin_Updates
	 */
	private function handle_premium_plugin_updates() {
		return new CAOS_Admin_Updates(
			[
				'3940' => [
					'basename'        => 'caos-pro/caos-pro.php',
					'transient_label' => 'caos_pro',
				],
			],
			'host-analyticsjs-local',
			'caos'
		);
	}

	/**
	 * @param $new_tracking_id
	 * @param $old_tracking_id
	 *
	 * @return mixed
	 */
	public function add_tracking_code_notice( $old_tracking_id, $new_tracking_id ) {
		if ( $new_tracking_id !== $old_tracking_id && ! empty( $new_tracking_id ) ) {
			CAOS_Admin_Notice::set_notice( sprintf( __( 'CAOS has connected WordPress to Google Analytics using Measurement ID: %s.', 'host-analyticsjs-local' ), $new_tracking_id ) );
		}

		if ( empty( $new_tracking_id ) ) {
			return $new_tracking_id;
		}

		return $new_tracking_id;
	}

	/**
	 * @param $new_position
	 * @param $old_position
	 *
	 * @return mixed
	 */
	public function add_script_position_notice( $old_position, $new_position ) {
		if ( $new_position !== $old_position && ! empty( $new_position ) ) {
			switch ( $new_position ) {
				case 'manual':
					CAOS_Admin_Notice::set_notice( __( 'Since you\'ve chosen to add it manually, don\'t forget to add the tracking code to your theme.', 'host-analyticsjs-local' ), 'info' );
					break;
				default:
					CAOS_Admin_Notice::set_notice( sprintf( __( 'CAOS has added the tracking code to the %s of your site.', 'host-analyticsjs-local' ), $new_position ), 'success' );
					break;
			}
		}

		return $new_position;
	}

	/**
	 * Perform a few checks before saving the Cache Directory value to the database.
	 *
	 * @param mixed $new_dir
	 * @param mixed $old_dir
	 * @return mixed
	 */
	public function validate_cache_dir( $new_dir, $old_dir ) {
		$allowed_path = WP_CONTENT_DIR . $new_dir;
		$mkdir        = true;

		if ( ! file_exists( $allowed_path ) ) {
			/**
			 * wp_mkdir_p() already does some simple checks for path traversal, but we check it again using realpath() later on anyway.
			 */
			$mkdir = wp_mkdir_p( $allowed_path );
		}

		if ( ! $mkdir ) {
			CAOS_Admin_Notice::set_notice( sprintf( __( 'Something went wrong while trying to create CAOS\' Cache Directory: %s. Setting wasn\'t updated.', 'host-analyticsjs-local' ), $new_dir ), 'error' );

			return $old_dir;
		}

		$real_path = realpath( $allowed_path );

		if ( $real_path !== rtrim( $allowed_path, '/' ) ) {
			CAOS_Admin_Notice::set_notice( __( 'CAOS\' Cache Directory wasn\'t changed. Attempted path traversal.', 'host-analyticsjs-local' ), 'error' );

			return $old_dir;
		}

		return $new_dir;
	}

	/**
	 * @param $old_dir
	 * @param $new_dir
	 *
	 * @return string
	 */
	public function set_cache_dir_notice( $old_dir, $new_dir ) {
		if ( $new_dir !== $old_dir && ! empty( $new_dir ) ) {
			CAOS_Admin_Notice::set_notice( sprintf( __( 'Gtag.js will now be saved in <em>%s</em>.', 'host-analyticsjs-local' ), $new_dir ) );
		}

		return $new_dir;
	}
}
