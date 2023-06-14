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
 * @copyright: © 2021 - 2023 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

class CAOS_Admin_Settings_Builder {

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
	public function __construct() {
		add_filter( 'caos_basic_settings_content', [ $this, 'do_promo' ] );
		add_filter( 'caos_advanced_settings_content', [ $this, 'do_promo' ] );
		add_filter( 'caos_extensions_settings_content', [ $this, 'do_promo' ] );
	}

	/**
	 *
	 */
	public function do_promo() {
		if ( defined( 'CAOS_PRO_ACTIVE' ) === false ) {
			$this->promo = sprintf( __( '<a href="%s" target="_blank">Get CAOS Pro</a> to unlock this option.' ), CAOS_Admin_Settings::DAAN_DEV_WORDPRESS_CAOS_PRO . $this->utm_tags );
		}
	}

	/**
	 * Return an array containing the reason why an option is disabled. Always returns an empty array
	 * if $is_pro_option is true.
	 *
	 * @var bool $is_pro_option
	 *
	 * @return bool
	 */
	public function display_reason( $is_pro_option = false ) {
		if ( $is_pro_option && ! defined( 'CAOS_PRO_ACTIVE' ) ) {
			return false;
		}

		return true;
	}

	/**
	 *
	 */
	public function do_before() {
		?>
		<table class="form-table">
		<?php
	}

	/**
	 *
	 */
	public function do_after() {
		?>
		</table>
		<?php
	}

	/**
	 *
	 */
	public function do_title() {
		?>
		<h2><?php echo $this->title; ?></h2>
		<?php
	}

	/**
	 * @param $class
	 */
	public function do_tbody_open( $class, $visible = true ) {
		?>
		<tbody class="<?php echo $class; ?>" <?php echo $visible ? '' : 'style="display: none;"'; ?>>
		<?php
	}


	/**
	 *
	 */
	public function do_tbody_close() {
		?>
		</tbody>
		<?php
	}

