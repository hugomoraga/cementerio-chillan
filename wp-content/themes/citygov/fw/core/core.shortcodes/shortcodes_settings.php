<?php

// Check if shortcodes settings are now used
if ( !function_exists( 'citygov_shortcodes_is_used' ) ) {
	function citygov_shortcodes_is_used() {
		return citygov_options_is_used() 															// All modes when Theme Options are used
			|| (is_admin() && isset($_POST['action']) 
					&& in_array($_POST['action'], array('vc_edit_form', 'wpb_show_edit_form')))		// AJAX query when save post/page
			|| (is_admin() && citygov_strpos($_SERVER['REQUEST_URI'], 'vc-roles')!==false)			// VC Role Manager
			|| (function_exists('citygov_vc_is_frontend') && citygov_vc_is_frontend());			// VC Frontend editor mode
	}
}

// Width and height params
if ( !function_exists( 'citygov_shortcodes_width' ) ) {
	function citygov_shortcodes_width($w="") {
		return array(
			"title" => esc_html__("Width", "citygov"),
			"divider" => true,
			"value" => $w,
			"type" => "text"
		);
	}
}
if ( !function_exists( 'citygov_shortcodes_height' ) ) {
	function citygov_shortcodes_height($h='') {
		return array(
			"title" => esc_html__("Height", "citygov"),
			"desc" => wp_kses_data( __("Width and height of the element", "citygov") ),
			"value" => $h,
			"type" => "text"
		);
	}
}

// Return sc_param value
if ( !function_exists( 'citygov_get_sc_param' ) ) {
	function citygov_get_sc_param($prm) {
		return citygov_storage_get_array('sc_params', $prm);
	}
}

// Set sc_param value
if ( !function_exists( 'citygov_set_sc_param' ) ) {
	function citygov_set_sc_param($prm, $val) {
		citygov_storage_set_array('sc_params', $prm, $val);
	}
}

// Add sc settings in the sc list
if ( !function_exists( 'citygov_sc_map' ) ) {
	function citygov_sc_map($sc_name, $sc_settings) {
		citygov_storage_set_array('shortcodes', $sc_name, $sc_settings);
	}
}

// Add sc settings in the sc list after the key
if ( !function_exists( 'citygov_sc_map_after' ) ) {
	function citygov_sc_map_after($after, $sc_name, $sc_settings='') {
		citygov_storage_set_array_after('shortcodes', $after, $sc_name, $sc_settings);
	}
}

// Add sc settings in the sc list before the key
if ( !function_exists( 'citygov_sc_map_before' ) ) {
	function citygov_sc_map_before($before, $sc_name, $sc_settings='') {
		citygov_storage_set_array_before('shortcodes', $before, $sc_name, $sc_settings);
	}
}

// Compare two shortcodes by title
if ( !function_exists( 'citygov_compare_sc_title' ) ) {
	function citygov_compare_sc_title($a, $b) {
		return strcmp($a['title'], $b['title']);
	}
}



