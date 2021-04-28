<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_title_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_title_theme_setup' );
	function citygov_sc_title_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_title_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_title_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_title id="unique_id" style='regular|iconed' icon='' image='' background="on|off" type="1-6"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_title]
*/

if (!function_exists('citygov_sc_title')) {	
	function citygov_sc_title($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"type" => "1",
			"style" => "regular",
			"align" => "",
			"font_weight" => "",
			"font_size" => "",
			"color" => "",
			"icon" => "",
			"image" => "",
			"picture" => "",
			"image_size" => "small",
			"position" => "left",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= citygov_get_css_dimensions_from_values($width)
			.($align && $align!='none' && !citygov_param_is_inherit($align) ? 'text-align:' . esc_attr($align) .';' : '')
			.($color ? 'color:' . esc_attr($color) .';' : '')
			.($font_weight && !citygov_param_is_inherit($font_weight) ? 'font-weight:' . esc_attr($font_weight) .';' : '')
			.($font_size   ? 'font-size:' . esc_attr($font_size) .';' : '')
			;
		$type = min(6, max(1, $type));
		if ($picture > 0) {
			$attach = wp_get_attachment_image_src( $picture, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$picture = $attach[0];
		}
		$pic = $style!='iconed' 
			? '' 
			: '<span class="sc_title_icon sc_title_icon_'.esc_attr($position).'  sc_title_icon_'.esc_attr($image_size).($icon!='' && $icon!='none' ? ' '.esc_attr($icon) : '').'"'.'>'
				.($picture ? '<img src="'.esc_url($picture).'" alt="" />' : '')
				.(empty($picture) && $image && $image!='none' ? '<img src="'.esc_url(citygov_strpos($image, 'http:')!==false ? $image : citygov_get_file_url('images/icons/'.($image).'.png')).'" alt="" />' : '')
				.'</span>';
		$output = '<h' . esc_attr($type) . ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_title sc_title_'.esc_attr($style)
					.($align && $align!='none' && !citygov_param_is_inherit($align) ? ' sc_align_' . esc_attr($align) : '')
					.(!empty($class) ? ' '.esc_attr($class) : '')
					.'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
				. '>'
					. ($pic)
					. ($style=='divider' ? '<span class="sc_title_divider_before"'.($color ? ' style="background-color: '.esc_attr($color).'"' : '').'></span>' : '')
					. do_shortcode($content) 
					. ($style=='divider' ? '<span class="sc_title_divider_after"'.($color ? ' style="background-color: '.esc_attr($color).'"' : '').'></span>' : '')
				. '</h' . esc_attr($type) . '>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_title', $atts, $content);
	}
	citygov_require_shortcode('trx_title', 'citygov_sc_title');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_title_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_title_reg_shortcodes');
	function citygov_sc_title_reg_shortcodes() {
	
		citygov_sc_map("trx_title", array(
			"title" => esc_html__("Title", "citygov"),
			"desc" => wp_kses_data( __("Create header tag (1-6 level) with many styles", "citygov") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Title content", "citygov"),
					"desc" => wp_kses_data( __("Title content", "citygov") ),
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"type" => array(
					"title" => esc_html__("Title type", "citygov"),
					"desc" => wp_kses_data( __("Title type (header level)", "citygov") ),
					"divider" => true,
					"value" => "1",
					"type" => "select",
					"options" => array(
						'1' => esc_html__('Header 1', 'citygov'),
						'2' => esc_html__('Header 2', 'citygov'),
						'3' => esc_html__('Header 3', 'citygov'),
						'4' => esc_html__('Header 4', 'citygov'),
						'5' => esc_html__('Header 5', 'citygov'),
						'6' => esc_html__('Header 6', 'citygov'),
					)
				),
				"style" => array(
					"title" => esc_html__("Title style", "citygov"),
					"desc" => wp_kses_data( __("Title style", "citygov") ),
					"value" => "regular",
					"type" => "select",
					"options" => array(
						'regular' => esc_html__('Regular', 'citygov'),
						'underline' => esc_html__('Underline', 'citygov'),
						'divider' => esc_html__('Divider', 'citygov'),
						'iconed' => esc_html__('With icon (image)', 'citygov')
					)
				),
				"align" => array(
					"title" => esc_html__("Alignment", "citygov"),
					"desc" => wp_kses_data( __("Title text alignment", "citygov") ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('align')
				), 
				"font_size" => array(
					"title" => esc_html__("Font_size", "citygov"),
					"desc" => wp_kses_data( __("Custom font size. If empty - use theme default", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"font_weight" => array(
					"title" => esc_html__("Font weight", "citygov"),
					"desc" => wp_kses_data( __("Custom font weight. If empty or inherit - use theme default", "citygov") ),
					"value" => "",
					"type" => "select",
					"size" => "medium",
					"options" => array(
						'inherit' => esc_html__('Default', 'citygov'),
						'100' => esc_html__('Thin (100)', 'citygov'),
						'300' => esc_html__('Light (300)', 'citygov'),
						'400' => esc_html__('Normal (400)', 'citygov'),
						'600' => esc_html__('Semibold (600)', 'citygov'),
						'700' => esc_html__('Bold (700)', 'citygov'),
						'900' => esc_html__('Black (900)', 'citygov')
					)
				),
				"color" => array(
					"title" => esc_html__("Title color", "citygov"),
					"desc" => wp_kses_data( __("Select color for the title", "citygov") ),
					"value" => "",
					"type" => "color"
				),
				"icon" => array(
					"title" => esc_html__('Title font icon',  'citygov'),
					"desc" => wp_kses_data( __("Select font icon for the title from Fontello icons set (if style=iconed)",  'citygov') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "",
					"type" => "icons",
					"options" => citygov_get_sc_param('icons')
				),
				"image" => array(
					"title" => esc_html__('or image icon',  'citygov'),
					"desc" => wp_kses_data( __("Select image icon for the title instead icon above (if style=iconed)",  'citygov') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "",
					"type" => "images",
					"size" => "small",
					"options" => citygov_get_sc_param('images')
				),
				"picture" => array(
					"title" => esc_html__('or URL for image file', "citygov"),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site (if style=iconed)", "citygov") ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"image_size" => array(
					"title" => esc_html__('Image (picture) size', "citygov"),
					"desc" => wp_kses_data( __("Select image (picture) size (if style='iconed')", "citygov") ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "small",
					"type" => "checklist",
					"options" => array(
						'small' => esc_html__('Small', 'citygov'),
						'medium' => esc_html__('Medium', 'citygov'),
						'large' => esc_html__('Large', 'citygov')
					)
				),
				"position" => array(
					"title" => esc_html__('Icon (image) position', "citygov"),
					"desc" => wp_kses_data( __("Select icon (image) position (if style=iconed)", "citygov") ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "left",
					"type" => "checklist",
					"options" => array(
						'top' => esc_html__('Top', 'citygov'),
						'left' => esc_html__('Left', 'citygov')
					)
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
if ( !function_exists( 'citygov_sc_title_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_title_reg_shortcodes_vc');
	function citygov_sc_title_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_title",
			"name" => esc_html__("Title", "citygov"),
			"description" => wp_kses_data( __("Create header tag (1-6 level) with many styles", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_title',
			"class" => "trx_sc_single trx_sc_title",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "content",
					"heading" => esc_html__("Title content", "citygov"),
					"description" => wp_kses_data( __("Title content", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				array(
					"param_name" => "type",
					"heading" => esc_html__("Title type", "citygov"),
					"description" => wp_kses_data( __("Title type (header level)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Header 1', 'citygov') => '1',
						esc_html__('Header 2', 'citygov') => '2',
						esc_html__('Header 3', 'citygov') => '3',
						esc_html__('Header 4', 'citygov') => '4',
						esc_html__('Header 5', 'citygov') => '5',
						esc_html__('Header 6', 'citygov') => '6'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Title style", "citygov"),
					"description" => wp_kses_data( __("Title style: only text (regular) or with icon/image (iconed)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Regular', 'citygov') => 'regular',
						esc_html__('Underline', 'citygov') => 'underline',
						esc_html__('Divider', 'citygov') => 'divider',
						esc_html__('With icon (image)', 'citygov') => 'iconed'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "citygov"),
					"description" => wp_kses_data( __("Title text alignment", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", "citygov"),
					"description" => wp_kses_data( __("Custom font size. If empty - use theme default", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "font_weight",
					"heading" => esc_html__("Font weight", "citygov"),
					"description" => wp_kses_data( __("Custom font weight. If empty or inherit - use theme default", "citygov") ),
					"class" => "",
					"value" => array(
						esc_html__('Default', 'citygov') => 'inherit',
						esc_html__('Thin (100)', 'citygov') => '100',
						esc_html__('Light (300)', 'citygov') => '300',
						esc_html__('Normal (400)', 'citygov') => '400',
						esc_html__('Semibold (600)', 'citygov') => '600',
						esc_html__('Bold (700)', 'citygov') => '700',
						esc_html__('Black (900)', 'citygov') => '900'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Title color", "citygov"),
					"description" => wp_kses_data( __("Select color for the title", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Title font icon", "citygov"),
					"description" => wp_kses_data( __("Select font icon for the title from Fontello icons set (if style=iconed)", "citygov") ),
					"class" => "",
					"group" => esc_html__('Icon &amp; Image', 'citygov'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => citygov_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("or image icon", "citygov"),
					"description" => wp_kses_data( __("Select image icon for the title instead icon above (if style=iconed)", "citygov") ),
					"class" => "",
					"group" => esc_html__('Icon &amp; Image', 'citygov'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => citygov_get_sc_param('images'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "picture",
					"heading" => esc_html__("or select uploaded image", "citygov"),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site (if style=iconed)", "citygov") ),
					"group" => esc_html__('Icon &amp; Image', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "image_size",
					"heading" => esc_html__("Image (picture) size", "citygov"),
					"description" => wp_kses_data( __("Select image (picture) size (if style=iconed)", "citygov") ),
					"group" => esc_html__('Icon &amp; Image', 'citygov'),
					"class" => "",
					"value" => array(
						esc_html__('Small', 'citygov') => 'small',
						esc_html__('Medium', 'citygov') => 'medium',
						esc_html__('Large', 'citygov') => 'large'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "position",
					"heading" => esc_html__("Icon (image) position", "citygov"),
					"description" => wp_kses_data( __("Select icon (image) position (if style=iconed)", "citygov") ),
					"group" => esc_html__('Icon &amp; Image', 'citygov'),
					"class" => "",
					"std" => "left",
					"value" => array(
						esc_html__('Top', 'citygov') => 'top',
						esc_html__('Left', 'citygov') => 'left'
					),
					"type" => "dropdown"
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
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Title extends CITYGOV_VC_ShortCodeSingle {}
	}
}
?>