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

use CAOS\Plugin as CAOS;
use CAOS\Admin\Notice;
use CAOS\Admin\Settings;

defined( 'ABSPATH' ) || exit;

class Admin {

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

		// Notices
		add_action( 'update_option_' . Settings::CAOS_BASIC_SETTING_TRACKING_ID, [ $this, 'add_tracking_code_notice' ], 10, 2 );
		add_action( 'update_option_' . Settings::CAOS_BASIC_SETTING_DUAL_TRACKING, [ $this, 'maybe_remove_related_settings' ], 10, 2 );
		add_action( 'update_option_' . Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID, [ $this, 'update_remote_js_file' ], 10, 2 );
		add_action( 'update_option_' . Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION, [ $this, 'add_script_position_notice' ], 10, 2 );
		add_action( 'update_option_' . Settings::CAOS_ADV_SETTING_CACHE_DIR, [ $this, 'set_cache_dir_notice' ], 10, 2 );
		add_action( 'pre_update_option_' . Settings::CAOS_ADV_SETTING_CACHE_DIR, [ $this, 'validate_cache_dir' ], 10, 2 );
	}

	/**
	 * Add notice to admin screen.
	 */
	public function add_notice() {
		Notice::print_notice();
	}

	/**
	 * @return Settings_Basic
	 */
	private function do_basic_settings() {
		new \CAOS\Admin\Settings\Basic();
	}

	/**
	 * @return Settings_Advanced
	 */
	private function do_advanced_settings() {
		new \CAOS\Admin\Settings\Advanced();
	}

	/**
	 * @return Settings_Extensions
	 */
	private function do_extensions_settings() {
		new \CAOS\Admin\Settings\Extensions();
	}

	/**
	 * @return Settings_Help
	 */
	private function do_help_section() {
		new \CAOS\Admin\Settings\Help();
	}

	/**
	 * @param $new_tracking_id
	 * @param $old_tracking_id
	 *
	 * @return mixed
	 */
	public function add_tracking_code_notice( $old_tracking_id, $new_tracking_id ) {
		if ( $new_tracking_id !== $old_tracking_id && ! empty( $new_tracking_id ) ) {
			Notice::set_notice( sprintf( __( 'CAOS has connected WordPress to Google Analytics using Tracking ID: %s.', 'host-analyticsjs-local' ), $new_tracking_id ) );
		}

		if ( empty( $new_tracking_id ) ) {
			return $new_tracking_id;
		}

		$is_ga4    = substr( $new_tracking_id, 0, 2 ) === 'G-';
		$is_dt     = CAOS::get( Settings::CAOS_BASIC_SETTING_DUAL_TRACKING ) && strpos( CAOS::get( Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID ), 'G-' ) !== false;
		$dt_notice = '';

		if ( $is_ga4 && $is_dt ) {
			$title     = 'Universal Analytics';
			$filename  = 'gtag.js';
			$dt_notice = 'but enabled Dual Tracking,';
		} elseif ( $is_ga4 ) {
			$title    = 'Google Analytics 4';
			$filename = 'gtag-v4.js';

			/**
			 * When a GA V4 measurement ID is used, the defined ID in Dual Tracking should be emptied, just to make sure it isn't used anywhere.
			 */
			delete_option( Settings::CAOS_BASIC_SETTING_DUAL_TRACKING );
			delete_option( Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID );
		} else {
			$title    = 'Universal Analytics';
			$filename = 'analytics.js';
		}

		/**
		 * If Minimal Analytics is used, don't throw notices and execute any further logic to prevent confusion.
		 *
		 * @since v4.4.0
		 */
		if ( CAOS::uses_minimal_analytics() ) {
			return $new_tracking_id;
		}

		update_option( Settings::CAOS_ADV_SETTING_JS_FILE, $filename );

		Notice::set_notice(
			sprintf( __( 'Since you\'ve entered a %1$s ID, %2$s the <em>file to download</em> was changed to %3$s.', 'host-analyticsjs-local' ), $title, $dt_notice, $filename ),
			'warning'
		);

		if ( $filename === 'analytics.js' ) {
			Notice::set_notice(
				__( 'You can change the <em>file to download</em> manually to gtag.js in <em>Advanced Settings</em> if you wish to do so.', 'host-analyticsjs-local' ),
				'info'
			);
		}

		return $new_tracking_id;
	}

	/**
	 * Check if Dual Tracking is disabled and if so, remove GA4 Measurement ID.
	 *
	 * @param mixed $old_value
	 * @param mixed $new_value
	 * @return mixed
	 */
	public function maybe_remove_related_settings( $old_value, $new_value ) {
		if ( $new_value === $old_value ) {
			return $new_value;
		}

		// Dual tracking has been enabled. Let's delete related options.
		if ( $new_value !== 'on' ) {
			delete_option( Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID );

			/**
			 * This prevents the option from being added after this action is done running.
			 *
			 * @see /wp-admin/options.php:305-314
			 */
			if ( isset( $_POST[ Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID ] ) ) {
				unset( $_POST[ Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID ] );
			}
		}

		return $new_value;
	}

	/**
	 * Throw appropriate notices for enabling Dual Tracking.
	 *
	 * @param mixed $old_id
	 * @param mixed $new_id
	 * @return mixed
	 */
	public function update_remote_js_file( $old_id, $new_id ) {
		if ( strpos( $new_id, 'G-' ) !== 0 ) {
			Notice::set_notice(
				__( 'The entered Measurement ID isn\'t correct. Fix it to avoid breaking your Analytics.', 'host-analyticsjs-local' ),
				'error'
			);
		} elseif ( CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE ) !== 'gtag.js' ) {
			Notice::set_notice(
				__( 'Dual Tracking is enabled and the <em>file to download</em> was changed to <em>gtag.js</em>.', 'host-analyticsjs-local' ),
				'info'
			);

			update_option( Settings::CAOS_ADV_SETTING_JS_FILE, 'gtag.js' );
		} else {
			Notice::set_notice(
				__( 'Dual Tracking is enabled.', 'host-analyticsjs-local' ),
				'info'
			);
		}

		return $new_id;
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
					Notice::set_notice( __( 'Since you\'ve chosen to add it manually, don\'t forget to add the tracking code to your theme.', 'host-analyticsjs-local' ), 'info' );
					break;
				default:
					Notice::set_notice( __( "CAOS has added the tracking code to the $new_position of your site.", 'host-analyticsjs-local' ), 'success' );
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
			Notice::set_notice( sprintf( __( 'Something went wrong while trying to create CAOS\' Cache Directory: %s. Setting wasn\'t updated.', 'host-analyticsjs-local' ), $new_dir ), 'error' );

			return $old_dir;
		}

		$real_path = realpath( $allowed_path );

		if ( $real_path !== rtrim( $allowed_path, '/' ) ) {
			Notice::set_notice( __( 'CAOS\' Cache Directory wasn\'t changed. Attempted path traversal.', 'host-analyticsjs-local' ), 'error' );

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
			Notice::set_notice( sprintf( __( '%1$s will now be saved in <em>%2$s</em>.', 'host-analyticsjs-local' ), ucfirst( CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE ) ), $new_dir ) );
		}

		return $new_dir;
	}
}
