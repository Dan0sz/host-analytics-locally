<?php
defined( 'ABSPATH' ) || exit;

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

class CAOS {
	/**
	 * Used to check if CAOS Pro is (de)activated and update files (e.g. gtag.js) accordingly.
	 */
	const CAOS_PRO_PLUGIN_SLUG = 'caos-pro';

	/**
	 * CAOS constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->do_setup();

		if ( version_compare( CAOS_STORED_DB_VERSION, CAOS_DB_VERSION ) < 0 ) {
			new CAOS_DB();
		}

		if ( is_admin() ) {
			do_action( 'caos_before_admin' );

			new CAOS_Ajax();
			new CAOS_Admin_Settings();
		}

		if ( ! is_admin() ) {
			do_action( 'caos_before_frontend' );

			new CAOS_Frontend_Compatibility();
			new CAOS_Frontend_Functions();
			new CAOS_Frontend_Tracking();
		}

		// Update Settings
		add_action( 'admin_init', [ $this, 'update_settings' ] );

		// Automatic File Updates
		add_action( 'activated_plugin', [ $this, 'maybe_do_update' ] );
		add_action( 'deactivated_plugin', [ $this, 'maybe_do_update' ] );
		add_action( 'admin_init', [ $this, 'do_update_after_save' ] );
		add_action( 'in_plugin_update_message-' . CAOS_PLUGIN_BASENAME, [ $this, 'render_update_notice' ], 11, 2 );
	}

	/**
	 * Define constants
	 */
	public function define_constants() {
		global $caos_file_aliases;

		$caos_file_aliases = get_option( CAOS_Admin_Settings::CAOS_CRON_FILE_ALIASES );

		define( 'CAOS_SITE_URL', 'https://daan.dev/blog' );
		define( 'CAOS_STORED_DB_VERSION', esc_attr( get_option( CAOS_Admin_Settings::CAOS_DB_VERSION, '4.2.1' ) ) );
	}

	/**
	 * @return CAOS_Setup
	 */
	private function do_setup() {
		register_uninstall_hook( CAOS_PLUGIN_FILE, 'CAOS::do_uninstall' );

		return new CAOS_Setup();
	}

	/**
	 * @param string $alias
	 * @param bool   $write
	 *
	 * @return bool
	 */
	public static function set_file_alias( $alias, $write = false ) {
		$file_aliases = self::get_file_aliases();

		$file_aliases[ 'gtag' ] = $alias;

		return self::set_file_aliases( $file_aliases, $write );
	}

	/**
	 * @return false|array Global variable containing all saved file aliases.
	 */
	public static function get_file_aliases() {
		global $caos_file_aliases;

		return $caos_file_aliases;
	}

	/**
	 * @param array $file_aliases
	 * @param bool  $write
	 *
	 * @return bool
	 */
	public static function set_file_aliases( $file_aliases, $write = false ) {
		global $caos_file_aliases;

		$caos_file_aliases = $file_aliases;

		if ( $write ) {
			return update_option( CAOS_Admin_Settings::CAOS_CRON_FILE_ALIASES, $file_aliases );
		}

		/**
		 * There's no reason to assume that updating a global variable would fail. Always return true at this point.
		 */
		return true;
	}

	/**
	 * Includes backwards compatibility for pre 3.11.0
	 * @since 3.11.0
	 * @return string|void
	 */
	public static function get_file_alias_path() {
		$file_path = self::get_local_dir() . 'gtag.js';

		// Backwards compatibility
		if ( ! self::get_file_aliases() ) {
			return $file_path;
		}

		$file_alias = self::get_file_alias() ?? '';

		// Backwards compatibility
		if ( ! $file_alias ) {
			return $file_path;
		}

		return self::get_local_dir() . $file_alias;
	}

