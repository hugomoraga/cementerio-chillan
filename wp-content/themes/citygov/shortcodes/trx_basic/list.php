<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_list_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_list_theme_setup' );
	function citygov_sc_list_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_list_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_list_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_list id="unique_id" style="arrows|iconed|ol|ul"]
	[trx_list_item id="unique_id" title="title_of_element"]Et adipiscing integer.[/trx_list_item]
	[trx_list_item]A pulvinar ut, parturient enim porta ut sed, mus amet nunc, in.[/trx_list_item]
	[trx_list_item]Duis sociis, elit odio dapibus nec, dignissim purus est magna integer.[/trx_list_item]
	[trx_list_item]Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus.[/trx_list_item]
[/trx_list]
*/

if (!function_exists('citygov_sc_list')) {	
	function citygov_sc_list($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "ul",
			"icon" => "icon-right",
			"icon_color" => "",
			"color" => "",
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
		$css .= $color !== '' ? 'color:' . esc_attr($color) .';' : '';
		if (trim($style) == '' || (trim($icon) == '' && $style=='iconed')) $style = 'ul';
		citygov_storage_set('sc_list_data', array(
			'counter' => 0,
            'icon' => empty($icon) || citygov_param_is_inherit($icon) ? "icon-right" : $icon,
            'icon_color' => $icon_color,
            'style' => $style
            )
        );
		$output = '<' . ($style=='ol' ? 'ol' : 'ul')
				. ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_list sc_list_style_' . esc_attr($style) . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
				. '>'
				. do_shortcode($content)
				. '</' .($style=='ol' ? 'ol' : 'ul') . '>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_list', $atts, $content);
	}
	citygov_require_shortcode('trx_list', 'citygov_sc_list');
}


