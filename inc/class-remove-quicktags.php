<?php
/**
 * AddQuicktag - Settings to remove core quicktags
 * @license    GPLv2
 * @package    AddQuicktag
 * @subpackage AddQuicktag Settings
 * @author     Frank Bueltge <frank@bueltge.de>
 * @version    05/22/2014
 */

if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a part of plugin, not much I can do when called directly.";
	exit;
}

class Add_Quicktag_Remove_Quicktags extends Add_Quicktag_Settings {

	// post types for the settings
	private static $post_types_for_js;

	// default buttons from WP Core
	private static $core_quicktags = 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,fullscreen';

	// Transient string
	private static $addquicktag_core_quicktags = 'addquicktag_core_quicktags';

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

		if ( ! isset( $options['core_buttons'] ) ) {
			$options['core_buttons'] = array();
		}
		?>
		<h3><?php _e( 'Remove Core Quicktag buttons', parent::get_textdomain() ); ?></h3>
		<p><?php _e( 'Select the checkbox below to remove a core quicktags in all editors.', $this->get_textdomain() ); ?></p>
		<p><?php _e( '<strong>Currently a Beta option</strong>, to validate and only usable global on each post type. Please give me hints, feedback via the support possibilities, like <a href="https://github.com/bueltge/AddQuicktag/issues">Github Issues</a> or <a href="http://wordpress.org/support/plugin/addquicktag">WP Support Forum</a>.', $this->get_textdomain() ); ?></p>

		<table class="widefat">
			<tr>
				<th class="row-title num" style="width:3%;">&#x2714;</th>
				<th class="row-title"><?php _e( 'Button', parent::get_textdomain() ); ?></th>
			</tr>

			<?php
			// Convert string to array
			$core_buttons = explode( ',', self::$core_quicktags );
			// Loop over items to remove and unset them from the buttons
			foreach( $core_buttons as $key => $value ) {

				if ( array_key_exists( $value, $options['core_buttons'] ) ) {
					$checked = ' checked="checked"';
				} else {
					$checked = '';
				}

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

				echo '<tr><td class="num"><input type="checkbox" name="' . parent :: get_option_string()
				     . '[core_buttons][' . $value . ']" value="1" '
				     . $checked . ' /></td><td>';
				echo '<input type="button" class="ed_button" title="" value="' . $text . '"' . $style . '> <code>' . $value . '</code></td></tr>';
			}

			// Convert new buttons array back into a comma-separated string
			$core_qt = implode( ',', $core_buttons );
			?>
		</table>
	<?php
	}

} // end class

$add_quicktag_remove_quicktags = Add_Quicktag_Remove_Quicktags::get_object();