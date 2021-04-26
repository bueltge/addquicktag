<?php
/**
 * AddQuicktag - Settings to remove core quicktags
 *
 * @package    AddQuicktag
 * @subpackage AddQuicktag Settings
 * @author     Frank Bueltge <frank@bueltge.de>
 * @version    2015-12-23
 */

if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a part of plugin, not much I can do when called directly.";
	exit;
}

/**
 * Class Add_Quicktag_Remove_Quicktags
 */
class Add_Quicktag_Remove_Quicktags extends Add_Quicktag_Settings {

	/**
	 * Default buttons from WP Core.
	 *
	 * @var string
	 */
	private static $core_quicktags = 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,fullscreen';

	/**
	 * Handler for the action 'init'. Instantiates this class.
	 *
	 * @access  public
	 * @since   2.0.0
	 * @return \Add_Quicktag|\Add_Quicktag_Remove_Quicktags|\Add_Quicktag_Settings $instance
	 */
	public static function get_object() {
		static $instance;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Constructor, init on defined hooks of WP and include second class
	 *
	 * @access  public
	 * @since   0.0.2
	 * @uses    register_activation_hook, register_uninstall_hook, add_action
	 */
	private function __construct() {
		add_action( 'addquicktag_settings_form_page', array( $this, 'get_remove_quicktag_area' ) );
	}

	/**
	 * Add settings area.
	 *
	 * @param array $options Store options content.
	 */
	public function get_remove_quicktag_area( $options ) {
		if ( ! array_key_exists( 'core_buttons', $options ) ) {
			$options['core_buttons'] = array();
		}
		?>
		<h3><?php esc_html_e( 'Remove Core Quicktag buttons', 'addquicktag' ); ?></h3>
		<p><?php esc_html_e( 'Select the checkbox below to remove a core quicktags in the editors of the respective post type.', 'addquicktag' ); ?></p>

		<?php
		// Loop about the post types, create html an values for title in table.
		$pt_title    = '';
		$pt_colgroup = '';
		foreach ( $this->get_post_types_for_js() as $post_type ) {
			$pt_title    .= '<th class="row-title rotate" title="Post Type"><span><code>' . $post_type . '</code></span></th>' . "\n";
			$pt_colgroup .= '<colgroup></colgroup>' . "\n";
		}
		?>

		<table class="widefat form-table rmnlCoreQuicktagSettings">
			<colgroup></colgroup>
			<?php echo $pt_colgroup; ?>
			<colgroup></colgroup>

			<thead>
			<tr>
				<th class="row-title"><?php esc_html_e( 'Button', 'addquicktag' ); ?></th>
				<?php echo $pt_title; ?>
				<th class="row-title num" style="width:3%;">&#x2714;</th>
			</tr>
			</thead>

			<tbody>
			<?php
			// Convert string to array.
			$core_buttons = explode( ',', self::$core_quicktags );
			// Loop over items to remove and unset them from the buttons.
			$i = 999;
			foreach ( $core_buttons as $key => $value ) {

				// Same style as in editor.
				if ( 'strong' === $value ) {
					$text  = 'b';
					$style = ' style="font-weight: bold;"';
				} elseif ( 'em' === $value ) {
					$text  = 'i';
					$style = ' style="font-style: italic;"';
				} elseif ( 'link' === $value ) {
					$text  = $value;
					$style = ' style="text-decoration: underline;"';
				} elseif ( 'del' === $value ) {
					$text  = $value;
					$style = ' style="text-decoration: line-through;"';
				} elseif ( 'block' === $value ) {
					$text  = 'b-quote';
					$style = '';
				} else {
					$text  = $value;
					$style = '';
				}

				echo '<tr id="rmqtb' . $i . '">' . "\n";
				echo '<td><input type="button" class="ed_button" title="" value="'
					. $text . '"' . $style . '> <code>' . $value . '</code></td>';

				// Loop about the post types, create html an values.
				$pt_checkboxes = '';
				foreach ( $this->get_post_types_for_js() as $post_type ) {
					$pt_checked = '';
					if ( isset( $options['core_buttons'][ $value ][ $post_type ] )
						&& 1 === (int) $options['core_buttons'][ $value ][ $post_type ] ) {
						$pt_checked = ' checked="checked"';
					}

					$pt_checkboxes .= '<td class="num"><input type="checkbox" name="' .
									parent::get_option_string() . '[core_buttons][' .
									$value . '][' . $post_type . ']" value="1"' .
									$pt_checked . '/></td>' . "\n";
				}
				echo $pt_checkboxes;

				echo '<td class="num"><input type="checkbox" class="toggle" id="select_all_'
					. $i . '" value="' . $i . '" /></td>' . "\n";

				echo '</tr>' . "\n";
				$i ++;
			}

			// Convert new buttons array back into a comma-separated string.
			$core_qt = implode( ',', $core_buttons );
			?>
			</tbody>
		</table>
		<?php
	}

} // end class

$add_quicktag_remove_quicktags = Add_Quicktag_Remove_Quicktags::get_object();
