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

class CAOS_Admin_Settings_Screen
{
    /** @var string $utm_tags */
    protected $utm_tags = '?utm_source=caos&utm_medium=plugin&utm_campaign=settings';

    /** @var $title */
    protected $title;

    /**
     *
     */
    public function do_before()
    {
        ?>
        <table class="form-table">
        <?php
    }

    public function do_title()
    {
        ?>
        <h3><?= $this->title ?></h3>
        <?php
    }

    /**
     * @param $class
     */
    public function do_tbody_open($class)
    {
        ?>
        <tbody class="<?= $class; ?>" <?= empty(CAOS_OPT_COMPATIBILITY_MODE) ? '' : 'style="display: none;"'; ?>>
        <?php
    }

    public function do_tbody_close()
    {
        ?>
        </tbody>
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
     * Generate checkbox setting.
     *
     * @param $label
     * @param $name
     * @param $checked
     * @param $description
     */
    public function do_checkbox($label, $name, $checked, $description)
    {
        ?>
        <tr>
            <th scope="row"><?= apply_filters($label . '_setting_label', $label); ?></th>
            <td>
                <input type="checkbox" class="<?= str_replace('_' , '-' , $name); ?>" name="<?= $name; ?>"
                    <?= $checked == "on" ? 'checked = "checked"' : ''; ?> />
                <p class="description">
                    <?= apply_filters($description . '_setting_description', $description); ?>
                </p>
            </td>
        </tr>
        <?php
    }
}
