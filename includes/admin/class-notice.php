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

class CAOS_Admin_Notice {

	const CAOS_ADMIN_NOTICE_TRANSIENT  = 'caos_admin_notice';
	const CAOS_ADMIN_NOTICE_EXPIRATION = 86400;

	/** @var array $notices */
	public static $notices = [];

	/**
	 * @param        $message
	 * @param bool   $die
	 * @param string $type
	 * @param int    $code
	 * @param string $screen_id
	 * @param string $id
	 */
	public static function set_notice( $message, $type = 'success', $screen_id = 'all', $id = '' ) {
		self::$notices                               = get_transient( self::CAOS_ADMIN_NOTICE_TRANSIENT );
		self::$notices[ $screen_id ][ $type ][ $id ] = $message;

		set_transient( self::CAOS_ADMIN_NOTICE_TRANSIENT, self::$notices, self::CAOS_ADMIN_NOTICE_EXPIRATION );
	}

	/**
	 * Prints notice (if any)
	 */
	public static function print_notice() {
		$admin_notices = get_transient( self::CAOS_ADMIN_NOTICE_TRANSIENT );

		if ( is_array( $admin_notices ) ) {
			$current_screen = get_current_screen();

			foreach ( $admin_notices as $screen => $notice ) {
				if ( $current_screen->id != $screen && $screen != 'all' ) {
					continue;
				}

				foreach ( $notice as $type => $message ) {
					?>
					<div id="message" class="notice notice-<?php echo $type; ?> is-dismissible">
						<?php foreach ( $message as $line ) : ?>
							<p><strong><?php echo $line; ?></strong></p>
						<?php endforeach; ?>
					</div>
					<?php
				}
			}
		}

		delete_transient( self::CAOS_ADMIN_NOTICE_TRANSIENT );
	}
}
