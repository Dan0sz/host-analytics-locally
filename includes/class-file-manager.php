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

defined('ABSPATH') || exit;

class CAOS_FileManager
{
    /**
     * @var $file
     */
    private $file_contents;

    /**
     * @var string
     */
    protected $plugin_text_domain = 'host-analyticsjs-local';

    /**
     * Downloads $remoteFile, check if $localFile exists and if so deletes it, then writes it to $localFile
     *
     * @param $localFile
     * @param $remoteFile
     * @param $file string
     * @param $is_plugin bool
     *
     * @return void|string
     */
    public function download_file($local_file, $remote_file, $file = '', $is_plugin = false)
    {
        do_action('caos_admin_update_before');

        $this->file_contents = wp_remote_get($remote_file);

        if (is_wp_error($this->file_contents)) {
            CAOS::debug(sprintf(__('An error occurred: %s - %s', $this->plugin_text_domain), $this->file_contents->get_error_code(), $this->file_contents->get_error_message()));

            return $this->file_contents->get_error_code() . ': ' . $this->file_contents->get_error_message();
        }

        /**
         * If $file is not set, extract it from $remoteFile, unless we're downloading a plugin.
         * 
         * @since 3.11.0
         * @since 4.0.3 Don't rename plugins.
         */
        $file = $file ?: pathinfo($remote_file)['filename'];

        if (!$is_plugin) {
            $file_aliases = CAOS::get_file_aliases();
            $file_alias   = $file_aliases[$file] ?? '';
        } else {
            $file_alias = $file . '.js';
        }

        /**
         * If no file alias has been set (yet) and we're not downloading a plugin, generate a new alias.
         * 
         * @since 4.0.2
         * @since 4.0.3 Don't rename plugins
         * 
         */
        if (!$file_alias && !$is_plugin) {
            $file_alias = bin2hex(random_bytes(4)) . '.js';
        }

        $local_dir = CAOS_LOCAL_DIR;

        /**
         * If file is a plugin, we use the same subdirectory structure Google uses.
         */
        if ($is_plugin) {
            $local_dir = untrailingslashit(CAOS_LOCAL_DIR) . str_replace(CAOS_GA_URL, '', $remote_file);
            $local_dir = trailingslashit(pathinfo($local_dir)['dirname']) ?? CAOS_LOCAL_DIR;

            CAOS::debug(__('File is a plugin.', $this->plugin_text_domain));
        }

        CAOS::debug(sprintf(__('Saving to %s.', $this->plugin_text_domain), $local_dir));

        /**
         * Some servers don't do a full overwrite if file already exists, so we delete it first.
         */
        if ($file_alias && file_exists($local_dir . $file_alias)) {
            $deleted = unlink($local_dir . $file_alias);

            if ($deleted) {
                CAOS::debug(sprintf(__('File %s successfully deleted.', $this->plugin_text_domain), $file_alias));
            } else {
                if ($error = error_get_last()) {
                    CAOS::debug(sprintf(__('File %s could not be deleted. Something went wrong: %s', $this->plugin_text_domain), $file_alias, $error['message']));
                } else {
                    CAOS::debug(sprintf(__('File %s could not be deleted. An unknown error occurred.', $this->plugin_text_domain), $file_alias));
                }
            }
        }

        $write = file_put_contents($local_dir . $file_alias, $this->file_contents['body']);

        if ($write) {
            CAOS::debug(sprintf(__('File %s successfully saved.', $this->plugin_text_domain), $file_alias));
        } else {
            if ($error = error_get_last()) {
                CAOS::debug(sprintf(__('File %s could not be saved. Something went wrong: %s', $this->plugin_text_domain), $file_alias, $error['message']));
            } else {
                CAOS::debug(sprintf(__('File %s could not be saved. An unknown error occurred.', $this->plugin_text_domain), $file_alias));
            }
        }

        /**
         * Update the file alias in temporary storage, for later use. The child download() method writes the values
         * to the database.
         * 
         * @see CAOS_Cron_Script::download()
         */
        CAOS::set_file_alias($file, $file_alias);

        do_action('caos_admin_update_after');

        return $local_dir . $file_alias;
    }

    /**
     * Returns false if path already exists.
     * 
     * @param mixed $path 
     * @return bool 
     */
    public function create_dir_recursive($path)
    {
        if (!file_exists($path)) {
            return wp_mkdir_p($path);
        }

        return false;
    }

    /**
     * Find $find in $file and replace with $replace.
     *
     * @param $file string Absolute Path|URL
     * @param $find array|string
     * @param $replace array|string
     */
    public function find_replace_in($file, $find, $replace)
    {
        CAOS::debug(sprintf(__('Replacing %s with %s in %s.', $this->plugin_text_domain), print_r($find, true), print_r($replace, true), $file));

        return file_put_contents($file, str_replace($find, $replace, file_get_contents($file)));
    }
}
