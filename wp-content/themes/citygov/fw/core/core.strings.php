<?php
/**
 * CityGov Framework: strings manipulations
 *
 * @package	citygov
 * @since	citygov 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Check multibyte functions
if ( ! defined( 'CITYGOV_MULTIBYTE' ) ) define( 'CITYGOV_MULTIBYTE', function_exists('mb_strpos') ? 'UTF-8' : false );

if (!function_exists('citygov_strlen')) {
	function citygov_strlen($text) {
		return CITYGOV_MULTIBYTE ? mb_strlen($text) : strlen($text);
	}
}

if (!function_exists('citygov_strpos')) {
	function citygov_strpos($text, $char, $from=0) {
		return CITYGOV_MULTIBYTE ? mb_strpos($text, $char, $from) : strpos($text, $char, $from);
	}
}

if (!function_exists('citygov_strrpos')) {
	function citygov_strrpos($text, $char, $from=0) {
		return CITYGOV_MULTIBYTE ? mb_strrpos($text, $char, $from) : strrpos($text, $char, $from);
	}
}

if (!function_exists('citygov_substr')) {
	function citygov_substr($text, $from, $len=-999999) {
		if ($len==-999999) { 
			if ($from < 0)
				$len = -$from; 
			else
				$len = citygov_strlen($text)-$from;
		}
		return CITYGOV_MULTIBYTE ? mb_substr($text, $from, $len) : substr($text, $from, $len);
	}
}

if (!function_exists('citygov_strtolower')) {
	function citygov_strtolower($text) {
		return CITYGOV_MULTIBYTE ? mb_strtolower($text) : strtolower($text);
	}
}

if (!function_exists('citygov_strtoupper')) {
	function citygov_strtoupper($text) {
		return CITYGOV_MULTIBYTE ? mb_strtoupper($text) : strtoupper($text);
	}
}

if (!function_exists('citygov_strtoproper')) {
	function citygov_strtoproper($text) { 
		$rez = ''; $last = ' ';
		for ($i=0; $i<citygov_strlen($text); $i++) {
			$ch = citygov_substr($text, $i, 1);
			$rez .= citygov_strpos(' .,:;?!()[]{}+=', $last)!==false ? citygov_strtoupper($ch) : citygov_strtolower($ch);
			$last = $ch;
		}
		return $rez;
	}
}

if (!function_exists('citygov_strrepeat')) {
	function citygov_strrepeat($str, $n) {
		$rez = '';
		for ($i=0; $i<$n; $i++)
			$rez .= $str;
		return $rez;
	}
}

if (!function_exists('citygov_strshort')) {
	function citygov_strshort($str, $maxlength, $add='...') {
	//	if ($add && citygov_substr($add, 0, 1) != ' ')
	//		$add .= ' ';
		if ($maxlength < 0) 
			return $str;
		if ($maxlength < 1 || $maxlength >= citygov_strlen($str)) 
			return strip_tags($str);
		$str = citygov_substr(strip_tags($str), 0, $maxlength - citygov_strlen($add));
		$ch = citygov_substr($str, $maxlength - citygov_strlen($add), 1);
		if ($ch != ' ') {
			for ($i = citygov_strlen($str) - 1; $i > 0; $i--)
				if (citygov_substr($str, $i, 1) == ' ') break;
			$str = trim(citygov_substr($str, 0, $i));
		}
		if (!empty($str) && citygov_strpos(',.:;-', citygov_substr($str, -1))!==false) $str = citygov_substr($str, 0, -1);
		return ($str) . ($add);
	}
}

// Clear string from spaces, line breaks and tags (only around text)
if (!function_exists('citygov_strclear')) {
	function citygov_strclear($text, $tags=array()) {
		if (empty($text)) return $text;
		if (!is_array($tags)) {
			if ($tags != '')
				$tags = explode($tags, ',');
			else
				$tags = array();
		}
		$text = trim(chop($text));
		if (is_array($tags) && count($tags) > 0) {
			foreach ($tags as $tag) {
				$open  = '<'.esc_attr($tag);
				$close = '</'.esc_attr($tag).'>';
				if (citygov_substr($text, 0, citygov_strlen($open))==$open) {
					$pos = citygov_strpos($text, '>');
					if ($pos!==false) $text = citygov_substr($text, $pos+1);
				}
				if (citygov_substr($text, -citygov_strlen($close))==$close) $text = citygov_substr($text, 0, citygov_strlen($text) - citygov_strlen($close));
				$text = trim(chop($text));
			}
		}
		return $text;
	}
}

// Return slug for the any title string
if (!function_exists('citygov_get_slug')) {
	function citygov_get_slug($title) {
		return citygov_strtolower(str_replace(array('\\','/','-',' ','.'), '_', $title));
	}
}

// Replace macros in the string
if (!function_exists('citygov_strmacros')) {
	function citygov_strmacros($str) {
		return str_replace(array("{{", "}}", "((", "))", "||"), array("<i>", "</i>", "<b>", "</b>", "<br>"), $str);
	}
}

// Unserialize string (try replace \n with \r\n)
if (!function_exists('citygov_unserialize')) {
	function citygov_unserialize($str) {
		if ( is_serialized($str) ) {
			try {
				$data = unserialize($str);
			} catch (Exception $e) {
				dcl($e->getMessage());
				$data = false;
			}
			if ($data===false) {
				try {
					$data = @unserialize(str_replace("\n", "\r\n", $str));
				} catch (Exception $e) {
					dcl($e->getMessage());
					$data = false;
				}
			}
			//if ($data===false) $data = @unserialize(str_replace(array("\n", "\r"), array('\\n','\\r'), $str));
			return $data;
		} else
			return $str;
	}
}
?>