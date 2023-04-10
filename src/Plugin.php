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
use CAOS\Admin\Settings;

defined( 'ABSPATH' ) || exit;
class Plugin {

	/**
	 * Used to check if CAOS Pro is (de)activated and update files (e.g. analytics.js) accordingly.
	 */
	const CAOS_PRO_PLUGIN_SLUG = 'caos-pro';

	/**
	 * Used for storing default values of certain settings.
	 */
	private static $defaults = [];

	/**
	 * CAOS constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->do_setup();

		if ( version_compare( CAOS_STORED_DB_VERSION, CAOS_DB_VERSION ) < 0 ) {
			$this->update_db();
		}

		// Save Settings
		add_action( 'admin_init', [ $this, 'update_settings' ] );

		if ( is_admin() ) {
			do_action( 'caos_before_admin' );

			$this->add_ajax_hooks();
			$this->do_settings();
		}

		if ( ! is_admin() ) {
			do_action( 'caos_before_frontend' );

			$this->do_frontend();
			$this->do_tracking_code();
		}

		// API Routes
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );

		// Automatic File Updates
		add_action( 'activated_plugin', [ $this, 'maybe_do_update' ] );
		add_action( 'deactivated_plugin', [ $this, 'maybe_do_update' ] );
		add_action( 'admin_init', [ $this, 'do_update_after_save' ] );
		add_action( 'upgrader_process_complete', [ $this, 'do_update_after_update' ], 10, 2 );
		add_action( 'in_plugin_update_message-' . CAOS_PLUGIN_BASENAME, [ $this, 'render_update_notice' ], 11, 2 );
	}

	/**
	 * Define constants
	 */
	public function define_constants() {
		global $caos_file_aliases;

		$caos_file_aliases      = get_option( Settings::CAOS_CRON_FILE_ALIASES );
		$translated_tracking_id = _x( 'UA-123456789', 'Define a different Tracking ID for this site.', 'host-analyticsjs-local' );

		self::$defaults = [
			Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER => 'google_analytics',
			Settings::CAOS_BASIC_SETTING_DOMAIN_NAME      => str_replace( [ 'https://', 'http://' ], '', get_home_url() ),
			Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION  => 'header',
			Settings::CAOS_ADV_SETTING_GA_SESSION_EXPIRY_DAYS => 30,
			Settings::CAOS_ADV_SETTING_SITE_SPEED_SAMPLE_RATE => 1,
			Settings::CAOS_ADV_SETTING_JS_FILE            => 'gtag.js',
			Settings::CAOS_ADV_SETTING_CACHE_DIR          => '/uploads/caos/',
		];

		define( 'CAOS_SITE_URL', 'https://daan.dev/blog' );
		define( 'CAOS_STORED_DB_VERSION', esc_attr( get_option( Settings::CAOS_DB_VERSION, '4.2.1' ) ) );
		define( 'CAOS_COOKIE_EXPIRY_SECONDS', CAOS::get( Settings::CAOS_ADV_SETTING_GA_SESSION_EXPIRY_DAYS ) ? CAOS::get( Settings::CAOS_ADV_SETTING_GA_SESSION_EXPIRY_DAYS ) * 86400 : 2592000 );
		define( 'CAOS_CRON', 'caos_update_analytics_js' );
		define( 'CAOS_GA_URL', 'https://www.google-analytics.com' );
		define( 'CAOS_GTM_URL', 'https://www.googletagmanager.com' );
		define( 'CAOS_LOCAL_DIR', WP_CONTENT_DIR . CAOS::get( Settings::CAOS_ADV_SETTING_CACHE_DIR ) );
	}

