<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_content_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_content_theme_setup' );
	function citygov_sc_content_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_content_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_content_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_content id="unique_id" class="class_name" style="css-styles"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_content]
*/

if (!function_exists('citygov_sc_content')) {	
	function citygov_sc_content($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			"scheme" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"top" => "",
			"bottom" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, '', $bottom);
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_content content_wrap' 
				. ($scheme && !citygov_param_is_off($scheme) && !citygov_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
				. ($class ? ' '.esc_attr($class) : '') 
				. '"'
			. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '').'>' 
			. do_shortcode($content) 
			. '</div>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_content', $atts, $content);
	}
	citygov_require_shortcode('trx_content', 'citygov_sc_content');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_content_reg_shortcodes' ) ) {
	function citygov_sc_content_reg_shortcodes() {
	
		citygov_sc_map("trx_content", array(
			"title" => esc_html__("Content block", "citygov"),
			"desc" => wp_kses_data( __("Container for main content block with desired class and style (use it only on fullscreen pages)", "citygov") ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"scheme" => array(
					"title" => esc_html__("Color scheme", "citygov"),
					"desc" => wp_kses_data( __("Select color scheme for this block", "citygov") ),
					"value" => "",
					"type" => "checklist",
					"options" => citygov_get_sc_param('schemes')
				),
				"_content_" => array(
					"title" => esc_html__("Container content", "citygov"),
					"desc" => wp_kses_data( __("Content for section container", "citygov") ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"top" => citygov_get_sc_param('top'),
				"bottom" => citygov_get_sc_param('bottom'),
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
if ( !function_exists( 'citygov_sc_content_reg_shortcodes_vc' ) ) {
	function citygov_sc_content_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_content",
			"name" => esc_html__("Content block", "citygov"),
			"description" => wp_kses_data( __("Container for main content block (use it only on fullscreen pages)", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_content',
			"class" => "trx_sc_collection trx_sc_content",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", "citygov"),
					"description" => wp_kses_data( __("Select color scheme for this block", "citygov") ),
					"group" => esc_html__('Colors and Images', 'citygov'),
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('schemes')),
					"type" => "dropdown"
				),
				/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("Container content", "citygov"),
					"description" => wp_kses_data( __("Content for section container", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				*/
				citygov_get_vc_param('id'),
				citygov_get_vc_param('class'),
				citygov_get_vc_param('animation'),
				citygov_get_vc_param('css'),
				citygov_get_vc_param('margin_top'),
				citygov_get_vc_param('margin_bottom')
			)
		) );
		
		class WPBakeryShortCode_Trx_Content extends CITYGOV_VC_ShortCodeCollection {}
	}
}
?>