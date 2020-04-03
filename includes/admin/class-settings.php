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
    const CAOS_ADMIN_SECTION_BASIC_SETTINGS           = 'caos-basic-settings';
    const CAOS_ADMIN_SECTION_ADV_SETTINGS             = 'caos-advanced-settings';

    /**
     * Option Values
     */
    const CAOS_ADMIN_ALLOW_TRACKING_OPTIONS     = array(
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
    const CAOS_ADMIN_SCRIPT_POSITION_OPTIONS    = array(
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
    const CAOS_ADMIN_JS_FILE_OPTIONS            = array(
        "Analytics.js (default)" => "analytics.js",
        "Gtag.js"                => "gtag.js",
        "Ga.js (legacy)"         => "ga.js"
    );
    const CAOS_ADMIN_COMPATIBILITY_OPTIONS      = array(
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

    /**
     * CAOS Basic/Advanced Settings
     */
    const CAOS_BASIC_SETTING_TRACKING_ID        = 'sgal_tracking_id';
    const CAOS_BASIC_SETTING_ALLOW_TRACKING     = 'caos_allow_tracking';
    const CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME = 'sgal_cookie_notice_name';
    const CAOS_BASIC_SETTING_COOKIE_VALUE       = 'caos_cookie_value';
    const CAOS_BASIC_SETTING_SNIPPET_TYPE       = 'caos_snippet_type';
    const CAOS_BASIC_SETTING_SCRIPT_POSITION    = 'sgal_script_position';
    const CAOS_ADV_SETTING_COMPATIBILITY_MODE   = 'caos_analytics_compatibility_mode';
    const CAOS_ADV_SETTING_STEALTH_MODE         = 'caos_stealth_mode';
    const CAOS_ADV_SETTING_PRECONNECT           = 'caos_preconnect';
    const CAOS_ADV_SETTING_JS_FILE              = 'caos_analytics_js_file';
    const CAOS_ADV_SETTING_CACHE_DIR            = 'caos_analytics_cache_dir';
    const CAOS_ADV_SETTING_CDN_URL              = 'caos_analytics_cdn_url';
    const CAOS_ADV_SETTING_CAPTURE_OUTBOUND_LINKS   = 'caos_capture_outbound_links';
    const CAOS_ADV_SETTING_GA_COOKIE_EXPIRY_DAYS    = 'sgal_ga_cookie_expiry_days';
    const CAOS_ADV_SETTING_ADJUSTED_BOUNCE_RATE     = 'sgal_adjusted_bounce_rate';
    const CAOS_ADV_SETTING_ENQUEUE_ORDER            = 'sgal_enqueue_order';
    const CAOS_ADV_SETTING_ANONYMIZE_IP             = 'sgal_anonymize_ip';
    const CAOS_ADV_SETTING_TRACK_ADMIN              = 'sgal_track_admin';
    const CAOS_ADV_SETTING_DISABLE_DISPLAY_FEATURES = 'caos_disable_display_features';
    const CAOS_ADV_SETTING_UNINSTALL_SETTINGS       = 'caos_analytics_uninstall_settings';

    /** @var string $active_tab */
    private $active_tab;

    /** @var string $page */
    private $page;

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
        add_action('caos_settings_tab', [$this, 'do_advanced_settings_tab'], 2);

        // Content
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

                    <a href="#" id="manual-download" class="button button-secondary"><?= __('Update', 'host-analyticsjs-local'); ?> <?= CAOS_OPT_REMOTE_JS_FILE; ?></a>
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
            && $this->active_tab !== self::CAOS_ADMIN_SECTION_ADV_SETTINGS) {
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

        if ($this->active_tab == 'caos-advanced-settings') {
            $needle = 'CAOS_ADV_SETTING';
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
        $this->generate_tab('caos-basic-settings', 'dashicons-analytics', __('Basic', 'host-analyticsjs-local'));
    }

    /**
     * Add Advanced Settings Tab to Settings Screen.
     */
    public function do_advanced_settings_tab()
    {
        $this->generate_tab('caos-advanced-settings', 'dashicons-admin-settings', __('Advanced', 'host-analyticsjs-local'));
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
        $tab = str_replace('caos-', '', $this->active_tab);

        $this->get_template($tab);
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
            wp_enqueue_script('caos_admin_script', plugins_url('assets/js/caos-admin.js', CAOS_PLUGIN_FILE), ['jquery'], CAOS_STATIC_VERSION, true);
        }
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
