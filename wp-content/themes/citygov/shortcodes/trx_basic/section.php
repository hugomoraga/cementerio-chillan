<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_section_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_section_theme_setup' );
	function citygov_sc_section_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_section_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_section_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_section id="unique_id" class="class_name" style="css-styles" dedicated="yes|no"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_section]
*/

citygov_storage_set('sc_section_dedicated', '');

if (!function_exists('citygov_sc_section')) {	
	function citygov_sc_section($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"dedicated" => "no",
			"align" => "none",
			"columns" => "none",
			"pan" => "no",
			"scroll" => "no",
			"scroll_dir" => "horizontal",
			"scroll_controls" => "hide",
			"color" => "",
			"scheme" => "",
			"bg_color" => "",
			"bg_image" => "",
			"bg_overlay" => "",
			"bg_texture" => "",
			"bg_tile" => "no",
			"bg_padding" => "yes",
			"font_size" => "",
			"font_weight" => "",
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
	
		if ($bg_image > 0) {
			$attach = wp_get_attachment_image_src( $bg_image, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$bg_image = $attach[0];
		}
	
		if ($bg_overlay > 0) {
			if ($bg_color=='') $bg_color = citygov_get_scheme_color('bg');
			$rgb = citygov_hex2rgb($bg_color);
		}
	
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= ($color !== '' ? 'color:' . esc_attr($color) . ';' : '')
			.($bg_color !== '' && $bg_overlay==0 ? 'background-color:' . esc_attr($bg_color) . ';' : '')
			.($bg_image !== '' ? 'background-image:url(' . esc_url($bg_image) . ');'.(citygov_param_is_on($bg_tile) ? 'background-repeat:repeat;' : 'background-repeat:no-repeat;background-size:cover;') : '')
			.(!citygov_param_is_off($pan) ? 'position:relative;' : '')
			.($font_size != '' ? 'font-size:' . esc_attr(citygov_prepare_css_value($font_size)) . '; line-height: 1.3em;' : '')
			.($font_weight != '' && !citygov_param_is_inherit($font_weight) ? 'font-weight:' . esc_attr($font_weight) . ';' : '');
		$css_dim = citygov_get_css_dimensions_from_values($width, $height);
		if ($bg_image == '' && $bg_color == '' && $bg_overlay==0 && $bg_texture==0 && citygov_strlen($bg_texture)<2) $css .= $css_dim;
		
		$width  = citygov_prepare_css_value($width);
		$height = citygov_prepare_css_value($height);
	
		if ((!citygov_param_is_off($scroll) || !citygov_param_is_off($pan)) && empty($id)) $id = 'sc_section_'.str_replace('.', '', mt_rand());
	
		if (!citygov_param_is_off($scroll)) citygov_enqueue_slider();
	
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_section' 
					. ($class ? ' ' . esc_attr($class) : '') 
					. ($scheme && !citygov_param_is_off($scheme) && !citygov_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($columns) && $columns!='none' ? ' column-'.esc_attr($columns) : '') 
					. '"'
				. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
				. ($css!='' || $css_dim!='' ? ' style="'.esc_attr($css.$css_dim).'"' : '')
				.'>' 
				. '<div class="sc_section_inner'
									. (citygov_param_is_on($scroll) && !citygov_param_is_off($scroll_controls) ? ' sc_scroll_controls sc_scroll_controls_'.esc_attr($scroll_dir).' sc_scroll_controls_type_'.esc_attr($scroll_controls) : '')

				.'">'
					. ($bg_image !== '' || $bg_color !== '' || $bg_overlay>0 || $bg_texture>0 || citygov_strlen($bg_texture)>2
						? '<div class="sc_section_overlay'.($bg_texture>0 ? ' texture_bg_'.esc_attr($bg_texture) : '') . '"'
							. ' style="' . ($bg_overlay>0 ? 'background-color:rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.min(1, max(0, $bg_overlay)).');' : '')
								. (citygov_strlen($bg_texture)>2 ? 'background-image:url('.esc_url($bg_texture).');' : '')
								. '"'
								. ($bg_overlay > 0 ? ' data-overlay="'.esc_attr($bg_overlay).'" data-bg_color="'.esc_attr($bg_color).'"' : '')
								. '>'
								. '<div class="sc_section_content' . (citygov_param_is_on($bg_padding) ? ' padding_on' : ' padding_off') . '"'
									. ' style="'.esc_attr($css_dim).'"'
									. '>'
						: '')
            . (!empty($subtitle) ? '<h6 class="sc_section_subtitle sc_item_subtitle">' . trim(citygov_strmacros($subtitle)) . '</h6>' : '')
            . (!empty($title) ? '<h2 class="sc_section_title sc_item_title">' . trim(citygov_strmacros($title)) . '</h2>' : '')
            . (!empty($description) ? '<div class="sc_section_descr sc_item_descr">' . trim(citygov_strmacros($description)) . '</div>' : '')
					. (citygov_param_is_on($scroll) 
						? '<div id="'.esc_attr($id).'_scroll" class="sc_scroll sc_scroll_'.esc_attr($scroll_dir).' swiper-slider-container scroll-container"'
							. ' style="'.($height != '' ? 'height:'.esc_attr($height).';' : '') . ($width != '' ? 'width:'.esc_attr($width).';' : '').'"'
							. '>'
							. '<div class="sc_scroll_wrapper swiper-wrapper">' 
							. '<div class="sc_scroll_slide swiper-slide">' 
						: '')
					. (citygov_param_is_on($pan) 
						? '<div id="'.esc_attr($id).'_pan" class="sc_pan sc_pan_'.esc_attr($scroll_dir).'">' 
						: '')

							. do_shortcode($content)
							. (!empty($link) ? '<div class="sc_section_button sc_item_button">'.citygov_do_shortcode('[trx_button link="'.esc_url($link).'" icon="icon-right"]'.esc_html($link_caption).'[/trx_button]').'</div>' : '')
					. (citygov_param_is_on($pan) ? '</div>' : '')
					. (citygov_param_is_on($scroll) 
						? '</div></div><div id="'.esc_attr($id).'_scroll_bar" class="sc_scroll_bar sc_scroll_bar_'.esc_attr($scroll_dir).' '.esc_attr($id).'_scroll_bar"></div></div>'
							. (!citygov_param_is_off($scroll_controls) ? '<div class="sc_scroll_controls_wrap"><a class="sc_scroll_prev" href="#"></a><a class="sc_scroll_next" href="#"></a></div>' : '')
						: '')
					. ($bg_image !== '' || $bg_color !== '' || $bg_overlay > 0 || $bg_texture>0 || citygov_strlen($bg_texture)>2 ? '</div></div>' : '')
					. '</div>'
				. '</div>';
		if (citygov_param_is_on($dedicated)) {
			if (citygov_storage_get('sc_section_dedicated')=='') {
				citygov_storage_set('sc_section_dedicated', $output);
			}
			$output = '';
		}
		return apply_filters('citygov_shortcode_output', $output, 'trx_section', $atts, $content);
	}
	citygov_require_shortcode('trx_section', 'citygov_sc_section');
	citygov_require_shortcode('trx_block', 'citygov_sc_section');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_section_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_section_reg_shortcodes');
	function citygov_sc_section_reg_shortcodes() {
	
		$sc = array(
			"title" => esc_html__("Block container", "citygov"),
			"desc" => wp_kses_data( __("Container for any block ([section] analog - to enable nesting)", "citygov") ),
			"decorate" => true,
			"container" => true,
			"params" => array(
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
				"dedicated" => array(
					"title" => esc_html__("Dedicated", "citygov"),
					"desc" => wp_kses_data( __("Use this block as dedicated content - show it before post title on single page", "citygov") ),
					"value" => "no",
					"type" => "switch",
					"options" => citygov_get_sc_param('yes_no')
				),
				"align" => array(
					"title" => esc_html__("Align", "citygov"),
					"desc" => wp_kses_data( __("Select block alignment", "citygov") ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('align')
				),
				"columns" => array(
					"title" => esc_html__("Columns emulation", "citygov"),
					"desc" => wp_kses_data( __("Select width for columns emulation", "citygov") ),
					"value" => "none",
					"type" => "checklist",
					"options" => citygov_get_sc_param('columns')
				), 
				"pan" => array(
					"title" => esc_html__("Use pan effect", "citygov"),
					"desc" => wp_kses_data( __("Use pan effect to show section content", "citygov") ),
					"divider" => true,
					"value" => "no",
					"type" => "switch",
					"options" => citygov_get_sc_param('yes_no')
				),
				"scroll" => array(
					"title" => esc_html__("Use scroller", "citygov"),
					"desc" => wp_kses_data( __("Use scroller to show section content", "citygov") ),
					"divider" => true,
					"value" => "no",
					"type" => "switch",
					"options" => citygov_get_sc_param('yes_no')
				),
				"scroll_dir" => array(
					"title" => esc_html__("Scroll and Pan direction", "citygov"),
					"desc" => wp_kses_data( __("Scroll and Pan direction (if Use scroller = yes or Pan = yes)", "citygov") ),
					"dependency" => array(
						'pan' => array('yes'),
						'scroll' => array('yes')
					),
					"value" => "horizontal",
					"type" => "switch",
					"size" => "big",
					"options" => citygov_get_sc_param('dir')
				),
				"scroll_controls" => array(
					"title" => esc_html__("Scroll controls", "citygov"),
					"desc" => wp_kses_data( __("Show scroll controls (if Use scroller = yes)", "citygov") ),
					"dependency" => array(
						'scroll' => array('yes')
					),
					"value" => "hide",
					"type" => "checklist",
					"options" => citygov_get_sc_param('controls')
				),
				"scheme" => array(
					"title" => esc_html__("Color scheme", "citygov"),
					"desc" => wp_kses_data( __("Select color scheme for this block", "citygov") ),
					"value" => "",
					"type" => "checklist",
					"options" => citygov_get_sc_param('schemes')
				),
				"color" => array(
					"title" => esc_html__("Fore color", "citygov"),
					"desc" => wp_kses_data( __("Any color for objects in this section", "citygov") ),
					"divider" => true,
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", "citygov"),
					"desc" => wp_kses_data( __("Any background color for this section", "citygov") ),
					"value" => "",
					"type" => "color"
				),
				"bg_image" => array(
					"title" => esc_html__("Background image URL", "citygov"),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the background", "citygov") ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"bg_tile" => array(
					"title" => esc_html__("Tile background image", "citygov"),
					"desc" => wp_kses_data( __("Do you want tile background image or image cover whole block?", "citygov") ),
					"value" => "no",
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
					"type" => "switch",
					"options" => citygov_get_sc_param('yes_no')
				),
				"bg_overlay" => array(
					"title" => esc_html__("Overlay", "citygov"),
					"desc" => wp_kses_data( __("Overlay color opacity (from 0.0 to 1.0)", "citygov") ),
					"min" => "0",
					"max" => "1",
					"step" => "0.1",
					"value" => "0",
					"type" => "spinner"
				),
				"bg_texture" => array(
					"title" => esc_html__("Texture", "citygov"),
					"desc" => wp_kses_data( __("Predefined texture style from 1 to 11. 0 - without texture.", "citygov") ),
					"min" => "0",
					"max" => "11",
					"step" => "1",
					"value" => "0",
					"type" => "spinner"
				),
				"bg_padding" => array(
					"title" => esc_html__("Paddings around content", "citygov"),
					"desc" => wp_kses_data( __("Add paddings around content in this section (only if bg_color or bg_image enabled).", "citygov") ),
					"value" => "yes",
					"dependency" => array(
						'compare' => 'or',
						'bg_color' => array('not_empty'),
						'bg_texture' => array('not_empty'),
						'bg_image' => array('not_empty')
					),
					"type" => "switch",
					"options" => citygov_get_sc_param('yes_no')
				),
				"font_size" => array(
					"title" => esc_html__("Font size", "citygov"),
					"desc" => wp_kses_data( __("Font size of the text (default - in pixels, allows any CSS units of measure)", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"font_weight" => array(
					"title" => esc_html__("Font weight", "citygov"),
					"desc" => wp_kses_data( __("Font weight of the text", "citygov") ),
					"value" => "",
					"type" => "select",
					"size" => "medium",
					"options" => array(
						'100' => esc_html__('Thin (100)', 'citygov'),
						'300' => esc_html__('Light (300)', 'citygov'),
						'400' => esc_html__('Normal (400)', 'citygov'),
						'700' => esc_html__('Bold (700)', 'citygov')
					)
				),
				"_content_" => array(
					"title" => esc_html__("Container content", "citygov"),
					"desc" => wp_kses_data( __("Content for section container", "citygov") ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
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
		);
		citygov_sc_map("trx_block", $sc);
		$sc["title"] = esc_html__("Section container", "citygov");
		$sc["desc"] = esc_html__("Container for any section ([block] analog - to enable nesting)", "citygov");
		citygov_sc_map("trx_section", $sc);
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_section_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_section_reg_shortcodes_vc');
	function citygov_sc_section_reg_shortcodes_vc() {
	
		$sc = array(
			"base" => "trx_block",
			"name" => esc_html__("Block container", "citygov"),
			"description" => wp_kses_data( __("Container for any block ([section] analog - to enable nesting)", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_block',
			"class" => "trx_sc_collection trx_sc_block",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "dedicated",
					"heading" => esc_html__("Dedicated", "citygov"),
					"description" => wp_kses_data( __("Use this block as dedicated content - show it before post title on single page", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(esc_html__('Use as dedicated content', 'citygov') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "citygov"),
					"description" => wp_kses_data( __("Select block alignment", "citygov") ),
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "columns",
					"heading" => esc_html__("Columns emulation", "citygov"),
					"description" => wp_kses_data( __("Select width for columns emulation", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('columns')),
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
				array(
					"param_name" => "pan",
					"heading" => esc_html__("Use pan effect", "citygov"),
					"description" => wp_kses_data( __("Use pan effect to show section content", "citygov") ),
					"group" => esc_html__('Scroll', 'citygov'),
					"class" => "",
					"value" => array(esc_html__('Content scroller', 'citygov') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "scroll",
					"heading" => esc_html__("Use scroller", "citygov"),
					"description" => wp_kses_data( __("Use scroller to show section content", "citygov") ),
					"group" => esc_html__('Scroll', 'citygov'),
					"admin_label" => true,
					"class" => "",
					"value" => array(esc_html__('Content scroller', 'citygov') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "scroll_dir",
					"heading" => esc_html__("Scroll direction", "citygov"),
					"description" => wp_kses_data( __("Scroll direction (if Use scroller = yes)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"group" => esc_html__('Scroll', 'citygov'),
					"value" => array_flip(citygov_get_sc_param('dir')),
					'dependency' => array(
						'element' => 'scroll',
						'not_empty' => true
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "scroll_controls",
					"heading" => esc_html__("Scroll controls", "citygov"),
					"description" => wp_kses_data( __("Show scroll controls (if Use scroller = yes)", "citygov") ),
					"class" => "",
					"group" => esc_html__('Scroll', 'citygov'),
					'dependency' => array(
						'element' => 'scroll',
						'not_empty' => true
					),
					"value" => array_flip(citygov_get_sc_param('controls')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", "citygov"),
					"description" => wp_kses_data( __("Select color scheme for this block", "citygov") ),
					"group" => esc_html__('Colors and Images', 'citygov'),
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('schemes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Fore color", "citygov"),
					"description" => wp_kses_data( __("Any color for objects in this section", "citygov") ),
					"group" => esc_html__('Colors and Images', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "citygov"),
					"description" => wp_kses_data( __("Any background color for this section", "citygov") ),
					"group" => esc_html__('Colors and Images', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_image",
					"heading" => esc_html__("Background image URL", "citygov"),
					"description" => wp_kses_data( __("Select background image from library for this section", "citygov") ),
					"group" => esc_html__('Colors and Images', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_tile",
					"heading" => esc_html__("Tile background image", "citygov"),
					"description" => wp_kses_data( __("Do you want tile background image or image cover whole block?", "citygov") ),
					"group" => esc_html__('Colors and Images', 'citygov'),
					"class" => "",
					'dependency' => array(
						'element' => 'bg_image',
						'not_empty' => true
					),
					"std" => "no",
					"value" => array(esc_html__('Tile background image', 'citygov') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "bg_overlay",
					"heading" => esc_html__("Overlay", "citygov"),
					"description" => wp_kses_data( __("Overlay color opacity (from 0.0 to 1.0)", "citygov") ),
					"group" => esc_html__('Colors and Images', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_texture",
					"heading" => esc_html__("Texture", "citygov"),
					"description" => wp_kses_data( __("Texture style from 1 to 11. Empty or 0 - without texture.", "citygov") ),
					"group" => esc_html__('Colors and Images', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_padding",
					"heading" => esc_html__("Paddings around content", "citygov"),
					"description" => wp_kses_data( __("Add paddings around content in this section (only if bg_color or bg_image enabled).", "citygov") ),
					"group" => esc_html__('Colors and Images', 'citygov'),
					"class" => "",
					'dependency' => array(
						'element' => array('bg_color','bg_texture','bg_image'),
						'not_empty' => true
					),
					"std" => "yes",
					"value" => array(esc_html__('Disable padding around content in this block', 'citygov') => 'no'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", "citygov"),
					"description" => wp_kses_data( __("Font size of the text (default - in pixels, allows any CSS units of measure)", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "font_weight",
					"heading" => esc_html__("Font weight", "citygov"),
					"description" => wp_kses_data( __("Font weight of the text", "citygov") ),
					"class" => "",
					"value" => array(
						esc_html__('Default', 'citygov') => 'inherit',
						esc_html__('Thin (100)', 'citygov') => '100',
						esc_html__('Light (300)', 'citygov') => '300',
						esc_html__('Normal (400)', 'citygov') => '400',
						esc_html__('Bold (700)', 'citygov') => '700'
					),
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
				citygov_vc_width(),
				citygov_vc_height(),
				citygov_get_vc_param('margin_top'),
				citygov_get_vc_param('margin_bottom'),
				citygov_get_vc_param('margin_left'),
				citygov_get_vc_param('margin_right')
			)
		);
		
		// Block
		vc_map($sc);
		
		// Section
		$sc["base"] = 'trx_section';
		$sc["name"] = esc_html__("Section container", "citygov");
		$sc["description"] = wp_kses_data( __("Container for any section ([block] analog - to enable nesting)", "citygov") );
		$sc["class"] = "trx_sc_collection trx_sc_section";
		$sc["icon"] = 'icon_trx_section';
		vc_map($sc);
		
		class WPBakeryShortCode_Trx_Block extends CITYGOV_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Section extends CITYGOV_VC_ShortCodeCollection {}
	}
}
?>