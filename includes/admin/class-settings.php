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
 * @url      : https://daan.dev/wordpress-plugins/caos/
 * @copyright: (c) 2020 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

defined('ABSPATH') || exit;

class CAOS_Admin_Settings extends CAOS_Admin
{
    /**
     * Admin Sections
     */
    const CAOS_ADMIN_SECTION_BASIC_SETTINGS = 'caos-basic-settings';
    const CAOS_ADMIN_SECTION_ADV_SETTINGS   = 'caos-advanced-settings';
    const CAOS_ADMIN_SECTION_EXT_SETTINGS   = 'caos-extensions-settings';
    /**
     * Option Values
     */
    const CAOS_ADMIN_ALLOW_TRACKING_OPTIONS  = [
        ''                  => 'Always (default)',
        'cookie_is_set'     => 'When cookie is set',
        'cookie_is_not_set' => 'When cookie is NOT set',
        'cookie_has_value'  => 'When cookie has a value',
    ];
    const CAOS_ADMIN_SNIPPET_TYPE_OPTIONS    = [
        ''        => 'Default',
        'async'   => 'Asynchronous',
        'minimal' => 'Minimal Analytics (fastest)'
    ];
    const CAOS_ADMIN_SCRIPT_POSITION_OPTIONS = [
        'header' => 'Header (default)',
        'footer' => 'Footer',
        'manual' => 'Add manually',
    ];
    const CAOS_ADMIN_JS_FILE_OPTIONS         = [
        "analytics.js" => "Analytics.js (default)",
        "gtag.js"      => "Gtag.js"
    ];
    const CAOS_ADMIN_COMPATIBILITY_OPTIONS   = [
        ''                 => 'None (default)',
        'woocommerce'      => 'WooCommerce Google Analytics Integration',
        'analytify'        => 'GADP for WP by Analytify',
        'exact_metrics'    => 'GAD for WP by ExactMetrics',
        'monster_insights' => 'GADP for WP by Monster Insights'
    ];
    const CAOS_ADMIN_EXT_PLUGIN_HANDLING     = [
        'set_redirect' => 'Safe Mode (default)',
        'send_file'    => 'Experimental (faster)'
    ];
    /**
     * CAOS Basic/Advanced Settings
     */
    const CAOS_BASIC_SETTING_TRACKING_ID            = 'sgal_tracking_id';
    const CAOS_BASIC_SETTING_ALLOW_TRACKING         = 'caos_allow_tracking';
    const CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME     = 'sgal_cookie_notice_name';
    const CAOS_BASIC_SETTING_COOKIE_VALUE           = 'caos_cookie_value';
    const CAOS_BASIC_SETTING_SNIPPET_TYPE           = 'caos_snippet_type';
    const CAOS_BASIC_SETTING_SCRIPT_POSITION        = 'sgal_script_position';
    const CAOS_ADV_SETTING_COMPATIBILITY_MODE       = 'caos_analytics_compatibility_mode';
    const CAOS_ADV_SETTING_JS_FILE                  = 'caos_analytics_js_file';
    const CAOS_ADV_SETTING_CACHE_DIR                = 'caos_analytics_cache_dir';
    const CAOS_ADV_SETTING_CDN_URL                  = 'caos_analytics_cdn_url';
    const CAOS_ADV_SETTING_CAPTURE_OUTBOUND_LINKS   = 'caos_capture_outbound_links';
    const CAOS_ADV_SETTING_GA_COOKIE_EXPIRY_DAYS    = 'sgal_ga_cookie_expiry_days';
    const CAOS_ADV_SETTING_ADJUSTED_BOUNCE_RATE     = 'sgal_adjusted_bounce_rate';
    const CAOS_ADV_SETTING_ENQUEUE_ORDER            = 'sgal_enqueue_order';
    const CAOS_ADV_SETTING_ANONYMIZE_IP             = 'sgal_anonymize_ip';
    const CAOS_ADV_SETTING_TRACK_ADMIN              = 'sgal_track_admin';
    const CAOS_ADV_SETTING_DISABLE_DISPLAY_FEATURES = 'caos_disable_display_features';
    const CAOS_ADV_SETTING_UNINSTALL_SETTINGS       = 'caos_analytics_uninstall_settings';
    const CAOS_EXT_SETTING_PLUGIN_HANDLING          = 'caos_extension_plugin_handling';
    const CAOS_EXT_SETTING_STEALTH_MODE             = 'caos_stealth_mode';
    const CAOS_EXT_SETTING_TRACK_AD_BLOCKERS        = 'caos_extension_track_ad_blockers';
    const CAOS_EXT_SETTING_LINKID                   = 'caos_extension_linkid';
    const CAOS_EXT_SETTING_OPTIMIZE                 = 'caos_extension_optimize';
    const CAOS_EXT_SETTING_OPTIMIZE_ID              = 'caos_extension_optimize_id';
    /**
     * Info URLs
     */
    const WOOSH_DEV_WORDPRESS_PLUGINS_SUPER_STEALTH = 'https://woosh.dev/wordpress-plugins/caos-super-stealth-upgrade/';
    const CAOS_ADMIN_SETTINGS_EXTENSIONS_TAB_URI    = 'options-general.php?page=host_analyticsjs_local&tab=caos-extensions-settings';
    const CAOS_SETTINGS_UTM_PARAMS_SUPPORT_TAB      = '?utm_source=caos&utm_medium=plugin&utm_campaign=support_tab';

