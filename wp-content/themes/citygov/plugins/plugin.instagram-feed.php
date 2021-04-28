<?php
/* Instagram Feed support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('citygov_instagram_feed_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_instagram_feed_theme_setup', 1 );
	function citygov_instagram_feed_theme_setup() {
		if (citygov_exists_instagram_feed()) {
			if (is_admin()) {
				add_filter( 'citygov_filter_importer_options',				'citygov_instagram_feed_importer_set_options' );
			}
		}
		if (is_admin()) {
			add_filter( 'citygov_filter_importer_required_plugins',		'citygov_instagram_feed_importer_required_plugins', 10, 2 );
			add_filter( 'citygov_filter_required_plugins',					'citygov_instagram_feed_required_plugins' );
		}
	}
}

// Check if Instagram Feed installed and activated
if ( !function_exists( 'citygov_exists_instagram_feed' ) ) {
	function citygov_exists_instagram_feed() {
		return defined('SBIVER');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'citygov_instagram_feed_required_plugins' ) ) {
	//add_filter('citygov_filter_required_plugins',	'citygov_instagram_feed_required_plugins');
	function citygov_instagram_feed_required_plugins($list=array()) {
		if (in_array('instagram_feed', citygov_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> 'Instagram Feed',
					'slug' 		=> 'instagram-feed',
					'required' 	=> false
				);
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check Instagram Feed in the required plugins
if ( !function_exists( 'citygov_instagram_feed_importer_required_plugins' ) ) {
	//add_filter( 'citygov_filter_importer_required_plugins',	'citygov_instagram_feed_importer_required_plugins', 10, 2 );
	function citygov_instagram_feed_importer_required_plugins($not_installed='', $list='') {
		if (citygov_strpos($list, 'instagram_feed')!==false && !citygov_exists_instagram_feed() )
			$not_installed .= '<br>Instagram Feed';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'citygov_instagram_feed_importer_set_options' ) ) {
	//add_filter( 'citygov_filter_importer_options',	'citygov_instagram_feed_importer_set_options' );
	function citygov_instagram_feed_importer_set_options($options=array()) {
		if ( in_array('instagram_feed', citygov_storage_get('required_plugins')) && citygov_exists_instagram_feed() ) {
			$options['additional_options'][] = 'sb_instagram_settings';		// Add slugs to export options for this plugin
		}
		return $options;
	}
}
?>