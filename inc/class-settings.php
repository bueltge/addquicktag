<?php
/**
 * AddQuicktag - Settings
 * 
 * @license    GPLv3
 * @package    AddQuicktag
 * @subpackage AddQuicktag Settings
 * @author     Frank Bueltge <frank@bueltge.de>
 * @version    02/09/2013
 * @since      2.0.0
 */

if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a part of plugin, not much I can do when called directly.";
	exit;
}

class Add_Quicktag_Settings extends Add_Quicktag {
	
	protected static $classobj = NULL;
	// string for translation
	static public    $textdomain;
	// string for options in table options
	static private   $option_string;
	// string for plugin file
	static private   $plugin;
	// post types for the settings
	static private   $post_types_for_js;
	// string for nonce fields
	static public    $nonce_string;
	
	protected        $page_hook;
	
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
	 * @uses    register_activation_hook, register_uninstall_hook, add_action
	 * @return  void
	 */
	public function __construct() {
		
		if ( ! is_admin() )
			return NULL;
		
		// textdomain from parent class
		self::$textdomain        = parent::get_textdomain();
		self::$option_string     = parent::get_option_string();
		self::$plugin            = parent::get_plugin_string();
		self::$post_types_for_js = parent::get_post_types_for_js();
		self::$nonce_string      = 'addquicktag_nonce';
		
		register_uninstall_hook( __FILE__,       array( 'Add_Quicktag_Settings', 'unregister_settings' ) );
		// settings for an active multisite
		if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) ) {
			add_action( 'network_admin_menu',    array( $this, 'add_settings_page' ) );
			// add settings link
			add_filter( 'network_admin_plugin_action_links', array( $this, 'network_admin_plugin_action_links' ), 10, 2 );
			// save settings on network
			add_action( 'network_admin_edit_' . self::$option_string, array( $this, 'save_network_settings_page' ) );
			// return message for update settings
			add_action( 'network_admin_notices', array( $this, 'get_network_admin_notices' ) );
			// add script on settings page
		} else {
			add_action( 'admin_menu',            array( $this, 'add_settings_page' ) );
			// add settings link
			add_filter( 'plugin_action_links',   array( $this, 'plugin_action_links' ), 10, 2 );
			// use settings API
			add_action( 'admin_init',            array( $this, 'register_settings' ) );
		}
		// include js 
		add_action( 'admin_print_scripts-settings_page_' . str_replace( '.php', '', plugin_basename( __FILE__ ) ), 
			array( $this, 'print_scripts' )
		);
			
		// add meta boxes on settings pages
		add_action( 'addquicktag_settings_page_sidebar', array( $this, 'get_plugin_infos' ) );
		add_action( 'addquicktag_settings_page_sidebar', array( $this, 'get_about_plugin' ) );
		// include class for im/export
		require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-imexport.php';
	}
	
	/**
	 * Retrun allowed post types for include scripts
	 * 
	 * @since   2.1.1
	 * @access  public
	 * @return  Array
	 */
	public function get_post_types_for_js() {
		
		return self::$post_types_for_js;
	}
	
	/**
	 * Return Textdomain string
	 * 
	 * @access  public
	 * @since   2.0.0
	 * @return  string
	 */
	public function get_textdomain() {
		
		return self :: $textdomain;
	}
	
	/**
	 * Add settings link on plugins.php in backend
	 * 
	 * @uses   
	 * @access public
	 * @param  array $links, string $file
	 * @since  2.0.0
	 * @return string $links
	 */
	public function plugin_action_links( $links, $file ) {
		
		if ( parent :: get_plugin_string() == $file  )
			$links[] = '<a href="options-general.php?page=' . plugin_basename( __FILE__ ) . '">' . __('Settings') . '</a>';
		
		return $links;
	}
	
	/**
	 * Add settings link on plugins.php on network admin in backend
	 * 
	 * @uses   
	 * @access public
	 * @param  array $links, string $file
	 * @since  2.0.0
	 * @return string $links
	 */
	public function network_admin_plugin_action_links( $links, $file ) {
		
		if ( parent :: get_plugin_string() == $file  )
			$links[] = '<a href="settings.php?page=' . plugin_basename( __FILE__ ) . '">' . __('Settings') . '</a>';
		
		return $links;
	}
	
	/**
	 * Add settings page in WP backend
	 * 
	 * @uses   add_options_page
	 * @access public
	 * @since  2.0.0
	 * @return void
	 */
	public function add_settings_page () {
		
		if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) ) {
			add_submenu_page(
				'settings.php',
				parent :: get_plugin_data( 'Name' ) . ' ' . __( 'Settings', $this->get_textdomain() ),
				parent :: get_plugin_data( 'Name' ),
				'manage_options',
				plugin_basename(__FILE__),
				array( $this, 'get_settings_page' )
			);
		} else {
			add_options_page(
				parent :: get_plugin_data( 'Name' ) . ' ' . __( 'Settings', $this->get_textdomain() ),
				parent :: get_plugin_data( 'Name' ),
				'manage_options',
				plugin_basename(__FILE__),
				array( $this, 'get_settings_page' )
			);
			add_action( 'contextual_help', array( $this, 'contextual_help' ), 10, 3 );
		}
	}
	
	/**
	 * Return form and markup on settings page
	 * 
	 * @uses settings_fields, normalize_whitespace, is_plugin_active_for_network, get_site_option, get_option
	 * @access public	
	 * @since 0.0.2
	 * @return void
	 */
	public function get_settings_page() {
		
		?>
		<div class="wrap">
			<?php screen_icon('options-general'); ?>
			<h2><?php echo parent :: get_plugin_data( 'Name' ); ?></h2>
			
			<h3><?php _e('Add or delete Quicktag buttons', $this->get_textdomain() ); ?></h3>
			<?php
			if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) )
				$action = 'edit.php?action=' . self::$option_string;
			else
				$action = 'options.php';
			?>
			<form method="post" action="<?php echo $action; ?>">
				<?php
				if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) ) {
					wp_nonce_field( self::$nonce_string );
					$options = get_site_option( self::$option_string );
				} else {
					settings_fields( self::$option_string . '_group' );
					$options = get_option( self::$option_string );
				}
				
				if ( ! $options )
					$options['buttons'] = array();
				
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
				
				// loop about the post types, create html an values for title in table
				$pt_title = '';
				foreach ( $this->get_post_types_for_js() as $post_type ) {
					
					$pt_title .= '<th class="row-title" title="Post Type"><code>' . $post_type . '</code></th>' . "\n";
				}
				?>
				
				<table class="widefat">
					<tr>
						<th class="row-title"><?php _e( 'Button Label*', $this->get_textdomain() ); ?></th>
						<th class="row-title"><?php _e( 'Title Attribute', $this->get_textdomain() ); ?></th>
						<th class="row-title"><?php _e( 'Start Tag(s)*', $this->get_textdomain() ); ?></th>
						<th class="row-title"><?php _e( 'End Tag(s)', $this->get_textdomain() ); ?></th>
						<th class="row-title"><?php _e( 'Access Key', $this->get_textdomain() ); ?></th>
						<th class="row-title"><?php _e( 'Order', $this->get_textdomain() ); ?></th>
						<th class="row-title"><?php _e( 'Visual', $this->get_textdomain() ); ?></th>
						<?php echo $pt_title ?>
						<th class="row-title">&#x2714;</th>
					</tr>
					<?php
					if ( empty($options['buttons']) )
						$options['buttons'] = array();
					$class = '';
					for ( $i = 0; $i < count( $options['buttons'] ); $i++ ) {
						$class = ( ' class="alternate"' == $class ) ? '' : ' class="alternate"';
						$b           = $options['buttons'][$i];
						$b['text']   = htmlentities( stripslashes($b['text']), ENT_COMPAT, get_option('blog_charset') );
						if ( isset( $b['title'] ) )
							$b['title'] = htmlentities( stripslashes($b['title']), ENT_COMPAT, get_option('blog_charset') );
						$b['start']  = htmlentities( $b['start'], ENT_COMPAT, get_option('blog_charset') );
						if ( isset( $b['end'] ) )
							$b['end'] = htmlentities( $b['end'], ENT_COMPAT, get_option('blog_charset') );
						if ( ! isset( $b['access'] ) )
							$b['access'] = '';
						$b['access'] = htmlentities( $b['access'], ENT_COMPAT, get_option('blog_charset') );
						if ( ! isset( $b['order'] ) )
							$b['order'] = 0;
						$b['order'] = intval( $b['order'] );
						if ( ! isset( $b['visual'] ) )
							$b['visual'] = 0;
						$b['visual'] = intval( $b['visual'] );
						if ( 1 == $b['visual'] )
							$checked = ' checked="checked"';
						else 
							$checked = '';
						// loop about the post types, create html an values
						$pt_checkboxes = '';
						foreach ( $this->get_post_types_for_js() as $post_type ) {
							
							if ( ! isset( $b[$post_type] ) )
								$b[$post_type] = 0;
							
							$b[$post_type] = intval( $b[$post_type] );
							
							if ( 1 == $b[$post_type] )
								$pt_checked = ' checked="checked"';
							else 
								$pt_checked = '';
							
							$pt_checkboxes .= '<td><input type="checkbox" name="' . 
								self::$option_string . '[buttons][' . 
								$i . '][' . $post_type . ']" value="1"' . 
								$pt_checked . '/></td>' . "\n";
						}
						
						$nr = $i + 1;
					
					echo '
					<tr id="rmqtb' . $i . '">
						<td><input type="text" name="' . self::$option_string . '[buttons][' . $i 
						. '][text]" value="' . $b['text'] . '" style="width: 95%;" /></td>
						<td><input type="text" name="' . self::$option_string . '[buttons][' . $i . '][title]" value="' 
						. $b['title'] . '" style="width: 95%;" /></td>
						<td><textarea class="code" name="' . self::$option_string . '[buttons][' . $i 
						. '][start]" rows="2" cols="25" style="width: 95%;">' . $b['start'] . '</textarea></td>
						<td><textarea class="code" name="' . self::$option_string . '[buttons][' . $i 
						. '][end]" rows="2" cols="25" style="width: 95%;">' . $b['end'] . '</textarea></td>
						<td><input type="text" name="' . self::$option_string . '[buttons][' . $i 
						. '][access]" value="' . $b['access'] . '" style="width: 95%;" /></td>
						<td><input type="text" name="' . self::$option_string . '[buttons][' . $i 
						. '][order]" value="' . $b['order'] . '" style="width: 95%;" /></td>
						<td><input type="checkbox" name="' . self::$option_string . '[buttons][' . $i 
						. '][visual]" value="1"' . $checked . '/></td>' . 
						$pt_checkboxes . '
						<td><input type="checkbox" class="toggle" id="select_all_' . $i . '" value="'. $i . '" /></td>' . '
					</tr>
					';
					}
					
					// loop about the post types, create html an values for empty new checkboxes
					$pt_new_boxes  = '';
					foreach ( $this->get_post_types_for_js() as $post_type ) {
						if ( ! isset( $b[$post_type] ) )
							$b[$post_type] = 0;
						
						$b[$post_type] = intval( $b[$post_type] );
						
						if ( 1 == $b[$post_type] )
							$pt_checked = ' checked="checked"';
						else 
							$pt_checked = '';
						
						$pt_new_boxes .= '<td><input type="checkbox" name="' . 
							self::$option_string . '[buttons][' . 
							$i . '][' . $post_type . ']" value="1" /></td>' . "\n";
					}
					?>
					<tr id="rmqtb<?php echo $i ?>">
						<td><input type="text" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][text]" value="" style="width: 95%;" /></td>
						<td><input type="text" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][title]" value="" style="width: 95%;" /></td>
						<td><textarea class="code" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][start]" rows="2" cols="25" style="width: 95%;"></textarea></td>
						<td><textarea class="code" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][end]" rows="2" cols="25" style="width: 95%;"></textarea></td>
						<td><input type="text" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][access]" value="" class="code" style="width: 95%;" /></td>
						<td><input type="text" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][order]" value="" style="width: 95%;" /></td>
						<td><input type="checkbox" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][visual]" value="1" /></td>
						<?php echo $pt_new_boxes; ?>
						<td><input type="checkbox" class="toggle" id="select_all_<?php echo $i ?>" value="<?php echo $i ?>" /></td>
					</tr>
				</table>
				
				<p><?php _e( 'Fill in the fields below to add or edit the quicktags. Fields with * are required. To delete a tag simply empty all fields.', $this->get_textdomain() ); ?></p>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
		
			</form>
			
			<div class="metabox-holder has-right-sidebar">
				
				<div class="inner-sidebar">
					<?php do_action( 'addquicktag_settings_page_sidebar' ); ?>
				</div> <!-- .inner-sidebar -->
				
				<div id="post-body">
					<div id="post-body-content">
						<?php do_action( 'addquicktag_settings_page' ); ?>
					</div> <!-- #post-body-content -->
				</div> <!-- #post-body -->
				
			</div> <!-- .metabox-holder -->
			
		</div>
		<?php
	}
	
	/*
	 * Return informations to donate
	 * 
	 * @uses   _e,esc_attr_e
	 * @access public
	 * @since  2.0.0
	 * @return void
	 */
	public function get_plugin_infos() {
		?>
		<div class="postbox">
			
			<h3><span><?php _e( 'Like this plugin?', $this->get_textdomain() ); ?></span></h3>
			<div class="inside">
				<p><?php _e( 'Here\'s how you can give back:', $this->get_textdomain() ); ?></p>
				<ul>
					<li><a href="http://wordpress.org/extend/plugins/addquicktag/" title="<?php esc_attr_e( 'The Plugin on the WordPress plugin repository', $this->get_textdomain() ); ?>"><?php _e( 'Give the plugin a good rating.', $this->get_textdomain() ); ?></a></li>
					<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=6069955" title="<?php esc_attr_e( 'Donate via PayPal', $this->get_textdomain() ); ?>"><?php _e( 'Donate a few euros.', $this->get_textdomain() ); ?></a></li>
					<li><a href="http://www.amazon.de/gp/registry/3NTOGEK181L23/ref=wl_s_3" title="<?php esc_attr_e( 'Frank Bültge\'s Amazon Wish List', $this->get_textdomain() ); ?>"><?php _e( 'Get me something from my wish list.', $this->get_textdomain() ); ?></a></li>
					<li><a href="https://github.com/bueltge/AddQuicktag" title="<?php _e( 'Please give me feedback, contribute and file technical bugs on this GitHub Repo, use Issues.', $this->get_textdomain() ); ?>"><?php _e( 'Github Repo for Contribute, Issues & Bugs', $this->get_textdomain() ); ?></a></li>
				</ul>
			</div>
		</div>
		<?php
	}
	
	/*
	 * Return informations about the plugin
	 * 
	 * @uses   _e,esc_attr_e
	 * @access public
	 * @since  2.0.0
	 * @return void
	 */
	public function get_about_plugin() {
		?>
		<div class="postbox">
			
			<h3><span><?php _e( 'About this plugin', $this->get_textdomain() ); ?></span></h3>
			<div class="inside">
				<p>
					<strong><?php _e( 'Version:', $this->get_textdomain() ); ?></strong>
					<?php echo parent :: get_plugin_data( 'Version' ); ?>
				</p>
				<p>
					<strong><?php _e( 'Description:', $this->get_textdomain() ); ?></strong>
					<?php echo parent :: get_plugin_data( 'Description' ); ?>
				</p>
			</div>
			
		</div>
		<?php
	}
	
	/*
	 * Save network settings
	 * 
	 * @uses   update_site_option, wp_redirect, add_query_arg, network_admin_url
	 * @access public
	 * @since  2.0.0
	 * @return void
	 */
	public function save_network_settings_page() {
		
		if ( ! wp_verify_nonce( $_REQUEST[ '_wpnonce' ], self::$nonce_string ) )
			wp_die( 'Sorry, you failed the nonce test.' );
		
		// validate options
		$value = $this->validate_settings( $_POST[self::$option_string] );
		
		// update options
		update_site_option( self::$option_string, $value );
		// redirect to settings page in network
		wp_redirect(
			add_query_arg(
				array( 'page' => plugin_basename( __FILE__ ), 'updated' => 'true' ),
				network_admin_url( 'settings.php' )
			)
		);
		exit();
	}
	
	/*
	 * Retrun string vor update message
	 * 
	 * @uses   
	 * @access public
	 * @since  2.0.0
	 * @return string $notice
	 */
	public function get_network_admin_notices() {
		
		// if updated and the right page
		if ( isset( $_GET['updated'] ) && 
			 'settings_page_addquicktag/inc/class-settings-network' === $GLOBALS['current_screen'] -> id
			) {
			$message = __( 'Options saved.', $this->get_textdomain() );
			$notice  = '<div id="message" class="updated"><p>' .$message . '</p></div>';
			echo $notice;
		}
	}
	
	/**
	 * Validate settings for options
	 * 
	 * @uses   normalize_whitespace
	 * @access public
	 * @param  array $value
	 * @since  2.0.0
	 * @return string $value
	 */
	public function validate_settings( $value ) {
		
		// set allowd values for import, only the defaults of plugin and custom post types
		$allowed_settings = (array) array_merge(
			$this->get_post_types_for_js(),
			array( 'text', 'title', 'start', 'end', 'access', 'order', 'visual' )
		);
		// filter for allowed values
		foreach ( $value['buttons'] as $key => $button ) {
			
			foreach ($button as $key => $val) {
				
				if ( ! in_array( $key, $allowed_settings) )
					unset( $button[$key] );
			}
			$buttons[] = $button;
		}
		// return filtered array
		$filtered_values['buttons'] = $buttons;
		$value = $filtered_values;
		
		$buttons = array();
		for ( $i = 0; $i < count( $value['buttons']); $i++ ) {
				
				$b = $value['buttons'][$i];
				if ($b['text']  != '' && $b['start'] != '') {
					$b['text']   = esc_html( $b['text'] );
					$b['title']  = esc_html( $b['title'] );
					$b['start']  = stripslashes( $b['start'] );
					$b['end']    = stripslashes( $b['end'] );
					if ( isset( $b['access'] ) )
						$b['access'] = esc_html( $b['access'] );
					if ( isset( $b['order'] ) )
						$b['order']  = intval( $b['order'] );
					// visual settings
					if ( isset( $b['visual'] ) )
						$b['visual'] = intval( $b['visual'] );
					else
						$b['visual'] = 0;
					// post types
					foreach ( $this->get_post_types_for_js() as $post_type ) {
						if ( isset( $b[$post_type] ) )
							$b[$post_type] = intval( $b[$post_type] );
						else
							$b[$post_type] = 0;
					}
					
					$buttons[]   = $b;
				}
				
		}
		$value['buttons'] = $buttons;
		
		return $value;
	}
	
	/**
	 * Register settings for options
	 * 
	 * @uses    register_setting
	 * @access  public
	 * @since   2.0.0
	 * @return  void
	 */
	public function register_settings() {
		
		register_setting( self::$option_string . '_group', self::$option_string, array( $this, 'validate_settings' ) );
	}
	
	/**
	 * Unregister and delete settings; clean database
	 * 
	 * @uses    unregister_setting, delete_option
	 * @access  public
	 * @since   0.0.2
	 * @return  void
	 */
	public function unregister_settings() {
		
		unregister_setting( self::$option_string . '_group', self::$option_string );
		delete_option( self::$option_string );
	}
	
	public function print_scripts( $where ) {
		
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
		
		wp_register_script(
			self::$option_string . '_admin_script', 
			plugins_url( '/js/settings' . $suffix. '.js', parent::get_plugin_string() ), 	
			array( 'jquery', 'quicktags' ),
			'',
			TRUE
		);
		wp_enqueue_script( self::$option_string . '_admin_script' );
	}
	
	/**
	 * Add help text
	 * 
	 * @uses    normalize_whitespace
	 * @param   string $contextual_help
	 * @param   string $screen_id
	 * @param   string $screen
	 * @since   2.0.0
	 * @return  string $contextual_help
	 */
	public function contextual_help( $contextual_help, $screen_id, $screen ) {
			
		if ( 'settings_page_' . self::$option_string . '_group' !== $screen_id )
			return $contextual_help;
			
		$contextual_help = 
			'<p>' . __( '' ) . '</p>';
			
		return normalize_whitespace( $contextual_help );
	}
	
}
$add_quicktag_settings = Add_Quicktag_Settings :: get_object();
