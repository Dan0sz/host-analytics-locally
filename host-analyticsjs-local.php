<?php
defined( 'ABSPATH' ) || exit;

/**
 * Plugin Name: CAOS
 * Plugin URI: https://daan.dev/wordpress/caos/
 * Description: Completely optimize Google Analytics for your WordPress Website - host analytics.js/gtag.js locally or use Minimal Analytics, bypass Ad Blockers in Stealth Mode, capture outbound links, and much more!
 * Version: 4.5.0
 * Author: Daan from Daan.dev
 * Author URI: https://daan.dev/
 * License: GPL2v2 or later
 * Text Domain: host-analyticsjs-local
 */

define( 'CAOS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAOS_PLUGIN_FILE', __FILE__ );
define( 'CAOS_PLUGIN_BASENAME', plugin_basename( CAOS_PLUGIN_FILE ) );
define( 'CAOS_STATIC_VERSION', '4.5.0' );
define( 'CAOS_DB_VERSION', '4.3.0' );

/**
 * Takes care of loading classes on demand.
 *
 * @param $class
 *
 * @return mixed|void
 */
require_once CAOS_PLUGIN_DIR . 'vendor/autoload.php';

/**
 * All systems GO!!!
 *
 * @return Plugin
 */
$caos = new CAOS\Plugin();