	/**
	 * We use a custom update action, because we're storing multidimensional arrays upon form submit.
	 *
	 * This prevents us from having to use AJAX, serialize(), stringify() and eventually having to json_decode() it, i.e.
	 * a lot of headaches.
	 *
	 * @since v4.6.0
	 */
	public function update_settings() {
		// phpcs:ignore WordPress.Security
		if ( empty( $_POST['action'] ) || $_POST['action'] !== 'caos-update' ) {
			return;
		}

		// phpcs:ignore
		$post_data = $this->clean($_POST);

		$options = apply_filters(
			/**
			 * Any options that're better off in their own DB row (e.g. due to size) can be added using this filter.
			 *
			 * @since v4.6.0
			 */
			'caos_update_settings_serialized',
			[
				'caos_settings',
			]
		);

		foreach ( $options as $option ) {
			if ( ! empty( $post_data[ $option ] ) ) {
				update_option( $option, $post_data[ $option ] );
			}
		}

		/**
		 * Additional update actions can be added here.
		 *
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
	 *
	 * @param string|array $var Sanitize the variable.
	 *
	 * @since 4.6.0
	 *
	 * @return string|array
	 */
	private function clean( $var ) {
		if ( is_array( $var ) ) {
			return array_map( [ __CLASS__, __METHOD__ ], $var );
		}

		return is_scalar( $var ) ? sanitize_text_field( wp_unslash( $var ) ) : $var;
	}

	/**
	 * Gets all settings for CAOS.
	 *
	 * @since 4.6.0
	 *
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
	 * Method to retrieve settings from database.
	 *
	 * @filter caos_setting_{$name}
	 *
	 * @param string $name
	 * @param mixed  $default (optional)
	 *
	 * @since v4.6.0
	 */
	public static function get( $name, $default = null ) {
		$value = self::get_settings()[ $name ] ?? '';

		if ( empty( $value ) && $default !== null ) {
			$value = $default;
		}

		/**
		 * If $default isn't set, let's check if a global default has been set.
		 */
		if ( empty( $value ) && $default !== null && isset( self::$defaults[ $name ] ) ) {
			$value = self::$defaults[ $name ];
		}

		return apply_filters( "caos_setting_$name", $value );
	}



	/**
	 * @return false|array
	 */
	public static function get_file_aliases() {
		global $caos_file_aliases;

		return $caos_file_aliases;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	public static function get_file_alias( $key = '' ) {
		$file_aliases = self::get_file_aliases();

		if ( ! $file_aliases ) {
			return '';
		}

		return $file_aliases[ $key ] ?? '';
	}

	/**
	 * Retrieves the currently used file key. Convenient when searching for file aliases.
	 *
	 * @return mixed
	 */
	public static function get_current_file_key() {
		return CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) === 'plausible' ? CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) : str_replace( '.js', '', CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE ) );
	}

	/**
	 * @param array $file_aliases
	 * @param bool $write
	 * @return bool
	 */
	public static function set_file_aliases( $file_aliases, $write = false ) {
		global $caos_file_aliases;

		$caos_file_aliases = $file_aliases;

		if ( $write ) {
			return update_option( Settings::CAOS_CRON_FILE_ALIASES, $file_aliases );
		}

		/**
		 * There's no reason to assume that updating a global variable would fail. Always return true at this point.
		 */
		return true;
	}

	/**
	 * @param string $key
	 * @param string $alias
	 * @param bool $write
	 * @return bool
	 */
	public static function set_file_alias( $key, $alias, $write = false ) {
		$file_aliases = self::get_file_aliases();

		$file_aliases[ $key ] = $alias;

		return self::set_file_aliases( $file_aliases, $write );
	}

	/**
	 * Includes backwards compatibility for pre 3.11.0
	 *
	 * @since 3.11.0
	 *
	 * @param mixed $key
	 * @return string|void
	 */
	public static function get_file_alias_path( $key ) {
		$file_path = CAOS_LOCAL_DIR . $key . '.js';

		// Backwards compatibility
		if ( ! self::get_file_aliases() ) {
			return $file_path;
		}

		$file_alias = self::get_file_alias( $key ) ?? '';

		// Backwards compatibility
		if ( ! $file_alias ) {
			return $file_path;
		}

		return CAOS_LOCAL_DIR . $file_alias;
	}

	/**
	 * Global debug logging function.
	 *
	 * @param mixed $message
	 * @return void
	 */
	public static function debug( $message ) {
		// phpcs:ignore
		if ( ! defined( 'CAOS_DEBUG_MODE' ) || CAOS_DEBUG_MODE === false ) {
			return;
		}

		// phpcs:ignore
		error_log( current_time( 'Y-m-d H:i:s' ) . ": $message\n", 3, trailingslashit( WP_CONTENT_DIR ) . 'caos-debug.log' );
	}

	/**
	 * @return \CAOS\Setup
	 */
	private function do_setup() {
		register_uninstall_hook( CAOS_PLUGIN_FILE, 'CAOS::do_uninstall' );

		return new \CAOS\Setup();
	}

	/**
	 * Triggers all required DB updates (if any).
	 *
	 * @return void
	 */
	private function update_db() {
		new \CAOS\DB();
	}

	/**
	 * Modify behavior of CAOS' AJAX hooks.
	 *
	 * @return void
	 */
	private function add_ajax_hooks() {
		new \CAOS\Ajax();
	}

	/**
	 * @return Settings
	 */
	private function do_settings() {
		new Settings();
	}

	/**
	 * @since v4.4.6 Write this class to a global variable to allow usage by 3rd parties.
	 *
	 * @return \CAOS\Frontend\Functions
	 */
	private function do_frontend() {
		global $caos_frontend;

		$caos_frontend = new \CAOS\Frontend\Functions();

		return $caos_frontend;
	}

	/**
	 * @since v4.4.6 Write this class to a global variable to allow usage by 3rd parties.
	 *
	 * @return \CAOS\Frontend\Tracking
	 */
	private function do_tracking_code() {
		global $caos_frontend_tracking;

		$caos_frontend_tracking = new \CAOS\Frontend\Tracking();

		return $caos_frontend_tracking;
	}

	/**
	 * Triggers when CAOS (Pro) is (de)activated.
	 *
	 * @return \CAOS\Cron
	 */
	public function trigger_cron_script() {
		return new \CAOS\Cron();
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
	 * @return UpdateFiles
	 */
	public function do_update_after_save() {
		// phpcs:disable
		$settings_page    = $_GET['page'] ?? '';
		$settings_updated = $_GET['settings-updated'] ?? '';
		// phpcs:enable

		if ( Settings::CAOS_ADMIN_PAGE !== $settings_page ) {
			return;
		}

		if ( ! $settings_updated ) {
			return;
		}

		return $this->trigger_cron_script();
	}

	/**
	 * Make sure downloaded files are updated after plugin is updated.
	 *
	 * @param mixed $upgrade_obj
	 * @param array $options
	 * @return void|CAOS_Cron
	 */
	public function do_update_after_update( $upgrade_obj, $options ) {
		if (
			isset( $options['action'] ) && $options['action'] !== 'update'
			&& isset( $options['type'] ) && $options['type'] !== 'plugin'
		) {
			return;
		}

		if ( ! isset( $options['plugins'] ) ) {
			return;
		}

		foreach ( $options['plugins'] as $plugin ) {
			if ( $plugin === CAOS_PLUGIN_BASENAME ) {
				return $this->trigger_cron_script();
			}
		}
	}

	/**
	 * Render update notices if available.
	 *
	 * @param mixed $plugin
	 * @param mixed $response
	 * @return void
	 */
	public function render_update_notice( $plugin, $response ) {
		$current_version = $plugin['Version'];
		$new_version     = $plugin['new_version'];

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
				' <strong>' . __( 'This update includes major changes. Please <a href="%s" target="_blank">read this</a> before updating.', 'host-analyticsjs-local' ) . '</strong>',
				$update_notices[ $new_version ]->url
			);
		}
	}

	/**
	 * Register CAOS Proxy so endpoint can be used.
	 * For using Stealth mode, SSL is required.
	 */
	public function register_routes() {
		if ( CAOS::get( Settings::CAOS_EXT_SETTING_TRACK_AD_BLOCKERS ) ) {
			$proxy = new \CAOS\API\AdBlockDetect();
			$proxy->register_routes();
		}
	}

	/**
	 * Returns early if File Aliases option doesn't exist for Backwards Compatibility.
	 *
	 * @since 3.11.0
	 *
	 * @return string
	 */
	public static function get_local_file_url() {
		$url = content_url() . CAOS::get( Settings::CAOS_ADV_SETTING_CACHE_DIR ) . CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE );

		/**
		 * is_ssl() fails when behind a load balancer or reverse proxy. That's why we double check here if
		 * SSL is enabled and rewrite accordingly.
		 */
		if ( strpos( home_url(), 'https://' ) !== false && ! is_ssl() ) {
			$url = str_replace( 'http://', 'https://', $url );
		}

		$cdn_url = CAOS::get( Settings::CAOS_ADV_SETTING_CDN_URL );

		if ( $cdn_url ) {
			$url = str_replace( get_home_url( get_current_blog_id() ), '//' . $cdn_url, $url );
		}

		if ( ! self::get_file_aliases() ) {
			return $url;
		}

		$file_alias = self::get_file_alias( CAOS::get_current_file_key() );

		if ( ! $file_alias ) {
			return $url;
		}

		$url = str_replace( CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE ), $file_alias, $url );

		return $url;
	}

	/**
	 * @return CAOS_Uninstall
	 * @throws ReflectionException
	 */
	public static function do_uninstall() {
		return new \CAOS\Uninstall();
	}

	/**
	 * File downloader
	 *
	 * @param mixed $local_file
	 * @param mixed $remote_file
	 * @param string $file
	 * @param bool $is_plugin
	 *
	 * @return string
	 */
	public static function download_file(
		$local_file,
		$remote_file,
		$file = '',
		$is_plugin = false
	) {
		$download = new \CAOS\FileManager();

		return $download->download_file( $local_file, $remote_file, $file, $is_plugin );
	}

	/**
	 * @param string $path
	 *
	 * @return bool
	 */
	public static function create_dir_r( $path ) {
		$file_manager = new \CAOS\FileManager();

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
		$file_manager = new \CAOS\FileManager();

		return $file_manager->find_replace_in( $file, $find, $replace );
	}

	/**
	 *
	 */
	public static function uses_minimal_analytics() {
		return CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) === 'minimal' || CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) === 'minimal_ga4';
	}

	/**
	 * Global method to figure out if CAOS is setup to use Dual Tracking.
	 *
	 * @return bool
	 */
	public static function dual_tracking_is_enabled() {
		return strpos( CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_ID ), 'UA-' ) === 0 && CAOS::get( Settings::CAOS_BASIC_SETTING_DUAL_TRACKING ) === 'on';
	}

	/**
	 * Global method to check if CAOS is set to use GA4.
	 *
	 * @return bool
	 */
	public static function uses_ga4() {
		return strpos( CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_ID ), 'G' ) === 0 && CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE ) === 'gtag-v4.js';
	}
}
