<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_socials_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_socials_theme_setup' );
	function citygov_sc_socials_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_socials_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_socials_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_socials id="unique_id" size="small"]
	[trx_social_item name="facebook" url="profile url" icon="path for the icon"]
	[trx_social_item name="twitter" url="profile url"]
[/trx_socials]
*/

if (!function_exists('citygov_sc_socials')) {	
	function citygov_sc_socials($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"size" => "small",		// tiny | small | medium | large
			"shape" => "square",	// round | square
			"type" => citygov_get_theme_setting('socials_type'),	// icons | images
			"socials" => "",
			"custom" => "no",
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
		citygov_storage_set('sc_social_data', array(
			'icons' => false,
            'type' => $type
            )
        );
		if (!empty($socials)) {
			$allowed = explode('|', $socials);
			$list = array();
			for ($i=0; $i<count($allowed); $i++) {
				$s = explode('=', $allowed[$i]);
				if (!empty($s[1])) {
					$list[] = array(
						'icon'	=> $type=='images' ? citygov_get_socials_url($s[0]) : 'icon-'.trim($s[0]),
						'url'	=> $s[1]
						);
				}
			}
			if (count($list) > 0) citygov_storage_set_array('sc_social_data', 'icons', $list);
		} else if (citygov_param_is_off($custom))
			$content = do_shortcode($content);
		if (citygov_storage_get_array('sc_social_data', 'icons')===false) citygov_storage_set_array('sc_social_data', 'icons', citygov_get_custom_option('social_icons'));
		$output = citygov_prepare_socials(citygov_storage_get_array('sc_social_data', 'icons'));
		$output = $output
			? '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_socials sc_socials_type_' . esc_attr($type) . ' sc_socials_shape_' . esc_attr($shape) . ' sc_socials_size_' . esc_attr($size) . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
				. '>' 
				. ($output)
				. '</div>'
			: '';
		return apply_filters('citygov_shortcode_output', $output, 'trx_socials', $atts, $content);
	}
	citygov_require_shortcode('trx_socials', 'citygov_sc_socials');
}


