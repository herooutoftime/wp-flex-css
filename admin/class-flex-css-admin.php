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
	private $configs = array();
	private $perms = array();
	private $matches = array();

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
		$this->configs = array(
			'file' => Flex_Css_Helper::get_file(),
			'versioning' => (bool) get_option('flex_css_versioning'),
			'simulate' => (bool) get_option('flex_css_simulate'),
		);
		$this->check_perms();
		$this->matches = Flex_Css_Helper::get_matches();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style('dashicons');
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_style( $this->plugin_name . '_cm', plugin_dir_url( __FILE__ ) . 'bower_components/codemirror/lib/codemirror.css', array(), $this->version );
		wp_enqueue_style($this->plugin_name . '_cm_fold', plugin_dir_url( __FILE__ ) . 'bower_components/codemirror/addon/fold/foldgutter.css', array(), $this->version);
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/flex-css-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script($this->plugin_name . '_cm', plugin_dir_url( __FILE__ ) . 'bower_components/codemirror/lib/codemirror.js', array('jquery', 'backbone'));
		wp_enqueue_script($this->plugin_name . '_cm_css', plugin_dir_url( __FILE__ ) . 'bower_components/codemirror/mode/css/css.js', array($this->plugin_name . '_cm'));
		wp_enqueue_script($this->plugin_name . '_cm_fold', plugin_dir_url( __FILE__ ) . 'bower_components/codemirror/addon/fold/foldcode.js', array($this->plugin_name . '_cm'));
		wp_enqueue_script($this->plugin_name . '_cm_panel', plugin_dir_url( __FILE__ ) . 'bower_components/codemirror/addon/display/panel.js', array($this->plugin_name . '_cm'));
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/flex-css-admin.js', array( 'jquery', 'backbone', 'jquery-ui-autocomplete', 'wp-color-picker' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'FlexCSS', array('ajaxurl' => admin_url('admin-ajax.php')) );
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
		add_settings_field(
			$this->option_name . '_file',
			__( 'Choose file', 'flex-css' ),
			array( $this, $this->option_name . '_file_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_file' )
		);

