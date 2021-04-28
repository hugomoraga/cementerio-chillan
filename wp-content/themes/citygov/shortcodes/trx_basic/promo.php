<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_promo_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_promo_theme_setup' );
	function citygov_sc_promo_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_promo_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_promo_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */


if (!function_exists('citygov_sc_promo')) {	
	function citygov_sc_promo($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"size" => "large",
			"align" => "none",
			"image" => "",
			"image_position" => "left",
			"image_width" => "50%",
			"text_margins" => '',
			"text_align" => "left",
			"scheme" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link_caption" => esc_html__('Learn more', 'citygov'),
			"link" => '',
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
	
		if ($image > 0) {
			$attach = wp_get_attachment_image_src($image, 'full');
			if (isset($attach[0]) && $attach[0]!='')
				$image = $attach[0];
		}
		if ($image == '') {
			$image_width = '0%';
			$text_margins = '';
		}
		
		$width  = citygov_prepare_css_value($width);
		$height = citygov_prepare_css_value($height);
		
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= citygov_get_css_dimensions_from_values($width, $height);
		
		$css_image = (!empty($image) ? 'background-image:url(' . esc_url($image) . ');' : '')
				     . (!empty($image_width) ? 'width:'.trim($image_width).';' : '')
				     . (!empty($image_position) ? $image_position.': 0;' : '');
	
		$text_width = citygov_strpos($image_width, '%')!==false
						? (100 - (int) str_replace('%', '', $image_width)).'%'
						: 'calc(100%-'.trim($image_width).')';
		$css_text = 'width: '.esc_attr($text_width).'; float: '.($image_position=='left' ? 'right' : 'left').';'.(!empty($text_margins) ? ' margin:'.esc_attr($text_margins).';' : '');
		
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_promo' 
						. ($class ? ' ' . esc_attr($class) : '') 
						. ($scheme && !citygov_param_is_off($scheme) && !citygov_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
						. ($size ? ' sc_promo_size_'.esc_attr($size) : '') 
						. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
						. (empty($image) ? ' no_image' : '')
						. '"'
					. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
					. ($css ? 'style="'.esc_attr($css).'"' : '')
					.'>' 
					. '<div class="sc_promo_inner">'
						. '<div class="sc_promo_image" style="'.esc_attr($css_image).'"></div>'
						. '<div class="sc_promo_block sc_align_'.esc_attr($text_align).'" style="'.esc_attr($css_text).'">'
							. '<div class="sc_promo_block_inner">'
									. (!empty($subtitle) ? '<h6 class="sc_promo_subtitle sc_item_subtitle">' . trim(citygov_strmacros($subtitle)) . '</h6>' : '')
									. (!empty($title) ? '<h2 class="sc_promo_title sc_item_title">' . trim(citygov_strmacros($title)) . '</h2>' : '')
									. (!empty($description) ? '<div class="sc_promo_descr sc_item_descr">' . trim(citygov_strmacros($description)) . '</div>' : '')
									. (!empty($content) ? '<div class="sc_promo_content">'.do_shortcode($content).'</div>' : '')
									. (!empty($link) ? '<div class="sc_promo_button sc_item_button">'.citygov_do_shortcode('[trx_button link="'.esc_url($link).'" icon="icon-right"]'.esc_html($link_caption).'[/trx_button]').'</div>' : '')
							. '</div>'
						. '</div>'
					. '</div>'
				. '</div>';
	
	
	
		return apply_filters('citygov_shortcode_output', $output, 'trx_promo', $atts, $content);
	}
	citygov_require_shortcode('trx_promo', 'citygov_sc_promo');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_promo_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_promo_reg_shortcodes');
	function citygov_sc_promo_reg_shortcodes() {
	
		citygov_sc_map("trx_promo", array(
			"title" => esc_html__("Promo", "citygov"),
			"desc" => wp_kses_data( __("Insert promo diagramm in your page (post)", "citygov") ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"align" => array(
					"title" => esc_html__("Alignment of the promo block", "citygov"),
					"desc" => wp_kses_data( __("Align whole promo block to left or right side of the page or parent container", "citygov") ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('float')
				), 
				"size" => array(
					"title" => esc_html__("Size of the promo block", "citygov"),
					"desc" => wp_kses_data( __("Size of the promo block: large - one in the row, small - insize two or greater columns", "citygov") ),
					"value" => "large",
					"type" => "switch",
					"options" => array(
						'small' => esc_html__('Small', 'citygov'),
						'large' => esc_html__('Large', 'citygov')
					)
				), 
				"image" => array(
					"title" => esc_html__("Image URL", "citygov"),
					"desc" => wp_kses_data( __("Select the promo image from the library for this section", "citygov") ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"image_position" => array(
					"title" => esc_html__("Image position", "citygov"),
					"desc" => wp_kses_data( __("Place the image to the left or to the right from the text block", "citygov") ),
					"value" => "left",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('hpos')
				),
				"image_width" => array(
					"title" => esc_html__("Image width", "citygov"),
					"desc" => wp_kses_data( __("Width (in pixels or percents) of the block with image", "citygov") ),
					"value" => "50%",
					"type" => "text"
				),
				"text_margins" => array(
					"title" => esc_html__("Text margins", "citygov"),
					"desc" => wp_kses_data( __("Margins for the all sides of the text block (Example: 30px 10px 40px 30px = top right botton left OR 30px = equal for all sides)", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"text_align" => array(
					"title" => esc_html__("Text alignment", "citygov"),
					"desc" => wp_kses_data( __("Align the text inside the block", "citygov") ),
					"value" => "left",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('align')
				),
				"scheme" => array(
					"title" => esc_html__("Color scheme", "citygov"),
					"desc" => wp_kses_data( __("Select color scheme for the section with text", "citygov") ),
					"value" => "",
					"type" => "checklist",
					"options" => citygov_get_sc_param('schemes')
				),
				"title" => array(
					"title" => esc_html__("Title", "citygov"),
					"desc" => wp_kses_data( __("Title for the block", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"subtitle" => array(
					"title" => esc_html__("Subtitle", "citygov"),
					"desc" => wp_kses_data( __("Subtitle for the block", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"description" => array(
					"title" => esc_html__("Description", "citygov"),
					"desc" => wp_kses_data( __("Short description for the block", "citygov") ),
					"value" => "",
					"type" => "textarea"
				),
				"link" => array(
					"title" => esc_html__("Button URL", "citygov"),
					"desc" => wp_kses_data( __("Link URL for the button at the bottom of the block", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"link_caption" => array(
					"title" => esc_html__("Button caption", "citygov"),
					"desc" => wp_kses_data( __("Caption for the button at the bottom of the block", "citygov") ),
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
if ( !function_exists( 'citygov_sc_promo_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_promo_reg_shortcodes_vc');
	function citygov_sc_promo_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_promo",
			"name" => esc_html__("Promo", "citygov"),
			"description" => wp_kses_data( __("Insert promo block", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_promo',
			"class" => "trx_sc_collection trx_sc_promo",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment of the promo block", "citygov"),
					"description" => wp_kses_data( __("Align whole promo block to left or right side of the page or parent container", "citygov") ),
					"class" => "",
					"std" => 'none',
					"value" => array_flip(citygov_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "size",
					"heading" => esc_html__("Size of the promo block", "citygov"),
					"description" => wp_kses_data( __("Size of the promo block: large - one in the row, small - insize two or greater columns", "citygov") ),
					"class" => "",
					"value" => array(esc_html__('Use small block', 'citygov') => 'small'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("Image URL", "citygov"),
					"description" => wp_kses_data( __("Select the promo image from the library for this section", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "image_position",
					"heading" => esc_html__("Image position", "citygov"),
					"description" => wp_kses_data( __("Place the image to the left or to the right from the text block", "citygov") ),
					"class" => "",
					"std" => 'left',
					"value" => array_flip(citygov_get_sc_param('hpos')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "image_width",
					"heading" => esc_html__("Image width", "citygov"),
					"description" => wp_kses_data( __("Width (in pixels or percents) of the block with image", "citygov") ),
					"value" => '',
					"std" => "50%",
					"type" => "textfield"
				),
				array(
					"param_name" => "text_margins",
					"heading" => esc_html__("Text margins", "citygov"),
					"description" => wp_kses_data( __("Margins for the all sides of the text block (Example: 30px 10px 40px 30px = top right botton left OR 30px = equal for all sides)", "citygov") ),
					"value" => '',
					"type" => "textfield"
				),
				array(
					"param_name" => "text_align",
					"heading" => esc_html__("Text alignment", "citygov"),
					"description" => wp_kses_data( __("Align text to the left or to the right side inside the block", "citygov") ),
					"class" => "",
					"std" => 'left',
					"value" => array_flip(citygov_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", "citygov"),
					"description" => wp_kses_data( __("Select color scheme for the section with text", "citygov") ),
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('schemes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "citygov"),
					"description" => wp_kses_data( __("Title for the block", "citygov") ),
					"admin_label" => true,
					"group" => esc_html__('Captions', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "subtitle",
					"heading" => esc_html__("Subtitle", "citygov"),
					"description" => wp_kses_data( __("Subtitle for the block", "citygov") ),
					"group" => esc_html__('Captions', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "description",
					"heading" => esc_html__("Description", "citygov"),
					"description" => wp_kses_data( __("Description for the block", "citygov") ),
					"group" => esc_html__('Captions', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textarea"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Button URL", "citygov"),
					"description" => wp_kses_data( __("Link URL for the button at the bottom of the block", "citygov") ),
					"group" => esc_html__('Captions', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link_caption",
					"heading" => esc_html__("Button caption", "citygov"),
					"description" => wp_kses_data( __("Caption for the button at the bottom of the block", "citygov") ),
					"group" => esc_html__('Captions', 'citygov'),
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
		
		class WPBakeryShortCode_Trx_Promo extends CITYGOV_VC_ShortCodeCollection {}
	}
}
?>