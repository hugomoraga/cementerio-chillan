<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_price_block_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_price_block_theme_setup' );
	function citygov_sc_price_block_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_price_block_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_price_block_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('citygov_sc_price_block')) {	
	function citygov_sc_price_block($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"style" => 1,
			"title" => "",
			"link" => "",
			"link_text" => "",
			"icon" => "",
			"money" => "",
			"currency" => "$",
			"period" => "",
			"align" => "",
			"scheme" => "",
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
		$output = '';
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= citygov_get_css_dimensions_from_values($width, $height);
		if ($money) $money = do_shortcode('[trx_price money="'.esc_attr($money).'" period="'.esc_attr($period).'"'.($currency ? ' currency="'.esc_attr($currency).'"' : '').']');
		$content = do_shortcode(citygov_sc_clear_around($content));
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_price_block sc_price_block_style_'.max(1, min(3, $style))
						. (!empty($class) ? ' '.esc_attr($class) : '')
						. ($scheme && !citygov_param_is_off($scheme) && !citygov_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
						. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
						. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
					. '>'
				. (!empty($title) ? '<div class="sc_price_block_title"><span>'.($title).'</span></div>' : '')
				. '<div class="sc_price_block_money">'
					. (!empty($icon) ? '<div class="sc_price_block_icon '.esc_attr($icon).'"></div>' : '')
					. ($money)
				. '</div>'
				. (!empty($content) ? '<div class="sc_price_block_description">'.($content).'</div>' : '')
				. (!empty($link_text) ? '<div class="sc_price_block_link">'.do_shortcode('[trx_button link="'.($link ? esc_url($link) : '#').'"]'.($link_text).'[/trx_button]').'</div>' : '')
			. '</div>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_price_block', $atts, $content);
	}
	citygov_require_shortcode('trx_price_block', 'citygov_sc_price_block');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_price_block_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_price_block_reg_shortcodes');
	function citygov_sc_price_block_reg_shortcodes() {
	
		citygov_sc_map("trx_price_block", array(
			"title" => esc_html__("Price block", "citygov"),
			"desc" => wp_kses_data( __("Insert price block with title, price and description", "citygov") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Block style", "citygov"),
					"desc" => wp_kses_data( __("Select style for this price block", "citygov") ),
					"value" => 1,
					"options" => citygov_get_list_styles(1, 3),
					"type" => "checklist"
				),
				"title" => array(
					"title" => esc_html__("Title", "citygov"),
					"desc" => wp_kses_data( __("Block title", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"link" => array(
					"title" => esc_html__("Link URL", "citygov"),
					"desc" => wp_kses_data( __("URL for link from button (at bottom of the block)", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"link_text" => array(
					"title" => esc_html__("Link text", "citygov"),
					"desc" => wp_kses_data( __("Text (caption) for the link button (at bottom of the block). If empty - button not showed", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"icon" => array(
					"title" => esc_html__("Icon",  'citygov'),
					"desc" => wp_kses_data( __('Select icon from Fontello icons set (placed before/instead price)',  'citygov') ),
					"value" => "",
					"type" => "icons",
					"options" => citygov_get_sc_param('icons')
				),
				"money" => array(
					"title" => esc_html__("Money", "citygov"),
					"desc" => wp_kses_data( __("Money value (dot or comma separated)", "citygov") ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"currency" => array(
					"title" => esc_html__("Currency", "citygov"),
					"desc" => wp_kses_data( __("Currency character", "citygov") ),
					"value" => "$",
					"type" => "text"
				),
				"period" => array(
					"title" => esc_html__("Period", "citygov"),
					"desc" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"scheme" => array(
					"title" => esc_html__("Color scheme", "citygov"),
					"desc" => wp_kses_data( __("Select color scheme for this block", "citygov") ),
					"value" => "",
					"type" => "checklist",
					"options" => citygov_get_sc_param('schemes')
				),
				"align" => array(
					"title" => esc_html__("Alignment", "citygov"),
					"desc" => wp_kses_data( __("Align price to left or right side", "citygov") ),
					"divider" => true,
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('float')
				), 
				"_content_" => array(
					"title" => esc_html__("Description", "citygov"),
					"desc" => wp_kses_data( __("Description for this price block", "citygov") ),
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
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_price_block_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_price_block_reg_shortcodes_vc');
	function citygov_sc_price_block_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_price_block",
			"name" => esc_html__("Price block", "citygov"),
			"description" => wp_kses_data( __("Insert price block with title, price and description", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_price_block',
			"class" => "trx_sc_single trx_sc_price_block",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Block style", "citygov"),
					"desc" => wp_kses_data( __("Select style of this price block", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"std" => 1,
					"value" => array_flip(citygov_get_list_styles(1, 3)),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "citygov"),
					"description" => wp_kses_data( __("Block title", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", "citygov"),
					"description" => wp_kses_data( __("URL for link from button (at bottom of the block)", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link_text",
					"heading" => esc_html__("Link text", "citygov"),
					"description" => wp_kses_data( __("Text (caption) for the link button (at bottom of the block). If empty - button not showed", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Icon", "citygov"),
					"description" => wp_kses_data( __("Select icon from Fontello icons set (placed before/instead price)", "citygov") ),
					"class" => "",
					"value" => citygov_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "money",
					"heading" => esc_html__("Money", "citygov"),
					"description" => wp_kses_data( __("Money value (dot or comma separated)", "citygov") ),
					"admin_label" => true,
					"group" => esc_html__('Money', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "currency",
					"heading" => esc_html__("Currency symbol", "citygov"),
					"description" => wp_kses_data( __("Currency character", "citygov") ),
					"admin_label" => true,
					"group" => esc_html__('Money', 'citygov'),
					"class" => "",
					"value" => "$",
					"type" => "textfield"
				),
				array(
					"param_name" => "period",
					"heading" => esc_html__("Period", "citygov"),
					"description" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", "citygov") ),
					"admin_label" => true,
					"group" => esc_html__('Money', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
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
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "citygov"),
					"description" => wp_kses_data( __("Align price to left or right side", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Description", "citygov"),
					"description" => wp_kses_data( __("Description for this price block", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
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
		
		class WPBakeryShortCode_Trx_PriceBlock extends CITYGOV_VC_ShortCodeSingle {}
	}
}
?>