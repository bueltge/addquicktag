<?php
/**
 * AddQuicktag - to TinyMCE Editor
 * 
 * @license    GPLv3
 * @package    AddQuicktag
 * @subpackage AddQuicktag 2 TinyMce
 * @author     Frank Bueltge <frank@bueltge.de>
 */

if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a part of plugin, not much I can do when called directly.";
	exit;
}

class Add_Quicktag_2_TinyMce extends Add_Quicktag {
	
	protected static $classobj = NULL;
	
	static private $option_string = 'rmnlQuicktagSettings_tmce';
	
	/**
	 * Handler for the action 'init'. Instantiates this class.
	 * 
	 * @access  public
	 * @since   2.0.0
	 * @return  $classobj
	 */
	public static function get_object() {
		
		if ( NULL === self :: $classobj ) {
			self :: $classobj = new self;
		}
		
		return self :: $classobj;
	}
	
	/**
	 * Constructor, init on defined hooks of WP and include second class
	 * 
	 * @access  public
	 * @since   0.0.2
	 * @uses    add_action
	 * @return  void
	 */
	public function __construct() {
		
		add_filter( 'mce_external_plugins', array( $this, 'add_externel_buttons' ) );
		add_filter( 'mce_buttons_2',        array( $this, 'extend_editor_buttons' ), 10, 2 );
	}
	
	public function add_externel_buttons( $plugins ) {
		
		if ( FALSE == is_array($plugins) )
			$plugins = array();
		
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
		
		$url = plugins_url('/tinymce/editor_plugin' . $suffix . '.js', __FILE__);
		$plugins = array_merge( $plugins, array( self :: $option_string => $url ) );
		
		return $plugins;
	}
	
	public function extend_editor_buttons( $buttons, $editor_id = FALSE ) {
		
		return array_merge( array( self :: $option_string ), $buttons );
	}
	
} // end class
$add_quicktag_2_tinymce = Add_Quicktag_2_TinyMce :: get_object();