    /** @var string $active_tab */
    private $active_tab;

    /** @var string $page */
    private $page;

    /** @var string $plugin_text_domain */
    private $plugin_text_domain = 'host-analyticsjs-local';

    /**
     * CAOS_Admin_Settings constructor.
     */
    public function __construct()
    {
        $this->active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'caos-basic-settings';
        $this->page       = isset($_GET['page']) ? $_GET['page'] : '';

        parent::__construct();

        // @formatter:off
        // Global
        add_action('admin_menu', array($this, 'create_menu'));
        add_filter('plugin_action_links_' . plugin_basename(CAOS_PLUGIN_FILE), array($this, 'settings_link'));

        if (!$this->page == 'host_analyticsjs_local') {
            return;
        }

        // Scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_js_scripts'));

        // Tabs
        add_action('caos_settings_tab', [$this, 'do_basic_settings_tab'], 1);

        if (CAOS_OPT_SNIPPET_TYPE != 'minimal') {
            add_action('caos_settings_tab', [$this, 'do_advanced_settings_tab'], 2);
        }

        add_action('caos_settings_tab', [$this, 'do_extensions_tab'], 3);

        add_action('caos_settings_content', [$this, 'do_content'], 1);
        // @formatter:on

        $this->do_cron_check();
    }

    /**
     * Create WP menu-item
     */
    public function create_menu()
    {
        // @formatter:off
        add_options_page(
            'CAOS',
            'Optimize Google Analytics',
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
            wp_die(__("You're not cool enough to access this page.", $this->plugin_text_domain));
        }
        ?>

        <div class="wrap">
            <h1><?php _e('CAOS | Complete Analytics Optimization Suite', $this->plugin_text_domain); ?></h1>

            <p>
                <?= get_plugin_data(CAOS_PLUGIN_FILE)['Description']; ?>
            </p>

            <div class="settings-column left">
                <h2 class="caos-nav nav-tab-wrapper">
                    <?php do_action('caos_settings_tab'); ?>
                </h2>

                <form method="post" action="options.php?tab=<?= $this->active_tab; ?>">
                    <?php
                    settings_fields($this->active_tab);
                    do_settings_sections($this->active_tab);
                    ?>

                    <?php do_action('caos_settings_content'); ?>

                    <?php
                    $current_section = str_replace('-', '_', $this->active_tab);
                    do_action( "after_$current_section");
                    ?>

                    <?php submit_button(null, 'primary', 'submit', false); ?>

                    <?php if (CAOS_OPT_SNIPPET_TYPE != 'minimal'): ?>
                        <a href="#" id="manual-download" class="button button-secondary"><?= __('Update', $this->plugin_text_domain); ?> <?= CAOS_OPT_REMOTE_JS_FILE; ?></a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="settings-column right">
                <div id="caos-welcome-panel" class="welcome-panel">
                    <?php $this->get_template('welcome'); ?>
                </div>
            </div>
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
        if ($this->active_tab !== self::CAOS_ADMIN_SECTION_BASIC_SETTINGS
            && $this->active_tab !== self::CAOS_ADMIN_SECTION_ADV_SETTINGS
            && $this->active_tab !== self::CAOS_ADMIN_SECTION_EXT_SETTINGS) {
            $this->active_tab = self::CAOS_ADMIN_SECTION_BASIC_SETTINGS;
        }

        foreach ($this->get_settings() as $constant => $value) {
            register_setting(
                $this->active_tab,
                $value
            );
        }
    }

