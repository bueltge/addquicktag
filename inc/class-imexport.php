<?php
/**
 * AddQuicktag - Settings
 *
 * @license    GPLv3
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
 * Class Add_Quicktag_Im_Export
 */
class Add_Quicktag_Im_Export extends Add_Quicktag_Settings {

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
	 * Store string for plugin file.
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
	 * Handler for the action 'init'. Instantiates this class.
	 *
	 * @access  public
	 * @since   2.0.0
	 * @return \Add_Quicktag|\Add_Quicktag_Im_Export|\Add_Quicktag_Settings $instance
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
		self::$option_string     = parent::get_option_string();
		self::$plugin            = parent::get_plugin_string();
		self::$post_types_for_js = parent::get_post_types_for_js();

		if ( isset( $_GET['addquicktag_download'] ) && check_admin_referer( parent::$nonce_string ) ) {
			$this->get_export_file();
		}

		if ( isset( $_POST['addquicktag_import'] ) && check_admin_referer( parent::$nonce_string ) ) {
			$this->import_file();
		}

		add_action( 'addquicktag_settings_page', array( $this, 'get_im_export_part' ) );
	}

	/**
	 * Get markup for ex- and import on settings page.
	 *
	 * @access  public
	 * @since   2.0.0
	 * @uses    wp_nonce_field
	 */
	public function get_im_export_part() {

		?>
		<div class="postbox">
			<h3><span><?php esc_html_e( 'Export', 'addquicktag' ); ?></span></h3>

			<div class="inside">
				<p><?php esc_html_e( 'When you click the button below the plugin will create an JSON file for you to save to your computer.', 'addquicktag' ); ?></p>

				<p><?php esc_html_e( 'This format, a custom JSON, will contain your options from quicktags.', 'addquicktag' ); ?></p>

				<p><?php esc_html_e( 'Once you’ve saved the download file, you can use the Import function in another WordPress installation to import this site.', 'addquicktag' ); ?></p>

				<form method="get" action="">
					<?php wp_nonce_field( parent::$nonce_string ); ?>
					<p class="submit">
						<input type="submit" name="submit" value="<?php esc_html_e( 'Download Export File', 'addquicktag' ); ?> &raquo;" />
						<input type="hidden" name="addquicktag_download" value="true" />
					</p>
				</form>
			</div>
		</div>

		<div class="postbox">
			<h3><span><?php esc_html_e( 'Import', 'addquicktag' ); ?></span></h3>

			<div class="inside">
				<p><?php esc_html_e( 'If you have quicktags from other installs, the plugin can import those into this site. To get started, choose a file to import. (json-Format)', 'addquicktag' ); ?></p>

				<form method="post" action="" enctype="multipart/form-data">
					<?php wp_nonce_field( parent::$nonce_string ); ?>
					<p class="submit">
						<input type="file" name="import_file" />
						<input type="submit" name="submit" value="<?php esc_html_e( 'Upload file and import', 'addquicktag' ); ?> &raquo;" />
						<input type="hidden" name="addquicktag_import" value="true" />
					</p>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Build export file, json
	 *
	 * @access  public
	 * @since   2.0.0
	 */
	public function get_export_file() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		check_admin_referer( parent::$nonce_string );

		if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) ) {
			$options = get_site_option( self::$option_string );
		} else {
			$options = get_option( self::$option_string );
		}

		ignore_user_abort( true );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=addquicktag.-' . gmdate( 'm-d-Y' ) . '.json' );
		header( 'Expires: 0' );

		echo json_encode( $options );
		exit;
	}

	/**
	 * Import json and update settings
	 *
	 * @access   public
	 * @since    2.0.0
	 *
	 * @internal param bool|string $filename
	 *
	 * @uses     current_user_can, wp_die, is_plugin_active_for_network, update_site_option, update_option
	 * @return  void
	 */
	public function import_file() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Options not update - you don&lsquo;t have the privileges to do this!', 'addquicktag' ) );
		}

		check_admin_referer( parent::$nonce_string );

		if ( ! isset( $_FILES ) || ! isset( $_FILES['import_file']['name'] ) ) {
			wp_die( esc_html__( 'Please upload a file to import.', 'addquicktag' ) );
		}

		$extension = explode( '.', sanitize_file_name( wp_unslash( $_FILES['import_file']['name'] ) ) );
		$extension = end( $extension );

		if ( 'json' !== $extension ) {
			wp_die( esc_html__( 'Please upload a valid .json file', 'addquicktag' ) );
		}

		if ( ! isset( $_FILES['import_file']['tmp_name'] ) ) {
			wp_die( esc_html__( 'Please upload a file to import.', 'addquicktag' ) );
		}

		$import_file = sanitize_file_name( wp_unslash( $_FILES['import_file']['tmp_name'] ) );

		if ( empty( $import_file ) ) {
			wp_die( esc_html__( 'Please upload a file to import.', 'addquicktag' ) );
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$options = (array) json_decode( file_get_contents( $import_file ), true );

		if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) ) {
			update_site_option( self::$option_string, $options );
		} else {
			update_option( self::$option_string, $options );
		}

		// Redirect to settings page in network.
		if ( is_multisite() && is_plugin_active_for_network( self::$plugin ) ) {
			wp_safe_redirect(
				add_query_arg(
					array(
						'page'    => self::$plugin,
						'updated' => 'true',
					),
					network_admin_url( 'settings.php' )
				)
			);
		} else {
			$page = str_replace( basename( __FILE__ ), 'class-settings.php', plugin_basename( __FILE__ ) );
			wp_safe_redirect(
				add_query_arg(
					array(
						'page'    => $page,
						'updated' => 'true',
					),
					admin_url( 'options-general.php' )
				)
			);
		}
		exit;
	}

} // end class

$add_quicktag_im_export = Add_Quicktag_Im_Export::get_object();
