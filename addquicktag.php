<?php
/**
 * Plugin Name: AddQuicktag
 * Plugin URI:  http://bueltge.de/wp-addquicktags-de-plugin/120/
 * Text Domain: addquicktag
 * Domain Path: /languages
 * Description: Allows you to easily add custom Quicktags to the html- and visual-editor.
 * Version:     2.2.2
 * Author:      Frank Bültge
 * Author URI:  http://bueltge.de
 * License:     GPLv3
 * 
 * 
 * 
License:
==============================================================================
Copyright 2011 - 2013 Frank Bültge  (email : frank@bueltge.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Requirements:
==============================================================================
This plugin requires WordPress >= 3.3 and tested with PHP Interpreter >= 5.3
 * 
 * 
 *
 * Add Quicktag Plugin class
 * 
 * @since   2.0.0
 */
class Add_Quicktag {
	
	protected static $classobj;
	
	static private $option_string      = 'rmnlQuicktagSettings';
	// use filter 'addquicktag_pages' for add custom pages
	static private $admin_pages_for_js = array(
		'post.php', 'post-new.php', 'comment.php', 'edit-comments.php'
	);
	// use filter 'addquicktag_post_types' for add custom post_types
	static private $post_types_for_js  = array(
		'post', 'page', 'comment', 'edit-comments'
	);
	
	static private $plugin;
	
	/**
	 * Constructor, init the functions inside WP
	 *
	 * @since   2.0.0
	 * @return  void
	 */
	function __construct() {
		
		if ( ! is_admin() )
			return;
		
		// get string of plugin
		self :: $plugin = plugin_basename( __FILE__ );
		
		// on uninstall remove capability from roles
		register_uninstall_hook( __FILE__, array('Add_Quicktag', 'uninstall' ) );
		// on deactivate delete all settings in database
		// register_deactivation_hook( __FILE__, array('Add_Quicktag', 'uninstall' ) );
		
		// load translation files
		add_action( 'admin_init', array( $this, 'localize_plugin' ) );
		// on init register post type for addquicktag and print js
		add_action( 'init', array( $this, 'on_admin_init' ) );
		
	}
	
	
	/**
	 * Include other files and print JS
	 * 
	 * @since   07/16/2012
	 * @return  void
	 */
	public function on_admin_init() {
		
		if ( ! is_admin() )
			return NULL;
		
		// Include settings
		require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'inc/class-settings.php';
		// Include solution for TinyMCE
		require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'inc/class-tinymce.php';
		
		foreach ( $this->get_admin_pages_for_js() as $page ) {
			add_action( 'admin_print_scripts-' . $page, array( $this, 'get_json' ) );
			add_action( 'admin_print_scripts-' . $page, array( $this, 'admin_enqueue_scripts') );
		}
	}
	
	/**
	 * Uninstall data in options table, if the plugin was uninstall via backend
	 *
	 * @since   2.0.0
	 * @return  void
	 */
	public function uninstall() {
		
		delete_option( self :: $option_string );
		delete_site_option( self :: $option_string );
	}
	
	/**
	 * Print json data in head
	 *
	 * @since   2.0.0
	 * @return  void
	 */
	public function get_json() {
		global $current_screen;
		
		if ( isset( $current_screen->id ) && 
			 ! in_array( 
				$current_screen->id,
				$this->get_post_types_for_js()
			 )
			)
			return NULL;
			
		if ( is_multisite() && is_plugin_active_for_network( $this -> get_plugin_string() ) )
			$options = get_site_option( self :: $option_string );
		else
			$options = get_option( self :: $option_string );
		
		// allow change or enhance buttons array
		$options['buttons'] = apply_filters( 'addquicktag_buttons', $options['buttons'] );
		// hook for filter options
		$options = apply_filters( 'addquicktag_options', $options );
		
		if ( ! $options )
			return NULL;
		
		if ( 1 < count($options['buttons']) ) {
			// sort array by order value
			$tmp = array();
			foreach( $options['buttons'] as $order ) {
				if ( isset( $order['order'] ) )
					$tmp[] = $order['order'];
				else
					$tmp[] = 0;
			}
			array_multisort( $tmp, SORT_ASC, $options['buttons'] );
		}
		
		?>
		<script type="text/javascript">
			var addquicktag_tags = <?php echo json_encode( $options ); ?>,
				addquicktag_post_type = <?php echo json_encode( $current_screen->id ); ?>,
				addquicktag_pt_for_js = <?php echo json_encode( $this->get_post_types_for_js() ); ?>;
		</script>
		<?php
	}
	
