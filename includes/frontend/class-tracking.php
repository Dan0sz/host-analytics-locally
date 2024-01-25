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

class CAOS_Frontend_Tracking {
	/** @var string $handle */
	public $handle = '';

	/**
	 * @var array $page_builders Array of keys set by page builders when they're displaying their previews.
	 */
	private $page_builders = [
		'bt-beaverbuildertheme',
		'ct_builder',
		'elementor-preview',
		'et_fb',
		'fb-edit',
		'fl_builder',
		'siteorigin_panels_live_editor',
		'tve',
		'vc_action',
	];

	/** @var bool $in_footer For use in wp_enqueue_scripts() etc. */
	private $in_footer = false;

	/**
	 * CAOS_Frontend_Tracking constructor.
	 */
	public function __construct() {
		$this->handle    =
			'caos-' .
			( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) ?
				CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) . '-' : '' ) .
			'gtag';
		$this->in_footer = CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION, 'header' ) === 'footer';

		add_action( 'caos_inline_scripts_before_tracking_code', [ $this, 'consent_mode' ] );
		add_filter( 'caos_frontend_tracking_consent_mode', [ $this, 'maybe_disable_consent_mode' ] );
		add_action( 'caos_gtag_additional_config', [ $this, 'consent_mode_listener' ] );
		add_filter( 'caos_frontend_tracking_consent_mode_listener', [ $this, 'maybe_disable_consent_mode_listener' ] );
		add_action( 'init', [ $this, 'insert_tracking_code' ] );
		add_filter( 'script_loader_tag', [ $this, 'add_attributes' ], 10, 2 );
		add_action( 'caos_process_settings', [ $this, 'disable_advertising_features' ] );
	}

	/**
	 * Inserts the code snippet required for Google Analytics' Consent Mode to be activated.
	 * @since v4.5.0
	 *
	 * @param mixed $handle
	 *
	 * @return void
	 */
	public function consent_mode( $handle ) {
		/**
		 * Setting this to true disables the required JS to run Consent Mode.
		 * @filter caos_frontend_tracking_consent_mode
		 * @since  v4.5.0
		 */
		if ( apply_filters( 'caos_frontend_tracking_consent_mode', false ) ) {
			return;
		}

		ob_start(); ?>

        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('consent', 'default', {
                'analytics_storage': 'denied',
                'wait_for_update': 15000
            });

			<?php
			/**
			 * Allows for adding additional defaults as HTML to Google Analytics' Consent Mode, e.g.
			 * gtag('consent', 'default', {s
			 *     'ad_storage': 'denied'
			 * });
			 * @since  v4.5.0
			 * @action caos_frontend_tracking_consent_mode_defaults
			 */
			do_action( 'caos_frontend_tracking_consent_mode_defaults' );
			?>
        </script>
		<?php
		$snippet = ob_get_clean();

		wp_add_inline_script( $handle, str_replace( [ '<script>', '</script>' ], '', $snippet ), 'before' );
	}

	/**
	 * Consent Mode framework should be disabled when Google Analytics 4 isn't used or when Allow Tracking
	 * is set to 'Always'
	 * @filter caos_frontend_tracking_consent_mode
	 * @since  v4.5.0
	 * @return bool
	 */
	public function maybe_disable_consent_mode() {
		return empty( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) );
	}

	/**
	 * Adds the option specific JS snippets to implement Google Analytics' Consent Mode in the frontend.
	 * @since v4.5.0
	 * @return void
	 */
	public function consent_mode_listener() {
		/**
		 * Setting this to true disables the "listening" part of the Consent Mode script to allow Cookie Notice plugins or other
		 * Google Analytics plugins to update the Consent state.
		 * @filter caos_frontend_tracking_consent_mode_listener
		 * @since  v4.5.0
		 */
		if ( apply_filters( 'caos_frontend_tracking_consent_mode_listener', false ) ) {
			return;
		}

		ob_start();
		?>

        <script>
            var caos_consent_mode = function () {
                var i = 0;

                return function () {
                    if (i >= 30) {
                        console.log('No cookie match found for 15 seconds, trying again on next pageload.');

                        clearInterval(caos_consent_mode_listener);
                    }

                    var cookie = document.cookie;

					<?php if ( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) === 'cookie_is_set' ) : ?>
                    if (cookie.match(/<?php echo esc_attr(
						CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME )
					); ?>=.*?/) !== null) {
                        consent_granted();
                    }
					<?php elseif ( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) === 'cookie_is_not_set' ) : ?>
                    if (cookie.match(/<?php echo esc_attr(
						CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME )
					); ?>=.*?/) === null) {
                        consent_granted();
                    }
					<?php elseif ( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) === 'cookie_has_value' ) : ?>
                    if (cookie.match(/<?php echo esc_attr(
						CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME )
					); ?>=<?php echo esc_attr( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_VALUE ) ); ?>/) !== null) {
                        consent_granted();
                    }
					<?php elseif ( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) === 'cookie_value_contains' ) : ?>
                    if (cookie.match(/<?php echo esc_attr(
						CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME )
					); ?>=.*?<?php echo esc_attr( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_VALUE ) ); ?>.*?/) !== null) {
                        consent_granted();
                    }
					<?php else : ?>
                    gtag('consent', 'update', {
                        'analytics_storage': 'denied'
                    });
					<?php endif; ?>

                    i++;
                };
            }();

            var caos_consent_mode_listener = window.setInterval(caos_consent_mode, 500);

            function consent_granted() {
                console.log('Cookie matched! Updating consent state to granted.');

                gtag('consent', 'update', {
                    'analytics_storage': 'granted'
                });

				<?php
				/**
				 * Allows for triggering additional update queries to Google Analytics' Consent Mode framework.
				 * @since  v4.5.0
				 * @action caos_frontend_tracking_consent_mode_listener_update
				 */
				do_action( 'caos_frontend_tracking_consent_mode_listener_update' );
				?>

                window.clearInterval(caos_consent_mode_listener);
            }
        </script>
		<?php

		echo str_replace( [ '<script>', '</script>' ], '', ob_get_clean() );
	}

	/**
	 * The "listening" part of Consent Mode should be disabled when Google Analytics 4 isn't used, or when
	 * Allow Tracking is set to 'Always' or 'Consent Mode'.
	 * @since  v4.5.0
	 * @filter caos_frontend_tracking_consent_mode_listener
	 * @return bool
	 */
	public function maybe_disable_consent_mode_listener() {
		return empty( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) ) ||
			CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) === 'consent_mode';
	}

	/**
	 * Render the tracking code in it's selected locations
	 */
	public function insert_tracking_code() {
		/**
		 * Google Analytics
		 */
		if ( CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE, '' ) && ! is_admin() ) {
			/**
			 * @since v4.3.1 For certain Page Cache plugins, we're using an alternative method,
			 *               to prevent breaking the page cache. We still use the same filter,
			 *               though.
			 */
			add_filter( 'caos_buffer_output', [ $this, 'insert_local_file' ] );
			// Autoptimize at 2. OMGF at 3. GDPRess at 4.
			add_action( 'template_redirect', [ $this, 'maybe_buffer_output' ], 3 );
		} elseif ( current_user_can( 'manage_options' ) && ! CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACK_ADMIN ) ) {
			switch ( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION, 'header' ) ) {
				case 'footer':
					add_action( 'wp_footer', [ $this, 'show_admin_message' ] );
					break;
				case 'manual':
					break;
				default:
					add_action( 'wp_head', [ $this, 'show_admin_message' ] );
					break;
			}
		} else {
			/**
			 * Allow WP Dev's to halt the rendering of the tracking code, effectively excluding
			 * the page from tracking.
			 * Example: add_filter('caos_exclude_from_tracking', '__return_true');
			 */
			if ( apply_filters( 'caos_exclude_from_tracking', false ) ) {
				return;
			}

			/**
			 * Since no other libraries are loaded when Minimal Analytics is enabled, we can't use
			 * wp_add_inline_script(). That's why we're echo-ing it into wp_head/wp_footer.
			 */
			if ( CAOS::uses_minimal_analytics() ) {
				switch ( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION, 'header' ) ) {
					case 'footer':
						add_action( 'wp_footer', [ $this, 'insert_minimal_tracking_snippet' ] );
						break;
					case 'manual':
						break;
					default:
						add_action( 'wp_head', [ $this, 'insert_minimal_tracking_snippet' ] );
						break;
				}

				return;
			}

			/**
			 * Allows WP DEV's to modify the output of the tracking code.
			 * E.g. add_action('caos_process_settings', 'your_function_name');
			 */
			do_action( 'caos_process_settings' );

			switch ( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION, 'header' ) ) {
				case 'manual':
					break;
				default:
					add_action( 'wp_enqueue_scripts', [ $this, 'render_tracking_code' ] );
					break;
			}
		}
	}

	/**
	 * Rewrite all external URLs in $html.
	 * @filter caos_buffer_output
	 *
	 * @param mixed $html
	 *
	 * @return mixed
	 */
	public function insert_local_file( $html ) {
		$cache_url = content_url( CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR, '/uploads/caos/' ) );

		$search = [
			'//www.googletagmanager.com/gtag/js',
			'https://www.googletagmanager.com/gtag/js',
		];

		$replace = [
			str_replace( [ 'https:', 'http:' ], '', $cache_url . CAOS::get_file_alias() ),
			$cache_url . CAOS::get_file_alias(),
		];

		return str_replace( $search, $replace, $html );
	}

	/**
	 * Start output buffer.
	 * @action template_redirect
	 * @return void
	 */
	public function maybe_buffer_output() {
		$start = true;

		/**
		 * Make sure Page Builder previews don't get optimized content.
		 */
		foreach ( $this->page_builders as $page_builder ) {
			if ( array_key_exists( $page_builder, $_GET ) ) {
				$start = false;
				break;
			}
		}

		/**
		 * Customizer previews shouldn't get optimized content.
		 */
		if ( function_exists( 'is_customize_preview' ) ) {
			$start = ! is_customize_preview();
		}

		/**
		 * Let's GO!
		 */
		if ( $start ) {
			ob_start( [ $this, 'return_buffer' ] );
		}
	}

	/**
	 * Returns the buffer for filtering, so page cache doesn't break.
	 * @see   https://wordpress.org/support/topic/completely-broke-wp-rocket-plugin/#post-15377538)
	 *               - W3 Total Cache v2.2.1:
	 *                 - Page Cache: Disk (basic)
	 *                 - Database/Object Cache: Off
	 *                 - JS/CSS minify/combine: On
	 *               - WP Fastest Cache v0.9.5
	 *                 - JS/CSS minify/combine: On
	 *                 - Page Cache: On
	 *               - WP Rocket v3.8.8:
	 *                 - Page Cache: Enabled
	 *                 - JS/CSS minify/combine: Enabled
	 *               - WP Super Cache v1.7.4
	 *                 - Page Cache: Enabled
	 *               Not tested (yet):
	 * TODO: [CAOS-33] - Swift Performance
	 * @since v4.3.1 Tested with:
	 *               - Cache Enabler v1.8.7
	 *                 - Default Settings
	 *               - LiteSpeed Cache
	 *                 - Don't know (Gal Baras tested it: @return void
	 */
	public function return_buffer( $html ) {
		if ( ! $html ) {
			return $html;
		}

		return apply_filters( 'caos_buffer_output', $html );
	}

	/**
	 * Adds async attribute to gtag.js script.
	 *
	 * @param $tag
	 * @param $handle
	 *
	 * @return string
	 */
	public function add_attributes( $tag, $handle ) {
		if ( ( ! CAOS::uses_minimal_analytics() && $handle === $this->handle ) ) {
			$tag = str_replace( 'script src', 'script async src', $tag );
		}

		if ( $handle === $this->handle && $custom_attributes = apply_filters( 'caos_script_custom_attributes', '' ) ) {
			return str_replace( '<script ', "<script $custom_attributes ", $tag );
		}

		return $tag;
	}

	/**
	 * Process disable advertising features setting.
	 */
	public function disable_advertising_features() {
		// When merging config array, gtag.js properly renders the boolean values.
		$ads_features_disabled = CAOS::get( CAOS_Admin_Settings::CAOS_ADV_SETTING_DISABLE_ADS_FEATURES ) === 'on' ? false : true;

		add_filter(
			'caos_gtag_config',
			function ( $config ) use ( $ads_features_disabled ) {
				return $config + [ 'allow_google_signals' => $ads_features_disabled ];
			}
		);
	}

	/**
	 * Render a HTML comment for logged in Administrators in the source code.
	 */
	public function show_admin_message() {
		echo '<!-- ' .
			__(
				'This site is using CAOS. You\'re logged in as an administrator, so we\'re not loading the tracking code.',
				'host-analyticsjs-local'
			) .
			" -->\n";
	}

	/**
	 * Generate tracking code and add to header (default) or footer.
	 */
	public function render_tracking_code() {
		if ( ! CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID ) ) {
			return;
		}

		if ( apply_filters( 'caos_frontend_tracking_promo_message', true ) ) {
			echo '<!-- ' . __( 'This site is running CAOS for WordPress', 'host-analyticsjs-local' ) . " -->\n";
		}

		if ( empty( CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) ) ) {
			wp_enqueue_script( $this->handle, $this->return_js_url(), [], null, $this->in_footer );
		}

		/**
		 * Allow WP DEVs to add additional JS before Gtag tracking code.
		 * @since v4.2.0
		 */
		do_action( 'caos_inline_scripts_before_tracking_code', $this->handle, CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID ) );

		wp_add_inline_script( $this->handle, $this->get_tracking_code_template() );

		/**
		 * Allow WP DEVs to add additional JS after Gtag tracking code.
		 * @since v4.2.0
		 */
		do_action( 'caos_add_script_after_tracking_code', $this->handle, CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID ) );
	}

	/**
	 * Render the URL of the cached local file
	 * @return string
	 */
	private function return_js_url() {
		$id = '?id=' . CAOS::get( CAOS_Admin_Settings::CAOS_BASIC_SETTING_MEASUREMENT_ID );

		return CAOS::get_local_file_url() . $id;
	}

	/**
	 * @param $name
	 *
	 * @return false|string
	 */
	public function get_tracking_code_template( $tracking_code = 'gtag', $strip = false ) {
		ob_start();

		include CAOS_PLUGIN_DIR . "templates/frontend-tracking-code-$tracking_code.phtml";

		if ( ! $strip ) {
			return str_replace( [ '<script>', '</script>' ], '', ob_get_clean() );
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * Insert the Minimal Analytics tracking code.
	 */
	public function insert_minimal_tracking_snippet() {
		echo "\n<!-- This site is using Minimal Analytics 4 brought to you by CAOS. -->\n";

		echo $this->get_tracking_code_template( 'minimal-ga4', true );
	}
}
