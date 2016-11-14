<?php

/**
 * Class Flex_Css_Helper
 */
class Flex_Css_Helper {

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
}