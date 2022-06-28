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

class CAOS_Admin_Settings_Builder
{
    /** @var string $plugin_text_domain */
    protected $plugin_text_domain = 'host-analyticsjs-local';

    /** @var string $utm_tags */
    protected $utm_tags = '?utm_source=caos&utm_medium=plugin&utm_campaign=settings';

    /** @var $title */
    protected $title;

    /** @var $promo string */
    protected $promo;

    /**
     * Only sets the promo string on settings load.
     *
     * CAOS_Admin_Settings_Builder constructor.
     */
    public function __construct()
    {
        add_filter('caos_basic_settings_content', [$this, 'do_promo']);
        add_filter('caos_advanced_settings_content', [$this, 'do_promo']);
        add_filter('caos_extensions_settings_content', [$this, 'do_promo']);
    }

    /**
     *
     */
    public function do_promo()
    {
        if (apply_filters('caos_pro_active', false) == false) {
            $this->promo = sprintf(__('<a href="%s" target="_blank">Get CAOS Pro</a> to enable this option.'), CAOS_Admin_Settings::FFW_PRESS_WORDPRESS_PLUGINS_CAOS_PRO . $this->utm_tags);
        }
    }

    /**
     *
     */
    public function do_before()
    {
?>
        <table class="form-table">
        <?php
    }

    /**
     *
     */
    public function do_after()
    {
        ?>
        </table>
    <?php
    }

    /**
     *
     */
    public function do_title()
    {
    ?>
        <h2><?= $this->title ?></h2>
    <?php
    }

    /**
     * @param $class
     */
    public function do_tbody_open($class, $visible = true)
    {
    ?>
        <tbody class="<?= $class; ?>" <?= $visible ? '' : 'style="display: none;"'; ?>>
        <?php
    }


    /**
     *
     */
    public function do_tbody_close()
    {
        ?>
        </tbody>
        <?php
    }

    /**
     * Show Compatibility Mode notice.
     */
    public function do_invisible_option_notice()
    {
        if (CAOS_OPT_COMPATIBILITY_MODE || CAOS_OPT_SERVICE_PROVIDER == 'plausible') : ?>
            <tr>
                <th></th>
                <td>
                    <p class="description">
                        <?= __('Some settings are not displayed, because Compatibility Mode or Plausible Analytics is enabled.', $this->plugin_text_domain); ?>
                    </p>
                </td>
            </tr>
        <?php endif;
    }

    /**
     * Generate radio setting
     *
     * @param $label
     * @param $inputs
     * @param $name
     * @param $checked
     * @param $description
     */
    public function do_radio($label, $inputs, $name, $checked, $description, $disabled = false)
    {
        $i = 0;
        ?>
        <tr>
            <th scope="row"><?= $label; ?></th>
            <td id="<?= $name . '_right_column'; ?>">
                <?php foreach ($inputs as $option => $option_label) : ?>
                    <label>
                        <input type="radio" <?= is_array($disabled) && $disabled[$i] !== false ? apply_filters($name . '_' . $option . '_setting_disabled', 'disabled') : ''; ?> class="<?= str_replace('_', '-', $name . '_' . $option); ?>" name="<?= $name; ?>" value="<?= $option; ?>" <?= $option == $checked ? 'checked="checked"' : ''; ?> />
                        <?= $option_label; ?>
                    </label>
                    <br />
                    <?php $i++; ?>
                <?php endforeach; ?>
                <p class="description">
                    <?= apply_filters($name . '_setting_description', $description); ?>
                </p>
            </td>
        </tr>
    <?php
    }

    /**
     * Generate select setting
     *
     * @param      $label
     * @param      $select
     * @param      $options
     * @param      $selected
     * @param      $description
     */
    public function do_select($label, $select, $options, $selected, $description)
    {
    ?>
        <tr>
            <th scope="row">
                <?= apply_filters($select . '_setting_label', $label); ?>
            </th>
            <td>
                <select name="<?= $select; ?>" class="<?= str_replace('_', '-', $select); ?>">
                    <?php
                    $options = apply_filters($select . '_setting_options', $options);
                    ?>
                    <?php foreach ($options as $option => $option_label) : ?>
                        <option value="<?= $option; ?>" <?= ($selected == $option) ? 'selected' : ''; ?>><?= $option_label; ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description">
                    <?= apply_filters($select . '_setting_description', $description); ?>
                </p>
            </td>
        </tr>
    <?php
    }

    /**
     * Generate number setting.
     *
     * @param $label
     * @param $name
     * @param $value
     * @param $description
     */
    public function do_number($label, $name, $value, $description, $min = 0)
    {
    ?>
        <tr valign="top">
            <th scope="row"><?= apply_filters($name . '_setting_label', $label); ?></th>
            <td>
                <input class="<?= str_replace('_', '-', $name); ?>" type="number" name="<?= $name; ?>" min="<?= $min; ?>" value="<?= $value; ?>" />
                <p class="description">
                    <?= apply_filters($name . '_setting_description', $description); ?>
                </p>
            </td>
        </tr>
    <?php
    }

    /**
     * Generate text setting.
     *
     * @param        $label
     * @param        $name
     * @param        $placeholder
     * @param        $value
     * @param string $description
     * @param bool   $update_required
     */
    public function do_text($label, $name, $placeholder, $value, $description = '', $visible = true)
    {
    ?>
        <tr class="<?= str_replace('_', '-', $name); ?>-row" <?= $visible ? '' : 'style="display: none;"'; ?>>
            <th scope="row"><?= apply_filters($name . '_setting_label', $label); ?></th>
            <td>
                <input class="<?= str_replace('_', '-', $name); ?>" type="text" name="<?= $name; ?>" placeholder="<?= $placeholder; ?>" value="<?= $value; ?>" />
                <p class="description">
                    <?= apply_filters($name . 'setting_description', $description); ?>
                </p>
            </td>
        </tr>
    <?php
    }

    /**
     * Generate checkbox setting.
     *
     * @param $label
     * @param $name
     * @param $checked
     * @param $description
     */
    public function do_checkbox($label, $name, $checked, $description, $disabled = false, $visible = true)
    {
    ?>
        <tr class='<?= str_replace('_', '-', $name); ?>-row' <?= $visible ? '' : 'style="display: none;"'; ?>>
            <th scope="row"><?= apply_filters($name . '_setting_label', $label); ?></th>
            <td>
                <label for="<?= $name; ?>">
                    <input <?= apply_filters($name . '_setting_disabled', $disabled) ? 'disabled' : ''; ?> type="checkbox" class="<?= str_replace('_', '-', $name); ?>" name="<?= $name; ?>" <?= $checked == "on" ? 'checked = "checked"' : ''; ?> />
                    <?= apply_filters($name . '_setting_description', $description); ?>
                </label>
            </td>
        </tr>
<?php
    }
}
