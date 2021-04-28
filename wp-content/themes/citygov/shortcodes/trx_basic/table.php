<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_table_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_table_theme_setup' );
	function citygov_sc_table_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_table_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_table_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('citygov_sc_table')) {
	function citygov_sc_table($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"align" => "",
            "style" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "100%"
		), $atts)));
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= citygov_get_css_dimensions_from_values($width);
		$content = str_replace(
					array('<p><table', 'table></p>', '><br />'),
					array('<table', 'table>', '>'),
					html_entity_decode($content, ENT_COMPAT, 'UTF-8'));
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_table' 
					. (!empty($align) && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '')
                    . (!empty($style) ? ' style_'.esc_attr($style) : '')
            . '"'
				. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				.'>' 
				. do_shortcode($content) 
				. '</div>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_table', $atts, $content);
	}
	citygov_require_shortcode('trx_table', 'citygov_sc_table');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_table_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_table_reg_shortcodes');
	function citygov_sc_table_reg_shortcodes() {
	
		citygov_sc_map("trx_table", array(
			"title" => esc_html__("Table", "citygov"),
			"desc" => wp_kses_data( __("Insert a table into post (page). ", "citygov") ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"align" => array(
					"title" => esc_html__("Content alignment", "citygov"),
					"desc" => wp_kses_data( __("Select alignment for each table cell", "citygov") ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('align')
				),
				"_content_" => array(
					"title" => esc_html__("Table content", "citygov"),
					"desc" => wp_kses_data( __("Content, created with any table-generator", "citygov") ),
					"divider" => true,
					"rows" => 8,
					"value" => "Paste here table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/",
					"type" => "textarea"
				),
				"width" => citygov_shortcodes_width(),
				"top" => citygov_get_sc_param('top'),
				"bottom" => citygov_get_sc_param('bottom'),
				"left" => citygov_get_sc_param('left'),
				"right" => citygov_get_sc_param('right'),
				"id" => citygov_get_sc_param('id'),
				"class" => citygov_get_sc_param('class'),
				"animation" => citygov_get_sc_param('animation'),
				"css" => citygov_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_table_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_table_reg_shortcodes_vc');
	function citygov_sc_table_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_table",
			"name" => esc_html__("Table", "citygov"),
			"description" => wp_kses_data( __("Insert a table", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_table',
			"class" => "trx_sc_container trx_sc_table",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "align",
					"heading" => esc_html__("Cells content alignment", "citygov"),
					"description" => wp_kses_data( __("Select alignment for each table cell", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Table content", "citygov"),
					"description" => wp_kses_data( __("Content, created with any table-generator", "citygov") ),
					"class" => "",
					"value" => esc_html__("Paste here table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/", 'citygov'),
					"type" => "textarea_html"
				),
                array(
                    "param_name" => "style",
                    "heading" => esc_html__("Table style", "citygov"),
                    "description" => wp_kses_data( __("Select table style", "citygov") ),
                    "class" => "",
                    "value" => array(
                        esc_html__('Original', 'citygov') => 'original',
                        esc_html__('Phone', 'citygov') => 'phone'
                    ),
                    "type" => "dropdown"
                ),
				citygov_get_vc_param('id'),
				citygov_get_vc_param('class'),
				citygov_get_vc_param('animation'),
				citygov_get_vc_param('css'),
				citygov_vc_width(),
				citygov_vc_height(),
				citygov_get_vc_param('margin_top'),
				citygov_get_vc_param('margin_bottom'),
				citygov_get_vc_param('margin_left'),
				citygov_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextContainerView'
		) );
		
		class WPBakeryShortCode_Trx_Table extends CITYGOV_VC_ShortCodeContainer {}
	}
}
?>