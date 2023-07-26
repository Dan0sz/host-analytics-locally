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
 * @copyright: © 2021 - 2023 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

use WpOrg\Requests\Exception\InvalidArgument;

defined( 'ABSPATH' ) || exit;

class CAOS_Admin {

	const CAOS_ADMIN_JS_HANDLE          = 'caos-admin-js';
	const CAOS_ADMIN_CSS_HANDLE         = 'caos-admin-css';
	const CAOS_ADMIN_UTM_PARAMS_NOTICES = '?utm_source=caos&utm_medium=plugin&utm_campaign=notices';

	/** @var string $plugin_text_domain */
	private $plugin_text_domain = 'host-analyticsjs-local';

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
		add_action( 'all_plugins', [ $this, 'maybe_display_premium_update_notice' ] );
		add_filter( 'wp_get_update_data', [ $this, 'maybe_add_update_count' ], 10, 1 );

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
	 * This function checks if:
	 * - CAOS Pro is installed,
	 * - And if so, if an update is already available for it.
	 * - And if not, if the current version is lower than the latest available version.
	 * - And if so, display a custom notice with instructions to download the update manually.
	 *
	 * CAOS Pro's internal ID is 3940.
	 *
	 * @param mixed $installed_plugins
	 *
	 * @return mixed
	 */
	public function maybe_display_premium_update_notice( $installed_plugins ) {
		$plugin_slugs = array_keys( $installed_plugins );

		/**
		 * If CAOS Pro isn't installed, there's no need to continue.
		 */
		if ( ! in_array( 'caos-pro/caos-pro.php', $plugin_slugs ) ) {
			return $installed_plugins;
		}

		if ( $this->update_already_displayed() ) {
			return $installed_plugins;
		}

		$latest_version  = $this->get_latest_version();
		$current_version = get_plugin_data( WP_PLUGIN_DIR . '/caos-pro/caos-pro.php' )['Version'] ?? '';

		/**
		 * If current version is lower than latest available version, take necessary measures to display notice.
		 */
		if ( version_compare( $current_version, $latest_version, '<' ) ) {
			$installed_plugins['caos-pro/caos-pro.php']['update'] = true;

			add_action( 'after_plugin_row_caos-pro/caos-pro.php', [ $this, 'display_premium_update_notice' ], 10, 3 );
		}

		return $installed_plugins;
	}

	/**
	 * Checks if there's already an update available for CAOS Pro in the Plugins screen.
	 *
	 * @return mixed
	 */
	private function update_already_displayed() {
		$available_updates = $this->get_available_updates();

		if ( ! is_object( $available_updates ) ) {
			return false;
		}

		$plugin_slugs = array_keys( $available_updates->response );

		return in_array( 'caos-pro/caos-pro.php', $plugin_slugs );
	}

	/**
	 * Fetch available updates from database.
	 *
	 * @return mixed
	 */
	private function get_available_updates() {
		static $available_updates;

		if ( $available_updates === null ) {
			$available_updates = get_site_transient( 'update_plugins' );
		}

		return $available_updates;
	}

	private function get_latest_version() {
		$latest_version = get_transient( 'caos_pro_latest_available_version' );

		/**
		 * If $latest_version is an empty string, that probably means something went wrong before. So,
		 * we should try and refresh it.
		 */
		if ( $latest_version === false || $latest_version === '' ) {
			$response       = wp_remote_get( 'https://daan.dev/?edd_action=get_version&item_id=3940' );
			$latest_version = json_decode( wp_remote_retrieve_body( $response ) )->new_version ?? '';

			set_transient( 'caos_pro_latest_available_version', $latest_version, DAY_IN_SECONDS );
		}

		return $latest_version;
	}

	/**
	 * Display a notice if current version of CAOS Pro is outdated, but updates can't be retrieved.
	 *
	 * @action after_plugin_row_caos-pro/caos-pro.php
	 *
	 * @param mixed $file
	 * @param mixed $plugin_data
	 * @param mixed $status
	 *
	 * @return void
	 */
	public function display_premium_update_notice( $file, $plugin_data, $status ) {
		$slug   = $plugin_data['slug'];
		$notice = sprintf( __( 'An update for CAOS Pro is available, but we\'re having trouble retrieving it. <a href=\'%s\' target=\'_blank\'>Download it from your account area</a> and install it manually.', 'host-analyticsjs-local' ), 'https://daan.dev/docs/pre-sales/download-files/' );

		/**
		 * This snippet of JS either overwrites or appends to the contents of the update message.
		 */
		?>
		<script>
			var row = document.getElementById('<?php echo esc_attr( $slug ); ?>-update');
			row.getElementsByTagName('p')[0].innerHTML = "<?php echo wp_kses( $notice, 'post' ); ?>";
		</script>
		<?php
	}

	/**
	 * This function check if:
	 * - CAOS Pro is installed,
	 * - And if so, if an update is already fetched and displayed by WP itself.
	 * - And if so, if the currently installed version is lower than the latest available version,
	 * - And if so, adds 1 to the little update nag next to "Plugins" in the sidebar to attract
	 *   attention to the fact that updates seem to be failing.
	 *
	 * @param mixed $update_data
	 * @param mixed $plugins
	 *
	 * @return mixed
	 *
	 * @throws InvalidArgument
	 */
	public function maybe_add_update_count( $update_data ) {
		if ( isset( $_GET['plugin_status'] ) && $_GET['plugin_status'] === 'upgrade' ) {
			return $update_data;
		}

		if ( $this->update_already_displayed() ) {
			return $update_data;
		}

		$latest_version  = $this->get_latest_version();
		$plugin_data     = get_plugin_data( WP_PLUGIN_DIR . '/caos-pro/caos-pro.php' );
		$current_version = $plugin_data['Version'] ?? '';

		if ( version_compare( $current_version, $latest_version, '<' ) ) {
			$update_data['counts']['plugins']++;
		}

		return $update_data;
	}

	/**
	 * @param $new_tracking_id
	 * @param $old_tracking_id
	 *
	 * @return mixed
	 */
	public function add_tracking_code_notice( $old_tracking_id, $new_tracking_id ) {
		if ( $new_tracking_id !== $old_tracking_id && ! empty( $new_tracking_id ) ) {
			CAOS_Admin_Notice::set_notice( sprintf( __( 'CAOS has connected WordPress to Google Analytics using Measurement ID: %s.', $this->plugin_text_domain ), $new_tracking_id ) );
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
					CAOS_Admin_Notice::set_notice( __( 'Since you\'ve chosen to add it manually, don\'t forget to add the tracking code to your theme.', $this->plugin_text_domain ), 'info' );
					break;
				default:
					CAOS_Admin_Notice::set_notice( __( "CAOS has added the tracking code to the $new_position of your site.", $this->plugin_text_domain ), 'success' );
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
			CAOS_Admin_Notice::set_notice( sprintf( __( 'Something went wrong while trying to create CAOS\' Cache Directory: %s. Setting wasn\'t updated.', $this->plugin_text_domain ), $new_dir ), 'error' );

			return $old_dir;
		}

		$real_path = realpath( $allowed_path );

		if ( $real_path != rtrim( $allowed_path, '/' ) ) {
			CAOS_Admin_Notice::set_notice( __( 'CAOS\' Cache Directory wasn\'t changed. Attempted path traversal.', $this->plugin_text_domain ), 'error' );

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
			CAOS_Admin_Notice::set_notice( sprintf( __( 'Gtag.js will now be saved in <em>%s</em>.', $this->plugin_text_domain ), $new_dir ) );
		}

		return $new_dir;
	}
}
