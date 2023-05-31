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
namespace CAOS\Frontend;

use CAOS\Admin\Settings;
use CAOS\Plugin as CAOS;

defined( 'ABSPATH' ) || exit;

class Tracking {

	const CAOS_SCRIPT_HANDLE_TRACK_AD_BLOCKERS = 'caos-track-ad-blockers';

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

	/** @var string $handle */
	public $handle = '';

	/** @var bool $in_footer For use in wp_enqueue_scripts() etc. */
	private $in_footer = false;

	/**
	 * CAOS_Frontend_Tracking constructor.
	 */
	public function __construct() {
		$snippet_type    = CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_CODE );
		$this->handle    = 'caos-' . ( $snippet_type ? $snippet_type . '-' : '' ) . str_replace( '.js', '', CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE ) );
		$this->in_footer = CAOS::get( Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION ) === 'footer';

		add_action( 'caos_inline_scripts_before_tracking_code', [ $this, 'consent_mode' ] );
		add_filter( 'caos_frontend_tracking_consent_mode', [ $this, 'maybe_disable_consent_mode' ] );
		add_action( 'caos_gtag_additional_config', [ $this, 'consent_mode_listener' ] );
		add_filter( 'caos_frontend_tracking_consent_mode_listener', [ $this, 'maybe_disable_consent_mode_listener' ] );
		add_action( 'init', [ $this, 'insert_tracking_code' ] );
		add_filter( 'script_loader_tag', [ $this, 'add_attributes' ], 10, 2 );
		add_action( 'caos_process_settings', [ $this, 'disable_advertising_features' ] );
		add_action( 'caos_process_settings', [ $this, 'anonymize_ip' ] );
		add_action( 'caos_process_settings', [ $this, 'site_speed_sample_rate' ] );
		add_action( 'caos_process_settings', [ $this, 'linkid' ] );
		add_action( 'caos_process_settings', [ $this, 'dual_tracking' ] );
	}

	/**
	 * Inserts the code snippet required for Google Analytics' Consent Mode to be activated.
	 *
	 * @param mixed $handle
	 *
	 * @since v4.5.0
	 *
	 * @return void
	 */
	public function consent_mode( $handle ) {
		/**
		 * Setting this to true disables the required JS to run Consent Mode.
		 *
		 * @filter caos_frontend_tracking_consent_mode
		 *
		 * @since v4.5.0
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
				'analytics_storage': 'denied'
			});

			<?php
			/**
			 * Allows for adding additional defaults as HTML to Google Analytics' Consent Mode, e.g.
			 *
			 * gtag('consent', 'default', {s
			 *     'ad_storage': 'denied'
			 * });
			 *
			 * @since v4.5.0
			 *
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
	 *
	 * @filter caos_frontend_tracking_consent_mode
	 *
	 * @since v4.5.0
	 *
	 * @return bool
	 */
	public function maybe_disable_consent_mode() {
		// phpcs:ignore
		return ! CAOS::uses_ga4() || CAOS::get( Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) != '';
	}

	/**
	 * Adds the option specific JS snippets to implement Google Analytics' Consent Mode in the frontend.
	 *
	 * @since v4.5.0
	 *
	 * @return void
	 */
	public function consent_mode_listener() {
		/**
		 * Setting this to true disables the "listening" part of the Consent Mode script to allow Cookie Notice plugins or other
		 * Google Analytics plugins to update the Consent state.
		 *
		 * @filter caos_frontend_tracking_consent_mode_listener
		 *
		 * @since v4.5.0
		 */
		if ( apply_filters( 'caos_frontend_tracking_consent_mode_listener', false ) ) {
			return;
		}

		$allow_tracking = CAOS::get( Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING );
		$cookie_name    = CAOS::get( Settings::CAOS_BASIC_SETTING_COOKIE_NAME );
		$cookie_value   = CAOS::get( Settings::CAOS_BASIC_SETTING_COOKIE_VALUE );

		ob_start();
		?>

		<script>
			var caos_consent_mode = function() {
				var i = 0;

				return function() {
					if (i >= 30) {
						console.log('No cookie match found for 15 seconds, trying again on next pageload.');

						clearInterval(caos_consent_mode_listener);
					}

					var cookie = document.cookie;

					<?php if ( $allow_tracking === 'cookie_is_set' ) : ?>
						if (cookie.match(/<?php echo $cookie_name; ?>=.*?/) !== null) {
							consent_granted();
						}
					<?php elseif ( $allow_tracking === 'cookie_is_not_set' ) : ?>
						if (cookie.match(/<?php echo $cookie_name; ?>=.*?/) === null) {
							consent_granted();
						}
					<?php elseif ( $allow_tracking === 'cookie_has_value' ) : ?>
						if (cookie.match(/<?php echo $cookie_name; ?>=<?php echo $cookie_value; ?>/) !== null) {
							consent_granted();
						}
					<?php elseif ( $allow_tracking === 'cookie_value_contains' ) : ?>
						if (cookie.match(/<?php echo $cookie_name; ?>=.*?<?php echo $cookie_value; ?>.*?/) !== null) {
							consent_granted();
						}
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
				 *
				 * @since v4.5.0
				 *
				 * @action caos_frontend_tracking_consent_mode_listener_update
				 */
				do_action( 'caos_frontend_tracking_consent_mode_listener_update' );
				?>

				window.clearInterval(caos_consent_mode_listener);
			}
		</script>
		<?php

		echo ob_get_clean();
	}

	/**
	 * The "listening" part of Consent Mode should be disabled when Google Analytics 4 isn't used, or when
	 * Allow Tracking is set to 'Always' or 'Consent Mode'.
	 *
	 * @since v4.5.0
	 *
	 * @filter caos_frontend_tracking_consent_mode_listener
	 *
	 * @return bool
	 */
	public function maybe_disable_consent_mode_listener() {
		// phpcs:ignore
		return ! CAOS::uses_ga4() || CAOS::get(Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING) != '' || CAOS::get( Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING ) === 'consent_mode';
	}

	/**
	 * Render the tracking code in it's selected locations
	 */
	public function insert_tracking_code() {
		/**
		 * Plausible Analytics
		 *
		 * @since v4.4.0
		 */
		if ( CAOS::get( Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER ) === 'plausible' ) {
			/**
			 * If Track Administrators is disabled, bail early.
			 */
			if ( current_user_can( 'manage_options' ) && ! CAOS::get( Settings::CAOS_BASIC_SETTING_TRACK_ADMIN ) ) {
				return;
			}

			add_action( 'wp_head', [ $this, 'insert_plausible_tracking_code' ] );

			/**
			 * We're done here.
			 */
			return;
		}

		/**
		 * Google Analytics
		 */
		if ( CAOS::get( Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE ) && ! is_admin() ) {
			/**
			 * @since v4.3.1 For certain Page Cache plugins, we're using an alternative method,
			 *               to prevent breaking the page cache. We still use the same filter,
			 *               though.
			 */
			add_filter( 'caos_buffer_output', [ $this, 'insert_local_file' ] );
			// Autoptimize at 2. OMGF at 3. GDPRess at 4.
			add_action( 'template_redirect', [ $this, 'maybe_buffer_output' ], 3 );
		} elseif ( current_user_can( 'manage_options' ) && ! CAOS::get( Settings::CAOS_BASIC_SETTING_TRACK_ADMIN ) ) {
			switch ( CAOS::get( Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION ) ) {
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
			if ( CAOS::get( Settings::CAOS_EXT_SETTING_TRACK_AD_BLOCKERS ) == 'on' ) {
				add_action( 'wp_enqueue_scripts', [ $this, 'insert_ad_blocker_tracking' ] );
			}

			/**
			 * Since no other libraries are loaded when Minimal Analytics is enabled, we can't use
			 * wp_add_inline_script(). That's why we're echo-ing it into wp_head/wp_footer.
			 */
			if ( CAOS::uses_minimal_analytics() ) {
				switch ( CAOS::get( Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION ) ) {
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
			 *
			 * E.g. add_action('caos_process_settings', 'your_function_name');
			 */
			do_action( 'caos_process_settings' );

			switch ( CAOS::get( Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION ) ) {
				case 'manual':
					break;
				default:
					add_action( 'wp_enqueue_scripts', [ $this, 'render_tracking_code' ] );
					break;
			}
		}
	}

	/**
	 * Add Plausible Analytics tracking code.
	 *
	 * @since v4.4.0
	 *
	 * @return void
	 */
	public function insert_plausible_tracking_code() {
		$cache       = content_url( CAOS::get( Settings::CAOS_ADV_SETTING_CACHE_DIR ) );
		$bounce_rate = CAOS::get( Settings::CAOS_BASIC_SETTING_ADJUSTED_BOUNCE_RATE );
		?>
		<script defer data-domain="<?php echo esc_url( CAOS::get( Settings::CAOS_BASIC_SETTING_DOMAIN_NAME ) ); ?>" data-api="<?php echo esc_url( apply_filters( 'caos_plausible_analytics_frontend_api', 'https://plausible.io/api/event' ) ); ?>" src="<?php echo esc_attr( $cache . CAOS::get_file_alias( 'plausible' ) ); ?>"></script>
		<?php if ( $bounce_rate ) : ?>
			<script>
				window.plausible = window.plausible || function() {
					(window.plausible.q = window.plausible.q || []).push(arguments)
				}
				setTimeout("plausible('Adjusted Bounce Rate', { props: { duration: '<?php echo $bounce_rate; ?>' } });", <?php echo $bounce_rate * 1000; ?>);
			</script>
			<?php
		endif;
	}

	/**
	 * Rewrite all external URLs in $html.
	 *
	 * @filter caos_buffer_output
	 * @param mixed $html
	 * @return mixed
	 */
	public function insert_local_file( $html ) {
		$cache = content_url( CAOS::get( Settings::CAOS_ADV_SETTING_CACHE_DIR ) );

		$search = [
			'//www.googletagmanager.com/gtag/js',
			'https://www.googletagmanager.com/gtag/js',
			'//www.google-analytics.com/analytics.js',
			'https://www.google-analytics.com/analytics.js',
		];

		$replace = [
			str_replace( [ 'https:', 'http:' ], '', $cache . CAOS::get_file_alias( 'gtag' ) ),
			$cache . CAOS::get_file_alias( 'gtag' ),
			str_replace( [ 'https:', 'http:' ], '', $cache . CAOS::get_file_alias( 'analytics' ) ),
			$cache . CAOS::get_file_alias( 'analytics' ),
		];

		return str_replace( $search, $replace, $html );
	}

	/**
	 * Start output buffer.
	 *
	 * @action template_redirect
	 *
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
	 *
	 * @since v4.3.1 Tested with:
	 *               - Cache Enabler v1.8.7
	 *                 - Default Settings
	 *               - LiteSpeed Cache
	 *                 - Don't know (Gal Baras tested it: @see https://wordpress.org/support/topic/completely-broke-wp-rocket-plugin/#post-15377538)
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
	 *
	 *                Not tested (yet):
	 * TODO: [CAOS-33] - Swift Performance
	 *
	 * @return void
	 */
	public function return_buffer( $html ) {
		if ( ! $html ) {
			return $html;
		}

		return apply_filters( 'caos_buffer_output', $html );
	}

	/**
	 * Adds async attribute to analytics.js/gtag.js script.
	 *
	 * @param $tag
	 * @param $handle
	 *
	 * @return string
	 */
	public function add_attributes( $tag, $handle ) {
		if ( ( CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) == 'async' && $handle == $this->handle ) ) {
			return str_replace( 'script src', 'script async src', $tag );
		}

		if ( $handle == self::CAOS_SCRIPT_HANDLE_TRACK_AD_BLOCKERS ) {
			return str_replace( 'script src', 'script defer src', $tag );
		}

		if ( $handle == $this->handle && $custom_attributes = apply_filters( 'caos_script_custom_attributes', '' ) ) {
			return str_replace( 'script id', "script $custom_attributes id", $tag );
		}

		return $tag;
	}

	/**
	 * Process disable advertising features setting.
	 */
	public function disable_advertising_features() {
		// When merging config array, gtag.js properly renders the boolean values.
		$ads_features_disabled = CAOS::get( Settings::CAOS_ADV_SETTING_DISABLE_ADS_FEATURES ) === 'on' ? false : true;

		add_filter(
			'caos_gtag_config',
			function ( $config ) use ( $ads_features_disabled ) {
				return $config + [ 'allow_google_signals' => $ads_features_disabled ];
			}
		);

		// Analytics.js requires a slightly different approach when merging the config.
		$ads_features_disabled = CAOS::get( Settings::CAOS_ADV_SETTING_DISABLE_ADS_FEATURES ) === 'on' ? 'false' : 'true';

		add_filter(
			'caos_analytics_before_send',
			function ( $config ) use ( $ads_features_disabled ) {
				$option = [
					'ads_features' => "ga('set', 'allowAdFeatures', $ads_features_disabled);",
				];

				return $config + $option;
			}
		);
	}

	/**
	 * Process Anonymize IP setting.
	 */
	public function anonymize_ip() {
		// phpcs:ignore
		if ( CAOS::get( Settings::CAOS_BASIC_SETTING_ANONYMIZE_IP_MODE ) == '' ) {
			return;
		}

		if ( $this->is_gtag() ) {
			add_filter(
				'caos_gtag_config',
				function ( $config, $trackingId ) {
					return $config + [ 'anonymize_ip' => true ];
				},
				10,
				2
			);
		}

		add_filter(
			'caos_analytics_before_send',
			function ( $config ) {
				$option = [
					'anonymizeIp' => "ga('set', 'anonymizeIp', true);",
				];

				return $config + $option;
			}
		);
	}

	/**
	 * Process Site Speed Sample Rate setting (defaults to 1)
	 *
	 * @return void
	 */
	public function site_speed_sample_rate() {
		$sample_rate = CAOS::get( Settings::CAOS_ADV_SETTING_SITE_SPEED_SAMPLE_RATE );

		if ( $this->is_gtag() ) {
			add_filter(
				'caos_gtag_config',
				function ( $config, $trackingId ) use ( $sample_rate ) {
					return $config + [ 'site_speed_sample_rate' => $sample_rate ];
				},
				10,
				2
			);
		}

		add_filter(
			'caos_analytics_ga_create_config',
			function ( $config ) use ( $sample_rate ) {
				$option = [
					'siteSpeedSampleRate' => $sample_rate,
				];

				return $config + $option;
			}
		);
	}

	/**
	 * Enhanced Link Attribution
	 *
	 * TODO: Set samesite flag as soon as it's available.
	 *       @see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-link-attribution
	 */
	public function linkid() {
		if ( CAOS::get( Settings::CAOS_EXT_SETTING_LINKID ) !== 'on' ) {
			return;
		}

		if ( $this->is_gtag() ) {
			add_filter(
				'caos_gtag_config',
				function ( $config, $tracking_id ) {
					return $config + [
						'link_attribution',
						[
							'cookie_name' => 'caos_linkid',
						],
					];
				},
				10,
				2
			);
		}

		add_filter(
			'caos_analytics_before_send',
			function ( $config ) {
				$option = [
					'linkid' => "ga('require', 'linkid', { 'cookieName':'caosLinkid', 'cookieFlags':'samesite=none;secure' });",
				];

				return $config + $option;
			}
		);
	}

	/**
	 * Add GA4 Measurement ID to Gtag (GA3) tracking code.
	 *
	 * @return void
	 */
	public function dual_tracking() {
		if ( CAOS::get( Settings::CAOS_BASIC_SETTING_DUAL_TRACKING ) !== 'on' ) {
			return;
		}

		if ( $this->is_ga4() || ! $this->is_gtag() ) {
			return;
		}

		$measurement_id = CAOS::get( Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID );

		if ( ! $measurement_id ) {
			return;
		}

		add_action(
			'caos_gtag_additional_config',
			function () use ( $measurement_id ) {
				?>
			gtag('config', '<?php echo esc_attr( $measurement_id ); ?>');
				<?php
			}
		);
	}

	/**
	 * Dual tracking uses the GA3 gtag.js library, this method explicitly checks if a Measurement ID (GA4) is set as a Tracking ID (GA3).
	 * @return bool
	 */
	private function is_ga4() {
		return strpos( CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_ID ), 'G-' ) === 0;
	}

	/**
	 * Check if Global Site Tag is used.
	 *
	 * @return bool
	 */
	private function is_gtag() {
		return CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE ) === 'gtag.js' || CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE ) === 'gtag-v4.js';
	}

	/**
	 * Render a HTML comment for logged in Administrators in the source code.
	 */
	public function show_admin_message() {
		echo '<!-- ' . __( 'This site is using CAOS. You\'re logged in as an administrator, so we\'re not loading the tracking code.', 'host-analyticsjs-local' ) . " -->\n";
	}

	/**
	 * @param mixed $snippet
	 * @return mixed
	 */
	public function modify_gtag_js_snippet( $snippet ) {
		return str_replace( 'https://www.googletagmanager.com/gtag/js', CAOS::get_local_file_url(), $snippet );
	}

	/**
	 * Render the URL of the cached local file
	 *
	 * @return string
	 */
	public function return_analytics_js_url() {
		$id = '';

		if ( CAOS::get_current_file_key() == 'gtag' ) {
			$id = '?id=' . CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_ID );
		}

		return CAOS::get_local_file_url() . $id;
	}

	/**
	 * Generate tracking code and add to header (default) or footer.
	 */
	public function render_tracking_code() {
		$tracking_id = CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_ID );
		if ( ! $tracking_id ) {
			return;
		}

		if ( apply_filters( 'caos_frontend_tracking_promo_message', true ) ) {
			echo '<!-- ' . esc_html__( 'This site is running CAOS for WordPress', 'host-analyticsjs-local' ) . " -->\n";
		}

		$deps = CAOS::get( Settings::CAOS_EXT_SETTING_TRACK_AD_BLOCKERS ) ? [ self::CAOS_SCRIPT_HANDLE_TRACK_AD_BLOCKERS ] : [];

		if ( CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) !== 'minimal' ) {
			wp_enqueue_script( $this->handle, $this->return_analytics_js_url(), $deps, CAOS_STATIC_VERSION, $this->in_footer );
		}

		$allow_tracking = CAOS::get( Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING );

		if ( ( $allow_tracking === 'cookie_has_value' || $allow_tracking === 'cookie_value_contains' )
			&& CAOS::get( Settings::CAOS_BASIC_SETTING_COOKIE_NAME )
			&& CAOS::get( Settings::CAOS_BASIC_SETTING_COOKIE_VALUE ) ) {
			wp_add_inline_script( $this->handle, $this->get_tracking_code_template( 'cookie-value' ) );
		}

		if ( ! CAOS::uses_ga4() ) {
			wp_add_inline_script( $this->handle, $this->get_tracking_code_template( 'ga-disable' ) );
		}

		/**
		 * Allow WP DEVs to add additional JS before Analytics/Gtag tracking code.
		 *
		 * @since v4.2.0
		 */
		do_action( 'caos_inline_scripts_before_tracking_code', $this->handle, $tracking_id );

		switch ( CAOS::get( Settings::CAOS_ADV_SETTING_JS_FILE ) ) {
			case 'gtag.js':
			case 'gtag-v4.js':
				wp_add_inline_script( $this->handle, $this->get_tracking_code_template( 'gtag' ) );
				break;
			default:
				wp_add_inline_script( $this->handle, $this->get_tracking_code_template( 'analytics' ) );
				break;
		}

		/**
		 * Allow WP DEVs to add additional JS after Analytics/Gtag tracking code.
		 *
		 * @since v4.2.0
		 */
		do_action( 'caos_add_script_after_tracking_code', $this->handle, $tracking_id );
	}

	/**
	 * @param $name
	 *
	 * @return false|string
	 */
	public function get_tracking_code_template( $name, $strip = false ) {
		ob_start();

		include CAOS_PLUGIN_DIR . 'templates/frontend-tracking-code-' . $name . '.phtml';

		if ( ! $strip ) {
			return str_replace( [ '<script>', '</script>' ], '', ob_get_clean() );
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * Respects the tracking code's position (header/footer) because this script needs to be triggered after the
	 * pageview is sent.
	 */
	public function insert_ad_blocker_tracking() {
		wp_enqueue_script( self::CAOS_SCRIPT_HANDLE_TRACK_AD_BLOCKERS, plugins_url( 'assets/js/detect-ad-block.js', CAOS_PLUGIN_FILE ), [], CAOS_STATIC_VERSION, $this->in_footer );
		wp_add_inline_script( self::CAOS_SCRIPT_HANDLE_TRACK_AD_BLOCKERS, $this->send_ad_blocker_result() );
	}

	/**
	 * Insert either of the Minimal Analytics tracking codes.
	 */
	public function insert_minimal_tracking_snippet() {
		echo "\n<!-- This site is using Minimal Analytics brought to you by CAOS. -->\n";

		if ( CAOS::get( Settings::CAOS_BASIC_SETTING_TRACKING_CODE ) === 'minimal' ) {
			echo $this->get_tracking_code_template( 'minimal', true );
		} else {
			echo $this->get_tracking_code_template( 'minimal-ga4', true );
		}
	}

	/**
	 * @return string
	 */
	private function send_ad_blocker_result() {
		$url = home_url( 'wp-json/caos/v1/block/detect' );
		/**
		 * DISCLAIMER:
		 *
		 * To developers who want to override this filter and insert clientIds themselves.
		 * Please beware of privacy laws in your country and (more importantly) of your visitors.
		 * Bypassing ad blockers AND sending unique client IDs to Google Analytics (which potentially
		 * could identify an individual) is forbidden by GDPR EU laws and might also be forbidden
		 * in your country.
		 *
		 * Client ID's can be added by making sure the ClientIDHashed variable exists anywhere inside
		 * the document before this script is loaded.
		 *
		 * You have been warned!
		 */
		$use_cid = apply_filters( 'caos_track_ad_blockers_use_cid', false );

		ob_start();
		?>
		<script>
			document.addEventListener('caos_track_ad_blockers', function(e) {
				document.addEventListener('DOMContentLoaded', function(e) {
					var caos_detect_ad_blocker = 1;

					<?php if ( $use_cid ) : ?>
						var cid = localStorage.getItem('GA_CLIENT_ID_HASHED') ?? '';
					<?php else : ?>
						var cid = '';
					<?php endif; ?>

					if (document.getElementById('caos-detect-ad-block')) {
						caos_detect_ad_blocker = 0;
					}
					var ajax = new XMLHttpRequest();
					ajax.open('POST', '<?php echo $url; ?>');
					ajax.onreadystatechange = function() {
						if (ajax.readyState !== 4 || ajax.readyState !== 200) return;
					};
					ajax.send("result=" + caos_detect_ad_blocker + '&cid=' + cid);
				});
			});
		</script>
		<?php

		return str_replace( [ '<script>', '</script>' ], '', ob_get_clean() );
	}
}
