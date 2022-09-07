<?php
defined('ABSPATH') || exit;

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
class CAOS
{
    /**
     * Used to check if CAOS Pro is (de)activated and update files (e.g. analytics.js) accordingly.
     */
    const CAOS_PRO_PLUGIN_SLUG = 'caos-pro';

    /** @var string $plugin_text_domain */
    private $plugin_text_domain = 'host-analyticsjs-local';

    /**
     * CAOS constructor.
     */
    public function __construct()
    {
        $this->define_constants();
        $this->do_setup();

        if (version_compare(CAOS_STORED_DB_VERSION, CAOS_DB_VERSION) < 0) {
            $this->update_db();
        }

        if (is_admin()) {
            do_action('caos_before_admin');

            $this->add_ajax_hooks();
            $this->do_settings();
        }

        if (!is_admin()) {
            do_action('caos_before_frontend');

            $this->do_frontend();
            $this->do_tracking_code();
        }

        // API Routes
        add_action('rest_api_init', [$this, 'register_routes']);

        // Automatic File Updates
        add_action('activated_plugin', [$this, 'maybe_do_update']);
        add_action('deactivated_plugin', [$this, 'maybe_do_update']);
        add_action('admin_init', [$this, 'do_update_after_save']);
        add_action('upgrader_process_complete', [$this, 'do_update_after_update'], 10, 2);
        add_action('in_plugin_update_message-' . CAOS_PLUGIN_BASENAME, [$this, 'render_update_notice'], 11, 2);
    }

