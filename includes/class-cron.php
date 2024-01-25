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

class CAOS_Cron {
	/** @var string $tweet */
	private $tweet = 'https://twitter.com/intent/tweet?text=I+am+now+hosting+gtag.js+locally+for+Google+Analytics.+Thanks+to+CAOS+for+@WordPress!+Try+it+for+yourself:&via=Dan0sz&hashtags=GoogleAnalytics,WordPress,Pagespeed,Insights&url=https://wordpress.org/plugins/host-analyticsjs-local/';

	/** @var string $review */
	private $review = 'https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post';

	/**
	 * CAOS_Cron_Script constructor.
	 */
	public function __construct() {
		do_action( 'caos_cron_update' );

		// Check if directory exists, otherwise create it.
		$create_dir = CAOS::create_dir_r( CAOS::get_local_dir() );

		if ( $create_dir ) {
			CAOS::debug( sprintf( __( '%s created successfully.', 'host-analyticsjs-local' ), CAOS::get_local_dir() ) );
		} else {
			CAOS::debug( sprintf( __( '%s already exists.', 'host-analyticsjs-local' ), CAOS::get_local_dir() ) );
		}

		$downloaded_files = $this->download();

		// Only sent a success message if this is an AJAX request.
		if ( ! wp_doing_cron() && ! empty( $downloaded_files ) ) {
			$review_link = apply_filters( 'caos_manual_download_review_link', $this->review );
			$tweet_link  = apply_filters( 'caos_manual_download_tweet_link', $this->tweet );
			$notice      = __(
				'Gtag.js is downloaded successfully and updated accordingly.',
				'host-analyticsjs-local'
			);

			CAOS_Admin_Notice::set_notice(
				$notice .
				' ' .
				sprintf(
					__(
						'Would you be willing to <a href="%1$s" target="_blank">write a review</a> or <a href="%2$s" target="_blank">tweet</a> about it?',
						'host-analyticsjs-local'
					),
					$review_link,
					$tweet_link
				),
				'success',
				'all',
				'file_downloaded'
			);
		}
	}

	/**
	 * Download files.
	 */
	private function download() {
		if ( CAOS::uses_minimal_analytics() ) {
			return '';
		}

		$remote_file     = 'https://www.googletagmanager.com/gtag/js?id=' . CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID );
		$downloaded_file = CAOS::download_file( $remote_file, 'gtag' );
		$file_alias      = CAOS::get_file_alias();
		$home_url        = str_replace( [ 'https:', 'http:' ], '', CAOS::get_local_dir() );
		$hit_type        = apply_filters( 'caos_gtag_hit_type', '"page_view"' );
		$finds           = [ '/gtag/js?id=', '"//www.googletagmanager.com"', '"page_view"' ];
		$replaces        = [ $file_alias . '?id=', "\"$home_url\"", $hit_type ];

		CAOS::find_replace_in( $downloaded_file, $finds, $replaces );

		$downloaded_file             = apply_filters( 'caos_cron_update_gtag', $downloaded_file );
		$downloaded_files['gtag.js'] = CAOS::get_file_alias();

		/**
		 * Writes all currently stored file aliases to the database.
		 */
		CAOS::set_file_aliases( CAOS::get_file_aliases(), true );

		return $downloaded_files;
	}
}
