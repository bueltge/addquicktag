<?php
/**
 * AddQuicktag - to TinyMCE Editor
 * @license    GPLv2
 * @package    AddQuicktag
 * @subpackage AddQuicktag 2 TinyMce
 * @author     Frank Bueltge <frank@bueltge.de>
 */

if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a part of plugin, not much I can do when called directly.";
	exit;
}

/**
 * Class Add_Quicktag_2_TinyMce
 * Add teh list box to the tinymce
 */
class Add_Quicktag_2_TinyMce extends Add_Quicktag {

	/**
	 * Key for custom button
	 * @var string
	 */
	static private $option_string = 'rmnlQuicktagSettings_tmce';

	/**
	 * Handler for the action 'init'. Instantiates this class.
	 * @access  public
	 * @since   2.0.0
	 * @return \Add_Quicktag|\Add_Quicktag_2_TinyMce $instance
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
	 * @access  public
	 * @since   0.0.2
	 * @uses    add_action
	 * @return \Add_Quicktag_2_TinyMce
	 */
	private function __construct() {

		add_filter( 'mce_external_plugins', array( $this, 'add_externel_buttons' ) );
		add_filter( 'mce_buttons_2', array( $this, 'extend_editor_buttons' ), 10, 2 );
	}

	/**
	 * Add the script url to plugins of TinyMCE
	 *
	 * @param $plugins
	 *
	 * @return array
	 */
	public function add_externel_buttons( $plugins ) {

		if ( FALSE == is_array( $plugins ) ) {
			$plugins = array();
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.dev' : '';

		$url     = plugins_url( '/tinymce/editor_plugin' . $suffix . '.js', __FILE__ );
		$plugins = array_merge( $plugins, array( self::$option_string => $url ) );

		return $plugins;
	}

	/**
	 * Add key for address the button via script
	 *
	 * @param bool $editor_id
	 *
	 * @return array
	 *
	 * @param      $buttons
	 */
	public function extend_editor_buttons( $buttons, $editor_id = FALSE ) {

		$buttons = array_merge( array( self::$option_string ), $buttons );

		return $buttons;
	}

} // end class
$add_quicktag_2_tinymce = Add_Quicktag_2_TinyMce::get_object();