    /**
     * Define constants
     */
    public function define_constants()
    {
        global $caos_file_aliases;

        $caos_file_aliases      = get_option(CAOS_Admin_Settings::CAOS_CRON_FILE_ALIASES);
        $translated_tracking_id = _x('UA-123456789', 'Define a different Tracking ID for this site.', $this->plugin_text_domain);

        define('CAOS_SITE_URL', 'https://daan.dev/blog');
        define('CAOS_STORED_DB_VERSION', esc_attr(get_option(CAOS_Admin_Settings::CAOS_DB_VERSION, '4.2.1')));
        define('CAOS_OPT_SERVICE_PROVIDER', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER, 'google_analytics')) ?: 'google_analytics');
        define('CAOS_OPT_DOMAIN_NAME', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_DOMAIN_NAME, $domain_name = str_replace(['https://', 'http://'], '', get_home_url()))) ?: $domain_name);
        define('CAOS_OPT_TRACKING_ID', $translated_tracking_id != 'UA-123456789' ? $translated_tracking_id : esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_ID)));
        define('CAOS_OPT_DUAL_TRACKING', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_DUAL_TRACKING)));
        define('CAOS_OPT_GA4_MEASUREMENT_ID', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID)));
        define('CAOS_OPT_ALLOW_TRACKING', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING)));
        define('CAOS_OPT_COOKIE_NAME', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME)));
        define('CAOS_OPT_COOKIE_VALUE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_VALUE)));
        define('CAOS_OPT_TRACKING_CODE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_CODE)));
        define('CAOS_OPT_SCRIPT_POSITION', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION)) ?: 'header');
        define('CAOS_OPT_ADJUSTED_BOUNCE_RATE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_ADJUSTED_BOUNCE_RATE)));
        define('CAOS_OPT_COMPATIBILITY_MODE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE)) ?: '');
        define('CAOS_OPT_SESSION_EXPIRY_DAYS', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_GA_SESSION_EXPIRY_DAYS, 30)));
        define('CAOS_OPT_SITE_SPEED_SAMPLE_RATE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_SITE_SPEED_SAMPLE_RATE, 1)));
        define('CAOS_OPT_ENQUEUE_ORDER', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_ENQUEUE_ORDER)) ?: 10);
        define('CAOS_OPT_ANONYMIZE_IP_MODE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_ANONYMIZE_IP_MODE, '')));
        define('CAOS_OPT_TRACK_ADMIN', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACK_ADMIN)));
        define('CAOS_OPT_DISABLE_ADS_FEAT', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_DISABLE_ADS_FEATURES)));
        define('CAOS_OPT_REMOTE_JS_FILE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_JS_FILE)) ?: 'analytics.js');
        define('CAOS_OPT_CACHE_DIR', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR)) ?: '/uploads/caos/');
        define('CAOS_OPT_CDN_URL', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_CDN_URL)));
        define('CAOS_OPT_EXT_CAPTURE_OUTBOUND_LINKS', esc_attr(get_option(CAOS_Admin_Settings::CAOS_EXT_SETTING_CAPTURE_OUTBOUND_LINKS)));
        define('CAOS_OPT_UNINSTALL_SETTINGS', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS)));
        define('CAOS_OPT_EXT_TRACK_AD_BLOCKERS', esc_attr(get_option(CAOS_Admin_Settings::CAOS_EXT_SETTING_TRACK_AD_BLOCKERS)));
        define('CAOS_OPT_EXT_LINKID', esc_attr(get_option(CAOS_Admin_Settings::CAOS_EXT_SETTING_LINKID)));
        define('CAOS_COOKIE_EXPIRY_SECONDS', CAOS_OPT_SESSION_EXPIRY_DAYS ? CAOS_OPT_SESSION_EXPIRY_DAYS * 86400 : 2592000);
        define('CAOS_CRON', 'caos_update_analytics_js');
        define('CAOS_GA_URL', 'https://www.google-analytics.com');
        define('CAOS_GTM_URL', 'https://www.googletagmanager.com');
        define('CAOS_LOCAL_DIR', WP_CONTENT_DIR . CAOS_OPT_CACHE_DIR);
    }

    /**
     * @return false|array 
     */
    public static function get_file_aliases()
    {
        global $caos_file_aliases;

        return $caos_file_aliases;
    }

    /**
     * @param string $key 
     * @return string 
     */
    public static function get_file_alias($key = '')
    {
        $file_aliases = self::get_file_aliases();

        if (!$file_aliases) {
            return '';
        }

        return $file_aliases[$key] ?? '';
    }

    /**
     * Retrieves the currently used file key. Convenient when searching for file aliases.
     * 
     * @return mixed 
     */
    public static function get_current_file_key()
    {
        return CAOS_OPT_SERVICE_PROVIDER == 'plausible' ? CAOS_OPT_SERVICE_PROVIDER : str_replace('.js', '', CAOS_OPT_REMOTE_JS_FILE);
    }

    /**
     * @param array $file_aliases 
     * @param bool $write 
     * @return bool 
     */
    public static function set_file_aliases($file_aliases, $write = false)
    {
        global $caos_file_aliases;

        $caos_file_aliases = $file_aliases;

        if ($write) {
            return update_option(CAOS_Admin_Settings::CAOS_CRON_FILE_ALIASES, $file_aliases);
        }

        /**
         * There's no reason to assume that updating a global variable would fail. Always return true at this point.
         */
        return true;
    }

    /**
     * @param string $key 
     * @param string $alias 
     * @param bool $write 
     * @return bool 
     */
    public static function set_file_alias($key, $alias, $write = false)
    {
        $file_aliases = self::get_file_aliases();

        $file_aliases[$key] = $alias;

        return self::set_file_aliases($file_aliases, $write);
    }

    /**
     * Includes backwards compatibility for pre 3.11.0
     * 
     * @since 3.11.0
     * 
     * @param mixed $key 
     * @return string|void 
     */
    public static function get_file_alias_path($key)
    {
        $file_path = CAOS_LOCAL_DIR . $key . '.js';

        // Backwards compatibility
        if (!self::get_file_aliases()) {
            return $file_path;
        }

        $file_alias = self::get_file_alias($key) ?? '';

        // Backwards compatibility
        if (!$file_alias) {
            return $file_path;
        }

        return CAOS_LOCAL_DIR . $file_alias;
    }

    /**
     * Global debug logging function.
     * 
     * @param mixed $message 
     * @return void 
     */
    public static function debug($message)
    {
        if (!defined('CAOS_DEBUG_MODE') || CAOS_DEBUG_MODE === false) {
            return;
        }

        error_log(current_time('Y-m-d H:i:s') . ": $message\n", 3, trailingslashit(WP_CONTENT_DIR) . 'caos-debug.log');
    }

    /**
     * @return CAOS_Setup
     */
    private function do_setup()
    {
        register_uninstall_hook(CAOS_PLUGIN_FILE, 'CAOS::do_uninstall');

        return new CAOS_Setup();
    }

    /**
     * Triggers all required DB updates (if any).
     * 
     * @return void 
     */
    private function update_db()
    {
        new CAOS_DB();
    }

    /**
     * Modify behavior of OMGF's AJAX hooks.
     * 
     * @return void 
     */
    private function add_ajax_hooks()
    {
        new CAOS_Ajax();
    }

    /**
     * @return CAOS_Admin_Settings
     */
    private function do_settings()
    {
        return new CAOS_Admin_Settings();
    }

    /**
     * @return CAOS_Frontend_Functions
     */
    private function do_frontend()
    {
        return new CAOS_Frontend_Functions();
    }

    /**
     * @return CAOS_Frontend_Tracking
     */
    private function do_tracking_code()
    {
        return new CAOS_Frontend_Tracking();
    }

    /**
     * Triggers when CAOS (Pro) is (de)activated.
     * 
     * @return CAOS_Cron 
     */
    public function trigger_cron_script()
    {
        if (self::uses_minimal_analytics()) {
            return;
        }

        return new CAOS_Cron();
    }

    /**
     * Check if (de)activated plugin is CAOS Pro and if so, update.
     */
    public function maybe_do_update($plugin)
    {
        if (strpos($plugin, self::CAOS_PRO_PLUGIN_SLUG) === false) {
            return;
        }

        $this->trigger_cron_script();
    }

    /**
     * @return CAOS_Admin_UpdateFiles 
     */
    public function do_update_after_save()
    {
        $settings_page    = $_GET['page'] ?? '';
        $settings_updated = $_GET['settings-updated'] ?? '';

        if (CAOS_Admin_Settings::CAOS_ADMIN_PAGE != $settings_page) {
            return;
        }

        if (!$settings_updated) {
            return;
        }

        return $this->trigger_cron_script();
    }

    /**
     * Make sure downloaded files are updated after plugin is updated.
     * 
     * @param mixed $upgrade_obj 
     * @param array $options 
     * @return void|CAOS_Cron 
     */
    public function do_update_after_update($upgrade_obj, $options)
    {
        if (
            isset($options['action']) && $options['action'] != 'update'
            && isset($options['type']) && $options['type'] != 'plugin'
        ) {
            return;
        }

        if (!isset($options['plugins'])) {
            return;
        }

        foreach ($options['plugins'] as $plugin) {
            if ($plugin == CAOS_PLUGIN_BASENAME) {
                return $this->trigger_cron_script();
            }
        }
    }

    /**
     * Render update notices if available.
     * 
     * @param mixed $plugin 
     * @param mixed $response 
     * @return void 
     */
    public function render_update_notice($plugin, $response)
    {
        $current_version = $plugin['Version'];
        $new_version     = $plugin['new_version'];

        if (version_compare($current_version, $new_version, '<')) {
            $response = wp_remote_get('https://daan.dev/caos-update-notices.json');

            if (is_wp_error($response)) {
                return;
            }

            $update_notices = (array) json_decode(wp_remote_retrieve_body($response));

            if (!isset($update_notices[$new_version])) {
                return;
            }

            printf(
                ' <strong>' . __('This update includes major changes. Please <a href="%s" target="_blank">read this</a> before updating.') . '</strong>',
                $update_notices[$new_version]->url
            );
        }
    }

    /**
     * Register CAOS Proxy so endpoint can be used.
     * For using Stealth mode, SSL is required.
     */
    public function register_routes()
    {
        if (CAOS_OPT_EXT_TRACK_AD_BLOCKERS) {
            $proxy = new CAOS_API_AdBlockDetect();
            $proxy->register_routes();
        }
    }

    /**
     * Returns early if File Aliases option doesn't exist for Backwards Compatibility.
     * 
     * @since 3.11.0
     *  
     * @return string
     */
    public static function get_local_file_url()
    {
        $url = content_url() . CAOS_OPT_CACHE_DIR . CAOS_OPT_REMOTE_JS_FILE;

        /**
         * is_ssl() fails when behind a load balancer or reverse proxy. That's why we double check here if 
         * SSL is enabled and rewrite accordingly.
         */
        if (strpos(home_url(), 'https://') !== false && !is_ssl()) {
            $url = str_replace('http://', 'https://', $url);
        }

        if (CAOS_OPT_CDN_URL) {
            $url = str_replace(get_home_url(get_current_blog_id()), '//' . CAOS_OPT_CDN_URL, $url);
        }

        if (!self::get_file_aliases()) {
            return $url;
        }

        $file_alias = self::get_file_alias(CAOS::get_current_file_key());

        if (!$file_alias) {
            return $url;
        }

        $url = str_replace(CAOS_OPT_REMOTE_JS_FILE, $file_alias, $url);

        return $url;
    }

    /**
     * @return CAOS_Uninstall
     * @throws ReflectionException
     */
    public static function do_uninstall()
    {
        return new CAOS_Uninstall();
    }

    /**
     * File downloader
     * 
     * @param mixed $local_file 
     * @param mixed $remote_file 
     * @param string $file 
     * @param bool $is_plugin 
     * 
     * @return string
     */
    public static function download_file(
        $local_file,
        $remote_file,
        $file = '',
        $is_plugin = false
    ) {
        $download = new CAOS_FileManager();

        return $download->download_file($local_file, $remote_file, $file, $is_plugin);
    }

    /**
     * @param string $path 
     * 
     * @return bool 
     */
    public static function create_dir_r($path)
    {
        $file_manager = new CAOS_FileManager();

        return $file_manager->create_dir_recursive($path);
    }

    /**
     * @param string $file 
     * @param string $find 
     * @param string $replace
     *  
     * @return int|false 
     */
    public static function find_replace_in($file, $find, $replace)
    {
        $file_manager = new CAOS_FileManager();

        return $file_manager->find_replace_in($file, $find, $replace);
    }

    /**
     * 
     */
    public static function uses_minimal_analytics()
    {
        return CAOS_OPT_TRACKING_CODE == 'minimal' || CAOS_OPT_TRACKING_CODE == 'minimal_ga4';
    }

    /**
     * Global method to figure out if CAOS is setup to use Dual Tracking.
     * 
     * @return bool 
     */
    public static function dual_tracking_is_enabled()
    {
        return strpos(CAOS_OPT_TRACKING_ID, 'UA-') === 0 && CAOS_OPT_DUAL_TRACKING == 'on';
    }
}