if (!function_exists('citygov_sc_social_item')) {	
	function citygov_sc_social_item($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"name" => "",
			"url" => "",
			"icon" => ""
		), $atts)));
		if (!empty($name) && empty($icon)) {
			$type = citygov_storage_get_array('sc_social_data', 'type');
			if ($type=='images') {
				if (file_exists(citygov_get_socials_dir($name.'.png')))
					$icon = citygov_get_socials_url($name.'.png');
			} else
				$icon = 'icon-'.esc_attr($name);
		}
		if (!empty($icon) && !empty($url)) {
			if (citygov_storage_get_array('sc_social_data', 'icons')===false) citygov_storage_set_array('sc_social_data', 'icons', array());
			citygov_storage_set_array2('sc_social_data', 'icons', '', array(
				'icon' => $icon,
				'url' => $url
				)
			);
		}
		return '';
	}
	citygov_require_shortcode('trx_social_item', 'citygov_sc_social_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_socials_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_socials_reg_shortcodes');
	function citygov_sc_socials_reg_shortcodes() {
	
		citygov_sc_map("trx_socials", array(
			"title" => esc_html__("Social icons", "citygov"),
			"desc" => wp_kses_data( __("List of social icons (with hovers)", "citygov") ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"type" => array(
					"title" => esc_html__("Icon's type", "citygov"),
					"desc" => wp_kses_data( __("Type of the icons - images or font icons", "citygov") ),
					"value" => citygov_get_theme_setting('socials_type'),
					"options" => array(
						'icons' => esc_html__('Icons', 'citygov'),
						'images' => esc_html__('Images', 'citygov')
					),
					"type" => "checklist"
				), 
				"size" => array(
					"title" => esc_html__("Icon's size", "citygov"),
					"desc" => wp_kses_data( __("Size of the icons", "citygov") ),
					"value" => "small",
					"options" => citygov_get_sc_param('sizes'),
					"type" => "checklist"
				), 
				"shape" => array(
					"title" => esc_html__("Icon's shape", "citygov"),
					"desc" => wp_kses_data( __("Shape of the icons", "citygov") ),
					"value" => "square",
					"options" => citygov_get_sc_param('shapes'),
					"type" => "checklist"
				), 
				"socials" => array(
					"title" => esc_html__("Manual socials list", "citygov"),
					"desc" => wp_kses_data( __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebook.com/my_profile. If empty - use socials from Theme options.", "citygov") ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"custom" => array(
					"title" => esc_html__("Custom socials", "citygov"),
					"desc" => wp_kses_data( __("Make custom icons from inner shortcodes (prepare it on tabs)", "citygov") ),
					"divider" => true,
					"value" => "no",
					"options" => citygov_get_sc_param('yes_no'),
					"type" => "switch"
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
				"name" => "trx_social_item",
				"title" => esc_html__("Custom social item", "citygov"),
				"desc" => wp_kses_data( __("Custom social item: name, profile url and icon url", "citygov") ),
				"decorate" => false,
				"container" => false,
				"params" => array(
					"name" => array(
						"title" => esc_html__("Social name", "citygov"),
						"desc" => wp_kses_data( __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", "citygov") ),
						"value" => "",
						"type" => "text"
					),
					"url" => array(
						"title" => esc_html__("Your profile URL", "citygov"),
						"desc" => wp_kses_data( __("URL of your profile in specified social network", "citygov") ),
						"value" => "",
						"type" => "text"
					),
					"icon" => array(
						"title" => esc_html__("URL (source) for icon file", "citygov"),
						"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the current social icon", "citygov") ),
						"readonly" => false,
						"value" => "",
						"type" => "media"
					)
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_socials_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_socials_reg_shortcodes_vc');
	function citygov_sc_socials_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_socials",
			"name" => esc_html__("Social icons", "citygov"),
			"description" => wp_kses_data( __("Custom social icons", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_socials',
			"class" => "trx_sc_collection trx_sc_socials",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"as_parent" => array('only' => 'trx_social_item'),
			"params" => array_merge(array(
				array(
					"param_name" => "type",
					"heading" => esc_html__("Icon's type", "citygov"),
					"description" => wp_kses_data( __("Type of the icons - images or font icons", "citygov") ),
					"class" => "",
					"std" => citygov_get_theme_setting('socials_type'),
					"value" => array(
						esc_html__('Icons', 'citygov') => 'icons',
						esc_html__('Images', 'citygov') => 'images'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "size",
					"heading" => esc_html__("Icon's size", "citygov"),
					"description" => wp_kses_data( __("Size of the icons", "citygov") ),
					"class" => "",
					"std" => "small",
					"value" => array_flip(citygov_get_sc_param('sizes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "shape",
					"heading" => esc_html__("Icon's shape", "citygov"),
					"description" => wp_kses_data( __("Shape of the icons", "citygov") ),
					"class" => "",
					"std" => "square",
					"value" => array_flip(citygov_get_sc_param('shapes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "socials",
					"heading" => esc_html__("Manual socials list", "citygov"),
					"description" => wp_kses_data( __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebook.com/my_profile. If empty - use socials from Theme options.", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "custom",
					"heading" => esc_html__("Custom socials", "citygov"),
					"description" => wp_kses_data( __("Make custom icons from inner shortcodes (prepare it on tabs)", "citygov") ),
					"class" => "",
					"value" => array(esc_html__('Custom socials', 'citygov') => 'yes'),
					"type" => "checkbox"
				),
				citygov_get_vc_param('id'),
				citygov_get_vc_param('class'),
				citygov_get_vc_param('animation'),
				citygov_get_vc_param('css'),
				citygov_get_vc_param('margin_top'),
				citygov_get_vc_param('margin_bottom'),
				citygov_get_vc_param('margin_left'),
				citygov_get_vc_param('margin_right')
			))
		) );
		
		
		vc_map( array(
			"base" => "trx_social_item",
			"name" => esc_html__("Custom social item", "citygov"),
			"description" => wp_kses_data( __("Custom social item: name, profile url and icon url", "citygov") ),
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => false,
			'icon' => 'icon_trx_social_item',
			"class" => "trx_sc_single trx_sc_social_item",
			"as_child" => array('only' => 'trx_socials'),
			"as_parent" => array('except' => 'trx_socials'),
			"params" => array(
				array(
					"param_name" => "name",
					"heading" => esc_html__("Social name", "citygov"),
					"description" => wp_kses_data( __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("Your profile URL", "citygov"),
					"description" => wp_kses_data( __("URL of your profile in specified social network", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("URL (source) for icon file", "citygov"),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for the current social icon", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				)
			)
		) );
		
		class WPBakeryShortCode_Trx_Socials extends CITYGOV_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Social_Item extends CITYGOV_VC_ShortCodeSingle {}
	}
}
?>