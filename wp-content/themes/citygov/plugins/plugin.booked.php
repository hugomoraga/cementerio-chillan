<?php
/* Booked Appointments support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('citygov_booked_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_booked_theme_setup', 1 );
	function citygov_booked_theme_setup() {
		// Register shortcode in the shortcodes list
		if (citygov_exists_booked()) {
			add_action('citygov_action_add_styles', 					'citygov_booked_frontend_scripts');
			add_action('citygov_action_shortcodes_list',				'citygov_booked_reg_shortcodes');
			if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
				add_action('citygov_action_shortcodes_list_vc',		'citygov_booked_reg_shortcodes_vc');
			if (is_admin()) {
				add_filter( 'citygov_filter_importer_options',			'citygov_booked_importer_set_options' );
			}
		}
		if (is_admin()) {
			add_filter( 'citygov_filter_importer_required_plugins',	'citygov_booked_importer_required_plugins', 10, 2);
			add_filter( 'citygov_filter_required_plugins',				'citygov_booked_required_plugins' );
		}
	}
}


// Check if plugin installed and activated
if ( !function_exists( 'citygov_exists_booked' ) ) {
	function citygov_exists_booked() {
		return class_exists('booked_plugin');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'citygov_booked_required_plugins' ) ) {
	function citygov_booked_required_plugins($list=array()) {
		if (in_array('booked', citygov_storage_get('required_plugins'))) {
			$path = citygov_get_file_dir('plugins/install/booked.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> 'Booked',
					'slug' 		=> 'booked',
					'source'	=> $path,
					'required' 	=> false
					);
			}
		}
		return $list;
	}
}

// Enqueue custom styles
if ( !function_exists( 'citygov_booked_frontend_scripts' ) ) {
	function citygov_booked_frontend_scripts() {
		if (file_exists(citygov_get_file_dir('css/plugin.booked.css')))
			citygov_enqueue_style( 'citygov-plugin.booked-style',  citygov_get_file_url('css/plugin.booked.css'), array(), null );
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check in the required plugins
if ( !function_exists( 'citygov_booked_importer_required_plugins' ) ) {
	function citygov_booked_importer_required_plugins($not_installed='', $list='') {
		if (citygov_strpos($list, 'booked')!==false && !citygov_exists_booked() )
			$not_installed .= '<br>Booked Appointments';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'citygov_booked_importer_set_options' ) ) {
	function citygov_booked_importer_set_options($options=array()) {
		if (in_array('booked', citygov_storage_get('required_plugins')) && citygov_exists_booked()) {
			$options['additional_options'][] = 'booked_%';		// Add slugs to export options for this plugin
		}
		return $options;
	}
}


// Lists
//------------------------------------------------------------------------

// Return booked calendars list, prepended inherit (if need)
if ( !function_exists( 'citygov_get_list_booked_calendars' ) ) {
	function citygov_get_list_booked_calendars($prepend_inherit=false) {
		return citygov_exists_booked() ? citygov_get_list_terms($prepend_inherit, 'booked_custom_calendars') : array();
	}
}



// Register plugin's shortcodes
//------------------------------------------------------------------------

// Register shortcode in the shortcodes list
if (!function_exists('citygov_booked_reg_shortcodes')) {
	function citygov_booked_reg_shortcodes() {
		if (citygov_storage_isset('shortcodes')) {

			$booked_cals = citygov_get_list_booked_calendars();

			citygov_sc_map('booked-appointments', array(
				"title" => esc_html__("Booked Appointments", "citygov"),
				"desc" => esc_html__("Display the currently logged in user's upcoming appointments", "citygov"),
				"decorate" => true,
				"container" => false,
				"params" => array()
				)
			);

			citygov_sc_map('booked-calendar', array(
				"title" => esc_html__("Booked Calendar", "citygov"),
				"desc" => esc_html__("Insert booked calendar", "citygov"),
				"decorate" => true,
				"container" => false,
				"params" => array(
					"calendar" => array(
						"title" => esc_html__("Calendar", "citygov"),
						"desc" => esc_html__("Select booked calendar to display", "citygov"),
						"value" => "0",
						"type" => "select",
						"options" => citygov_array_merge(array(0 => esc_html__('- Select calendar -', 'citygov')), $booked_cals)
					),
					"year" => array(
						"title" => esc_html__("Year", "citygov"),
						"desc" => esc_html__("Year to display on calendar by default", "citygov"),
						"value" => date("Y"),
						"min" => date("Y"),
						"max" => date("Y")+10,
						"type" => "spinner"
					),
					"month" => array(
						"title" => esc_html__("Month", "citygov"),
						"desc" => esc_html__("Month to display on calendar by default", "citygov"),
						"value" => date("m"),
						"min" => 1,
						"max" => 12,
						"type" => "spinner"
					)
				)
			));
		}
	}
}


// Register shortcode in the VC shortcodes list
if (!function_exists('citygov_booked_reg_shortcodes_vc')) {
	function citygov_booked_reg_shortcodes_vc() {

		$booked_cals = citygov_get_list_booked_calendars();

		// Booked Appointments
		vc_map( array(
				"base" => "booked-appointments",
				"name" => esc_html__("Booked Appointments", "citygov"),
				"description" => esc_html__("Display the currently logged in user's upcoming appointments", "citygov"),
				"category" => esc_html__('Content', 'citygov'),
				'icon' => 'icon_trx_booked',
				"class" => "trx_sc_single trx_sc_booked_appointments",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => false,
				"params" => array()
			) );
			
		class WPBakeryShortCode_Booked_Appointments extends CITYGOV_VC_ShortCodeSingle {}

		// Booked Calendar
		vc_map( array(
				"base" => "booked-calendar",
				"name" => esc_html__("Booked Calendar", "citygov"),
				"description" => esc_html__("Insert booked calendar", "citygov"),
				"category" => esc_html__('Content', 'citygov'),
				'icon' => 'icon_trx_booked',
				"class" => "trx_sc_single trx_sc_booked_calendar",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "calendar",
						"heading" => esc_html__("Calendar", "citygov"),
						"description" => esc_html__("Select booked calendar to display", "citygov"),
						"admin_label" => true,
						"class" => "",
						"std" => "0",
						"value" => array_flip(citygov_array_merge(array(0 => esc_html__('- Select calendar -', 'citygov')), $booked_cals)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "year",
						"heading" => esc_html__("Year", "citygov"),
						"description" => esc_html__("Year to display on calendar by default", "citygov"),
						"admin_label" => true,
						"class" => "",
						"std" => date("Y"),
						"value" => date("Y"),
						"type" => "textfield"
					),
					array(
						"param_name" => "month",
						"heading" => esc_html__("Month", "citygov"),
						"description" => esc_html__("Month to display on calendar by default", "citygov"),
						"admin_label" => true,
						"class" => "",
						"std" => date("m"),
						"value" => date("m"),
						"type" => "textfield"
					)
				)
			) );
			
		class WPBakeryShortCode_Booked_Calendar extends CITYGOV_VC_ShortCodeSingle {}

	}
}
?>