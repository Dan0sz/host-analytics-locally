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
class CAOS_DB_Migrate {

	/**
	 * @var array Should contain an array of old and new option_name values, e.g.
	 *            [ 'old_option_name' => 'new_option_name' ]
	 */
	protected $migrate_option_names = [];

	/**
	 * @var array Should contain an array of option_names containing an array of allowed values.
	 */
	protected $update_option_values = [];

	/**
	 * @var string $version The version number this migration script was introduced with.
	 */
	protected $version = '';

	/**
	 * @return void
	 */
	protected function migrate_option_names() {
		foreach ( $this->migrate_option_names as $old => $new ) {
			$old_value = get_option( $old );

			update_option( $new, $old_value );
			delete_option( $old );
		}
	}

	/**
	 * @return void
	 */
	protected function update_option_values() {
		foreach ( $this->update_option_values as $option_name => $allowed_values ) {
			$old_value = get_option( $option_name );

			/**
			 * If current value isn't allowed after this DB upgrade, update it to the first allowed value.
			 */
			if ( ! in_array( $old_value, $allowed_values ) ) {
				/**
				 * If the old option was 'off', make sure the new option is still marked as 'off'.
				 */
				if ( $old_value == '' ) {
					update_option( $option_name, '' );
				}

				update_option( $option_name, $allowed_values[0] );
			}
		}
	}

	/**
	 * Update stored version number.
	 */
	protected function update_db_version() {
		update_option( CAOS_Admin_Settings::CAOS_DB_VERSION, $this->version );
	}
}
