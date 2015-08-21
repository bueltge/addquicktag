<?php
/**
 * AddQuicktag - Settings
 * @license    GPLv2
 * @package    AddQuicktag
 * @subpackage AddQuicktag Settings
 * @author     Frank Bueltge <frank@bueltge.de>
 * @version    06/19/2014
 * @since      2.0.0
 */

if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a part of plugin, not much I can do when called directly.";
	exit;
}

/**
 * Class Add_Quicktag_Settings
 */
class Add_Quicktag_Settings extends Add_Quicktag {

	/**
	 * string for translation
	 * @var string
	 */
	static public $textdomain;

	/**
	 * string for options in table options
	 * @var string
	 */
	static private $option_string;

	/**
	 * string for plugin file
	 * @var string
	 */
	static private $plugin;

	/**
	 * post types for the settings
	 * @var Array
	 */
	static private $post_types_for_js;

	/**
	 * string for nonce fields
	 * @var string
	 */
	static public $nonce_string;

	/**
	 * @var
	 */
	protected $page_hook;

	/**
	 * Handler for the action 'init'. Instantiates this class.
	 * @access  public
	 * @since   2.0.0
	 * @return  \Add_Quicktag|\Add_Quicktag_Settings $instance
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
	 * @uses    register_activation_hook, register_uninstall_hook, add_action
	 * @return  \Add_Quicktag_Settings
	 */
	private function __construct() {

		if ( ! is_admin() ) {
			return;
		}

		// textdomain from parent class
		self::$textdomain        = parent::get_textdomain();
		self::$option_string     = parent::get_option_string();
		self::$plugin            = parent::get_plugin_string();
		self::$post_types_for_js = parent::get_post_types_for_js();
		self::$nonce_string      = 'addquicktag_nonce';

		register_uninstall_hook( __FILE__, array( 'Add_Quicktag_Settings', 'unregister_settings' ) );
		// settings for an active multisite
		if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) ) {
			add_action( 'network_admin_menu', array( $this, 'add_settings_page' ) );
			// add settings link
			add_filter(
				'network_admin_plugin_action_links', array(
				$this,
				'network_admin_plugin_action_links'
			), 10, 2
			);
			// save settings on network
			add_action( 'network_admin_edit_' . self::$option_string, array( $this, 'save_network_settings_page' ) );
			// return message for update settings
			add_action( 'network_admin_notices', array( $this, 'get_network_admin_notices' ) );
			// add script on settings page
		} else {
			add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
			// add settings link
			add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
			// use settings API
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}
		// include js 
		add_action(
			'admin_print_scripts-settings_page_' . str_replace( '.php', '', plugin_basename( __FILE__ ) ),
			array( $this, 'print_scripts' )
		);

		// add meta boxes on settings pages
		add_action( 'addquicktag_settings_page_sidebar', array( $this, 'get_plugin_infos' ) );
		add_action( 'addquicktag_settings_page_sidebar', array( $this, 'get_about_plugin' ) );

		// include class for remove core quicktags
		require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-remove-quicktags.php';
		// include class for add enhanced code quicktags
		// @TODO Solution for special code tags in quicktags
		require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-code-quicktags.php';
		// include class for im/export
		require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-imexport.php';
	}

	/**
	 * Return allowed post types for include scripts
	 * @since   2.1.1
	 * @access  public
	 * @return  Array
	 */
	public function get_post_types_for_js() {

		return self::$post_types_for_js;
	}

	/**
	 * Return Textdomain string
	 * @access  public
	 * @since   2.0.0
	 * @return  string
	 */
	public function get_textdomain() {

		return self::$textdomain;
	}

	/**
	 * Add settings link on plugins.php in backend
	 * @uses
	 * @access  public
	 *
	 * @param  array  $links , string $file
	 * @param  string $file
	 *
	 * @since   2.0.0
	 * @return  string $links
	 */
	public function plugin_action_links( $links, $file ) {

		if ( parent::get_plugin_string() === $file ) {
			$links[ ] = '<a href="options-general.php?page=' . plugin_basename( __FILE__ ) . '">' . esc_html__( 'Settings' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Add settings link on plugins.php on network admin in backend
	 * @uses
	 * @access public
	 * @since  2.0.0
	 *
	 * @param  array $links , string $file
	 * @param        $file
	 *
	 * @return string $links
	 */
	public function network_admin_plugin_action_links( $links, $file ) {

		if ( parent::get_plugin_string() === $file ) {
			$links[ ] = '<a href="settings.php?page=' . plugin_basename( __FILE__ ) . '">' . esc_html__( 'Settings' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Add settings page in WP backend
	 * @uses   add_options_page
	 * @access public
	 * @since  2.0.0
	 */
	public function add_settings_page() {

		if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) ) {
			add_submenu_page(
				'settings.php',
				parent::get_plugin_data( 'Name' ) . ' ' . esc_html__( 'Settings', $this->get_textdomain() ),
				parent::get_plugin_data( 'Name' ),
				'manage_options',
				plugin_basename( __FILE__ ),
				array( $this, 'get_settings_page' )
			);
		} else {
			add_options_page(
				parent::get_plugin_data( 'Name' ) . ' ' . esc_html__( 'Settings', $this->get_textdomain() ),
				parent::get_plugin_data( 'Name' ),
				'manage_options',
				plugin_basename( __FILE__ ),
				array( $this, 'get_settings_page' )
			);
		}
	}

	/**
	 * Return form and markup on settings page
	 * @uses   settings_fields, normalize_whitespace, is_plugin_active_for_network, get_site_option, get_option
	 * @access public
	 * @since  0.0.2
	 */
	public function get_settings_page() {

		?>
		<div class="wrap">
			<h2><?php echo parent::get_plugin_data( 'Name' ); ?></h2>

			<h3><?php esc_html_e( 'Add or delete Quicktag buttons', $this->get_textdomain() ); ?></h3>

			<p><?php esc_html_e( 'Fill in the fields below to add or edit the quicktags. Fields with * are required. To delete a tag simply empty all fields.', $this->get_textdomain() ); ?></p>

			<?php
			if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) ) {
				$action = 'edit.php?action=' . self::$option_string;
			} else {
				$action = 'options.php';
			}
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

				if ( ! isset( $options[ 'buttons' ] ) ) {
					$options[ 'buttons' ] = array();
				}

				if ( 1 < count( $options[ 'buttons' ] ) ) {
					// sort array by order value
					$tmp = array();
					foreach ( $options[ 'buttons' ] as $order ) {
						if ( isset( $order[ 'order' ] ) ) {
							$tmp[ ] = $order[ 'order' ];
						} else {
							$tmp[ ] = 0;
						}
					}
					array_multisort( $tmp, SORT_ASC, $options[ 'buttons' ] );
				}

				// loop about the post types, create html an values for title in table
				$pt_title    = '';
				$pt_colgroup = '';
				foreach ( $this->get_post_types_for_js() as $post_type ) {

					$pt_title .= '<th class="row-title rotate" title="Post Type"><span><code>' . $post_type . '</code></span></th>' . "\n";
					$pt_colgroup .= '<colgroup></colgroup>' . "\n";
				}
				?>

				<table class="widefat form-table rmnlQuicktagSettings">
					<colgroup></colgroup>
					<colgroup></colgroup>
					<colgroup></colgroup>
					<colgroup></colgroup>
					<colgroup></colgroup>
					<?php echo $pt_colgroup; ?>
					<colgroup></colgroup>

					<tr class="rmnlqsheader">
						<th class="row-title"><?php esc_html_e( 'Button Label* and', $this->get_textdomain() ); ?><br />
							<?php esc_html_e( 'Title Attribute', $this->get_textdomain() ); ?></th>
						<th class="row-title"><?php esc_html_e( 'Start Tag(s)* and', $this->get_textdomain() ); ?><br />
							<?php esc_html_e( 'End Tag(s)', $this->get_textdomain() ); ?></th>
						<th class="row-title"><?php esc_html_e( 'Access Key and', $this->get_textdomain() ); ?><br />
							<?php esc_html_e( 'Order', $this->get_textdomain() ); ?></th>
						<th class="row-title rotate"><span><?php esc_html_e( 'Visual', $this->get_textdomain() ); ?></span></th>
						<?php echo $pt_title ?>
						<th class="row-title rotate">&#x2714;</th>
					</tr>
					<?php
					if ( empty( $options[ 'buttons' ] ) ) {
						$options[ 'buttons' ] = array();
					}
					$class = '';
					for ( $i = 0; $i < count( $options[ 'buttons' ] ); $i ++ ) {
						$class       = ( ' class="alternate"' == $class ) ? '' : ' class="alternate"';
						$b           = $options[ 'buttons' ][ $i ];
						$b[ 'text' ] = htmlentities( stripslashes( $b[ 'text' ] ), ENT_COMPAT, get_option( 'blog_charset' ) );
						if ( isset( $b[ 'title' ] ) ) {
							$b[ 'title' ] = htmlentities( stripslashes( $b[ 'title' ] ), ENT_COMPAT, get_option( 'blog_charset' ) );
						}
						$b[ 'start' ] = htmlentities( $b[ 'start' ], ENT_COMPAT, get_option( 'blog_charset' ) );
						if ( isset( $b[ 'end' ] ) ) {
							$b[ 'end' ] = htmlentities( $b[ 'end' ], ENT_COMPAT, get_option( 'blog_charset' ) );
						}
						if ( ! isset( $b[ 'access' ] ) ) {
							$b[ 'access' ] = '';
						}
						$b[ 'access' ] = htmlentities( $b[ 'access' ], ENT_COMPAT, get_option( 'blog_charset' ) );
						if ( ! isset( $b[ 'order' ] ) ) {
							$b[ 'order' ] = 0;
						}
						$b[ 'order' ] = (int) $b[ 'order' ];
						if ( ! isset( $b[ 'visual' ] ) ) {
							$b[ 'visual' ] = 0;
						}
						$b[ 'visual' ] = (int) $b[ 'visual' ];
						if ( 1 == $b[ 'visual' ] ) {
							$checked = ' checked="checked"';
						} else {
							$checked = '';
						}
						// loop about the post types, create html an values
						$pt_checkboxes = '';
						foreach ( $this->get_post_types_for_js() as $post_type ) {

							if ( ! isset( $b[ $post_type ] ) ) {
								$b[ $post_type ] = 0;
							}

							$b[ $post_type ] = (int) $b[ $post_type ];

							if ( 1 === $b[ $post_type ] ) {
								$pt_checked = ' checked="checked"';
							} else {
								$pt_checked = '';
							}

							$pt_checkboxes .= '<td class="num"><input type="checkbox" name="' .
							                  self::$option_string . '[buttons][' .
							                  $i . '][' . $post_type . ']" value="1" ' .
							                  $pt_checked . '/></td>' . "\n";
						}

						echo '
					<tr id="rmqtb' . $i . '">
						<td><input type="text" name="' . self::$option_string . '[buttons][' . $i
						     . '][text]" value="' . $b[ 'text' ] . '" /><br />
						<input type="text" name="' . self::$option_string . '[buttons][' . $i . '][title]" value="'
						     . $b[ 'title' ] . '" /></td>
						<td><textarea class="code" name="' . self::$option_string . '[buttons][' . $i
						     . '][start]" rows="2" cols="25" >' . $b[ 'start' ] . '</textarea><br />
						<textarea class="code" name="' . self::$option_string . '[buttons][' . $i
						     . '][end]" rows="2" cols="25" >' . $b[ 'end' ] . '</textarea></td>
						<td><input class="small-text" type="text" name="' . self::$option_string . '[buttons][' . $i
						     . '][access]" value="' . $b[ 'access' ] . '" /><br />
						<input class="small-text" type="text" name="' . self::$option_string . '[buttons][' . $i
						     . '][order]" value="' . $b[ 'order' ] . '" /></td>
						<td class="num"><input type="checkbox" name="' . self::$option_string . '[buttons][' . $i
						     . '][visual]" value="1"' . $checked . '/></td>' .
						     $pt_checkboxes . '
						<td class="num"><input type="checkbox" class="toggle" id="select_all_' . $i . '" value="' . $i . '" /></td>' . '
					</tr>
					';
					}

					// loop about the post types, create html an values for empty new checkboxes
					$pt_new_boxes = '';
					foreach ( $this->get_post_types_for_js() as $post_type ) {
						if ( ! isset( $b[ $post_type ] ) ) {
							$b[ $post_type ] = 0;
						}

						$b[ $post_type ] = (int) $b[ $post_type ];

						$pt_new_boxes .= '<td class="num"><input type="checkbox" name="' .
						                 self::$option_string . '[buttons][' .
						                 $i . '][' . $post_type . ']" value="1" /></td>' . "\n";
					}
					?>
					<tr id="rmqtb<?php echo $i ?>">
						<td>
							<input type="text" placeholder="<?php esc_html_e( 'Button Label*', $this->get_textdomain() ); ?>" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][text]" value="" /><br />
							<input type="text" placeholder="<?php esc_html_e( 'Title Attribute', $this->get_textdomain() ); ?>" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][title]" value="" />
						</td>
						<td>
							<textarea placeholder="<?php esc_html_e( 'Start Tag(s)*', $this->get_textdomain() ); ?>" class="code" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][start]" rows="2" cols="25"></textarea><br />
							<textarea placeholder="<?php esc_html_e( 'End Tag(s)', $this->get_textdomain() ); ?>" class="code" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][end]" rows="2" cols="25"></textarea>
						</td>
						<td>
							<input type="text" placeholder="<?php esc_html_e( 'Access Key', $this->get_textdomain() ); ?>" title="<?php esc_html_e( 'Access Key', $this->get_textdomain() ); ?>" class="small-text" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][access]" value="" /><br />
							<input type="text" placeholder="<?php esc_html_e( 'Order', $this->get_textdomain() ); ?>" title="<?php esc_html_e( 'Order', $this->get_textdomain() ); ?>" class="small-text" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][order]" value="" />
						</td>
						<td class="num">
							<label>
								<input type="checkbox" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][visual]" value="1" />
							</label>
						</td>
						<?php echo $pt_new_boxes; ?>
						<td class="num">
							<label for="select_all_<?php echo $i ?>"><input type="checkbox" class="toggle" id="select_all_<?php echo $i ?>" value="<?php echo $i ?>" /></label>
						</td>
					</tr>
				</table>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes' ) ?>" />
				</p>

				<?php do_action( 'addquicktag_settings_form_page', $options ); ?>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes' ) ?>" />
				</p>

			</form>

			<div class="metabox-holder has-right-sidebar">

				<div class="inner-sidebar">
					<?php do_action( 'addquicktag_settings_page_sidebar' ); ?>
				</div>
				<!-- .inner-sidebar -->

				<div id="post-body">
					<div id="post-body-content">
						<?php do_action( 'addquicktag_settings_page', $options ); ?>
					</div>
					<!-- #post-body-content -->
				</div>
				<!-- #post-body -->

			</div>
			<!-- .metabox-holder -->

		</div>
	<?php
	}

	/*
	 * Return information to donate
	 * 
	 * @uses   _e,esc_attr_e
	 * @access public
	 * @since  2.0.0
	 * @return void
	 */
	public function get_plugin_infos() {

		?>
		<div class="postbox">

			<h3><span><?php esc_html_e( 'Like this plugin?', $this->get_textdomain() ); ?></span></h3>

			<div class="inside">
				<p><?php esc_html_e( 'Here\'s how you can give back:', $this->get_textdomain() ); ?></p>
				<ul>
					<li>
						<a href="http://wordpress.org/support/view/plugin-reviews/addquicktag" title="<?php esc_html_e( 'The Plugin on the WordPress plugin repository', $this->get_textdomain() ); ?>"><?php esc_html_e( 'Give the plugin a good rating.', $this->get_textdomain() ); ?></a>
					</li>
					<li>
						<a href="http://wordpress.org/support/plugin/addquicktag" title="<?php esc_html_e( 'Help inside the community other useres and write answer to this plugin questions.', $this->get_textdomain() ); ?>"><?php esc_html_e( 'Help other users in the Support Forum.', $this->get_textdomain() ); ?></a>
					</li>
					<li>
						<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=6069955" title="<?php esc_html_e( 'Donate via PayPal', $this->get_textdomain() ); ?>"><?php esc_html_e( 'Donate a few euros.', $this->get_textdomain() ); ?></a>
					</li>
					<li>
						<a href="http://www.amazon.de/gp/registry/3NTOGEK181L23/ref=wl_s_3" title="<?php esc_html_e( 'Frank Bültge\'s Amazon Wish List', $this->get_textdomain() ); ?>"><?php esc_html_e( 'Get me something from my wish list.', $this->get_textdomain() ); ?></a>
					</li>
					<li>
						<a href="https://github.com/bueltge/AddQuicktag" title="<?php esc_html_e( 'Please give me feedback, contribute and file technical bugs on this GitHub Repo, use Issues.', $this->get_textdomain() ); ?>"><?php esc_html_e( 'Github Repo for Contribute, Issues & Bugs', $this->get_textdomain() ); ?></a>
					</li>
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

			<h3><span><?php esc_html_e( 'About this plugin', $this->get_textdomain() ); ?></span></h3>

			<div class="inside">
				<p>
					<strong><?php esc_html_e( 'Version:', $this->get_textdomain() ); ?></strong>
					<?php echo parent::get_plugin_data( 'Version' ); ?>
				</p>

				<p>
					<strong><?php esc_html_e( 'Description:', $this->get_textdomain() ); ?></strong>
					<?php echo parent::get_plugin_data( 'Description' ); ?>
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

		if ( ! wp_verify_nonce( $_REQUEST[ '_wpnonce' ], self::$nonce_string ) ) {
			wp_die( 'Sorry, you failed the nonce test.' );
		}

		// validate options
		$value = $this->validate_settings( $_POST[ self::$option_string ] );

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
		if ( isset( $_GET[ 'updated' ] ) &&
		     'settings_page_addquicktag/inc/class-settings-network' === $GLOBALS[ 'current_screen' ]->id
		) {
			$message = esc_html__( 'Options saved.', $this->get_textdomain() );
			$notice  = '<div id="message" class="updated"><p>' . $message . '</p></div>';
			echo $notice;
		}
	}

	/**
	 * Validate settings for options
	 * @uses   normalize_whitespace
	 * @access public
	 *
	 * @param  array $value
	 *
	 * @since  2.0.0
	 * @return string $value
	 */
	public function validate_settings( $value ) {

		// Save core buttons changes
		if ( isset( $value[ 'core_buttons' ] ) ) {
			$core_buttons = $value[ 'core_buttons' ];
		}

		// Save Code buttons
		if ( isset( $value[ 'code_buttons' ] ) ) {
			$code_buttons = $value[ 'code_buttons' ];
		}

		// set allowed values for import, only the defaults of plugin and custom post types
		$allowed_settings = (array) array_merge(
			$this->get_post_types_for_js(),
			array( 'text', 'title', 'start', 'end', 'access', 'order', 'visual' )
		);

		$buttons = '';
		// filter for allowed values
		foreach ( $value[ 'buttons' ] as $key => $button ) {

			foreach ( $button as $label => $val ) {

				if ( ! in_array( $label, $allowed_settings ) ) {
					unset( $button[ $label ] );
				}
			}

			$buttons[ ] = $button;
		}

		// return filtered array
		$filtered_values[ 'buttons' ] = $buttons;
		$value                        = $filtered_values;

		$buttons = array();
		for ( $i = 0; $i < count( $value[ 'buttons' ] ); $i ++ ) {

			$b = $value[ 'buttons' ][ $i ];
			if ( ! empty( $b[ 'text' ] ) && ! empty( $b[ 'start' ] ) ) {

				//preg_replace( '~[^\p{L}]~u', '', $string );

				$b[ 'text' ]  = sanitize_text_field( $b[ 'text' ] );
				$b[ 'title' ] = sanitize_text_field( $b[ 'title' ] );
				$b[ 'start' ] = wp_kses_stripslashes( $b[ 'start' ] );
				$b[ 'end' ]   = wp_kses_stripslashes( $b[ 'end' ] );

				if ( isset( $b[ 'access' ] ) ) {
					$b[ 'access' ] = esc_html( $b[ 'access' ] );
				}

				if ( isset( $b[ 'order' ] ) ) {
					$b[ 'order' ] = intval( $b[ 'order' ] );
				}

				// visual settings
				if ( isset( $b[ 'visual' ] ) ) {
					$b[ 'visual' ] = intval( $b[ 'visual' ] );
				} else {
					$b[ 'visual' ] = 0;
				}

				// post types
				foreach ( $this->get_post_types_for_js() as $post_type ) {

					if ( isset( $b[ $post_type ] ) ) {
						$b[ $post_type ] = intval( $b[ $post_type ] );
					} else {
						$b[ $post_type ] = 0;
					}

				}

				$buttons[ ] = $b;
			}

		}
		$value[ 'buttons' ] = $buttons;
		// Check for wrong empty values and kill
		foreach ( $value[ 'buttons' ] as $key => $b ) {

			if ( empty( $b[ 'text' ] ) && empty( $b[ 'start' ] ) ) {
				unset( $value[ 'buttons' ][ $key ] );
			}
		}
		// reorder the array
		$value[ 'buttons' ] = array_values( $value[ 'buttons' ] );

		// Filter core button values, strings and convert to integer
		if ( ! empty( $core_buttons ) ) {

			/**
			 * $key is core-string
			 * 'core_buttons' =>
			 * array (size=1)
			 * 'strong' =>
			 * array (size=2)
			 * 'post' => string '1' (length=1)
			 * 'page' => string '1' (length=1)
			 */
			$filtered_core_buttons = array();
			foreach ( $core_buttons as $key => $var ) {

				$core_button = array();
				foreach ( $var as $post_type => $val ) {
					$core_button[ $post_type ] = intval( $val );
				}

				$filtered_core_buttons[ $key ] = $core_button;

			}

			$value[ 'core_buttons' ] = $filtered_core_buttons;

		}

		// Filter code button values, strings and convert to integer
		if ( ! empty( $code_buttons ) ) {

			$filtered_code_buttons = array();
			foreach ( $code_buttons as $key => $var ) {

				$code_button = array();
				foreach ( $var as $post_type => $val ) {
					$code_button[ $post_type ] = intval( $val );
				}

				$filtered_code_buttons[ $key ] = $code_button;

			}

			$value[ 'code_buttons' ] = $filtered_code_buttons;

		}

		return $value;
	}

	/**
	 * Register settings for options
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
	 * @uses    unregister_setting, delete_option
	 * @access  public
	 * @since   0.0.2
	 * @return  void
	 */
	public function unregister_settings() {

		unregister_setting( self::$option_string . '_group', self::$option_string );
		delete_option( self::$option_string );
	}

	/**
	 * Enqueue scripts and stylesheets
	 * @since    0.0.2
	 *
	 * @internal param $where
	 */
	public function print_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.dev' : '';

		wp_register_script(
			self::$option_string . '_admin_script',
			plugins_url( '/js/settings' . $suffix . '.js', parent::get_plugin_string() ),
			array( 'jquery', 'quicktags' ),
			'',
			TRUE
		);
		wp_enqueue_script( self::$option_string . '_admin_script' );

		wp_register_style(
			self::$option_string . '_admin_style',
			plugins_url( '/css/settings' . $suffix . '.css', parent::get_plugin_string() ),
			array(),
			FALSE,
			'screen'
		);
		wp_enqueue_style( self::$option_string . '_admin_style' );
	}

}

$add_quicktag_settings = Add_Quicktag_Settings:: get_object();