//		add_settings_field(
//			$this->option_name . '_versioning',
//			__( 'Enable versioning', 'flex-css' ),
//			array( $this, $this->option_name . '_versioning_cb' ),
//			$this->plugin_name,
//			$this->option_name . '_general',
//			array( 'label_for' => $this->option_name . '_versioning' )
//		);

		add_settings_field(
			$this->option_name . '_simulate',
			__( 'Enable simulate', 'flex-css' ),
			array( $this, $this->option_name . '_simulate_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_simulate' )
		);

		add_settings_field(
			$this->option_name . '_hits',
			__('Variables in CSS found', 'flex-css'),
			array($this, $this->option_name . '_hits_cb'),
			$this->plugin_name,
			$this->option_name . '_general',
			array('label_for' => $this->option_name . '_hits')
		);

		add_settings_section(
			$this->option_name . '_general',
			__( 'General', 'flex-css' ),
			array( $this, $this->option_name . '_general_cb' ),
			$this->plugin_name
		);
		register_setting( $this->plugin_name, $this->option_name . '_file', array( $this, $this->option_name . '_sanitize_file' )  );
		register_setting( $this->plugin_name, $this->option_name . '_data', array( $this, $this->option_name . '_sanitize_data' ) );
		register_setting( $this->plugin_name, $this->option_name . '_versioning' );
		register_setting( $this->plugin_name, $this->option_name . '_simulate' );
	}

	public function flex_css_file_cb()
	{
		echo Flex_Css_Helper::get_css_files_select();
	}

	public function flex_css_versioning_cb()
	{
		$checked = get_option('flex_css_versioning') ? 'checked' : '';
		echo '<input type="checkbox" ' . $checked . ' name="' . $this->option_name . '_versioning' . '" id="' . $this->option_name . '_versioning' . '" value="1">';
	}

	public function flex_css_simulate_cb()
	{
		$checked = get_option('flex_css_simulate') ? 'checked' : '';
		echo '<input type="checkbox" ' . $checked . ' name="' . $this->option_name . '_simulate' . '" id="' . $this->option_name . '_simulate' . '" value="1">';
	}

	public function flex_css_hits_cb()
	{
		$results = $this->matches;
		$css_vars = array_column(maybe_unserialize(get_option('flex_css_data')), 'property');
		array_walk($results['vars'], function($var, $idx, $css_vars) {
			$used_bg = '';
			if(in_array($var, $css_vars))
				$used_bg = ' style="background:#3b9b7c;color:white"';
			echo "<code {$used_bg}>{$var}</code>, ";
		}, $css_vars);
		echo '<p class="description">' . __('Green variables are in use', 'flex-css') . '</p>';
	}
	public function flex_css_data_cb() {
		echo '';
//		echo '<p>' . __( 'Please change the settings accordingly.', 'flex-css' ) . '</p>';
	}
	public function flex_css_general_cb() {
		echo '<p>' . __( 'Please change the settings accordingly.', 'flex-css' ) . '</p>';
	}
	public function flex_css_sanitize_data($data)
	{
		array_walk($data, function(&$item, $idx, $css_vars) {
			if(!in_array($item['property'], $css_vars)) {
				$this->add_messages(array(
					'flex-css-error-no-var',
					'flex-css-error-no-var',
					sprintf(__('<code>%1$s</code> was not found in CSS. Add <code>/*! @edit:%1$s*/</code>', 'flex-css'), $item['property']),
					'error'
				));
			}
			if($item['value'] == '' || empty($item['value'])) {
				$this->add_messages(array(
					'flex-css-error-empty-val',
					'flex-css-error-empty-val',
					sprintf(__('<code>%1$s</code> has no value. Add a value in the settings below.', 'flex-css'), $item['property']),
					'error'
				));
			}

			$item['value'] = addslashes($item['value']);
		}, $this->matches['vars']);
		return $data;
	}

	public function flex_css_sanitize_file($file)
	{
		if($file != '' || !empty($file) || !file_exists($file))
			return $file;

		$this->add_messages(array(
			'flex-css-error-empty-file',
			'flex-css-error-empty-file',
			__('No CSS file chosen.', 'flex-css'),
			'error'
		));
		return null;
	}

	public function update_option($first, $second)
	{
		$mode = 'update';
		if($first == 'flex_css_data')
			$mode = 'create';

		if(!$this->check_perms()) {
			$this->add_messages(array(
				'flex-css-error-perms',
				'flex-css-error-perms',
				__('Please make sure your CSS file exists, is writeable and its folder is writeable!', 'flex-css'),
				'error'
			));
			return;
		}
		if($this->edit_css())
			$this->add_messages(array(
				'flex-css-success-bup',
				'flex-css-success-bup',
				__('Backup of original CSS file was succesfully created', 'flex-css'),
				'updated'
			));
	}

	public function flex_css_getvars()
	{
		$results = array();
//		$items = array_column(maybe_unserialize(get_option('flex_css_data')), 'property');
		$items = $this->get_matches()['vars'];
		foreach ($items as $result) {
			if(!empty($_GET['term']) || $_GET['term'] != '')
				if (strpos($result, $_GET['term']) === false)
					continue;

			$results[] = array(
				'label' => $result,
				'value' => $result
			);
		}
		echo json_encode($results);
		wp_die();
	}

	public function flex_css_get_file_contents()
	{
		$file = Flex_Css_Helper::get_file();
//		$file = get_template_directory() . '/style.min.css';
		$fh = fopen($file, 'r');
		$first = fgets($fh);
		$linecount = 0;
		$editable = array();
		while(!feof($fh)) {
			$line = fgets($fh);
			$linecount++;
			if(strpos($line, '@edit:') !== FALSE)
				$editable[] = $linecount+1;
		}
		fclose($fh);
		$results = array(
			'minified' => !preg_match('/\\n/', substr($first, 0, 1024)),
			'linenumbers' => $linecount,
			'editable' => $editable,
			'content' => file_get_contents($file),
		);
		echo json_encode($results);
		wp_die();
	}

	public function get_matches()
	{
		$css_contents = file_get_contents($this->configs['file']);
		$orig_contents = $css_contents;
		$strip_whitespace = false;
		if($strip_whitespace)
			preg_replace('/\s+/', '', $css_contents);

		$css_vars = Flex_Css_Helper::get_option(true, false);
		foreach($css_vars as $css_var) {
			$css[$css_var['property']] = $css_var['value'];
		}

		$keys = implode('|', array_keys($css));
		$pattern = '/@edit:\s*([\w]+)\s*\*\/(.*?);/s';
		$hits = preg_match_all($pattern, $css_contents, $matches);
		list($combined, $vars, $properties) = $matches;
		return compact('combined', 'vars', 'properties', 'hits');
	}

	public function edit_css()
	{
		$css_contents = file_get_contents($this->configs['file']);
		$orig_contents = $css_contents;
		$strip_whitespace = false;
		if($strip_whitespace)
			preg_replace('/\s+/', '', $css_contents);

		$css_vars = Flex_Css_Helper::get_option(true, false);
		foreach($css_vars as $css_var) {
			$css[$css_var['property']] = $css_var['value'];
		}

		$keys = implode('|', array_keys($css));
//    $pattern = '/([\w#-]+)[;]?\/\*\![\s+]@edit:([\w]+)[\s+]\*\//';
//    $pattern = '/([\w#-]+)?[;]\s?\/\*!\s?@edit:([\w]+) \*\//';
//    $pattern = '/([\w#-]+)?[;].+?\/\*!\s?@edit:([\w]+) \*\//';
//    $pattern = '/\/\*\![\s]?@edit:\s?([\w]+).+?\*\/.+?([\w#-]+)?;/';
//    $pattern = '/\/\*\![\s]?@edit:\s?([\w]+)(.+?):([\w#-]+)/';
//    $pattern = '/@edit:([\w]+)\*\/.+?(.+?):([\w#-]+)/';
//    $pattern = '/@edit:([\w]+).+?([\w]+):([\w#-]+);/s';
		$pattern = '/@edit:\s*([\w]+)\s*\*\/(.*?);/s';
		$css_contents = preg_replace_callback($pattern, function ($matches) use ($css) {
			list($attribute, $value) = explode(':', $matches[2]);
			if(!empty($css[$matches[1]])) {
				$return = "@edit: {$matches[1]}*/{$attribute}: {$css[$matches[1]]};";
			} else {
				$return = "@edit: {$matches[1]}*/{$attribute}:{$value};";
			}
			return $return;
		}, $css_contents);

		if($this->configs['simulate'])
			return;

		if(Flex_Css_Helper::backup())
			file_put_contents($this->configs['file'], $css_contents);

	}

	public function flex_restore()
	{
		if(file_exists(get_template_directory() . '/style.orig.css') && is_writable($this->configs['file'])) {
			rename($this->configs['file'], get_template_directory() . '/style.latest.css');
			rename(get_template_directory() . '/style.orig.css', $this->configs['file']);
		}
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();
	}

	public function check_perms()
	{
		$this->perms = array(
			'exist' => file_exists($this->configs['file']),
			'write' => is_writable($this->configs['file']),
			'dir_write' => is_writeable(get_template_directory()),
		);
		$this->perms['pass'] = count(array_unique($this->perms)) === 1;
//		if(!$this->perms['pass']) {
//			$this->add_messages(array(
//				'flex-css-error-perms',
//				'flex-css-error-perms',
//				__('Please make sure your CSS file exists, is writeable and its folder is writeable!', 'flex-css'),
//				'error'
//			));
//		}
		return count(array_unique($this->perms)) === 1;
	}

	public function add_messages($msg)
	{
		list($setting, $code, $message, $type) = $msg;
		add_settings_error($setting, $code, $message, $type);
		return;
	}

	public function admin_notices()
	{
		settings_errors();
		$screen = get_current_screen();
		if(!$this->perms['pass']) { ?>
			<div class="notice notice-error is-dismissible">
				<p><?php _e('Please make sure your CSS file exists, is writeable and its folder is writeable!', 'flex-css'); ?></p>
			</div>
		<?php }
	}
}
