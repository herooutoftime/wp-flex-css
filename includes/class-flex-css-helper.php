<?php

/**
 * Class Flex_Css_Helper
 */
class Flex_Css_Helper {

	private static $option_name = 'flex_css';

	/**
	 * @return mixed
	 */
	public static function get_option($slashes = true, $ent = false)
	{
		$data = maybe_unserialize(get_option('flex_css_data'));
		foreach ($data as &$item) {
			if($slashes) $item['value'] = stripslashes($item['value']);
			if($ent) $item['value'] = htmlentities($item['value']);
		}
		return $data;
	}

	public static function get_file()
	{
		return get_option('flex_css_file');
	}

	public static function get_css_files()
	{
		$it = new RecursiveDirectoryIterator(get_template_directory());
		$display = array('css');
		$files = array();
		foreach(new RecursiveIteratorIterator($it) as $file)
		{
			if (in_array(strtolower(array_pop(explode('.', $file))), $display))
				$files[] = $file;
		}
		return $files;
	}

	public static function get_css_files_select()
	{
		$files = self::get_css_files();
		$current_file = get_option('flex_css_file');
		$opts[] = "<option value=''>" . __('Choose stylesheet') . "</option>";
		foreach($files as $file)
		{
			$file_nice = str_replace(trailingslashit(get_template_directory()), '', $file);
			$opts[] = "<option " . selected( $current_file, $file, false ) . " value='{$file}'>{$file_nice}</option>";
		}
		return '<select name="' . self::$option_name . '_file">' . implode('', $opts) . '</select>';
	}

	public static function backup()
	{
		$file = self::get_file();
		$fi = pathinfo($file);
		$bup_file_path = trailingslashit($fi['dirname']) . $fi['filename'] . '.orig.' . $fi['extension'];
		if(!is_writable($fi['dirname']))
			return false;
		if(file_exists($bup_file_path))
			return true;
//		if($this->configs['versioning'])
			file_put_contents($bup_file_path, $orig_contents);
		return true;
	}

	public static function get_matches()
	{
		$css_contents = file_get_contents(self::get_file());
		$orig_contents = $css_contents;
		$strip_whitespace = false;
		if($strip_whitespace)
			preg_replace('/\s+/', '', $css_contents);

		$css_vars = self::get_option(true, false);
		foreach($css_vars as $css_var) {
			$css[$css_var['property']] = $css_var['value'];
		}

		$keys = implode('|', array_keys($css));
		$pattern = '/@edit:\s*([\w]+)\s*\*\/(.*?);/s';
		$hits = preg_match_all($pattern, $css_contents, $matches);
		list($combined, $vars, $properties) = $matches;
		return compact('combined', 'vars', 'properties', 'hits');
	}

	public static function get_match($property)
	{
		if(in_array($property, self::get_matches()['vars']))
			return true;
		return false;
	}
}