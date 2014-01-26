<?php
/**
 * AddQuicktag - Settings for enhanced code buttons
 * 
 * @license    GPLv3
 * @package    AddQuicktag
 * @subpackage AddQuicktag Settings
 * @author     Frank Bueltge <frank@bueltge.de>
 * @since      01/26/2014
 */

if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a part of plugin, not much I can do when called directly.";
	exit;
}

class Add_Quicktag_Code_Quicktags extends Add_Quicktag_Settings {
	
	// post types for the settings
	private static $post_types_for_js;
	// static var for textdomain
	public static $textdomain = '';
	
	/**
	 * Handler for the action 'init'. Instantiates this class.
	 * 
	 * @access  public
	 * @since   2.0.0
	 * @return  $instance
	 */
	public static function get_object() {
		
		static $instance;
		
		if ( NULL === $instance )
			$instance = new self();
		
		return $instance;
	}
	
	/**
	 * Constructor, init on defined hooks of WP and include second class
	 * 
	 * @access  public
	 * @since   0.0.2
	 * @uses    register_activation_hook, register_uninstall_hook, add_action
	 * @return  void
	 */
	private function __construct() {
		
		self::$textdomain = parent::get_textdomain();
		
		add_action( 'addquicktag_settings_form_page', array( $this, 'get_code_quicktag_area' ) );
	}
	
	public function get_code_quicktag_area( $options ) {
		
		if ( ! isset( $options['code_buttons'] ) )
			$options['code_buttons'] = array();
		
		$checked_enhanced_code = $checked_ende_coding = '';
		if ( array_key_exists( 'enhanced_code', $options['code_buttons'] ) )
			$checked_enhanced_code = ' checked="checked"';
		if ( array_key_exists( 'ende_coding', $options['code_buttons'] ) )
			$checked_ende_coding = ' checked="checked"';
		?>
		<h3><?php _e('Enhanced Code Quicktag buttons', self::$textdomain ); ?></h3>
		<table class="widefat">
			<tr>
				<th class="row-title num" style="width:3%;">&#x2714;</th>
				<th class="row-title"><?php _e( 'Button', self::$textdomain ); ?></th>
			</tr>
			
			<tr>
				<td class="num">
					<input type="checkbox" name="' . parent::get_option_string()
						. '[code_buttons]['enhanced_code']" value="1" ' 
						. $checked_enhanced_code . ' />
				</td>
				<td>
					<?php _e( 'Enhanced Code buttons.<br />Add a pull down menu for different languages before the default code button and include this as class inside the code tag. Also add a pre button for preformatted text.', self::$textdomain ); ?>
				</td>
			<td>
			<tr>
				<td class="num">
					<?php echo '<input type="checkbox" name="' . parent::get_option_string()
						. '[code_buttons][ende_coding]" value="1" ' 
						. $checked_ende_coding . ' />';
					?>
				</td>
				<td><?php _e( 'Add buttons to do the inconvient HTML encoding/decoding.', self::$textdomain ); ?></td>
			</tr>
		</table>
		<?php
	}
	
} // end class

$add_quicktag_code_quicktags = Add_Quicktag_Code_Quicktags::get_object();
