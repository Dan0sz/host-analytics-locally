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
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

defined('ABSPATH') || exit;

class CAOS_Admin_Settings
{
    const CAOS_ADMIN_ALLOW_TRACKING_OPTIONS  = array(
        ''                  => array(
            'label' => 'Always (default)',
            'show'  => null,
            'hide'  => 'caos_gdpr_setting'
        ),
        'cookie_is_set'     => array(
            'label' => 'When cookie is set',
            'show'  => 'caos_allow_tracking_name',
            'hide'  => 'caos_allow_tracking_value'
        ),
        'cookie_is_not_set' => array(
            'label' => 'When cookie is NOT set',
            'show'  => 'caos_allow_tracking_name',
            'hide'  => 'caos_allow_tracking_value'
        ),
        'cookie_has_value'  => array(
            'label' => 'When cookie has a value',
            'show'  => 'caos_allow_tracking_name, caos_allow_tracking_value',
            'hide'  => null
        )
    );
    const CAOS_ADMIN_SCRIPT_POSITION_OPTIONS = array(
        'header' => array(
            'label' => 'Header (default)',
            'hide'  => 'caos_add_manually',
            'show'  => null
        ),
        'footer' => array(
            'label' => 'Footer',
            'hide'  => 'caos_add_manually',
            'show'  => null
        ),
        'manual' => array(
            'label' => 'Add manually',
            'hide'  => null,
            'show'  => 'caos_add_manually'
        )
    );
    const CAOS_ADMIN_JS_FILE_OPTIONS         = array(
        "Analytics.js (default)" => "analytics.js",
        "Gtag.js"                => "gtag.js",
        "Ga.js (legacy)"         => "ga.js"
    );
    const CAOS_ADMIN_COMPATIBILITY_OPTIONS = array(
        ''                 => array(
            'label' => 'None (default)'
        ),
        'woocommerce'      => array(
            'label' => 'WooCommerce Google Analytics Integration'
        ),
        'analytify'        => array(
            'label' => 'GADP for WP by Analytify'
        ),
        'exact_metrics'    => array(
            'label' => 'GAD for WP by ExactMetrics'
        ),
        'monster_insights' => array(
            'label' => 'GADP for WP by Monster Insights'
        )
    );
    const CAOS_SETTING_TRACKING_ID              = 'sgal_tracking_id';
    const CAOS_SETTING_ALLOW_TRACKING           = 'caos_allow_tracking';
    const CAOS_SETTING_COOKIE_NOTICE_NAME       = 'sgal_cookie_notice_name';
    const CAOS_SETTING_COOKIE_VALUE             = 'caos_cookie_value';
    const CAOS_SETTING_COMPATIBILITY_MODE       = 'caos_analytics_compatibility_mode';
    const CAOS_SETTING_STEALTH_MODE             = 'caos_stealth_mode';
    const CAOS_SETTING_JS_FILE                  = 'caos_analytics_js_file';
    const CAOS_SETTING_CACHE_DIR                = 'caos_analytics_cache_dir';
    const CAOS_SETTING_CDN_URL                  = 'caos_analytics_cdn_url';
    const CAOS_SETTING_CAPTURE_OUTBOUND_LINKS   = 'caos_capture_outbound_links';
    const CAOS_SETTING_GA_COOKIE_EXPIRY_DAYS    = 'sgal_ga_cookie_expiry_days';
    const CAOS_SETTING_ADJUSTED_BOUNCE_RATE     = 'sgal_adjusted_bounce_rate';
    const CAOS_SETTING_SCRIPT_POSITION          = 'sgal_script_position';
    const CAOS_SETTING_SNIPPET_TYPE             = 'caos_snippet_type';
    const CAOS_SETTING_ENQUEUE_ORDER            = 'sgal_enqueue_order';
    const CAOS_SETTING_ANONYMIZE_IP             = 'sgal_anonymize_ip';
    const CAOS_SETTING_TRACK_ADMIN              = 'sgal_track_admin';
    const CAOS_SETTING_DISABLE_DISPLAY_FEATURES = 'caos_disable_display_features';
    const CAOS_SETTING_UNINSTALL_SETTINGS       = 'caos_analytics_uninstall_settings';

    /**
     * CAOS_Admin_Settings constructor.
     */
    public function __construct()
    {
        $caosLink = plugin_basename(CAOS_PLUGIN_FILE);

        // @formatter:off
        add_action('admin_menu', array($this, 'create_menu'));
        add_filter("plugin_action_links_$caosLink", array($this, 'settings_link'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_js_scripts'));
        // @formatter:on
    }

    /**
     * Create WP menu-item
     */
    public function create_menu()
    {
        // @formatter:off
        add_options_page(
            'CAOS',
            'Optimize Analytics',
            'manage_options',
            'host_analyticsjs_local',
            array($this, 'settings_page')
        );

        add_action('admin_init', array($this, 'register_settings'));
        // @formatter:on
    }

    /**
     * Create settings page
     */
    public function settings_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__("You're not cool enough to access this page.", 'host-analyticsjs-local'));
        }
        ?>

        <div class="wrap">
            <h1><?php _e('CAOS | Complete Analytics Optimization Suite', 'host-analyticsjs-local'); ?></h1>

            <div id="caos-notices"></div>

            <?php $this->get_template('welcome'); ?>

            <form method="post" action="options.php">
                <?php
                settings_fields('caos-basic-settings');
                do_settings_sections('caos-basic-settings');
                ?>

                <?php $this->get_template('settings-form'); ?>

                <?php do_action('caos_after_form_settings'); ?>

                <div style="clear: left; display: inline-block;">
                    <?php submit_button(); ?>
                </div>

                <div style="display: inline-block;">
                    <p class="submit">
                        <input id="manual-download" class="button button-secondary" name="caos-download" value="Update <?= CAOS_OPT_REMOTE_JS_FILE; ?>" type="button" onclick="caosDownloadManually();"/>
                    </p>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Register all settings.
     *
     * @throws ReflectionException
     */
    public function register_settings()
    {
        foreach ($this->get_settings() as $constant => $value) {
            register_setting(
                'caos-basic-settings',
                $value
            );
        }
    }

    /**
     * Add settings link to plugin overview
     *
     * @param $links
     *
     * @return mixed
     */
    public function settings_link($links)
    {
        $adminUrl     = admin_url() . 'options-general.php?page=host_analyticsjs_local';
        $settingsLink = "<a href='$adminUrl'>" . __('Settings', 'host-analyticsjs-local') . "</a>";
        array_push($links, $settingsLink);

        return $links;
    }

    /**
     * Enqueue JS scripts for Administrator Area.
     *
     * @param $hook
     */
    public function enqueue_admin_js_scripts($hook)
    {
        if ($hook == 'settings_page_host_analyticsjs_local') {
            wp_enqueue_script('caos_admin_script', plugins_url('js/caos-admin.js', CAOS_PLUGIN_FILE), ['jquery'], CAOS_STATIC_VERSION, true);
        }
    }

    /**
     * Get all settings using the constants in this class.
     *
     * @return array
     * @throws ReflectionException
     */
    public function get_settings()
    {
        $reflection     = new ReflectionClass($this);
        $constants      = $reflection->getConstants();

        return array_filter(
            $constants,
            function ($key) {
                return strpos($key, 'CAOS_SETTING') !== false;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param $file
     *
     * @return mixed
     */
    private function get_template($file)
    {
        return include CAOS_PLUGIN_DIR . 'templates/admin/block-' . $file . '.php';
    }
}
