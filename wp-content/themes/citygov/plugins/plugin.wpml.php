<?php
/* WPML support functions
------------------------------------------------------------------------------- */

// Check if WPML installed and activated
if ( !function_exists( 'citygov_exists_wpml' ) ) {
	function citygov_exists_wpml() {
		return defined('ICL_SITEPRESS_VERSION') && class_exists('sitepress');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'citygov_wpml_required_plugins' ) ) {
	//add_filter('citygov_filter_required_plugins',	'citygov_wpml_required_plugins');
	function citygov_wpml_required_plugins($list=array()) {
		if (in_array('wpml', citygov_storage_get('required_plugins'))) {
			$path = citygov_get_file_dir('plugins/install/wpml.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> 'WPML',
					'slug' 		=> 'wpml',
					'source'	=> $path,
					'required' 	=> false
					);
			}
		}
		return $list;
	}
}
?>