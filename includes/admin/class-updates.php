<?php
defined( 'ABSPATH' ) || exit;

/**
 * A (kind of) portable file, which allows me to add some extra handling to updates for premium "daughters"
 * of freemium (mother) plugins.
 *
 * Basically, what this class does is make sure that, if automatic updates seem to be failing, the user is
 * informed (in a non-intrusive manner, i.e. the Plugins screen) of the fact that an update is indeed
 * available and where/how to download/install it manually.
 *
 * @package Daan/Updates
 */
class CAOS_Admin_Updates {
	/** @var string $label */
	private $label = 'CAOS Pro';

	/** @var string $basename */
	private $basename = 'caos-pro/caos-pro.php';

	/** @var string $id */
	private $id = '3940';

	/** @var string $transient_label */
	private $transient_label = 'caos_pro';

	/**
	 * Action & Filter hooks.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'all_plugins', [ $this, 'maybe_display_premium_update_notice' ] );
		add_filter( 'wp_get_update_data', [ $this, 'maybe_add_update_count' ], 10, 1 );
	}

	/**
	 * This function checks if:
	 * - Premium plugin is installed,
	 * - And if so, if an update is already available for it.
	 * - And if not, if the current version is lower than the latest available version.
	 * - And if so, display a custom notice with instructions to download the update manually.
	 *
	 * @param mixed $installed_plugins
	 *
	 * @return mixed
	 */
	public function maybe_display_premium_update_notice( $installed_plugins ) {
		$plugin_slugs = array_keys( $installed_plugins );

		/**
		 * If premium plugin isn't installed, there's no need to continue.
		 */
		if ( ! in_array( $this->basename, $plugin_slugs ) ) {
			return $installed_plugins;
		}

		if ( $this->update_already_displayed() ) {
			return $installed_plugins;
		}

		$latest_version  = $this->get_latest_version();
		$current_version = get_plugin_data( WP_PLUGIN_DIR . '/' . $this->basename )['Version'] ?? '';

		/**
		 * If current version is lower than latest available version, take necessary measures to display notice.
		 */
		if ( version_compare( $current_version, $latest_version, '<' ) ) {
			$installed_plugins[ $this->basename ]['update'] = true;

			add_action( 'after_plugin_row_' . $this->basename, [ $this, 'display_premium_update_notice' ], 10, 2 );
		}

		return $installed_plugins;
	}

	/**
	 * Checks if there's already an update available for the premium plugin in the Plugins screen.
	 *
	 * @return mixed
	 */
	private function update_already_displayed() {
		$available_updates = $this->get_available_updates();

		if ( ! is_object( $available_updates ) ) {
			return false;
		}

		$plugin_slugs = array_keys( $available_updates->response );

		return in_array( $this->basename, $plugin_slugs );
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

	/**
	 * Gets the latest available version of the current premium plugin.
	 */
	private function get_latest_version() {
		static $latest_version;

		/**
		 * This prevents duplicate DB reads.
		 */
		if ( $latest_version === null ) {
			$latest_version = get_transient( $this->transient_label . '_latest_available_version' );
		}

		/**
		 * If $latest_version is an empty string, that probably means something went wrong before. So,
		 * we should try and refresh it. If $latest_version is false, then the transient doesn't exist.
		 */
		if ( $latest_version === false || $latest_version === '' ) {
			$response       = wp_remote_get( 'https://daan.dev/?edd_action=get_version&item_id=' . $this->id );
			$latest_version = json_decode( wp_remote_retrieve_body( $response ) )->new_version ?? '';

			set_transient( $this->transient_label . '_latest_available_version', $latest_version, DAY_IN_SECONDS );
		}

		return $latest_version;
	}

	/**
	 * Display a notice if current version of premium plugin is outdated, but updates can't be retrieved.
	 *
	 * @action after_plugin_row_{plugin_basename}
	 *
	 * @param mixed $file
	 * @param mixed $plugin_data
	 * @param mixed $status
	 *
	 * @return void
	 */
	public function display_premium_update_notice( $file, $plugin_data ) {
		$slug   = $plugin_data['slug'];
		$notice = sprintf( __( 'An update for %1$s is available, but we\'re having trouble retrieving it. <a href=\'%2$s\' target=\'_blank\'>Download it from your account area</a> and install it manually.', 'host-analyticsjs-local' ), $this->label, 'https://daan.dev/docs/pre-sales/download-files/' );

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
	 * - Premium plugin is installed,
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
		$plugin_data     = get_plugin_data( WP_PLUGIN_DIR . '/' . $this->basename );
		$current_version = $plugin_data['Version'] ?? '';

		if ( version_compare( $current_version, $latest_version, '<' ) ) {
			$update_data['counts']['plugins']++;
		}

		return $update_data;
	}
}
