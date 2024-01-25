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

class CAOS_FileManager {

	/**
	 * @var $file
	 */
	private $file_contents;

	/**
	 * Downloads $remoteFile, check if $localFile exists and if so deletes it, then writes it to $localFile
	 *
	 * @param $localFile
	 * @param $remoteFile
	 * @param $file string
	 *
	 * @return void|string
	 */
	public function download_file( $remote_file, $file = '' ) {
		do_action( 'caos_admin_update_before' );

		$this->file_contents = wp_remote_get( $remote_file );

		if ( is_wp_error( $this->file_contents ) ) {
			CAOS::debug( sprintf( __( 'An error occurred: %1$s - %2$s', 'host-analyticsjs-local' ), $this->file_contents->get_error_code(), $this->file_contents->get_error_message() ) );

			return $this->file_contents->get_error_code() . ': ' . $this->file_contents->get_error_message();
		}

		/**
		 * If $file is not set, extract it from $remote_file.
		 *
		 * @since 3.11.0
		 * @since 4.0.3  Don't rename plugins.
		 * @since 4.7.0  Plugins no longer exist.
		 */
		$file         = $file ? $file : pathinfo( $remote_file )['filename'];
		$file_aliases = CAOS::get_file_aliases();
		/**
		 * @var string $file_alias     Should end with .js!
		 *
		 * @filter     caos_file_alias Allows devs to set the filename of the downloaded JS library.
		 */
		$file_alias = apply_filters( 'caos_file_alias', $file_aliases[ $file ] ?? '' );

		/**
		 * If $file_alias equals 'gtag' then something has gone wrong previously while generating an alias. Let's try again.
		 *
		 * @since v4.7.3
		 */
		if ( empty( $file_alias ) || $file_alias === 'gtag' ) {
			$file_alias = bin2hex( random_bytes( 4 ) ) . '.js';
		}

		$local_dir = CAOS::get_local_dir();

		CAOS::debug( sprintf( __( 'Saving to %s.', 'host-analyticsjs-local' ), $local_dir ) );

		/**
		 * Some servers don't do a full overwrite if file already exists, so we delete it first.
		 */
		if ( $file_alias && file_exists( $local_dir . $file_alias ) ) {
			$deleted = unlink( $local_dir . $file_alias );

			if ( $deleted ) {
				CAOS::debug( sprintf( __( 'File %s successfully deleted.', 'host-analyticsjs-local' ), $file_alias ) );
			} else {
				if ( $error = error_get_last() ) {
					CAOS::debug( sprintf( __( 'File %1$s could not be deleted. Something went wrong: %2$s', 'host-analyticsjs-local' ), $file_alias, $error['message'] ) );
				} else {
					CAOS::debug( sprintf( __( 'File %s could not be deleted. An unknown error occurred.', 'host-analyticsjs-local' ), $file_alias ) );
				}
			}
		}

		$write = file_put_contents( $local_dir . $file_alias, $this->file_contents['body'] );

		if ( $write ) {
			CAOS::debug( sprintf( __( 'File %s successfully saved.', 'host-analyticsjs-local' ), $file_alias ) );
		} else {
			if ( $error = error_get_last() ) {
				CAOS::debug( sprintf( __( 'File %1$s could not be saved. Something went wrong: %2$s', 'host-analyticsjs-local' ), $file_alias, $error['message'] ) );
			} else {
				CAOS::debug( sprintf( __( 'File %s could not be saved. An unknown error occurred.', 'host-analyticsjs-local' ), $file_alias ) );
			}
		}

		/**
		 * Update the file alias in the global variable AND database.
		 */
		CAOS::set_file_alias( $file_alias, true );

		do_action( 'caos_admin_update_after' );

		return $local_dir . $file_alias;
	}

	/**
	 * Returns false if path already exists.
	 *
	 * @param mixed $path
	 * @return bool
	 */
	public function create_dir_recursive( $path ) {
		if ( ! file_exists( $path ) ) {
			return wp_mkdir_p( $path );
		}

		return false;
	}

	/**
	 * Find $find in $file and replace with $replace.
	 *
	 * @param $file string Absolute Path|URL
	 * @param $find array|string
	 * @param $replace array|string
	 */
	public function find_replace_in( $file, $find, $replace ) {
		CAOS::debug( sprintf( __( 'Replacing %1$s with %2$s in %3$s.', 'host-analyticsjs-local' ), print_r( $find, true ), print_r( $replace, true ), $file ) );

		return file_put_contents( $file, str_replace( $find, $replace, file_get_contents( $file ) ) );
	}
}
