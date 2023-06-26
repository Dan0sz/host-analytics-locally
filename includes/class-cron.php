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

defined( 'ABSPATH' ) || exit;

class CAOS_Cron {

	/** @var [] $files */
	private $files;

	/** @var string $tweet */
	private $tweet = 'https://twitter.com/intent/tweet?text=I+am+now+hosting+%s+locally+for+Google+Analytics.+Thanks+to+CAOS+for+@WordPress!+Try+it+for+yourself:&via=Dan0sz&hashtags=GoogleAnalytics,WordPress,Pagespeed,Insights&url=https://wordpress.org/plugins/host-analyticsjs-local/';

	/** @var string $review */
	private $review = 'https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post';

	/** @var string $plugin_text_domain */
	private $plugin_text_domain = 'host-analyticsjs-local';

	/**
	 * CAOS_Cron_Script constructor.
	 */
	public function __construct() {
		do_action( 'caos_cron_update' );

		$this->files = $this->build_download_queue();

		CAOS::debug( sprintf( __( 'Built file queue: %s', $this->plugin_text_domain ), print_r( $this->files, true ) ) );

		// Check if directory exists, otherwise create it.
		$create_dir = CAOS::create_dir_r( CAOS_LOCAL_DIR );

		if ( $create_dir ) {
			CAOS::debug( sprintf( __( '%s created successfully.', $this->plugin_text_domain ), CAOS_LOCAL_DIR ) );
		} else {
			CAOS::debug( sprintf( __( '%s already exists.', $this->plugin_text_domain ), CAOS_LOCAL_DIR ) );
		}

		$downloaded_files = $this->download();

		// Only sent a success message if this is a AJAX request.
		if ( ! wp_doing_cron() && ! empty( $downloaded_files ) ) {
			$review_link = apply_filters( 'caos_manual_download_review_link', $this->review );
			$tweet_link  = apply_filters( 'caos_manual_download_tweet_link', $this->tweet );
			$notice      = __(
				'Gtag.js is downloaded successfully and updated accordingly.',
				$this->plugin_text_domain
			);

			CAOS_Admin_Notice::set_notice( $notice . ' ' . sprintf( __( 'Would you be willing to <a href="%1$s" target="_blank">write a review</a> or <a href="%2$s" target="_blank">tweet</a> about it?', 'host-analyticsjs-local' ), $review_link, $tweet_link ), 'success', 'all', 'file_downloaded' );
		}
	}

	/**
	 * Enqueues the files that need to be downloaded, depending on the settings.
	 *
	 * @since v4.2.0 Added Dual Tracking compatibility.
	 *
	 * @return array
	 */
	private function build_download_queue() {
		if ( CAOS::uses_minimal_analytics() ) {
			return [];
		}

		$queue = [];

		/**
		 * This is a "fix" for the undefined method error @since v4.2.2.
		 */
		if ( ! method_exists( 'CAOS', 'get_current_file_key' ) ) {
			return $queue;
		}

		$key   = CAOS::get_current_file_key();
		$queue = [
			$key => [
				'remote' => CAOS_GTM_URL . '/gtag/js?id=' . CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID ),
			],
		];

		return $queue;
	}

	/**
	 * Download files.
	 */
	private function download() {
		$this->tweet     = sprintf( $this->tweet, 'gtag.js' );
		$downloaded_file = CAOS::download_file( $this->files['gtag']['remote'], 'gtag' );
		$file_alias      = CAOS::get_file_alias();
		$home_url        = str_replace( [ 'https:', 'http:' ], '', WP_CONTENT_URL . CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR, '/uploads/caos/' ) );
		$hit_type        = apply_filters( 'caos_gtag_hit_type', '"pageview"' );
		$finds           = [ '/gtag/js?id=', '"//www.googletagmanager.com"', '"pageview"' ];
		$replaces        = [ $file_alias . '?id=', "\"$home_url\"", $hit_type ];

		CAOS::find_replace_in( $downloaded_file, $finds, $replaces );

		$this->tweet                 = sprintf( $this->tweet, 'gtag.js' );
		$downloaded_file             = apply_filters( 'caos_cron_update_gtag', $downloaded_file );
		$downloaded_files['gtag.js'] = CAOS::get_file_alias();

		/**
		 * Writes all currently stored file aliases to the database.
		 */
		CAOS::set_file_aliases( CAOS::get_file_aliases(), true );

		return $downloaded_files;
	}
}
