<?php
/* Mega Main Menu support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('citygov_megamenu_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_megamenu_theme_setup', 1 );
	function citygov_megamenu_theme_setup() {
		if (citygov_exists_megamenu()) {
			if (is_admin()) {
				add_filter( 'citygov_filter_importer_options',				'citygov_megamenu_importer_set_options' );
			}
		}
		if (is_admin()) {
			add_filter( 'citygov_filter_importer_required_plugins',		'citygov_megamenu_importer_required_plugins', 10, 2 );
			add_filter( 'citygov_filter_required_plugins',					'citygov_megamenu_required_plugins' );
		}
	}
}

// Check if MegaMenu installed and activated
if ( !function_exists( 'citygov_exists_megamenu' ) ) {
	function citygov_exists_megamenu() {
		return class_exists('mega_main_init');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'citygov_megamenu_required_plugins' ) ) {
	//add_filter('citygov_filter_required_plugins',	'citygov_megamenu_required_plugins');
	function citygov_megamenu_required_plugins($list=array()) {
		if (in_array('mega_main_menu', citygov_storage_get('required_plugins'))) {
			$path = citygov_get_file_dir('plugins/install/mega_main_menu.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> 'Mega Main Menu',
					'slug' 		=> 'mega_main_menu',
					'source'	=> $path,
					'required' 	=> false
				);
			}
		}
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check Mega Menu in the required plugins
if ( !function_exists( 'citygov_megamenu_importer_required_plugins' ) ) {
	//add_filter( 'citygov_filter_importer_required_plugins',	'citygov_megamenu_importer_required_plugins', 10, 2 );
	function citygov_megamenu_importer_required_plugins($not_installed='', $list='') {
		if (citygov_strpos($list, 'mega_main_menu')!==false && !citygov_exists_megamenu())
			$not_installed .= '<br>Mega Main Menu';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'citygov_megamenu_importer_set_options' ) ) {
	//add_filter( 'citygov_filter_importer_options',	'citygov_megamenu_importer_set_options' );
	function citygov_megamenu_importer_set_options($options=array()) {
		if ( in_array('mega_main_menu', citygov_storage_get('required_plugins')) && citygov_exists_megamenu() ) {
			$options['additional_options'][] = 'mega_main_menu_options';		// Add slugs to export options for this plugin

		}
		return $options;
	}
}
?>