/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'citygov_shortcodes_settings_theme_setup' ) ) {
//	if ( citygov_vc_is_frontend() )
	if ( (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') || (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline') )
		add_action( 'citygov_action_before_init_theme', 'citygov_shortcodes_settings_theme_setup', 20 );
	else
		add_action( 'citygov_action_after_init_theme', 'citygov_shortcodes_settings_theme_setup' );
	function citygov_shortcodes_settings_theme_setup() {
		if (citygov_shortcodes_is_used()) {

			// Sort templates alphabetically
			$tmp = citygov_storage_get('registered_templates');
			ksort($tmp);
			citygov_storage_set('registered_templates', $tmp);

			// Prepare arrays 
			citygov_storage_set('sc_params', array(
			
				// Current element id
				'id' => array(
					"title" => esc_html__("Element ID", "citygov"),
					"desc" => wp_kses_data( __("ID for current element", "citygov") ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
			
				// Current element class
				'class' => array(
					"title" => esc_html__("Element CSS class", "citygov"),
					"desc" => wp_kses_data( __("CSS class for current element (optional)", "citygov") ),
					"value" => "",
					"type" => "text"
				),
			
				// Current element style
				'css' => array(
					"title" => esc_html__("CSS styles", "citygov"),
					"desc" => wp_kses_data( __("Any additional CSS rules (if need)", "citygov") ),
					"value" => "",
					"type" => "text"
				),
			
			
				// Switcher choises
				'list_styles' => array(
					'ul'	=> esc_html__('Unordered', 'citygov'),
					'ol'	=> esc_html__('Ordered', 'citygov'),
					'iconed'=> esc_html__('Iconed', 'citygov')
				),

				'yes_no'	=> citygov_get_list_yesno(),
				'on_off'	=> citygov_get_list_onoff(),
				'dir' 		=> citygov_get_list_directions(),
				'align'		=> citygov_get_list_alignments(),
				'float'		=> citygov_get_list_floats(),
				'hpos'		=> citygov_get_list_hpos(),
				'show_hide'	=> citygov_get_list_showhide(),
				'sorting' 	=> citygov_get_list_sortings(),
				'ordering' 	=> citygov_get_list_orderings(),
				'shapes'	=> citygov_get_list_shapes(),
				'sizes'		=> citygov_get_list_sizes(),
				'sliders'	=> citygov_get_list_sliders(),
				'controls'	=> citygov_get_list_controls(),
				'categories'=> citygov_get_list_categories(),
				'columns'	=> citygov_get_list_columns(),
				'images'	=> array_merge(array('none'=>"none"), citygov_get_list_files("images/icons", "png")),
				'icons'		=> array_merge(array("inherit", "none"), citygov_get_list_icons()),
				'locations'	=> citygov_get_list_dedicated_locations(),
				'filters'	=> citygov_get_list_portfolio_filters(),
				'formats'	=> citygov_get_list_post_formats_filters(),
				'hovers'	=> citygov_get_list_hovers(true),
				'hovers_dir'=> citygov_get_list_hovers_directions(true),
				'schemes'	=> citygov_get_list_color_schemes(true),
				'animations'		=> citygov_get_list_animations_in(),
				'margins' 			=> citygov_get_list_margins(true),
				'blogger_styles'	=> citygov_get_list_templates_blogger(),
				'forms'				=> citygov_get_list_templates_forms(),
				'posts_types'		=> citygov_get_list_posts_types(),
				'googlemap_styles'	=> citygov_get_list_googlemap_styles(),
				'field_types'		=> citygov_get_list_field_types(),
				'label_positions'	=> citygov_get_list_label_positions()
				)
			);

			// Common params
			citygov_set_sc_param('animation', array(
				"title" => esc_html__("Animation",  'citygov'),
				"desc" => wp_kses_data( __('Select animation while object enter in the visible area of page',  'citygov') ),
				"value" => "none",
				"type" => "select",
				"options" => citygov_get_sc_param('animations')
				)
			);
			citygov_set_sc_param('top', array(
				"title" => esc_html__("Top margin",  'citygov'),
				"divider" => true,
				"value" => "inherit",
				"type" => "select",
				"options" => citygov_get_sc_param('margins')
				)
			);
			citygov_set_sc_param('bottom', array(
				"title" => esc_html__("Bottom margin",  'citygov'),
				"value" => "inherit",
				"type" => "select",
				"options" => citygov_get_sc_param('margins')
				)
			);
			citygov_set_sc_param('left', array(
				"title" => esc_html__("Left margin",  'citygov'),
				"value" => "inherit",
				"type" => "select",
				"options" => citygov_get_sc_param('margins')
				)
			);
			citygov_set_sc_param('right', array(
				"title" => esc_html__("Right margin",  'citygov'),
				"desc" => wp_kses_data( __("Margins around this shortcode", "citygov") ),
				"value" => "inherit",
				"type" => "select",
				"options" => citygov_get_sc_param('margins')
				)
			);

			citygov_storage_set('sc_params', apply_filters('citygov_filter_shortcodes_params', citygov_storage_get('sc_params')));

			// Shortcodes list
			//------------------------------------------------------------------
			citygov_storage_set('shortcodes', array());
			
			// Register shortcodes
			do_action('citygov_action_shortcodes_list');

			// Sort shortcodes list
			$tmp = citygov_storage_get('shortcodes');
			uasort($tmp, 'citygov_compare_sc_title');
			citygov_storage_set('shortcodes', $tmp);
		}
	}
}
?>