<?php
defined('ABSPATH') || exit;

/**
 * @package   CAOS
 * @author    Daan van den Bergh
 *            https://ffw.press
 * @copyright © 2022 Daan van den Bergh
 * @license   BY-NC-ND-4.0
 *            http://creativecommons.org/licenses/by-nc-nd/4.0/
 */

class CAOS_Ajax
{
    private $plugin_text_domain = 'host-analyticsjs-local';

    /**
     * Build class.
     * 
     * @return void 
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Init hooks and filters.
     * 
     * @return void 
     */
    private function init()
    {
        add_action('wp_ajax_caos_update_files', [$this, 'update_files']);
    }

    /**
     * Update aliases in database.
     * 
     * @return void 
     */
    public function update_files()
    {
        check_ajax_referer(CAOS_Admin_Settings::CAOS_ADMIN_PAGE, 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__("Sorry, you're not allowed to do this.", $this->plugin_text_domain));
        }

        global $caos_file_aliases;

        if (empty($caos_file_aliases) || !$caos_file_aliases) {
            return;
        }

        foreach ($caos_file_aliases as $file => $alias) {
            $caos_file_aliases[$file] = bin2hex(random_bytes(4)) . '.js';
        }

        CAOS::set_file_aliases($caos_file_aliases, true);
    }
}
