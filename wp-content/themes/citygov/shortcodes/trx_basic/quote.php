<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_quote_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_quote_theme_setup' );
	function citygov_sc_quote_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_quote_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_quote_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_quote id="unique_id" cite="url" title=""]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/quote]
*/

if (!function_exists('citygov_sc_quote')) {	
	function citygov_sc_quote($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"cite" => "",
            "style" => "original",
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
		$css .= citygov_get_css_dimensions_from_values($width);
		$cite_param = $cite != '' ? ' cite="'.esc_attr($cite).'"' : '';
		$title = $title=='' ? $cite : $title;
		$content = do_shortcode($content);
		if (citygov_substr($content, 0, 2)!='<p') $content = '<p>' . ($content) . '</p>';
		$output = '<blockquote' 
			. ($id ? ' id="'.esc_attr($id).'"' : '') . ($cite_param) 
			. ' class="sc_quote'. (!empty($class) ? ' ' .esc_attr($class) : '')
            . ' sc_quote_style_'. (!empty($style) ? esc_attr($style) : '')
            .'"'
			. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. '>'
				. ($content)
				. ($title == '' ? '' : ('<p class="sc_quote_title">' . ($cite!='' ? '<a href="'.esc_url($cite).'">' : '') . ($title) . ($cite!='' ? '</a>' : '') . '</p>'))
			.'</blockquote>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_quote', $atts, $content);
	}
	citygov_require_shortcode('trx_quote', 'citygov_sc_quote');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_quote_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_quote_reg_shortcodes');
	function citygov_sc_quote_reg_shortcodes() {
	
		citygov_sc_map("trx_quote", array(
			"title" => esc_html__("Quote", "citygov"),
			"desc" => wp_kses_data( __("Quote text", "citygov") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
                "style" => array(
                    "title" => esc_html__("Quote style", "citygov"),
                    "desc" => wp_kses_data( __("Select quote style", "citygov") ),
                    "value" => "original",
                    "options" => array(
                        'original' => esc_html__('Original', 'citygov'),
                        'bordered' => esc_html__('Bordered', 'citygov')
                    ),
                    "type" => "switch"
                ),
				"cite" => array(
					"title" => esc_html__("Quote cite", "citygov"),
					"desc" => wp_kses_data( __("URL for quote cite", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"title" => array(
					"title" => esc_html__("Title (author)", "citygov"),
					"desc" => wp_kses_data( __("Quote title (author name)", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"_content_" => array(
					"title" => esc_html__("Quote content", "citygov"),
					"desc" => wp_kses_data( __("Quote content", "citygov") ),
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"width" => citygov_shortcodes_width(),
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
if ( !function_exists( 'citygov_sc_quote_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_quote_reg_shortcodes_vc');
	function citygov_sc_quote_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_quote",
			"name" => esc_html__("Quote", "citygov"),
			"description" => wp_kses_data( __("Quote text", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_quote',
			"class" => "trx_sc_single trx_sc_quote",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
                array(
                    "param_name" => "style",
                    "heading" => esc_html__("Quote style", "citygov"),
                    "description" => wp_kses_data( __("Select quote style", "citygov") ),
                    "class" => "",
                    "value" => array(
                        esc_html__('Original', 'citygov') => 'original',
                        esc_html__('Bordered', 'citygov') => 'bordered'
                    ),
                    "type" => "dropdown"
                ),
				array(
					"param_name" => "cite",
					"heading" => esc_html__("Quote cite", "citygov"),
					"description" => wp_kses_data( __("URL for the quote cite link", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title (author)", "citygov"),
					"description" => wp_kses_data( __("Quote title (author name)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Quote content", "citygov"),
					"description" => wp_kses_data( __("Quote content", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				citygov_get_vc_param('id'),
				citygov_get_vc_param('class'),
				citygov_get_vc_param('animation'),
				citygov_get_vc_param('css'),
				citygov_vc_width(),
				citygov_get_vc_param('margin_top'),
				citygov_get_vc_param('margin_bottom'),
				citygov_get_vc_param('margin_left'),
				citygov_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Quote extends CITYGOV_VC_ShortCodeSingle {}
	}
}
?>