    /**
     * Get all settings for the current section using the constants in this class.
     *
     * @return array
     * @throws ReflectionException
     */
    public function get_settings()
    {
        $reflection = new ReflectionClass($this);
        $constants  = $reflection->getConstants();
        $needle     = 'CAOS_BASIC_SETTING';

        if ($this->active_tab == self::CAOS_ADMIN_SECTION_ADV_SETTINGS) {
            $needle = 'CAOS_ADV_SETTING';
        }

        if ($this->active_tab == self::CAOS_ADMIN_SECTION_EXT_SETTINGS) {
            $needle = 'CAOS_EXT_SETTING';
        }

        return array_filter(
            $constants,
            function ($key) use ($needle) {
                return strpos($key, $needle) !== false;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Add Basic Settings Tab to Settings Screen.
     */
    public function do_basic_settings_tab()
    {
        $this->generate_tab(self::CAOS_ADMIN_SECTION_BASIC_SETTINGS, 'dashicons-analytics', __('Basic Settings', $this->plugin_text_domain));
    }

    /**
     * Add Advanced Settings Tab to Settings Screen.
     */
    public function do_advanced_settings_tab()
    {
        $this->generate_tab(self::CAOS_ADMIN_SECTION_ADV_SETTINGS, 'dashicons-admin-settings', __('Advanced Settings', $this->plugin_text_domain));
    }

    /**
     * Add Connect Tab to Settings Screen.
     */
    public function do_extensions_tab()
    {
        $this->generate_tab(self::CAOS_ADMIN_SECTION_EXT_SETTINGS, 'dashicons-admin-plugins', __('Extensions', $this->plugin_text_domain));
    }

    /**
     * @param      $id
     * @param null $icon
     * @param null $label
     */
    private function generate_tab($id, $icon = null, $label = null)
    {
        ?>
        <a class="nav-tab dashicons-before <?= $icon; ?> <?= $this->active_tab == $id ? 'nav-tab-active' : ''; ?>" href="<?= $this->generate_tab_link($id);?>">
            <?= $label; ?>
        </a>
        <?php
    }

    /**
     * @param $tab
     *
     * @return string
     */
    private function generate_tab_link($tab)
    {
        return admin_url("options-general.php?page=host_analyticsjs_local&tab=$tab");
    }

    /**
     * Render active content.
     */
    public function do_content()
    {
        echo apply_filters(str_replace('-', '_', $this->active_tab) . '_content', '');
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
        $settingsLink = "<a href='$adminUrl'>" . __('Settings', $this->plugin_text_domain) . "</a>";
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
            wp_enqueue_script('caos_admin_script', plugins_url('assets/js/caos-admin.js', CAOS_PLUGIN_FILE), ['jquery'], CAOS_STATIC_VERSION, true);
            wp_enqueue_script('caos_track_ad_blockers', plugins_url('assets/js/detect-ad-block.js', CAOS_PLUGIN_FILE), [ 'jquery' ], CAOS_STATIC_VERSION, true);
            wp_add_inline_script('caos_track_ad_blockers', $this->is_ad_blocker_active());
        }
    }

    /**
     * Add inline script which checks if admin's Ad Blocker is active.
     *
     * @return string
     */
    private function is_ad_blocker_active()
    {
        $warning = sprintf(__("Your Ad Blocker is enabled. If 'Update %s' doesn't work, please disable your Ad Blocker.", $this->plugin_text_domain), CAOS_OPT_REMOTE_JS_FILE);

        $script = "jQuery(document).ready(function ($) { var caos_detect_ad_blocker = 1; if (document.getElementById('caos-detect-ad-block')) { caos_detect_ad_blocker = 0; } if (caos_detect_ad_blocker === 1) { $('.settings-column form').before(\"<p style='color: #FF4136;'><strong>$warning</strong></p>\"); } });";

        return $script;
    }

    /**
     * Checks downloaded file and cron health.
     *
     * @return CAOS_Admin_Functions
     */
    private function do_cron_check()
    {
        return new CAOS_Admin_Functions();
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
