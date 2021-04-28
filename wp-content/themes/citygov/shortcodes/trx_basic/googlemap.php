<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_googlemap_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_googlemap_theme_setup' );
	function citygov_sc_googlemap_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_googlemap_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_googlemap_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_googlemap id="unique_id" width="width_in_pixels_or_percent" height="height_in_pixels"]
//	[trx_googlemap_marker address="your_address"]
//[/trx_googlemap]

if (!function_exists('citygov_sc_googlemap')) {	
	function citygov_sc_googlemap($atts, $content = null) {
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"zoom" => 16,
			"style" => 'default',
			"scheme" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "100%",
			"height" => "400",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= citygov_get_css_dimensions_from_values($width, $height);
		if (empty($id)) $id = 'sc_googlemap_'.str_replace('.', '', mt_rand());
		if (empty($style)) $style = citygov_get_custom_option('googlemap_style');
        $api_key = citygov_get_theme_option('api_google');
        citygov_enqueue_script( 'googlemap', citygov_get_protocol().'://maps.google.com/maps/api/js'.($api_key ? '?key='.$api_key : ''), array(), null, true );
        citygov_enqueue_script( 'citygov-googlemap-script', citygov_get_file_url('js/core.googlemap.js'), array(), null, true );
		citygov_storage_set('sc_googlemap_markers', array());
		$content = do_shortcode($content);
		$output = '';
		$markers = citygov_storage_get('sc_googlemap_markers');
		if (count($markers) == 0) {
			$markers[] = array(
				'title' => citygov_get_custom_option('googlemap_title'),
				'description' => citygov_strmacros(citygov_get_custom_option('googlemap_description')),
				'latlng' => citygov_get_custom_option('googlemap_latlng'),
				'address' => citygov_get_custom_option('googlemap_address'),
				'point' => citygov_get_custom_option('googlemap_marker')
			);
		}
		$output .= 
			($content ? '<div id="'.esc_attr($id).'_wrap" class="sc_googlemap_wrap'
					. ($scheme && !citygov_param_is_off($scheme) && !citygov_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
					. '">' : '')
			. '<div id="'.esc_attr($id).'"'
				. ' class="sc_googlemap'. (!empty($class) ? ' '.esc_attr($class) : '').'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
				. ' data-zoom="'.esc_attr($zoom).'"'
				. ' data-style="'.esc_attr($style).'"'
				. '>';
		$cnt = 0;
		foreach ($markers as $marker) {
			$cnt++;
			if (empty($marker['id'])) $marker['id'] = $id.'_'.intval($cnt);
			$output .= '<div id="'.esc_attr($marker['id']).'" class="sc_googlemap_marker"'
				. ' data-title="'.esc_attr($marker['title']).'"'
				. ' data-description="'.esc_attr(citygov_strmacros($marker['description'])).'"'
				. ' data-address="'.esc_attr($marker['address']).'"'
				. ' data-latlng="'.esc_attr($marker['latlng']).'"'
				. ' data-point="'.esc_attr($marker['point']).'"'
				. '></div>';
		}
		$output .= '</div>'
			. ($content ? '<div class="sc_googlemap_content">' . trim($content) . '</div></div>' : '');
			
		return apply_filters('citygov_shortcode_output', $output, 'trx_googlemap', $atts, $content);
	}
	citygov_require_shortcode("trx_googlemap", "citygov_sc_googlemap");
}


if (!function_exists('citygov_sc_googlemap_marker')) {	
	function citygov_sc_googlemap_marker($atts, $content = null) {
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"address" => "",
			"latlng" => "",
			"point" => "",
			// Common params
			"id" => ""
		), $atts)));
		if (!empty($point)) {
			if ($point > 0) {
				$attach = wp_get_attachment_image_src( $point, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$point = $attach[0];
			}
		}
		citygov_storage_set_array('sc_googlemap_markers', '', array(
			'id' => $id,
			'title' => $title,
			'description' => do_shortcode($content),
			'latlng' => $latlng,
			'address' => $address,
			'point' => $point ? $point : citygov_get_custom_option('googlemap_marker')
			)
		);
		return '';
	}
	citygov_require_shortcode("trx_googlemap_marker", "citygov_sc_googlemap_marker");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_googlemap_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_googlemap_reg_shortcodes');
	function citygov_sc_googlemap_reg_shortcodes() {
	
		citygov_sc_map("trx_googlemap", array(
			"title" => esc_html__("Google map", "citygov"),
			"desc" => wp_kses_data( __("Insert Google map with specified markers", "citygov") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"zoom" => array(
					"title" => esc_html__("Zoom", "citygov"),
					"desc" => wp_kses_data( __("Map zoom factor", "citygov") ),
					"divider" => true,
					"value" => 16,
					"min" => 1,
					"max" => 20,
					"type" => "spinner"
				),
				"style" => array(
					"title" => esc_html__("Map style", "citygov"),
					"desc" => wp_kses_data( __("Select map style", "citygov") ),
					"value" => "default",
					"type" => "checklist",
					"options" => citygov_get_sc_param('googlemap_styles')
				),
				"scheme" => array(
					"title" => esc_html__("Color scheme", "citygov"),
					"desc" => wp_kses_data( __("Select color scheme for this block", "citygov") ),
					"value" => "",
					"type" => "checklist",
					"options" => citygov_get_sc_param('schemes')
				),
				"width" => citygov_shortcodes_width('100%'),
				"height" => citygov_shortcodes_height(240),
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
				"name" => "trx_googlemap_marker",
				"title" => esc_html__("Google map marker", "citygov"),
				"desc" => wp_kses_data( __("Google map marker", "citygov") ),
				"decorate" => false,
				"container" => true,
				"params" => array(
					"address" => array(
						"title" => esc_html__("Address", "citygov"),
						"desc" => wp_kses_data( __("Address of this marker", "citygov") ),
						"value" => "",
						"type" => "text"
					),
					"latlng" => array(
						"title" => esc_html__("Latitude and Longitude", "citygov"),
						"desc" => wp_kses_data( __("Comma separated marker's coorditanes (instead Address)", "citygov") ),
						"value" => "",
						"type" => "text"
					),
					"point" => array(
						"title" => esc_html__("URL for marker image file", "citygov"),
						"desc" => wp_kses_data( __("Select or upload image or write URL from other site for this marker. If empty - use default marker", "citygov") ),
						"readonly" => false,
						"value" => "",
						"type" => "media"
					),
					"title" => array(
						"title" => esc_html__("Title", "citygov"),
						"desc" => wp_kses_data( __("Title for this marker", "citygov") ),
						"value" => "",
						"type" => "text"
					),
					"_content_" => array(
						"title" => esc_html__("Description", "citygov"),
						"desc" => wp_kses_data( __("Description for this marker", "citygov") ),
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
					),
					"id" => citygov_get_sc_param('id')
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_googlemap_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_googlemap_reg_shortcodes_vc');
	function citygov_sc_googlemap_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_googlemap",
			"name" => esc_html__("Google map", "citygov"),
			"description" => wp_kses_data( __("Insert Google map with desired address or coordinates", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_googlemap',
			"class" => "trx_sc_collection trx_sc_googlemap",
			"content_element" => true,
			"is_container" => true,
			"as_parent" => array('only' => 'trx_googlemap_marker,trx_form,trx_section,trx_block,trx_promo'),
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "zoom",
					"heading" => esc_html__("Zoom", "citygov"),
					"description" => wp_kses_data( __("Map zoom factor", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "16",
					"type" => "textfield"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", "citygov"),
					"description" => wp_kses_data( __("Map custom style", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('googlemap_styles')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", "citygov"),
					"description" => wp_kses_data( __("Select color scheme for this block", "citygov") ),
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('schemes')),
					"type" => "dropdown"
				),
				citygov_get_vc_param('id'),
				citygov_get_vc_param('class'),
				citygov_get_vc_param('animation'),
				citygov_get_vc_param('css'),
				citygov_vc_width('100%'),
				citygov_vc_height(240),
				citygov_get_vc_param('margin_top'),
				citygov_get_vc_param('margin_bottom'),
				citygov_get_vc_param('margin_left'),
				citygov_get_vc_param('margin_right')
			)
		) );
		
		vc_map( array(
			"base" => "trx_googlemap_marker",
			"name" => esc_html__("Googlemap marker", "citygov"),
			"description" => wp_kses_data( __("Insert new marker into Google map", "citygov") ),
			"class" => "trx_sc_collection trx_sc_googlemap_marker",
			'icon' => 'icon_trx_googlemap_marker',
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => true,
			"as_child" => array('only' => 'trx_googlemap'), // Use only|except attributes to limit parent (separate multiple values with comma)
			"params" => array(
				array(
					"param_name" => "address",
					"heading" => esc_html__("Address", "citygov"),
					"description" => wp_kses_data( __("Address of this marker", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "latlng",
					"heading" => esc_html__("Latitude and Longitude", "citygov"),
					"description" => wp_kses_data( __("Comma separated marker's coorditanes (instead Address)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "citygov"),
					"description" => wp_kses_data( __("Title for this marker", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "point",
					"heading" => esc_html__("URL for marker image file", "citygov"),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for this marker. If empty - use default marker", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				citygov_get_vc_param('id')
			)
		) );
		
		class WPBakeryShortCode_Trx_Googlemap extends CITYGOV_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Googlemap_Marker extends CITYGOV_VC_ShortCodeCollection {}
	}
}
?>