	/**
	 * Generate radio setting
	 *
	 * @param string $label
	 * @param array  $inputs
	 * @param string $name
	 * @param bool   $checked
	 * @param string $description
	 * @param bool   $disabled
	 * @param bool   $is_pro_option
	 * @param string $explanation
	 */
	public function do_radio( $label, $inputs, $name, $checked, $description, $disabled = false, $is_pro_option = false, $explanation = '' ) {
		$i = 0;
		?>
		<tr>
			<th scope="row"><?php echo $label; ?></th>
			<td id="<?php echo $name . '_right_column'; ?>">
				<fieldset>
					<?php foreach ( $inputs as $option => $option_label ) : ?>
						<label>
							<input type="radio" <?php echo is_array( $disabled ) && $disabled[ $i ] !== false || ( ! is_array( $disabled ) && $disabled ) ? 'disabled' : ''; ?> class="<?php echo str_replace( '_', '-', $name . '_' . $option ); ?>" name="caos_settings[<?php echo $name; ?>]" value="<?php echo $option; ?>" <?php echo $option == $checked ? 'checked="checked"' : ''; ?> />
							<?php echo $option_label; ?>
						</label>
						<br />
						<?php $i++; ?>
					<?php endforeach; ?>
					<?php if ( ! is_array( $disabled ) && $disabled && $this->display_reason() ) : ?>
						<p class="option-disabled">
							<?php echo sprintf( __( 'This option is disabled. %s', 'host-webfonts-local' ), $explanation ); ?>
						</p>
					<?php else : ?>
						<p class="description">
							<?php echo apply_filters( $name . '_setting_description', $description, $label, $name ); ?>
						</p>
					<?php endif; ?>
				</fieldset>
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
	public function do_select( $label, $select, $options, $selected, $description, $disabled = false, $explanation = '' ) {
		?>
		<tr>
			<th scope="row">
				<?php echo apply_filters( $select . '_setting_label', $label ); ?>
			</th>
			<td>
				<fieldset>
					<select <?php echo $disabled ? 'disabled' : ''; ?> name="caos_settings[<?php echo $select; ?>]" class="<?php echo str_replace( '_', '-', $select ); ?>">
						<?php
						$options = apply_filters( $select . '_setting_options', $options );
						?>
						<?php foreach ( $options as $option => $option_label ) : ?>
							<option value="<?php echo $option; ?>" <?php echo ( $selected == $option ) ? 'selected' : ''; ?>><?php echo $option_label; ?></option>
						<?php endforeach; ?>
					</select>
					<?php if ( $disabled && $this->display_reason() ) : ?>
						<p class="option-disabled">
							<?php echo sprintf( __( 'This option is disabled. %s', 'host-webfonts-local' ), $explanation ); ?>
						</p>
					<?php else : ?>
						<p class="description">
							<?php echo apply_filters( $select . '_setting_description', $description, $label, $select ); ?>
						</p>
					<?php endif; ?>
				</fieldset>
			</td>
		</tr>
		<?php
	}

	/**
	 * Generate number setting.
	 *
	 * @param string $label
	 * @param string $name
	 * @param int    $value
	 * @param string $description
	 * @param int    $min
	 * @param bool   $disabled
	 * @param string $explanation
	 */
	public function do_number( $label, $name, $value, $description, $min = 0, $disabled = false, $explanation = '' ) {
		?>
		<tr valign="top">
			<th scope="row"><?php echo apply_filters( $name . '_setting_label', $label ); ?></th>
			<td>
				<fieldset>
					<input <?php echo $disabled ? 'disabled' : ''; ?> class="<?php echo str_replace( '_', '-', $name ); ?>" type="number" name="caos_settings[<?php echo $name; ?>]" min="<?php echo $min; ?>" value="<?php echo $value; ?>" />
					<?php if ( $disabled && $this->display_reason() ) : ?>
						<p class="option-disabled">
							<?php echo sprintf( __( 'This option is disabled. %s', 'host-webfonts-local' ), $explanation ); ?>
						</p>
					<?php else : ?>
						<p class="description">
							<?php echo apply_filters( $name . '_setting_description', $description, $label, $name ); ?>
						</p>
					<?php endif; ?>
				</fieldset>
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
	 * @param bool   $visible
	 * @param bool   $disabled
	 * @param string $explanation Offer an explanation
	 */
	public function do_text( $label, $name, $placeholder, $value, $description = '', $visible = true, $disabled = false, $explanation = '' ) {
		?>
		<tr class="<?php echo str_replace( '_', '-', $name ); ?>-row" <?php echo $visible ? '' : 'style="display: none;"'; ?>>
			<th scope="row"><?php echo apply_filters( $name . '_setting_label', $label ); ?></th>
			<td>
				<input <?php echo $disabled ? 'disabled' : ''; ?> class="<?php echo str_replace( '_', '-', $name ); ?>" type="text" name="caos_settings[<?php echo $name; ?>]" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" />
				<?php if ( $disabled && $this->display_reason() ) : ?>
					<p class="option-disabled">
						<?php echo sprintf( __( 'This option is disabled. %s', 'host-webfonts-local' ), $explanation ); ?>
					</p>
				<?php else : ?>
					<p class="description">
						<?php echo apply_filters( $name . 'setting_description', $description, $label, $name ); ?>
					</p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Generate checkbox setting.
	 *
	 * @param string $label
	 * @param string $name
	 * @param bool $checked
	 * @param string $description
	 * @param bool $disabled
	 * @param bool $visible
	 * @param bool $is_pro_option
	 * @param string $explanation Offer an explanation as to why an option is disabled (recommended -- only displayed when NOT a Pro option,
	 *                            because the promo message is more important to display)
	 */
	public function do_checkbox( $label, $name, $checked, $description, $disabled = false, $visible = true, $is_pro_option = false, $explanation = '' ) {
		?>
		<tr class='<?php echo str_replace( '_', '-', $name ); ?>-row' <?php echo $visible ? '' : 'style="display: none;"'; ?>>
			<th scope="row"><?php echo apply_filters( $name . '_setting_label', $label ); ?></th>
			<td>
				<fieldset>
					<label for="<?php echo $name; ?>">
						<input <?php echo $disabled ? 'disabled' : ''; ?> type="checkbox" class="<?php echo str_replace( '_', '-', $name ); ?>" name="caos_settings[<?php echo $name; ?>]" <?php echo $checked == 'on' ? 'checked = "checked"' : ''; ?> />
						<?php if ( $disabled && $this->display_reason( $is_pro_option ) ) : ?>
							<p class="description option-disabled">
								<?php echo sprintf( __( 'This option is disabled. %s', 'host-webfonts-local' ), $explanation ); ?>
							</p>
						<?php else : ?>
							<?php echo apply_filters( $name . '_setting_description', $description, $label, $name ); ?>
						<?php endif; ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<?php
	}
}
