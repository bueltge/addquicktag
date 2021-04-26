<?php
/**
 * AddQuicktag - to TinyMCE Editor
 *
 * @package    AddQuicktag
 * @subpackage AddQuicktag 2 TinyMce
 * @author     Frank Bueltge <frank@bueltge.de>
 * @version    2015-12-23
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
	 *
	 * @var string
	 */
	private static $option_string = 'rmnlQuicktagSettings_tmce';

	/**
	 * Handler for the action 'init'. Instantiates this class.
	 *
	 * @access  public
	 * @since   2.0.0
	 * @return \Add_Quicktag|\Add_Quicktag_2_TinyMce $instance
	 */
	public static function get_object() {
		static $instance;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Constructor, init on defined hooks of WP and include second class.
	 *
	 * @access  public
	 * @since   0.0.2
	 * @uses    add_action
	 */
	private function __construct() {
		add_filter( 'mce_external_plugins', array( $this, 'add_externel_buttons' ) );
		add_filter( 'mce_buttons_2', array( $this, 'extend_editor_buttons' ), 10, 2 );
	}

	/**
	 * Add the script url to plugins of TinyMCE.
	 *
	 * @param array $plugins List of plugins.
	 *
	 * @return array
	 */
	public function add_externel_buttons( $plugins ) {
		if ( ! is_array( $plugins ) ) {
			$plugins = array();
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.dev' : '';

		$url = plugins_url( '/js/editor_plugin' . $suffix . '.js', __DIR__ );
		return array_merge( $plugins, array( self::$option_string => $url ) );
	}

	/**
	 * Add key for address the button via script.
	 *
	 * @param array $buttons TinyMCE buttons.
	 * @param bool  $editor_id ID of the editor.
	 *
	 * @return array
	 */
	public function extend_editor_buttons( $buttons, $editor_id = false ) {
		return array_merge( array( self::$option_string ), $buttons );
	}

} // end class
$add_quicktag_2_tinymce = Add_Quicktag_2_TinyMce::get_object();
