<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_infobox_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_infobox_theme_setup' );
	function citygov_sc_infobox_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_infobox_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_infobox_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_infobox id="unique_id" style="regular|info|success|error|result" static="0|1"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_infobox]
*/

if (!function_exists('citygov_sc_infobox')) {	
	function citygov_sc_infobox($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "regular",
			"closeable" => "no",
			"icon" => "",
			"color" => "",
			"bg_color" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= ($color !== '' ? 'color:' . esc_attr($color) .';' : '')
			. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) .';' : '');
		if (empty($icon)) {
			if ($icon=='none')
				$icon = '';
			else if ($style=='regular')
				$icon = 'icon-cogs';
			else if ($style=='success')
				$icon = 'icon-ok';
			else if ($style=='error')
				$icon = 'icon-error-message-icon';
			else if ($style=='info')
				$icon = 'icon-info-circled';
		}
		$content = do_shortcode($content);
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_infobox sc_infobox_style_' . esc_attr($style) 
					. (citygov_param_is_on($closeable) ? ' sc_infobox_closeable' : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '') 
					. ($icon!='' && !citygov_param_is_inherit($icon) ? ' sc_infobox_iconed '. esc_attr($icon) : '') 
					. '"'
				. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>'
				. trim($content)
				. '</div>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_infobox', $atts, $content);
	}
	citygov_require_shortcode('trx_infobox', 'citygov_sc_infobox');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_infobox_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_infobox_reg_shortcodes');
	function citygov_sc_infobox_reg_shortcodes() {
	
		citygov_sc_map("trx_infobox", array(
			"title" => esc_html__("Infobox", "citygov"),
			"desc" => wp_kses_data( __("Insert infobox into your post (page)", "citygov") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", "citygov"),
					"desc" => wp_kses_data( __("Infobox style", "citygov") ),
					"value" => "regular",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						'regular' => esc_html__('Regular', 'citygov'),
						'info' => esc_html__('Info', 'citygov'),
						'success' => esc_html__('Success', 'citygov'),
						'error' => esc_html__('Error', 'citygov')
					)
				),
				"closeable" => array(
					"title" => esc_html__("Closeable box", "citygov"),
					"desc" => wp_kses_data( __("Create closeable box (with close button)", "citygov") ),
					"value" => "no",
					"type" => "switch",
					"options" => citygov_get_sc_param('yes_no')
				),
				"icon" => array(
					"title" => esc_html__("Custom icon",  'citygov'),
					"desc" => wp_kses_data( __('Select icon for the infobox from Fontello icons set. If empty - use default icon',  'citygov') ),
					"value" => "",
					"type" => "icons",
					"options" => citygov_get_sc_param('icons')
				),
				"color" => array(
					"title" => esc_html__("Text color", "citygov"),
					"desc" => wp_kses_data( __("Any color for text and headers", "citygov") ),
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", "citygov"),
					"desc" => wp_kses_data( __("Any background color for this infobox", "citygov") ),
					"value" => "",
					"type" => "color"
				),
				"_content_" => array(
					"title" => esc_html__("Infobox content", "citygov"),
					"desc" => wp_kses_data( __("Content for infobox", "citygov") ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
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
if ( !function_exists( 'citygov_sc_infobox_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_infobox_reg_shortcodes_vc');
	function citygov_sc_infobox_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_infobox",
			"name" => esc_html__("Infobox", "citygov"),
			"description" => wp_kses_data( __("Box with info or error message", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_infobox',
			"class" => "trx_sc_container trx_sc_infobox",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", "citygov"),
					"description" => wp_kses_data( __("Infobox style", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
							esc_html__('Regular', 'citygov') => 'regular',
							esc_html__('Info', 'citygov') => 'info',
							esc_html__('Success', 'citygov') => 'success',
							esc_html__('Error', 'citygov') => 'error',
							esc_html__('Result', 'citygov') => 'result'
						),
					"type" => "dropdown"
				),
				array(
					"param_name" => "closeable",
					"heading" => esc_html__("Closeable", "citygov"),
					"description" => wp_kses_data( __("Create closeable box (with close button)", "citygov") ),
					"class" => "",
					"value" => array(esc_html__('Close button', 'citygov') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Custom icon", "citygov"),
					"description" => wp_kses_data( __("Select icon for the infobox from Fontello icons set. If empty - use default icon", "citygov") ),
					"class" => "",
					"value" => citygov_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Text color", "citygov"),
					"description" => wp_kses_data( __("Any color for the text and headers", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "citygov"),
					"description" => wp_kses_data( __("Any background color for this infobox", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("Message text", "citygov"),
					"description" => wp_kses_data( __("Message for the infobox", "citygov") ),
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
				citygov_get_vc_param('margin_bottom'),
				citygov_get_vc_param('margin_left'),
				citygov_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextContainerView'
		) );
		
		class WPBakeryShortCode_Trx_Infobox extends CITYGOV_VC_ShortCodeContainer {}
	}
}
?>