	/**
	 * @since v4.7.3
	 * @return string Absolute path to CAOS' cache directory.
	 */
	public static function get_local_dir() {
		return apply_filters( 'caos_local_dir', WP_CONTENT_DIR . self::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR, '/uploads/caos/' ) );
	}

	/**
	 * Method to retrieve settings from database.
	 * @filter caos_setting_{$name}
	 * @since  v4.5.1
	 *
	 * @param mixed  $default (optional) The option's default value if it isn't set (yet).
	 * @param string $name    Any constant from the CAOS_Admin_Settings class.
	 *
	 * @return mixed
	 */
	public static function get( $name, $default = null ) {
		$value = self::get_settings()[ $name ] ?? '';

		if ( empty( $value ) && $default !== null ) {
			$value = $default;
		}

		/**
		 * This allows for WPML (and similar plugins) to use different tracking IDs on different languages/sites.
		 */
		if ( $name === CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID ) {
			$translated_tracking_id = _x( 'G-123ABC789', 'Define a different Measurement ID for this language/site.', 'host-analyticsjs-local' );

			if ( $translated_tracking_id !== 'G-123ABC789' ) {
				$value = $translated_tracking_id;
			}
		}

		return apply_filters( "caos_setting_$name", $value );
	}

	/**
	 * Gets all settings for CAOS.
	 * @since 4.5.1
	 * @return array
	 */
	public static function get_settings() {
		static $settings;

		if ( empty( $settings ) ) {
			$settings = get_option( 'caos_settings', [] );
		}

		return apply_filters( 'caos_settings', $settings );
	}

	/**
	 * Get alias of JS library.
	 * @return string
	 */
	public static function get_file_alias() {
		$file_aliases = self::get_file_aliases();

		if ( ! $file_aliases ) {
			return '';
		}

		return $file_aliases[ 'gtag' ] ?? '';
	}

	/**
	 * Global debug logging function.
	 *
	 * @param mixed $message
	 *
	 * @return void
	 */
	public static function debug( $message ) {
		if ( ! defined( 'CAOS_DEBUG_MODE' ) || CAOS_DEBUG_MODE === false ) {
			return;
		}

		// phpcs:ignore
		error_log( current_time( 'Y-m-d H:i:s' ) . ": $message\n", 3, trailingslashit( WP_CONTENT_DIR ) . 'caos-debug.log' );
	}

	/**
	 * Returns early if File Aliases option doesn't exist for Backwards Compatibility.
	 * @since 3.11.0
	 * @return string
	 */
	public static function get_local_file_url() {
		$url = content_url() . self::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR, '/uploads/caos/' ) . 'gtag.js';

		/**
		 * is_ssl() fails when behind a load balancer or reverse proxy. That's why we double check here if
		 * SSL is enabled and rewrite accordingly.
		 */
		if ( strpos( home_url(), 'https://' ) !== false && ! is_ssl() ) {
			$url = str_replace( 'http://', 'https://', $url );
		}

		if ( self::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_CDN_URL ) ) {
			$url = str_replace( get_home_url( get_current_blog_id() ), '//' . self::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_CDN_URL ), $url );
		}

		$file_alias = self::get_file_alias();

		if ( ! $file_alias ) {
			return $url;
		}

		$url = str_replace( 'gtag.js', $file_alias, $url );

		return apply_filters( 'caos_local_file_url', $url );
	}

	/**
	 * @return CAOS_Uninstall
	 * @throws ReflectionException
	 */
	public static function do_uninstall() {
		return new CAOS_Uninstall();
	}

	/**
	 * File downloader
	 *
	 * @param mixed  $local_file
	 * @param mixed  $remote_file
	 * @param string $file
	 *
	 * @return string
	 */
	public static function download_file( $remote_file, $file = '' ) {
		$download = new CAOS_FileManager();

		return $download->download_file( $remote_file, $file );
	}

	/**
	 * @param string $path
	 *
	 * @return bool
	 */
	public static function create_dir_r( $path ) {
		$file_manager = new CAOS_FileManager();

		return $file_manager->create_dir_recursive( $path );
	}

	/**
	 * @param string $file
	 * @param string $find
	 * @param string $replace
	 *
	 * @return int|false
	 */
	public static function find_replace_in( $file, $find, $replace ) {
		$file_manager = new CAOS_FileManager();

		return $file_manager->find_replace_in( $file, $find, $replace );
	}

	/**
	 *
	 */
	public static function uses_minimal_analytics() {
		return self::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) === 'minimal_ga4';
	}

	/**
	 * Check if (de)activated plugin is CAOS Pro and if so, update.
	 */
	public function maybe_do_update( $plugin ) {
		if ( strpos( $plugin, self::CAOS_PRO_PLUGIN_SLUG ) === false ) {
			return;
		}

		$this->trigger_cron_script();
	}

	/**
	 * Triggers when CAOS (Pro) is (de)activated.
	 * @return CAOS_Cron
	 */
	public function trigger_cron_script() {
		return new CAOS_Cron();
	}

	/**
	 * @return CAOS_Cron|void
	 */
	public function do_update_after_save() {
		$settings_page    = $_GET[ 'page' ] ?? '';
		$settings_updated = $_GET[ 'settings-updated' ] ?? '';

		if ( CAOS_Admin_Settings::CAOS_ADMIN_PAGE !== $settings_page ) {
			return;
		}

		if ( ! $settings_updated ) {
			return;
		}

		/**
		 * No need to update any files if we're using Minimal Analytics. Can't believe I'm only finding out about this now...
		 * @since 4.7.0
		 */
		if ( self::get( 'tracking_code' ) === 'minimal_ga4' ) {
			return;
		}

		return $this->trigger_cron_script();
	}

	/**
	 * Render update notices if available.
	 *
	 * @param mixed $plugin
	 * @param mixed $response
	 *
	 * @return void
	 */
	public function render_update_notice( $plugin, $response ) {
		$current_version = $plugin[ 'Version' ];
		$new_version     = $plugin[ 'new_version' ];

		if ( version_compare( $current_version, $new_version, '<' ) ) {
			$response = wp_remote_get( 'https://daan.dev/caos-update-notices.json' );

			if ( is_wp_error( $response ) ) {
				return;
			}

			$update_notices = (array) json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! isset( $update_notices[ $new_version ] ) ) {
				return;
			}

			printf(
				' <strong>' . __(
					'This update includes major changes. Please <a href="%s" target="_blank">read this</a> before updating.',
					'host-analyticsjs-local'
				) . '</strong>',
				$update_notices[ $new_version ]->url
			);
		}
	}

	/**
	 * We use a custom update action, because we're storing multidimensional arrays upon form submit.
	 * This prevents us from having to use AJAX, serialize(), stringify() and eventually having to json_decode() it, i.e.
	 * a lot of headaches.
	 * @since v4.6.0
	 */
	public function update_settings() {
		if ( empty( $_POST[ 'action' ] ) || $_POST[ 'action' ] !== 'caos-update' ) {
			return;
		}

		$action = $_GET[ 'tab' ] ? $_GET[ 'tab' ] . '-options' : 'caos-basic-settings-options';
		$nonce  = $_POST[ '_wpnonce' ] ?? '';

		if ( wp_verify_nonce( $nonce, $action ) < 1 ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$post_data = $this->clean( $_POST );

		/**
		 * Any options that're better off in their own DB row (e.g. due to size) can be added using this filter.
		 * @since v4.6.0
		 */
		$options = apply_filters( 'caos_update_settings_serialized', [ 'caos_settings', ] );

		foreach ( $options as $option ) {
			if ( ! empty( $post_data[ $option ] ) ) {
				$current_options = get_option( $option );

				if ( $current_options ) {
					$merged = array_replace( $current_options, $post_data[ $option ] );

					update_option( $option, $merged );
				} else {
					update_option( $option, $post_data[ $option ] );
				}
			}
		}

		/**
		 * Additional update actions can be added here.
		 * @since v4.6.0
		 */
		do_action( 'caos_update_settings' );

		// Redirect back to the settings page that was submitted.
		$goback = add_query_arg( 'settings-updated', 'true', wp_get_referer() );
		wp_redirect( $goback );
		exit;
	}

	/**
	 * Clean variables using `sanitize_text_field`.
	 * Arrays are cleaned recursively. Non-scalar values are ignored.
	 * @since 4.6.0
	 *
	 * @param string|array $var Sanitize the variable.
	 *
	 * @return string|array
	 */
	private function clean( $var ) {
		if ( is_array( $var ) ) {
			return array_map( [ __CLASS__, __METHOD__ ], $var );
		}

		return is_scalar( $var ) ? sanitize_text_field( wp_unslash( $var ) ) : $var;
	}
}
