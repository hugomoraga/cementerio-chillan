<?php
/**
 * CityGov Framework: theme variables storage
 *
 * @package	citygov
 * @since	citygov 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Get theme variable
if (!function_exists('citygov_storage_get')) {
	function citygov_storage_get($var_name, $default='') {
		global $CITYGOV_STORAGE;
		return isset($CITYGOV_STORAGE[$var_name]) ? $CITYGOV_STORAGE[$var_name] : $default;
	}
}

// Set theme variable
if (!function_exists('citygov_storage_set')) {
	function citygov_storage_set($var_name, $value) {
		global $CITYGOV_STORAGE;
		$CITYGOV_STORAGE[$var_name] = $value;
	}
}

// Check if theme variable is empty
if (!function_exists('citygov_storage_empty')) {
	function citygov_storage_empty($var_name, $key='', $key2='') {
		global $CITYGOV_STORAGE;
		if (!empty($key) && !empty($key2))
			return empty($CITYGOV_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return empty($CITYGOV_STORAGE[$var_name][$key]);
		else
			return empty($CITYGOV_STORAGE[$var_name]);
	}
}

// Check if theme variable is set
if (!function_exists('citygov_storage_isset')) {
	function citygov_storage_isset($var_name, $key='', $key2='') {
		global $CITYGOV_STORAGE;
		if (!empty($key) && !empty($key2))
			return isset($CITYGOV_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return isset($CITYGOV_STORAGE[$var_name][$key]);
		else
			return isset($CITYGOV_STORAGE[$var_name]);
	}
}

// Inc/Dec theme variable with specified value
if (!function_exists('citygov_storage_inc')) {
	function citygov_storage_inc($var_name, $value=1) {
		global $CITYGOV_STORAGE;
		if (empty($CITYGOV_STORAGE[$var_name])) $CITYGOV_STORAGE[$var_name] = 0;
		$CITYGOV_STORAGE[$var_name] += $value;
	}
}

// Concatenate theme variable with specified value
if (!function_exists('citygov_storage_concat')) {
	function citygov_storage_concat($var_name, $value) {
		global $CITYGOV_STORAGE;
		if (empty($CITYGOV_STORAGE[$var_name])) $CITYGOV_STORAGE[$var_name] = '';
		$CITYGOV_STORAGE[$var_name] .= $value;
	}
}

// Get array (one or two dim) element
if (!function_exists('citygov_storage_get_array')) {
	function citygov_storage_get_array($var_name, $key, $key2='', $default='') {
		global $CITYGOV_STORAGE;
		if (empty($key2))
			return !empty($var_name) && !empty($key) && isset($CITYGOV_STORAGE[$var_name][$key]) ? $CITYGOV_STORAGE[$var_name][$key] : $default;
		else
			return !empty($var_name) && !empty($key) && isset($CITYGOV_STORAGE[$var_name][$key][$key2]) ? $CITYGOV_STORAGE[$var_name][$key][$key2] : $default;
	}
}

// Set array element
if (!function_exists('citygov_storage_set_array')) {
	function citygov_storage_set_array($var_name, $key, $value) {
		global $CITYGOV_STORAGE;
		if (!isset($CITYGOV_STORAGE[$var_name])) $CITYGOV_STORAGE[$var_name] = array();
		if ($key==='')
			$CITYGOV_STORAGE[$var_name][] = $value;
		else
			$CITYGOV_STORAGE[$var_name][$key] = $value;
	}
}

// Set two-dim array element
if (!function_exists('citygov_storage_set_array2')) {
	function citygov_storage_set_array2($var_name, $key, $key2, $value) {
		global $CITYGOV_STORAGE;
		if (!isset($CITYGOV_STORAGE[$var_name])) $CITYGOV_STORAGE[$var_name] = array();
		if (!isset($CITYGOV_STORAGE[$var_name][$key])) $CITYGOV_STORAGE[$var_name][$key] = array();
		if ($key2==='')
			$CITYGOV_STORAGE[$var_name][$key][] = $value;
		else
			$CITYGOV_STORAGE[$var_name][$key][$key2] = $value;
	}
}

// Add array element after the key
if (!function_exists('citygov_storage_set_array_after')) {
	function citygov_storage_set_array_after($var_name, $after, $key, $value='') {
		global $CITYGOV_STORAGE;
		if (!isset($CITYGOV_STORAGE[$var_name])) $CITYGOV_STORAGE[$var_name] = array();
		if (is_array($key))
			citygov_array_insert_after($CITYGOV_STORAGE[$var_name], $after, $key);
		else
			citygov_array_insert_after($CITYGOV_STORAGE[$var_name], $after, array($key=>$value));
	}
}

// Add array element before the key
if (!function_exists('citygov_storage_set_array_before')) {
	function citygov_storage_set_array_before($var_name, $before, $key, $value='') {
		global $CITYGOV_STORAGE;
		if (!isset($CITYGOV_STORAGE[$var_name])) $CITYGOV_STORAGE[$var_name] = array();
		if (is_array($key))
			citygov_array_insert_before($CITYGOV_STORAGE[$var_name], $before, $key);
		else
			citygov_array_insert_before($CITYGOV_STORAGE[$var_name], $before, array($key=>$value));
	}
}

// Push element into array
if (!function_exists('citygov_storage_push_array')) {
	function citygov_storage_push_array($var_name, $key, $value) {
		global $CITYGOV_STORAGE;
		if (!isset($CITYGOV_STORAGE[$var_name])) $CITYGOV_STORAGE[$var_name] = array();
		if ($key==='')
			array_push($CITYGOV_STORAGE[$var_name], $value);
		else {
			if (!isset($CITYGOV_STORAGE[$var_name][$key])) $CITYGOV_STORAGE[$var_name][$key] = array();
			array_push($CITYGOV_STORAGE[$var_name][$key], $value);
		}
	}
}

// Pop element from array
if (!function_exists('citygov_storage_pop_array')) {
	function citygov_storage_pop_array($var_name, $key='', $defa='') {
		global $CITYGOV_STORAGE;
		$rez = $defa;
		if ($key==='') {
			if (isset($CITYGOV_STORAGE[$var_name]) && is_array($CITYGOV_STORAGE[$var_name]) && count($CITYGOV_STORAGE[$var_name]) > 0) 
				$rez = array_pop($CITYGOV_STORAGE[$var_name]);
		} else {
			if (isset($CITYGOV_STORAGE[$var_name][$key]) && is_array($CITYGOV_STORAGE[$var_name][$key]) && count($CITYGOV_STORAGE[$var_name][$key]) > 0) 
				$rez = array_pop($CITYGOV_STORAGE[$var_name][$key]);
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if (!function_exists('citygov_storage_inc_array')) {
	function citygov_storage_inc_array($var_name, $key, $value=1) {
		global $CITYGOV_STORAGE;
		if (!isset($CITYGOV_STORAGE[$var_name])) $CITYGOV_STORAGE[$var_name] = array();
		if (empty($CITYGOV_STORAGE[$var_name][$key])) $CITYGOV_STORAGE[$var_name][$key] = 0;
		$CITYGOV_STORAGE[$var_name][$key] += $value;
	}
}

// Concatenate array element with specified value
if (!function_exists('citygov_storage_concat_array')) {
	function citygov_storage_concat_array($var_name, $key, $value) {
		global $CITYGOV_STORAGE;
		if (!isset($CITYGOV_STORAGE[$var_name])) $CITYGOV_STORAGE[$var_name] = array();
		if (empty($CITYGOV_STORAGE[$var_name][$key])) $CITYGOV_STORAGE[$var_name][$key] = '';
		$CITYGOV_STORAGE[$var_name][$key] .= $value;
	}
}

// Call object's method
if (!function_exists('citygov_storage_call_obj_method')) {
	function citygov_storage_call_obj_method($var_name, $method, $param=null) {
		global $CITYGOV_STORAGE;
		if ($param===null)
			return !empty($var_name) && !empty($method) && isset($CITYGOV_STORAGE[$var_name]) ? $CITYGOV_STORAGE[$var_name]->$method(): '';
		else
			return !empty($var_name) && !empty($method) && isset($CITYGOV_STORAGE[$var_name]) ? $CITYGOV_STORAGE[$var_name]->$method($param): '';
	}
}

// Get object's property
if (!function_exists('citygov_storage_get_obj_property')) {
	function citygov_storage_get_obj_property($var_name, $prop, $default='') {
		global $CITYGOV_STORAGE;
		return !empty($var_name) && !empty($prop) && isset($CITYGOV_STORAGE[$var_name]->$prop) ? $CITYGOV_STORAGE[$var_name]->$prop : $default;
	}
}
?>