	/**
	 * Enqueue Scripts for plugin
	 * 
	 * @param   $where  string
	 * @since   2.0.0
	 * @access  public
	 * @return  void
	 */
	public function admin_enqueue_scripts( $where ) {
		global $current_screen;
		
		if ( isset( $current_screen->id ) && 
			 ! in_array( 
				$current_screen->id,
				$this->get_post_types_for_js()
			 )
			)
			return NULL;
		
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
		
		if ( version_compare( $GLOBALS['wp_version'], '3.3alpha', '>=' ) ) {
			wp_enqueue_script(
				self :: get_textdomain() . '_script', 
				plugins_url( '/js/add-quicktags' . $suffix. '.js', __FILE__ ), 	
				array( 'jquery', 'quicktags' ),
				'',
				TRUE
			);
		} else {
			wp_enqueue_script(
				self :: get_textdomain() . '_script', 
				plugins_url( '/js/add-quicktags_32' . $suffix. '.js', __FILE__ ), 	
				array( 'jquery', 'quicktags' ),
				'',
				TRUE
			);
		}
		// Alternative to JSON function
		// wp_localize_script( self :: get_textdomain() . '_script', 'addquicktag_tags', get_option( self :: $option_string ) );
	}
	
	/**
	 * Handler for the action 'init'. Instantiates this class.
	 *
	 * @since   2.0.0
	 * @access  public
	 * @return  $classobj
	 */
	public static function get_object() {
		
		if ( NULL === self :: $classobj ) {
			self :: $classobj = new self;
		}
	
		return self :: $classobj;
	}
	
	/**
	 * Localize_plugin function.
	 *
	 * @uses	load_plugin_textdomain, plugin_basename
	 * @access  public
	 * @since   2.0.0
	 * @return  void
	 */
	public function localize_plugin() {
		
		load_plugin_textdomain( $this -> get_textdomain(), FALSE, dirname( plugin_basename(__FILE__) ) . '/languages' );
	}
	
	/**
	 * return plugin comment data
	 * 
	 * @since  2.0.0
	 * @access public
	 * @param  $value string, default = 'TextDomain'
	 *         Name, PluginURI, Version, Description, Author, AuthorURI, TextDomain, DomainPath, Network, Title
	 * @return string
	 */
	public function get_plugin_data( $value = 'TextDomain' ) {
		
		static $plugin_data = array ();
		
		// fetch the data just once.
		if ( isset( $plugin_data[ $value ] ) )
			return $plugin_data[ $value ];
		
		if ( ! function_exists( 'get_plugin_data' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		
		$plugin_data  = get_plugin_data( __FILE__ );
		$plugin_value = $plugin_data[$value];
		
		return empty ( $plugin_data[ $value ] ) ? '' : $plugin_data[ $value ];
	}
	
	/**
	 * Return string of plugin
	 * 
	 * @since   2.0.0
	 * @return  string
	 */
	public function get_plugin_string() {
		
		return self::$plugin;
	}
	
	/**
	 * Retrun allowed post types for include scripts
	 * 
	 * @since   2.1.1
	 * @access  public
	 * @return  Array
	 */
	public function get_post_types_for_js() {
		
		return apply_filters( 'addquicktag_post_types', self::$post_types_for_js );
	}
	
	/**
	 * Retrun allowed post types for include scripts
	 * 
	 * @since   2.1.1
	 * @access  public
	 * @return  Array
	 */
	public function get_admin_pages_for_js() {
		
		return apply_filters( 'addquicktag_pages', self::$admin_pages_for_js );
	}
	
	/**
	 * Retrun textdomain string
	 * 
	 * @since   2.0.0
	 * @access  public
	 * @return  string
	 */
	public function get_textdomain() {
		
		return self::get_plugin_data( 'TextDomain' );
	}
	
	/**
	 * Return string for options
	 *
	 * @since   2.0.0
	 * @retrun  string
	 */
	public function get_option_string() {
		
		return self :: $option_string;
	}
	
	
} // end class

if ( function_exists( 'add_action' ) && class_exists( 'Add_Quicktag' ) ) {
	add_action( 'plugins_loaded', array( 'Add_Quicktag', 'get_object' ) );
} else {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
