<?php
/**
 * AddQuicktag - Settings
 *
 * @package    AddQuicktag
 * @subpackage AddQuicktag Settings
 * @author     Frank Bueltge <frank@bueltge.de>
 * @version    2017-02-22
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
	 * String for translation.
	 *
	 * @var string
	 */
	public static $textdomain;

	/**
	 * String for options in table options.
	 *
	 * @var string
	 */
	private static $option_string;

	/**
	 * String for plugin file.
	 *
	 * @var string
	 */
	private static $plugin;

	/**
	 * Post types for the settings.
	 *
	 * @var array
	 */
	private static $post_types_for_js;

	/**
	 * String for nonce fields.
	 *
	 * @var string
	 */
	public static $nonce_string;

	/**
	 * Store page string for hook to the settings page.
	 *
	 * @var string
	 */
	protected $page_hook;

	/**
	 * Handler for the action 'init'. Instantiates this class.
	 *
	 * @access  public
	 * @since   2.0.0
	 * @return  \Add_Quicktag|\Add_Quicktag_Settings $instance
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
		if ( ! is_admin() ) {
			return;
		}

		self::$option_string     = parent::get_option_string();
		self::$plugin            = parent::get_plugin_string();
		self::$post_types_for_js = parent::get_post_types_for_js();
		self::$nonce_string      = 'addquicktag_nonce';

		// Makes sure the plugin is defined before trying to use it.
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		register_uninstall_hook( __FILE__, array( 'Add_Quicktag_Settings', 'unregister_settings' ) );
		// Settings for an active multisite.
		if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) ) {
			add_action( 'network_admin_menu', array( $this, 'add_settings_page' ) );
			// add settings link.
			add_filter(
				'network_admin_plugin_action_links',
				array(
					$this,
					'network_admin_plugin_action_links',
				),
				10,
				2
			);
			// Save settings on network.
			add_action( 'network_admin_edit_' . self::$option_string, array( $this, 'save_network_settings_page' ) );
			// Return message for update settings.
			add_action( 'network_admin_notices', array( $this, 'get_network_admin_notices' ) );
			// Add script on settings page.
		} else {
			add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
			// Add settings link.
			add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
			// Use settings API.
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}
		// Include js.
		add_action(
			'admin_print_scripts-settings_page_' . str_replace( '.php', '', plugin_basename( __FILE__ ) ),
			array( $this, 'print_scripts' )
		);

		// Add meta boxes on settings pages.
		add_action( 'addquicktag_settings_page_sidebar', array( $this, 'get_plugin_infos' ) );
		add_action( 'addquicktag_settings_page_sidebar', array( $this, 'get_about_plugin' ) );

		// Include class for remove core quicktags.
		require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-remove-quicktags.php';
		// Include class for add enhanced code quicktags.
		// @TODO Solution for special code tags in quicktags.
		require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-code-quicktags.php';
		// Include class for im/export.
		require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-imexport.php';
	}

	/**
	 * Return allowed post types for include scripts.
	 *
	 * @since   2.1.1
	 * @access  public
	 * @return  array
	 */
	public function get_post_types_for_js() {
		return self::$post_types_for_js;
	}

	/**
	 * Add settings link on plugins.php in backend.
	 *
	 * @uses
	 * @access  public
	 *
	 * @param  array  $links An array of plugin action links.
	 * @param  string $file  Path to the plugin file relative to the plugins directory.
	 *
	 * @since   2.0.0
	 * @return  string $links
	 */
	public function plugin_action_links( $links, $file ) {
		if ( parent::get_plugin_string() === $file ) {
			$links[] = '<a href="options-general.php?page=' . plugin_basename( __FILE__ ) . '">' . esc_html__(
				'Settings', 'addquicktag'
			) . '</a>';
		}

		return $links;
	}

	/**
	 * Add settings link on plugins.php on network admin in backend.
	 *
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
			$links[] = '<a href="settings.php?page=' . plugin_basename( __FILE__ ) . '">' . esc_html__(
				'Settings'
			) . '</a>';
		}

		return $links;
	}

	/**
	 * Add settings page in WP backend.
	 *
	 * @uses   add_options_page
	 * @access public
	 * @since  2.0.0
	 */
	public function add_settings_page() {
		if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) ) {
			add_submenu_page(
				'settings.php',
				parent::get_plugin_data( 'Name' ) . ' ' . esc_html__( 'Settings', 'addquicktag' ),
				parent::get_plugin_data( 'Name' ),
				'manage_options',
				plugin_basename( __FILE__ ),
				array( $this, 'get_settings_page' )
			);
		} else {
			add_options_page(
				parent::get_plugin_data( 'Name' ) . ' ' . esc_html__( 'Settings', 'addquicktag' ),
				parent::get_plugin_data( 'Name' ),
				'manage_options',
				plugin_basename( __FILE__ ),
				array( $this, 'get_settings_page' )
			);
		}
	}

	/**
	 * Return form and markup on settings page.
	 *
	 * @uses   settings_fields, normalize_whitespace, is_plugin_active_for_network, get_site_option, get_option
	 * @access public
	 * @since  0.0.2
	 */
	public function get_settings_page() {

		?>
		<div class="wrap">
			<h2><?php echo parent::get_plugin_data( 'Name' ); ?></h2>

			<h3><?php esc_html_e( 'Add or delete Quicktag buttons', 'addquicktag' ); ?></h3>

			<p>
			<?php
			esc_html_e(
				'Fill in the fields below to add or edit the quicktags. Fields with * are required. To delete a tag simply empty all fields.',
				'addquicktag'
			);
			?>
				</p>
			<p><?php esc_html_e( 'Leave the Button Label to don\'t add the button to the quicktags, html mode.', 'addquicktag' ); ?></p>

			<?php
			$action = 'options.php';
			if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) ) {
				$action = 'edit.php?action=' . self::$option_string;
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

				/** @var array $options */
				if ( ! isset( $options['buttons'] ) ) {
					$options['buttons'] = array();
				}

				if ( 1 < count( $options['buttons'] ) ) {
					// Sort array by order value.
					$tmp = array();
					foreach ( (array) $options['buttons'] as $order ) {
						if ( isset( $order['order'] ) ) {
							$tmp[] = $order['order'];
						} else {
							$tmp[] = 0;
						}
					}
					array_multisort( $tmp, SORT_ASC, $options['buttons'] );
				}

				// Loop about the post types, create html an values for title in table.
				$pt_title    = '';
				$pt_colgroup = '';
				foreach ( $this->get_post_types_for_js() as $post_type ) {
					$pt_title    .= '<th class="row-title rotate" title="Post Type"><span><code>' . $post_type . '</code></span></th>' . "\n";
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
					<thead>
					<tr class="rmnlqsheader">
						<th class="row-title"><?php esc_html_e( 'Button Label, Dashicon', 'addquicktag' ); ?>
							<br />
							<?php esc_html_e( 'Title Attribute', 'addquicktag' ); ?></th>
						<th class="row-title"><?php esc_html_e( 'Start Tag(s)* and', 'addquicktag' ); ?><br />
							<?php esc_html_e( 'End Tag(s)', 'addquicktag' ); ?></th>
						<th class="row-title"><?php esc_html_e( 'Access Key and', 'addquicktag' ); ?><br />
							<?php esc_html_e( 'Order', 'addquicktag' ); ?></th>
						<th class="row-title rotate">
							<span><?php esc_html_e( 'Visual', 'addquicktag' ); ?></span></th>
						<?php echo $pt_title; ?>
						<th class="row-title rotate">&#x2714;</th>
					</tr>
					</thead>
					<tbody>
					<?php
					if ( empty( $options['buttons'] ) ) {
						$options['buttons'] = array();
					}
					$class = '';
					$imax  = count( $options['buttons'] );
					for ( $i = 0; $i < $imax; $i ++ ) {
						$class     = ( ' class="alternate"' === $class ) ? '' : ' class="alternate"';
						$b         = $options['buttons'][ $i ];
						$b['text'] = htmlentities(
							stripslashes( $b['text'] ),
							ENT_COMPAT,
							get_option( 'blog_charset' )
						);
						if ( ! isset( $b['dashicon'] ) ) {
							$b['dashicon'] = '';
						}
						$b['dashicon'] = htmlentities(
							stripslashes( $b['dashicon'] ),
							ENT_COMPAT,
							get_option( 'blog_charset' )
						);
						if ( isset( $b['title'] ) ) {
							$b['title'] = htmlentities(
								stripslashes( $b['title'] ),
								ENT_COMPAT,
								get_option( 'blog_charset' )
							);
						}
						$b['start'] = htmlentities( $b['start'], ENT_COMPAT, get_option( 'blog_charset' ) );
						if ( isset( $b['end'] ) ) {
							$b['end'] = htmlentities( $b['end'], ENT_COMPAT, get_option( 'blog_charset' ) );
						}
						if ( ! isset( $b['access'] ) ) {
							$b['access'] = '';
						}
						$b['access'] = htmlentities( $b['access'], ENT_COMPAT, get_option( 'blog_charset' ) );
						if ( ! isset( $b['order'] ) ) {
							$b['order'] = 0;
						}
						$b['order'] = (int) $b['order'];
						if ( ! isset( $b['visual'] ) ) {
							$b['visual'] = 0;
						}
						$b['visual'] = (int) $b['visual'];
						$checked     = '';
						if ( 1 === $b['visual'] ) {
							$checked = ' checked="checked"';
						}

						// Loop about the post types, create html an values.
						$pt_checkboxes = '';
						foreach ( $this->get_post_types_for_js() as $post_type ) {
							if ( ! isset( $b[ $post_type ] ) ) {
								$b[ $post_type ] = 0;
							}

							$b[ $post_type ] = (int) $b[ $post_type ];

							$pt_checked = '';
							if ( 1 === $b[ $post_type ] ) {
								$pt_checked = ' checked="checked"';
							}

							$pt_checkboxes .= '<td class="num"><input type="checkbox" name="' .
								self::$option_string . '[buttons][' .
								$i . '][' . $post_type . ']" value="1" ' .
								$pt_checked . '/></td>' . "\n";
						}

						echo '
					<tr id="rmqtb' . $i . '">
						<td>
						<input type="text" placeholder="' . esc_html__( 'Button Label', 'addquicktag' )
							. '" name="' . self::$option_string . '[buttons][' . $i
							. '][text]" value="' . $b['text'] . '" /><br />
						<input class="small" id="dashicons_picker_icon_' . $i
							. '" type="text" placeholder="' . esc_html__( 'Dashicon', 'addquicktag' )
							. '" name="' . self::$option_string . '[buttons][' . $i
							. '][dashicon]" value="' . $b['dashicon'] . '" />
						<button type="button" data-target="#dashicons_picker_icon_'
							. $i . '"class="button dashicons-picker dashicons dashicons-dashboard"></button>
						<br />
						<input type="text" placeholder="' . esc_html__( 'Title Attribute', 'addquicktag' )
							. '" name="' . self::$option_string . '[buttons][' . $i . '][title]" value="'
							. $b['title'] . '" />
						</td>
						<td>
						<textarea placeholder="' . esc_html__( 'Start Tag(s)*', 'addquicktag' )
							. '" class="code" name="' . self::$option_string . '[buttons][' . $i
							. '][start]" rows="2" cols="25" >' . $b['start'] . '</textarea><br />
						<textarea placeholder="' . esc_html__( 'End Tag(s)', 'addquicktag' )
							. '" class="code" name="' . self::$option_string . '[buttons][' . $i
							. '][end]" rows="2" cols="25" >' . $b['end'] . '</textarea>
						</td>
						<td>
						<input placeholder="' . esc_html__( 'Access Key', 'addquicktag' )
							. '" class="small-text" type="text" name="' . self::$option_string . '[buttons][' . $i
							. '][access]" value="' . $b['access'] . '" /><br />
						<input placeholder="' . esc_html__( 'Order', 'addquicktag' )
							. '" class="small-text" type="text" name="' . self::$option_string . '[buttons][' . $i
							. '][order]" value="' . $b['order'] . '" />
						</td>
						<td class="num">
						<input type="checkbox" name="' . self::$option_string . '[buttons][' . $i
							. '][visual]" value="1"' . $checked . '/>' .
						'</td>' .
						$pt_checkboxes . '
						<td class="num">
						<input type="checkbox" class="toggle" id="select_all_' . $i . '" value="' . $i . '" />
						</td>' . '
					</tr>
					';
					}

					// Loop about the post types, create html an values for empty new checkboxes.
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
					<tr id="rmqtb<?php echo $i; ?>">
						<td>
							<input type="text" placeholder="<?php esc_html_e( 'Button Label*', 'addquicktag' ); ?>"
								name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][text]" value="" /><br />
							<input type="text" class="small" id="dashicons_picker_icon_new" placeholder="
							<?php esc_html_e( 'Dashicon', 'addquicktag' ); ?>
							" name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][dashicon]" value="" />
							<button type="button" data-target="#dashicons_picker_icon_new"
								class="button dashicons-picker dashicons dashicons-dashboard"></button>
							<br />
							<input type="text" placeholder="<?php esc_html_e( 'Title Attribute', 'addquicktag' ); ?>"
								name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][title]" value="" />
						</td>
						<td>
							<textarea placeholder="<?php esc_html_e( 'Start Tag(s)*', 'addquicktag' ); ?>"
								class="code" name="<?php echo self::$option_string; ?>[buttons][
															  <?php	echo $i; ?>
							][start]" rows="2" cols="25"></textarea><br />
							<textarea placeholder="<?php esc_html_e( 'End Tag(s)', 'addquicktag' ); ?>"
								class="code" name="<?php echo self::$option_string; ?>[buttons][
															  <?php	echo $i; ?>
							][end]" rows="2" cols="25"></textarea>
						</td>
						<td>
							<input type="text" placeholder="
							<?php
							esc_html_e(
								'Access Key',
								'addquicktag'
							);
							?>
							"
								title="<?php esc_html_e( 'Access Key', 'addquicktag' ); ?>"
								class="small-text" name="<?php echo self::$option_string; ?>[buttons][
																	<?php
																	echo $i;
																	?>
							][access]" value="" /><br />
							<input type="text" placeholder="<?php esc_html_e( 'Order', 'addquicktag' ); ?>"
								title="<?php esc_html_e( 'Order', 'addquicktag' ); ?>" class="small-text"
								name="<?php echo self::$option_string; ?>[buttons][<?php echo $i; ?>][order]" value="" />
						</td>
						<td class="num">
							<label>
								<input type="checkbox" name="<?php echo self::$option_string; ?>[buttons][
																		<?php
																		echo $i;
																		?>
								][visual]" value="1" />
							</label>
						</td>
						<?php echo $pt_new_boxes; ?>
						<td class="num">
							<label for="select_all_<?php echo $i; ?>">
								<input type="checkbox" class="toggle" id="select_all_<?php echo $i; ?>" value="<?php echo $i; ?>" />
							</label>
						</td>
					</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes', 'addquicktag' ); ?>" />
				</p>

				<?php do_action( 'addquicktag_settings_form_page', $options ); ?>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes', 'addquicktag' ); ?>" />
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
	 * Return information to donate.
	 *
	 * @uses   _e,esc_attr_e
	 * @access public
	 * @since  2.0.0
	 * @return void
	 */
	public function get_plugin_infos() {

		?>
		<div class="postbox">

			<h3><span><?php esc_html_e( 'Like this plugin?', 'addquicktag' ); ?></span></h3>

			<div class="inside">
				<p><?php esc_html_e( 'Here\'s how you can give back:', 'addquicktag' ); ?></p>
				<ul>
					<li>
						<a href="http://wordpress.org/support/view/plugin-reviews/addquicktag" title="
						<?php
						esc_html_e(
							'The Plugin on the WordPress plugin repository',
							'addquicktag'
						);
						?>
						"><?php esc_html_e( 'Give the plugin a good rating.', 'addquicktag' ); ?></a>
					</li>
					<li>
						<a href="http://wordpress.org/support/plugin/addquicktag" title="
						<?php
						esc_html_e(
							'Help inside the community other useres and write answer to this plugin questions.',
							'addquicktag'
						);
						?>
						">
						<?php
						esc_html_e(
							'Help other users in the Support Forum.',
							'addquicktag'
						);
						?>
							</a>
					</li>
					<li>
						<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=6069955" title="
						<?php
						esc_html_e(
							'Donate via PayPal',
							'addquicktag'
						);
						?>
						"><?php esc_html_e( 'Donate a few euros.', 'addquicktag' ); ?></a>
					</li>
					<li>
						<a href="http://www.amazon.de/gp/registry/3NTOGEK181L23/ref=wl_s_3" title="
						<?php
						esc_html_e(
							'Frank Bültge\'s Amazon Wish List',
							'addquicktag'
						);
						?>
						">
						<?php
						esc_html_e(
							'Get me something from my wish list.',
							'addquicktag'
						);
						?>
							</a>
					</li>
					<li>
						<a href="https://github.com/bueltge/AddQuicktag" title="
						<?php
						esc_html_e(
							'Please give me feedback, contribute and file technical bugs on this GitHub Repo, use Issues.',
							'addquicktag'
						);
						?>
						">
						<?php
						esc_html_e(
							'Github Repo for Contribute, Issues & Bugs',
							'addquicktag'
						);
						?>
							</a>
					</li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Return information about the plugin.
	 *
	 * @uses   _e, esc_attr_e
	 * @access public
	 * @since  2.0.0
	 * @return void
	 */
	public function get_about_plugin() {

		?>
		<div class="postbox">

			<h3><span><?php esc_html_e( 'About this plugin', 'addquicktag' ); ?></span></h3>

			<div class="inside">
				<p>
					<strong><?php esc_html_e( 'Version:', 'addquicktag' ); ?></strong>
					<?php echo parent::get_plugin_data( 'Version' ); ?>
				</p>

				<p>
					<strong><?php esc_html_e( 'Description:', 'addquicktag' ); ?></strong>
					<?php echo parent::get_plugin_data( 'Description' ); ?>
				</p>
			</div>

		</div>
		<?php
	}

	/**
	 * Save network settings.
	 *
	 * @uses   update_site_option, wp_redirect, add_query_arg, network_admin_url
	 * @access public
	 * @since  2.0.0
	 * @return void
	 */
	public function save_network_settings_page() {
		if ( null !== wp_unslash( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), self::$nonce_string ) ) {
			wp_die( 'Sorry, you failed the nonce test.' );
		}

		// Validate options.
		if ( isset( $_POST[ self::$option_string ] ) ) {
        	$value = $this->validate_settings( wp_unslash( $_POST[ self::$option_string ] ) );

            // Update options.
        	update_site_option( self::$option_string, $value );
        }
		
		// Redirect to settings page in network.
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => plugin_basename( __FILE__ ),
					'updated' => 'true',
				),
				network_admin_url( 'settings.php' )
			)
		);
		exit();
	}

	/**
	 * Retrun string vor update message.
	 *
	 * @uses
	 * @access public
	 * @since  2.0.0
	 * @return string $notice
	 */
	public function get_network_admin_notices() {

		// If updated and the right page.
		if ( array_key_exists(
			'updated',
			$_GET
		)
			&& 'settings_page_addquicktag/inc/class-settings-network' === $GLOBALS['current_screen']->id
		) {
			$message = esc_html__( 'Options saved.', 'addquicktag' );
			echo '<div id="message" class="updated"><p>' . $message . '</p></div>';
		}
	}

	/**
	 * Validate settings for options.
	 *
	 * @uses   normalize_whitespace
	 * @access public
	 *
	 * @param  array $value String for validation.
	 *
	 * @since  2.0.0
	 * @return string $value
	 */
	public function validate_settings( $value ) {

		// Save core buttons changes.
		if ( array_key_exists( 'core_buttons', $value ) ) {
			$core_buttons = $value['core_buttons'];
		}

		// Save Code buttons.
		if ( array_key_exists( 'code_buttons', $value ) ) {
			$code_buttons = $value['code_buttons'];
		}

		// Set allowed values for import, only the defaults of plugin and custom post types.
		$allowed_settings = (array) array_merge(
			$this->get_post_types_for_js(),
			array( 'text', 'dashicon', 'title', 'start', 'end', 'access', 'order', 'visual' )
		);

		$buttons = array();
		// Filter for allowed values.
		foreach ( (array) $value['buttons'] as $key => $button ) {
			foreach ( (array) $button as $label => $val ) {
				if ( ! in_array( $label, $allowed_settings, true ) ) {
					unset( $button[ $label ] );
				}
			}

			$buttons[] = $button;
		}

		// Return filtered array.
		$filtered_values['buttons'] = $buttons;
		$value                      = $filtered_values;

		$buttons   = array();
		$c_buttons = count( $value['buttons'] );
		for ( $i = 0; $i < $c_buttons; $i ++ ) {
			$b = $value['buttons'][ $i ];
			if ( ! empty( $b['start'] ) ) {
				$b['text']     = sanitize_text_field( $b['text'] );
				$b['dashicon'] = sanitize_text_field( $b['dashicon'] );
				$b['title']    = sanitize_text_field( $b['title'] );
				$b['start']    = wp_kses_stripslashes( $b['start'] );
				$b['end']      = wp_kses_stripslashes( $b['end'] );

				if ( array_key_exists( 'access', $b ) ) {
					$b['access'] = esc_html( $b['access'] );
				}

				if ( array_key_exists( 'order', $b ) ) {
					$b['order'] = (int) $b['order'];
				}

				if ( array_key_exists( 'visual', $b ) ) {
					$b['visual'] = (int) $b['visual'];
				} else {
					$b['visual'] = 0;
				}

				foreach ( $this->get_post_types_for_js() as $post_type ) {
					if ( array_key_exists( $post_type, $b ) ) {
						$b[ $post_type ] = (int) $b[ $post_type ];
					} else {
						$b[ $post_type ] = 0;
					}
				}

				$buttons[] = $b;
			}
		}
		$value['buttons'] = $buttons;
		// Check for wrong empty values and kill.
		foreach ( $value['buttons'] as $key => $b ) {
			if ( empty( $b['text'] ) && empty( $b['start'] ) ) {
				unset( $value['buttons'][ $key ] );
			}
		}
		// Reorder the array.
		$value['buttons'] = array_values( $value['buttons'] );

		// Filter core button values, strings and convert to integer.
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
			foreach ( (array) $core_buttons as $key => $var ) {
				$core_button = array();
				foreach ( (array) $var as $post_type => $val ) {
					$core_button[ $post_type ] = (int) $val;
				}

				$filtered_core_buttons[ $key ] = $core_button;
			}

			$value['core_buttons'] = $filtered_core_buttons;
		}

		// Filter code button values, strings and convert to integer.
		if ( ! empty( $code_buttons ) ) {
			$filtered_code_buttons = array();
			foreach ( (array) $code_buttons as $key => $var ) {
				$code_button = array();
				foreach ( (array) $var as $post_type => $val ) {
					$code_button[ $post_type ] = (int) $val;
				}

				$filtered_code_buttons[ $key ] = $code_button;
			}

			$value['code_buttons'] = $filtered_code_buttons;
		}

		return $value;
	}

	/**
	 * Register settings for options.
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
	 * Unregister and delete settings; clean database.
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

	/**
	 * Enqueue scripts and stylesheets.
	 *
	 * @since    0.0.2
	 *
	 * @internal param $where
	 */
	public function print_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.dev' : '';

		wp_register_script(
			self::$option_string . '_dashicon_picker',
			plugins_url( '/js/dashicons-picker' . $suffix . '.js', parent::get_plugin_string() ),
			array( 'jquery' ),
			'2021-04-26',
			true
		);

		wp_register_script(
			self::$option_string . '_admin_script',
			plugins_url( '/js/settings' . $suffix . '.js', parent::get_plugin_string() ),
			array( 'jquery', 'quicktags' ),
			'2021-04-26',
			true
		);
		wp_enqueue_script( self::$option_string . '_dashicon_picker' );
		wp_enqueue_script( self::$option_string . '_admin_script' );

		wp_register_style(
			self::$option_string . '_dashicon_picker',
			plugins_url( '/css/dashicons-picker' . $suffix . '.css', parent::get_plugin_string() ),
			array( 'dashicons' ),
			'2021-04-26',
			'screen'
		);

		wp_register_style(
			self::$option_string . '_admin_style',
			plugins_url( '/css/settings' . $suffix . '.css', parent::get_plugin_string() ),
			array(),
			'2021-04-26',
			'screen'
		);
		wp_enqueue_style( self::$option_string . '_dashicon_picker' );
		wp_enqueue_style( self::$option_string . '_admin_style' );
	}

}

$add_quicktag_settings = Add_Quicktag_Settings::get_object();
