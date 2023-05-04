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
namespace CAOS\Admin\Settings;

use CAOS\Admin\Settings;

class Builder {
	/** @var string $utm_tags */
	protected $utm_tags = '?utm_source=caos&utm_medium=plugin&utm_campaign=settings';

	/** @var $title */
	protected $title;

	/** @var $promo string */
	protected $promo;

	/** @var array $allowed_html */
	protected $allowed_html;

	/**
	 * Only sets the promo string on settings load.
	 *
	 * Builder constructor.
	 */
	public function __construct() {
		global $allowedposttags;

		$this->allowed_html = $allowedposttags;

		add_action( 'caos_basic_settings_content', [ $this, 'do_promo' ] );
		add_action( 'caos_advanced_settings_content', [ $this, 'do_promo' ] );
		add_action( 'caos_extensions_settings_content', [ $this, 'do_promo' ] );
	}

	/**
	 *
	 */
	public function do_promo() {
		if ( apply_filters( 'caos_pro_active', false ) === false ) {
			$this->promo = sprintf( __( '<a href="%s" target="_blank">Get CAOS Pro</a> to unlock this option.' ), Settings::DAAN_DEV_WORDPRESS_CAOS_PRO . $this->utm_tags );
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
		if ( $is_pro_option && ! defined( 'CAOS_PRO_STEALTH_MODE' ) ) {
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
		<h2><?php echo esc_html( $this->title ); ?></h2>
		<?php
	}

	/**
	 * @param $class
	 */
	public function do_tbody_open( $class, $visible = true ) {
		?>
		<tbody class="<?php echo esc_attr( $class ); ?>" <?php echo $visible ? '' : 'style="display: none;"'; ?>>
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
			<th scope="row"><?php echo esc_html( $label ); ?></th>
			<td id="<?php echo esc_attr( $name . '_right_column' ); ?>">
				<fieldset>
					<?php foreach ( $inputs as $option => $option_label ) : ?>
						<label>
							<input type="radio" <?php echo is_array( $disabled ) && $disabled[ $i ] !== false || ( ! is_array( $disabled ) && $disabled ) ? 'disabled' : ''; ?> class="<?php echo esc_attr( str_replace( '_', '-', $name . '_' . $option ) ); ?>" name="caos_settings[<?php echo esc_attr( $name ); ?>]" value="<?php echo esc_attr( $option ); ?>" <?php echo esc_attr( $option === $checked ? 'checked="checked"' : '' ); ?> />
							<?php echo wp_kses( $option_label, $this->allowed_html ); ?>
						</label>
						<br />
						<?php $i++; ?>
					<?php endforeach; ?>
					<?php if ( ! is_array( $disabled ) && $disabled && $this->display_reason() ) : ?>
						<p class="option-disabled">
							<?php echo wp_kses( sprintf( __( 'This option is disabled. %s', 'host-webfonts-local' ), $explanation ), $this->allowed_html ); ?>
						</p>
					<?php else : ?>
						<p class="description">
							<?php echo wp_kses( apply_filters( $name . '_setting_description', $description, $label, $name ), $this->allowed_html ); ?>
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
				<?php echo esc_html( apply_filters( $select . '_setting_label', $label ) ); ?>
			</th>
			<td>
				<fieldset>
					<select <?php echo $disabled ? 'disabled' : ''; ?> name="caos_settings[<?php echo esc_attr( $select ); ?>]" class="<?php echo esc_attr( str_replace( '_', '-', $select ) ); ?>">
						<?php
						$options = apply_filters( $select . '_setting_options', $options );
						?>
						<?php foreach ( $options as $option => $option_label ) : ?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php echo ( $selected === $option ) ? esc_attr( 'selected' ) : ''; ?>><?php echo esc_html( $option_label ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php if ( $disabled && $this->display_reason() ) : ?>
						<p class="option-disabled">
							<?php echo wp_kses( sprintf( __( 'This option is disabled. %s', 'host-webfonts-local' ), $explanation ), $this->allowed_html ); ?>
						</p>
					<?php else : ?>
						<p class="description">
							<?php echo wp_kses( apply_filters( $select . '_setting_description', $description, $label, $select ), $this->allowed_html ); ?>
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
			<th scope="row"><?php echo esc_html( apply_filters( $name . '_setting_label', $label ) ); ?></th>
			<td>
				<fieldset>
					<input <?php echo $disabled ? 'disabled' : ''; ?> class="<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>" type="number" name="caos_settings[<?php echo esc_attr( $name ); ?>]" min="<?php echo esc_attr( $min ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					<?php if ( $disabled && $this->display_reason() ) : ?>
						<p class="option-disabled">
							<?php echo wp_kses( sprintf( __( 'This option is disabled. %s', 'host-webfonts-local' ), $explanation ), $this->allowed_html ); ?>
						</p>
					<?php else : ?>
						<p class="description">
							<?php echo wp_kses( apply_filters( $name . '_setting_description', $description, $label, $name ), $this->allowed_html ); ?>
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
		<tr class="<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>-row" <?php echo $visible ? '' : 'style="display: none;"'; ?>>
			<th scope="row"><?php echo esc_html( apply_filters( $name . '_setting_label', $label ) ); ?></th>
			<td>
				<input <?php echo $disabled ? 'disabled' : ''; ?> class="<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>" type="text" name="caos_settings[<?php echo esc_attr( $name ); ?>]" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php echo esc_attr( $value ); ?>" />
				<?php if ( $disabled && $this->display_reason() ) : ?>
					<p class="option-disabled">
						<?php echo wp_kses( sprintf( __( 'This option is disabled. %s', 'host-webfonts-local' ), $explanation ), $this->allowed_html ); ?>
					</p>
				<?php else : ?>
					<p class="description">
						<?php echo wp_kses( apply_filters( $name . 'setting_description', $description, $label, $name ), $this->allowed_html ); ?>
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
		<tr class='<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>-row' <?php echo $visible ? '' : 'style="display: none;"'; ?>>
			<th scope="row"><?php echo esc_html( apply_filters( $name . '_setting_label', $label ) ); ?></th>
			<td>
				<fieldset>
					<label for="<?php echo esc_attr( $name ); ?>">
						<input <?php echo $disabled ? 'disabled' : ''; ?> type="checkbox" class="<?php echo esc_attr( str_replace( '_', '-', $name ) ); ?>" name="caos_settings[<?php echo esc_attr( $name ); ?>]" <?php echo esc_attr( $checked === 'on' ? 'checked = "checked"' : '' ); ?> />
						<?php if ( $disabled && $this->display_reason( $is_pro_option ) ) : ?>
							<p class="description option-disabled">
								<?php echo wp_kses( sprintf( __( 'This option is disabled. %s', 'host-webfonts-local' ), $explanation ), $this->allowed_html ); ?>
							</p>
						<?php else : ?>
							<?php echo wp_kses( apply_filters( $name . '_setting_description', $description, $label, $name ), $this->allowed_html ); ?>
						<?php endif; ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<?php
	}
}
