<?php
/* Mail Chimp support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('citygov_mailchimp_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_mailchimp_theme_setup', 1 );
	function citygov_mailchimp_theme_setup() {
		if (citygov_exists_mailchimp()) {
			if (is_admin()) {
				add_filter( 'citygov_filter_importer_options',				'citygov_mailchimp_importer_set_options' );
			}
		}
		if (is_admin()) {
			add_filter( 'citygov_filter_importer_required_plugins',		'citygov_mailchimp_importer_required_plugins', 10, 2 );
			add_filter( 'citygov_filter_required_plugins',					'citygov_mailchimp_required_plugins' );
		}
	}
}

// Check if Instagram Feed installed and activated
if ( !function_exists( 'citygov_exists_mailchimp' ) ) {
	function citygov_exists_mailchimp() {
		return function_exists('mc4wp_load_plugin');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'citygov_mailchimp_required_plugins' ) ) {
	//add_filter('citygov_filter_required_plugins',	'citygov_mailchimp_required_plugins');
	function citygov_mailchimp_required_plugins($list=array()) {
		if (in_array('mailchimp', citygov_storage_get('required_plugins')))
			$list[] = array(
				'name' 		=> 'MailChimp for WP',
				'slug' 		=> 'mailchimp-for-wp',
				'required' 	=> false
			);
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check Mail Chimp in the required plugins
if ( !function_exists( 'citygov_mailchimp_importer_required_plugins' ) ) {
	//add_filter( 'citygov_filter_importer_required_plugins',	'citygov_mailchimp_importer_required_plugins', 10, 2 );
	function citygov_mailchimp_importer_required_plugins($not_installed='', $list='') {
		if (citygov_strpos($list, 'mailchimp')!==false && !citygov_exists_mailchimp() )
			$not_installed .= '<br>Mail Chimp';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'citygov_mailchimp_importer_set_options' ) ) {
	//add_filter( 'citygov_filter_importer_options',	'citygov_mailchimp_importer_set_options' );
	function citygov_mailchimp_importer_set_options($options=array()) {
		if ( in_array('mailchimp', citygov_storage_get('required_plugins')) && citygov_exists_mailchimp() ) {
			$options['additional_options'][] = 'mc4wp_lite_checkbox';		// Add slugs to export options for this plugin
			$options['additional_options'][] = 'mc4wp_lite_form';
		}
		return $options;
	}
}
?>