<?php
/**
 * AddQuicktag - Settings to remove core quicktags
 * @license    GPLv2
 * @package    AddQuicktag
 * @subpackage AddQuicktag Settings
 * @author     Frank Bueltge <frank@bueltge.de>
 * @version    06/19/2014
 */

if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a part of plugin, not much I can do when called directly.";
	exit;
}

/**
 * Class Add_Quicktag_Remove_Quicktags
 */
class Add_Quicktag_Remove_Quicktags extends Add_Quicktag_Settings {

	// default buttons from WP Core
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

		if ( NULL === $instance ) {
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
	 * @return \Add_Quicktag_Remove_Quicktags
	 */
	private function __construct() {

		add_action( 'addquicktag_settings_form_page', array( $this, 'get_remove_quicktag_area' ) );
	}

	/**
	 * Add settings area
	 *
	 * @param $options
	 */
	public function get_remove_quicktag_area( $options ) {

		if ( ! isset( $options[ 'core_buttons' ] ) ) {
			$options[ 'core_buttons' ] = array();
		}
		?>
		<h3><?php esc_html_e( 'Remove Core Quicktag buttons', parent::get_textdomain() ); ?></h3>
		<p><?php esc_html_e( 'Select the checkbox below to remove a core quicktags in the editors of the respective post type.', $this->get_textdomain() ); ?></p>

		<?php
		// loop about the post types, create html an values for title in table
		$pt_title    = '';
		$pt_colgroup = '';
		foreach ( $this->get_post_types_for_js() as $post_type ) {
			$pt_title .= '<th class="row-title rotate" title="Post Type"><span><code>' . $post_type . '</code></span></th>' . "\n";
			$pt_colgroup .= '<colgroup></colgroup>' . "\n";
		}
		?>

		<table class="widefat form-table rmnlCoreQuicktagSettings">
			<colgroup></colgroup>
			<?php echo $pt_colgroup; ?>
			<colgroup></colgroup>

			<tr>
				<th class="row-title"><?php esc_html_e( 'Button', parent::get_textdomain() ); ?></th>
				<?php echo $pt_title; ?>
				<th class="row-title num" style="width:3%;">&#x2714;</th>
			</tr>

			<?php
			// Convert string to array
			$core_buttons = explode( ',', self::$core_quicktags );
			// Loop over items to remove and unset them from the buttons
			$i = 999;
			foreach ( $core_buttons as $key => $value ) {

				// same style as in editor
				if ( 'strong' === $value ) {
					$text  = 'b';
					$style = ' style="font-weight: bold;"';
				} else if ( 'em' === $value ) {
					$text  = 'i';
					$style = ' style="font-style: italic;"';
				} else if ( 'link' === $value ) {
					$text  = $value;
					$style = ' style="text-decoration: underline;"';
				} else if ( 'del' === $value ) {
					$text  = $value;
					$style = ' style="text-decoration: line-through;"';
				} else if ( 'block' === $value ) {
					$text  = 'b-quote';
					$style = '';
				} else {
					$text  = $value;
					$style = '';
				}

				echo '<tr id="rmqtb' . $i . '">' . "\n";
				echo '<td><input type="button" class="ed_button" title="" value="' . $text . '"' . $style . '> <code>' . $value . '</code></td>';

				// loop about the post types, create html an values
				$pt_checkboxes = '';
				foreach ( $this->get_post_types_for_js() as $post_type ) {

					$pt_checked = '';
					if ( isset( $options[ 'core_buttons' ][ $value ][ $post_type ] ) && 1 == $options[ 'core_buttons' ][ $value ][ $post_type ] ) {
						$pt_checked = ' checked="checked"';
					}

					$pt_checkboxes .= '<td class="num"><input type="checkbox" name="' .
					                  parent :: get_option_string() . '[core_buttons][' .
					                  $value . '][' . $post_type . ']" value="1"' .
					                  $pt_checked . '/></td>' . "\n";
				}
				echo $pt_checkboxes;

				echo '<td class="num"><input type="checkbox" class="toggle" id="select_all_' . $i . '" value="' . $i . '" /></td>' . "\n";

				echo '</tr>' . "\n";
				$i ++;
			}

			// Convert new buttons array back into a comma-separated string
			$core_qt = implode( ',', $core_buttons );
			?>
		</table>
	<?php
	}

} // end class

$add_quicktag_remove_quicktags = Add_Quicktag_Remove_Quicktags::get_object();