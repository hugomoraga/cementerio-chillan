<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_image_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_image_theme_setup' );
	function citygov_sc_image_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_image_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_image_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_image id="unique_id" src="image_url" width="width_in_pixels" height="height_in_pixels" title="image's_title" align="left|right"]
*/

if (!function_exists('citygov_sc_image')) {	
	function citygov_sc_image($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"align" => "",
			"shape" => "square",
			"src" => "",
			"url" => "",
			"icon" => "",
			"link" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "",
			"height" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= citygov_get_css_dimensions_from_values($width, $height);
		$src = $src!='' ? $src : $url;
		if ($src > 0) {
			$attach = wp_get_attachment_image_src( $src, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$src = $attach[0];
		}
		if (!empty($width) || !empty($height)) {
			$w = !empty($width) && strlen(intval($width)) == strlen($width) ? $width : null;
			$h = !empty($height) && strlen(intval($height)) == strlen($height) ? $height : null;
			if ($w || $h) $src = citygov_get_resized_image_url($src, $w, $h);
		}
		if (trim($link)) citygov_enqueue_popup();
		$output = empty($src) ? '' : ('<figure' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_image ' . ($align && $align!='none' ? ' align' . esc_attr($align) : '') . (!empty($shape) ? ' sc_image_shape_'.esc_attr($shape) : '') . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
			. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '>'
				. (trim($link) ? '<a href="'.esc_url($link).'">' : '')
				. '<img src="'.esc_url($src).'" alt="" />'
				. (trim($link) ? '</a>' : '')
				. (trim($title) || trim($icon) ? '<figcaption><span'.($icon ? ' class="'.esc_attr($icon).'"' : '').'></span> ' . ($title) . '</figcaption>' : '')
			. '</figure>');
		return apply_filters('citygov_shortcode_output', $output, 'trx_image', $atts, $content);
	}
	citygov_require_shortcode('trx_image', 'citygov_sc_image');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_image_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_image_reg_shortcodes');
	function citygov_sc_image_reg_shortcodes() {
	
		citygov_sc_map("trx_image", array(
			"title" => esc_html__("Image", "citygov"),
			"desc" => wp_kses_data( __("Insert image into your post (page)", "citygov") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"url" => array(
					"title" => esc_html__("URL for image file", "citygov"),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site", "citygov") ),
					"readonly" => false,
					"value" => "",
					"type" => "media",
					"before" => array(
						'sizes' => true		// If you want allow user select thumb size for image. Otherwise, thumb size is ignored - image fullsize used
					)
				),
				"title" => array(
					"title" => esc_html__("Title", "citygov"),
					"desc" => wp_kses_data( __("Image title (if need)", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"icon" => array(
					"title" => esc_html__("Icon before title",  'citygov'),
					"desc" => wp_kses_data( __('Select icon for the title from Fontello icons set',  'citygov') ),
					"value" => "",
					"type" => "icons",
					"options" => citygov_get_sc_param('icons')
				),
				"align" => array(
					"title" => esc_html__("Float image", "citygov"),
					"desc" => wp_kses_data( __("Float image to left or right side", "citygov") ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('float')
				), 
				"shape" => array(
					"title" => esc_html__("Image Shape", "citygov"),
					"desc" => wp_kses_data( __("Shape of the image: square (rectangle) or round", "citygov") ),
					"value" => "square",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						"square" => esc_html__('Square', 'citygov'),
						"round" => esc_html__('Round', 'citygov')
					)
				), 
				"link" => array(
					"title" => esc_html__("Link", "citygov"),
					"desc" => wp_kses_data( __("The link URL from the image", "citygov") ),
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
if ( !function_exists( 'citygov_sc_image_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_image_reg_shortcodes_vc');
	function citygov_sc_image_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_image",
			"name" => esc_html__("Image", "citygov"),
			"description" => wp_kses_data( __("Insert image", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_image',
			"class" => "trx_sc_single trx_sc_image",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "url",
					"heading" => esc_html__("Select image", "citygov"),
					"description" => wp_kses_data( __("Select image from library", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Image alignment", "citygov"),
					"description" => wp_kses_data( __("Align image to left or right side", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "shape",
					"heading" => esc_html__("Image shape", "citygov"),
					"description" => wp_kses_data( __("Shape of the image: square or round", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Square', 'citygov') => 'square',
						esc_html__('Round', 'citygov') => 'round'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "citygov"),
					"description" => wp_kses_data( __("Image's title", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Title's icon", "citygov"),
					"description" => wp_kses_data( __("Select icon for the title from Fontello icons set", "citygov") ),
					"class" => "",
					"value" => citygov_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link", "citygov"),
					"description" => wp_kses_data( __("The link URL from the image", "citygov") ),
					"admin_label" => true,
					"class" => "",
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
			)
		) );
		
		class WPBakeryShortCode_Trx_Image extends CITYGOV_VC_ShortCodeSingle {}
	}
}
?>