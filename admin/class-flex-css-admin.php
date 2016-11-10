<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.herooutoftime.com
 * @since      1.0.0
 *
 * @package    Flex_Css
 * @subpackage Flex_Css/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Flex_Css
 * @subpackage Flex_Css/admin
 * @author     Andreas Bilz <andreas.bilz@gmail.com>
 */
class Flex_Css_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $option_name = 'flex_css';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Flex_Css_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Flex_Css_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/flex-css-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Flex_Css_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Flex_Css_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/flex-css-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function add_options_page() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Flex CSS Settings', 'flex-css' ),
			__( 'Flex CSS', 'flex-css' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_options_page' )
		);
	}

	public function display_options_page() {
		include_once 'partials/flex-css-admin-display.php';
	}

	public function register_setting()
	{
		add_settings_section(
			$this->option_name . '_general',
			__( 'General', 'flex-css' ),
			array( $this, $this->option_name . '_general_cb' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_position',
			__( 'Text position', 'flex-css' ),
			array( $this, $this->option_name . '_position_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_position' )
		);
		add_settings_field(
			$this->option_name . '_day',
			__( 'Post is outdated after', 'flex-css' ),
			array( $this, $this->option_name . '_day_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_day' )
		);
		register_setting( $this->plugin_name, $this->option_name . '_position', array( $this, $this->option_name . '_sanitize_position' ) );
		register_setting( $this->plugin_name, $this->option_name . '_day', 'intval' );

//		var_dump(get_option( $this->option_name . '_position' ));
//		var_dump(get_option( $this->option_name . '_day' ));
	}

	public function flex_css_general_cb() {
		echo '<p>' . __( 'Please change the settings accordingly.', 'flex-css' ) . '</p>';
	}
	public function flex_css_position_cb() {
		?>
		<fieldset>
			<label>
				<input type="radio" name="<?php echo $this->option_name . '_position' ?>" id="<?php echo $this->option_name . '_position' ?>" value="before">
				<?php _e( 'Before the content', 'flex-css' ); ?>
			</label>
			<br>
			<label>
				<input type="radio" name="<?php echo $this->option_name . '_position' ?>" value="after">
				<?php _e( 'After the content', 'flex-css' ); ?>
			</label>
		</fieldset>
		<?php
	}
	public function flex_css_day_cb() {
		echo '<input type="text" name="' . $this->option_name . '_day' . '" id="' . $this->option_name . '_day' . '"> '. __( 'days', 'flex-css' );
	}
	public function flex_css_sanitize_position( $position ) {
		if ( in_array( $position, array( 'before', 'after' ), true ) ) {
			return $position;
		}
	}
}
