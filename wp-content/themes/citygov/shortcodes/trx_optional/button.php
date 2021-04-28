<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_button_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_button_theme_setup' );
	function citygov_sc_button_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_button_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_button_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_button id="unique_id" type="square|round" fullsize="0|1" style="global|light|dark" size="mini|medium|big|huge|banner" icon="icon-name" link='#' target='']Button caption[/trx_button]
*/

if (!function_exists('citygov_sc_button')) {	
	function citygov_sc_button($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"type" => "square",
			"style" => "filled",
			"size" => "small",
            "style_color" => "original",
			"icon" => "",
			"color" => "",
			"bg_color" => "",
			"link" => "",
			"target" => "",
			"align" => "",
			"rel" => "",
			"popup" => "no",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= citygov_get_css_dimensions_from_values($width, $height)
			. ($color !== '' ? 'color:' . esc_attr($color) .';' : '')
			. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) . '; border-color:'. esc_attr($bg_color) .';' : '');
		if (citygov_param_is_on($popup)) citygov_enqueue_popup('magnific');
		$output = '<a href="' . (empty($link) ? '#' : $link) . '"'
			. (!empty($target) ? ' target="'.esc_attr($target).'"' : '')
			. (!empty($rel) ? ' rel="'.esc_attr($rel).'"' : '')
			. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
			. ' class="sc_button sc_button_' . esc_attr($type) 
					. ' sc_button_style_' . esc_attr($style)
                    . ' style_' . esc_attr($style_color)
                    . ' sc_button_size_' . esc_attr($size)
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ($icon!='' ? '  sc_button_iconed '. esc_attr($icon) : '') 
					. (citygov_param_is_on($popup) ? ' sc_popup_link' : '') 
					. '"'
			. ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. '>'
			. do_shortcode($content)
			. '</a>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_button', $atts, $content);
	}
	citygov_require_shortcode('trx_button', 'citygov_sc_button');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_button_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_button_reg_shortcodes');
	function citygov_sc_button_reg_shortcodes() {
	
		citygov_sc_map("trx_button", array(
			"title" => esc_html__("Button", "citygov"),
			"desc" => wp_kses_data( __("Button with link", "citygov") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Caption", "citygov"),
					"desc" => wp_kses_data( __("Button caption", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"type" => array(
					"title" => esc_html__("Button's shape", "citygov"),
					"desc" => wp_kses_data( __("Select button's shape", "citygov") ),
					"value" => "square",
					"size" => "medium",
					"options" => array(
						'square' => esc_html__('Square', 'citygov'),
						'round' => esc_html__('Round', 'citygov')
					),
					"type" => "switch"
				), 
				"style" => array(
					"title" => esc_html__("Button's style", "citygov"),
					"desc" => wp_kses_data( __("Select button's style", "citygov") ),
					"value" => "default",
					"dir" => "horizontal",
					"options" => array(
						'filled' => esc_html__('Filled', 'citygov'),
						'border' => esc_html__('Border', 'citygov')
					),
					"type" => "checklist"
				),
                "style_color" => array(
                    "title" => esc_html__("Button's style color", "citygov"),
                    "desc" => wp_kses_data( __("Select button's style color", "citygov") ),
                    "value" => "original",
                    "dir" => "horizontal",
                    "options" => array(
                        'original' => esc_html__('Original', 'citygov'),
                        'dark' => esc_html__('Dark', 'citygov')
                    ),
                    "type" => "checklist"
                ),
				"size" => array(
					"title" => esc_html__("Button's size", "citygov"),
					"desc" => wp_kses_data( __("Select button's size", "citygov") ),
					"value" => "small",
					"dir" => "horizontal",
					"options" => array(
						'small' => esc_html__('Small', 'citygov'),
						'medium' => esc_html__('Medium', 'citygov'),
						'large' => esc_html__('Large', 'citygov')
					),
					"type" => "checklist"
				), 
				"icon" => array(
					"title" => esc_html__("Button's icon",  'citygov'),
					"desc" => wp_kses_data( __('Select icon for the title from Fontello icons set',  'citygov') ),
					"value" => "",
					"type" => "icons",
					"options" => citygov_get_sc_param('icons')
				),
				"color" => array(
					"title" => esc_html__("Button's text color", "citygov"),
					"desc" => wp_kses_data( __("Any color for button's caption", "citygov") ),
					"std" => "",
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Button's backcolor", "citygov"),
					"desc" => wp_kses_data( __("Any color for button's background", "citygov") ),
					"value" => "",
					"type" => "color"
				),
				"align" => array(
					"title" => esc_html__("Button's alignment", "citygov"),
					"desc" => wp_kses_data( __("Align button to left, center or right", "citygov") ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('align')
				), 
				"link" => array(
					"title" => esc_html__("Link URL", "citygov"),
					"desc" => wp_kses_data( __("URL for link on button click", "citygov") ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"target" => array(
					"title" => esc_html__("Link target", "citygov"),
					"desc" => wp_kses_data( __("Target for link on button click", "citygov") ),
					"dependency" => array(
						'link' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"popup" => array(
					"title" => esc_html__("Open link in popup", "citygov"),
					"desc" => wp_kses_data( __("Open link target in popup window", "citygov") ),
					"dependency" => array(
						'link' => array('not_empty')
					),
					"value" => "no",
					"type" => "switch",
					"options" => citygov_get_sc_param('yes_no')
				), 
				"rel" => array(
					"title" => esc_html__("Rel attribute", "citygov"),
					"desc" => wp_kses_data( __("Rel attribute for button's link (if need)", "citygov") ),
					"dependency" => array(
						'link' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"width" => citygov_shortcodes_width(),
				"height" => citygov_shortcodes_height(),
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
if ( !function_exists( 'citygov_sc_button_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_button_reg_shortcodes_vc');
	function citygov_sc_button_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_button",
			"name" => esc_html__("Button", "citygov"),
			"description" => wp_kses_data( __("Button with link", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_button',
			"class" => "trx_sc_single trx_sc_button",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "content",
					"heading" => esc_html__("Caption", "citygov"),
					"description" => wp_kses_data( __("Button caption", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "type",
					"heading" => esc_html__("Button's shape", "citygov"),
					"description" => wp_kses_data( __("Select button's shape", "citygov") ),
					"class" => "",
					"value" => array(
						esc_html__('Square', 'citygov') => 'square',
						esc_html__('Round', 'citygov') => 'round'
					),
					"type" => "dropdown"
				),
                array(
                    "param_name" => "style_color",
                    "heading" => esc_html__("Button's style color", "citygov"),
                    "description" => wp_kses_data( __("Select button's style color", "citygov") ),
                    "class" => "",
                    "value" => array(
                        esc_html__('Original', 'citygov') => 'original',
                        esc_html__('Dark', 'citygov') => 'dark'
                    ),
                    "type" => "dropdown"
                ),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Button's style", "citygov"),
					"description" => wp_kses_data( __("Select button's style", "citygov") ),
					"class" => "",
					"value" => array(
						esc_html__('Filled', 'citygov') => 'filled',
						esc_html__('Border', 'citygov') => 'border'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "size",
					"heading" => esc_html__("Button's size", "citygov"),
					"description" => wp_kses_data( __("Select button's size", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Small', 'citygov') => 'small',
						esc_html__('Medium', 'citygov') => 'medium',
						esc_html__('Large', 'citygov') => 'large'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Button's icon", "citygov"),
					"description" => wp_kses_data( __("Select icon for the title from Fontello icons set", "citygov") ),
					"class" => "",
					"value" => citygov_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Button's text color", "citygov"),
					"description" => wp_kses_data( __("Any color for button's caption", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Button's backcolor", "citygov"),
					"description" => wp_kses_data( __("Any color for button's background", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Button's alignment", "citygov"),
					"description" => wp_kses_data( __("Align button to left, center or right", "citygov") ),
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", "citygov"),
					"description" => wp_kses_data( __("URL for the link on button click", "citygov") ),
					"class" => "",
					"group" => esc_html__('Link', 'citygov'),
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "target",
					"heading" => esc_html__("Link target", "citygov"),
					"description" => wp_kses_data( __("Target for the link on button click", "citygov") ),
					"class" => "",
					"group" => esc_html__('Link', 'citygov'),
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "popup",
					"heading" => esc_html__("Open link in popup", "citygov"),
					"description" => wp_kses_data( __("Open link target in popup window", "citygov") ),
					"class" => "",
					"group" => esc_html__('Link', 'citygov'),
					"value" => array(esc_html__('Open in popup', 'citygov') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "rel",
					"heading" => esc_html__("Rel attribute", "citygov"),
					"description" => wp_kses_data( __("Rel attribute for the button's link (if need", "citygov") ),
					"class" => "",
					"group" => esc_html__('Link', 'citygov'),
					"value" => "",
					"type" => "textfield"
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
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Button extends CITYGOV_VC_ShortCodeSingle {}
	}
}
?>