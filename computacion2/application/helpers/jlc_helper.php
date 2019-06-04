<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('lm'))
{

	function lm($message)
	{
		log_message('error', print_r($message, TRUE));
	}
}

if (!function_exists('pm')) {

	function pm($message) {
		echo '<pre>';
		print_r($message);
		echo '</pre>';
	}
}


if (!function_exists('auto_ver'))
{

	function auto_ver($url)
	{
		$path = pathinfo($url);
		$string = $path['basename'];
		$ver = '.version' . filemtime(SIS_AUTO_VER_PATH . $url) . '.';
		$str = '.';
		if (( $pos = strrpos($string, $str) ) !== FALSE)
		{
			$search_length = strlen($str);
			$str = substr_replace($string, $ver, $pos, $search_length);
			return $path['dirname'] . '/' . $str;
		}
		else
		{
			return $url;
		}
	}
}

/* End of file zetta_helper.php */
/* Location: ./application/helpers/zetta_helper.php */