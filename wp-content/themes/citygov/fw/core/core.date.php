<?php
/**
 * CityGov Framework: date and time manipulations
 *
 * @package	citygov
 * @since	citygov 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Convert date from MySQL format (YYYY-mm-dd) to Date (dd.mm.YYYY)
if (!function_exists('citygov_sql_to_date')) {
	function citygov_sql_to_date($str) {
		return (trim($str)=='' || trim($str)=='0000-00-00' ? '' : trim(citygov_substr($str,8,2).'.'.citygov_substr($str,5,2).'.'.citygov_substr($str,0,4).' '.citygov_substr($str,11)));
	}
}

// Convert date from Date format (dd.mm.YYYY) to MySQL format (YYYY-mm-dd)
if (!function_exists('citygov_date_to_sql')) {
	function citygov_date_to_sql($str) {
		if (trim($str)=='') return '';
		$str = strtr(trim($str),'/\-,','....');
		if (trim($str)=='00.00.0000' || trim($str)=='00.00.00') return '';
		$pos = citygov_strpos($str,'.');
		$d=trim(citygov_substr($str,0,$pos));
		$str=citygov_substr($str,$pos+1);
		$pos = citygov_strpos($str,'.');
		$m=trim(citygov_substr($str,0,$pos));
		$y=trim(citygov_substr($str,$pos+1));
		$y=($y<50?$y+2000:($y<1900?$y+1900:$y));
		return ''.($y).'-'.(citygov_strlen($m)<2?'0':'').($m).'-'.(citygov_strlen($d)<2?'0':'').($d);
	}
}

// Return difference or date
if (!function_exists('citygov_get_date_or_difference')) {
	function citygov_get_date_or_difference($dt1, $dt2=null, $max_days=-1) {
		static $gmt_offset = 999;
		if ($gmt_offset==999) $gmt_offset = (int) get_option('gmt_offset');
		if ($max_days < 0) $max_days = citygov_get_theme_option('show_date_after', 30);
		if ($dt2 == null) $dt2 = date('Y-m-d H:i:s');
		$dt2n = strtotime($dt2)+$gmt_offset*3600;
		$dt1n = strtotime($dt1);
		$diff = $dt2n - $dt1n;
		$days = floor($diff / (24*3600));
		if (abs($days) < $max_days)
			return sprintf($days >= 0 ? esc_html__('%s ago', 'citygov') : esc_html__('in %s', 'citygov'), citygov_get_date_difference($days >= 0 ? $dt1 : $dt2, $days >= 0 ? $dt2 : $dt1));
		else
			return citygov_get_date_translations(date(get_option('date_format'), $dt1n));
	}
}

// Difference between two dates
if (!function_exists('citygov_get_date_difference')) {
	function citygov_get_date_difference($dt1, $dt2=null, $short=1, $sec = false) {
		static $gmt_offset = 999;
		if ($gmt_offset==999) $gmt_offset = (int) get_option('gmt_offset');
		if ($dt2 == null) $dt2 = time()+$gmt_offset*3600;
		else $dt2 = strtotime($dt2)+$gmt_offset*3600;
		$dt1 = strtotime($dt1);
		$diff = $dt2 - $dt1;
		$days = floor($diff / (24*3600));
		$months = floor($days / 30);
		$diff -= $days * 24 * 3600;
		$hours = floor($diff / 3600);
		$diff -= $hours * 3600;
		$min = floor($diff / 60);
		$diff -= $min * 60;
		$rez = '';
		if ($months > 0 && $short == 2)
			$rez .= ($rez!='' ? ' ' : '') . sprintf($months > 1 ? esc_html__('%s months', 'citygov') : esc_html__('%s month', 'citygov'), $months);
		if ($days > 0 && ($short < 2 || $rez==''))
			$rez .= ($rez!='' ? ' ' : '') . sprintf($days > 1 ? esc_html__('%s days', 'citygov') : esc_html__('%s day', 'citygov'), $days);
		if ((!$short || $rez=='') && $hours > 0)
			$rez .= ($rez!='' ? ' ' : '') . sprintf($hours > 1 ? esc_html__('%s hours', 'citygov') : esc_html__('%s hour', 'citygov'), $hours);
		if ((!$short || $rez=='') && $min > 0)
			$rez .= ($rez!='' ? ' ' : '') . sprintf($min > 1 ? esc_html__('%s minutes', 'citygov') : esc_html__('%s minute', 'citygov'), $min);
		if ($sec || $rez=='')
			$rez .=  $rez!='' || $sec ? (' ' . sprintf($diff > 1 ? esc_html__('%s seconds', 'citygov') : esc_html__('%s second', 'citygov'), $diff)) : esc_html__('less then minute', 'citygov');
		return $rez;
	}
}

// Prepare month names in date for translation
if (!function_exists('citygov_get_date_translations')) {
	function citygov_get_date_translations($dt) {
		return str_replace(
			array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',
				  'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'),
			array(
				esc_html__('January', 'citygov'),
				esc_html__('February', 'citygov'),
				esc_html__('March', 'citygov'),
				esc_html__('April', 'citygov'),
				esc_html__('May', 'citygov'),
				esc_html__('June', 'citygov'),
				esc_html__('July', 'citygov'),
				esc_html__('August', 'citygov'),
				esc_html__('September', 'citygov'),
				esc_html__('October', 'citygov'),
				esc_html__('November', 'citygov'),
				esc_html__('December', 'citygov'),
				esc_html__('Jan', 'citygov'),
				esc_html__('Feb', 'citygov'),
				esc_html__('Mar', 'citygov'),
				esc_html__('Apr', 'citygov'),
				esc_html__('May', 'citygov'),
				esc_html__('Jun', 'citygov'),
				esc_html__('Jul', 'citygov'),
				esc_html__('Aug', 'citygov'),
				esc_html__('Sep', 'citygov'),
				esc_html__('Oct', 'citygov'),
				esc_html__('Nov', 'citygov'),
				esc_html__('Dec', 'citygov'),
			),
			$dt);
	}
}
?>