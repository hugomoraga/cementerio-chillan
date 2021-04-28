<?php
/* Instagram Widget support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('citygov_instagram_widget_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_instagram_widget_theme_setup', 1 );
	function citygov_instagram_widget_theme_setup() {
		if (citygov_exists_instagram_widget()) {
			add_action( 'citygov_action_add_styles', 						'citygov_instagram_widget_frontend_scripts' );
		}
		if (is_admin()) {
			add_filter( 'citygov_filter_importer_required_plugins',		'citygov_instagram_widget_importer_required_plugins', 10, 2 );
			add_filter( 'citygov_filter_required_plugins',					'citygov_instagram_widget_required_plugins' );
		}
	}
}

// Check if Instagram Widget installed and activated
if ( !function_exists( 'citygov_exists_instagram_widget' ) ) {
	function citygov_exists_instagram_widget() {
		return function_exists('wpiw_init');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'citygov_instagram_widget_required_plugins' ) ) {
	//add_filter('citygov_filter_required_plugins',	'citygov_instagram_widget_required_plugins');
	function citygov_instagram_widget_required_plugins($list=array()) {
		if (in_array('instagram_widget', citygov_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> 'Instagram Widget',
					'slug' 		=> 'wp-instagram-widget',
					'required' 	=> false
				);
		return $list;
	}
}

// Enqueue custom styles
if ( !function_exists( 'citygov_instagram_widget_frontend_scripts' ) ) {
	//add_action( 'citygov_action_add_styles', 'citygov_instagram_widget_frontend_scripts' );
	function citygov_instagram_widget_frontend_scripts() {
		if (file_exists(citygov_get_file_dir('css/plugin.instagram-widget.css')))
			citygov_enqueue_style( 'citygov-plugin.instagram-widget-style',  citygov_get_file_url('css/plugin.instagram-widget.css'), array(), null );
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check Instagram Widget in the required plugins
if ( !function_exists( 'citygov_instagram_widget_importer_required_plugins' ) ) {
	//add_filter( 'citygov_filter_importer_required_plugins',	'citygov_instagram_widget_importer_required_plugins', 10, 2 );
	function citygov_instagram_widget_importer_required_plugins($not_installed='', $list='') {
		if (citygov_strpos($list, 'instagram_widget')!==false && !citygov_exists_instagram_widget() )
			$not_installed .= '<br>WP Instagram Widget';
		return $not_installed;
	}
}
?>