if (!function_exists('citygov_sc_list_item')) {	
	function citygov_sc_list_item($atts, $content=null) {
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts( array(
			// Individual params
			"color" => "",
			"icon" => "",
			"icon_color" => "",
			"title" => "",
			"link" => "",
			"target" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		citygov_storage_inc_array('sc_list_data', 'counter');
		$css .= $color !== '' ? 'color:' . esc_attr($color) .';' : '';
		if (trim($icon) == '' || citygov_param_is_inherit($icon)) $icon = citygov_storage_get_array('sc_list_data', 'icon');
		if (trim($color) == '' || citygov_param_is_inherit($icon_color)) $icon_color = citygov_storage_get_array('sc_list_data', 'icon_color');
		$content = do_shortcode($content);
		if (empty($content)) $content = $title;
		$output = '<li' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_list_item'
			. (!empty($class) ? ' '.esc_attr($class) : '')
			. (citygov_storage_get_array('sc_list_data', 'counter') % 2 == 1 ? ' odd' : ' even') 
			. (citygov_storage_get_array('sc_list_data', 'counter') == 1 ? ' first' : '')  
			. '"' 
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. ($title ? ' title="'.esc_attr($title).'"' : '') 
			. '>'
			. (!empty($link) ? '<a href="'.esc_url($link).'"' . (!empty($target) ? ' target="'.esc_attr($target).'"' : '') . '>' : '')
			. (citygov_storage_get_array('sc_list_data', 'style')=='iconed' && $icon!='' ? '<span class="sc_list_icon '.esc_attr($icon).'"'.($icon_color !== '' ? ' style="color:'.esc_attr($icon_color).';"' : '').'></span>' : '')
            . '<span class="list_content">'
			. trim($content)
            . '</span>'
			. (!empty($link) ? '</a>': '')
			. '</li>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_list_item', $atts, $content);
	}
	citygov_require_shortcode('trx_list_item', 'citygov_sc_list_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_list_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_list_reg_shortcodes');
	function citygov_sc_list_reg_shortcodes() {
	
		citygov_sc_map("trx_list", array(
			"title" => esc_html__("List", "citygov"),
			"desc" => wp_kses_data( __("List items with specific bullets", "citygov") ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Bullet's style", "citygov"),
					"desc" => wp_kses_data( __("Bullet's style for each list item", "citygov") ),
					"value" => "ul",
					"type" => "checklist",
					"options" => citygov_get_sc_param('list_styles')
				), 
				"color" => array(
					"title" => esc_html__("Color", "citygov"),
					"desc" => wp_kses_data( __("List items color", "citygov") ),
					"value" => "",
					"type" => "color"
				),
				"icon" => array(
					"title" => esc_html__('List icon',  'citygov'),
					"desc" => wp_kses_data( __("Select list icon from Fontello icons set (only for style=Iconed)",  'citygov') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "",
					"type" => "icons",
					"options" => citygov_get_sc_param('icons')
				),
				"icon_color" => array(
					"title" => esc_html__("Icon color", "citygov"),
					"desc" => wp_kses_data( __("List icons color", "citygov") ),
					"value" => "",
					"dependency" => array(
						'style' => array('iconed')
					),
					"type" => "color"
				),
				"top" => citygov_get_sc_param('top'),
				"bottom" => citygov_get_sc_param('bottom'),
				"left" => citygov_get_sc_param('left'),
				"right" => citygov_get_sc_param('right'),
				"id" => citygov_get_sc_param('id'),
				"class" => citygov_get_sc_param('class'),
				"animation" => citygov_get_sc_param('animation'),
				"css" => citygov_get_sc_param('css')
			),
			"children" => array(
				"name" => "trx_list_item",
				"title" => esc_html__("Item", "citygov"),
				"desc" => wp_kses_data( __("List item with specific bullet", "citygov") ),
				"decorate" => false,
				"container" => true,
				"params" => array(
					"_content_" => array(
						"title" => esc_html__("List item content", "citygov"),
						"desc" => wp_kses_data( __("Current list item content", "citygov") ),
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
					),
					"title" => array(
						"title" => esc_html__("List item title", "citygov"),
						"desc" => wp_kses_data( __("Current list item title (show it as tooltip)", "citygov") ),
						"value" => "",
						"type" => "text"
					),
					"color" => array(
						"title" => esc_html__("Color", "citygov"),
						"desc" => wp_kses_data( __("Text color for this item", "citygov") ),
						"value" => "",
						"type" => "color"
					),
					"icon" => array(
						"title" => esc_html__('List icon',  'citygov'),
						"desc" => wp_kses_data( __("Select list item icon from Fontello icons set (only for style=Iconed)",  'citygov') ),
						"value" => "",
						"type" => "icons",
						"options" => citygov_get_sc_param('icons')
					),
					"icon_color" => array(
						"title" => esc_html__("Icon color", "citygov"),
						"desc" => wp_kses_data( __("Icon color for this item", "citygov") ),
						"value" => "",
						"type" => "color"
					),
					"link" => array(
						"title" => esc_html__("Link URL", "citygov"),
						"desc" => wp_kses_data( __("Link URL for the current list item", "citygov") ),
						"divider" => true,
						"value" => "",
						"type" => "text"
					),
					"target" => array(
						"title" => esc_html__("Link target", "citygov"),
						"desc" => wp_kses_data( __("Link target for the current list item", "citygov") ),
						"value" => "",
						"type" => "text"
					),
					"id" => citygov_get_sc_param('id'),
					"class" => citygov_get_sc_param('class'),
					"css" => citygov_get_sc_param('css')
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_list_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_list_reg_shortcodes_vc');
	function citygov_sc_list_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_list",
			"name" => esc_html__("List", "citygov"),
			"description" => wp_kses_data( __("List items with specific bullets", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			"class" => "trx_sc_collection trx_sc_list",
			'icon' => 'icon_trx_list',
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"as_parent" => array('only' => 'trx_list_item'),
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Bullet's style", "citygov"),
					"description" => wp_kses_data( __("Bullet's style for each list item", "citygov") ),
					"class" => "",
					"admin_label" => true,
					"value" => array_flip(citygov_get_sc_param('list_styles')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Color", "citygov"),
					"description" => wp_kses_data( __("List items color", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("List icon", "citygov"),
					"description" => wp_kses_data( __("Select list icon from Fontello icons set (only for style=Iconed)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => citygov_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_color",
					"heading" => esc_html__("Icon color", "citygov"),
					"description" => wp_kses_data( __("List icons color", "citygov") ),
					"class" => "",
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => "",
					"type" => "colorpicker"
				),
				citygov_get_vc_param('id'),
				citygov_get_vc_param('class'),
				citygov_get_vc_param('animation'),
				citygov_get_vc_param('css'),
				citygov_get_vc_param('margin_top'),
				citygov_get_vc_param('margin_bottom'),
				citygov_get_vc_param('margin_left'),
				citygov_get_vc_param('margin_right')
			),
			'default_content' => '
				[trx_list_item][/trx_list_item]
				[trx_list_item][/trx_list_item]
			'
		) );
		
		
		vc_map( array(
			"base" => "trx_list_item",
			"name" => esc_html__("List item", "citygov"),
			"description" => wp_kses_data( __("List item with specific bullet", "citygov") ),
			"class" => "trx_sc_container trx_sc_list_item",
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => true,
			'icon' => 'icon_trx_list_item',
			"as_child" => array('only' => 'trx_list'), // Use only|except attributes to limit parent (separate multiple values with comma)
			"as_parent" => array('except' => 'trx_list'),
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("List item title", "citygov"),
					"description" => wp_kses_data( __("Title for the current list item (show it as tooltip)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", "citygov"),
					"description" => wp_kses_data( __("Link URL for the current list item", "citygov") ),
					"admin_label" => true,
					"group" => esc_html__('Link', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "target",
					"heading" => esc_html__("Link target", "citygov"),
					"description" => wp_kses_data( __("Link target for the current list item", "citygov") ),
					"admin_label" => true,
					"group" => esc_html__('Link', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Color", "citygov"),
					"description" => wp_kses_data( __("Text color for this item", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("List item icon", "citygov"),
					"description" => wp_kses_data( __("Select list item icon from Fontello icons set (only for style=Iconed)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => citygov_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_color",
					"heading" => esc_html__("Icon color", "citygov"),
					"description" => wp_kses_data( __("Icon color for this item", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("List item text", "citygov"),
					"description" => wp_kses_data( __("Current list item content", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
*/
				citygov_get_vc_param('id'),
				citygov_get_vc_param('class'),
				citygov_get_vc_param('css')
			)
		
		) );
		
		class WPBakeryShortCode_Trx_List extends CITYGOV_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_List_Item extends CITYGOV_VC_ShortCodeContainer {}
	}
}
?>