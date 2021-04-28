<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_reviews_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_reviews_theme_setup' );
	function citygov_sc_reviews_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_reviews_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_reviews_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_reviews]
*/

if (!function_exists('citygov_sc_reviews')) {	
	function citygov_sc_reviews($atts, $content = null) {
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"align" => "right",
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
		$output = citygov_param_is_off(citygov_get_custom_option('show_sidebar_main'))
			? '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_reviews'
							. ($align && $align!='none' ? ' align'.esc_attr($align) : '')
							. ($class ? ' '.esc_attr($class) : '')
							. '"'
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
						. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
						. '>'
					. trim(citygov_get_reviews_placeholder())
					. '</div>'
			: '';
		return apply_filters('citygov_shortcode_output', $output, 'trx_reviews', $atts, $content);
	}
	citygov_require_shortcode("trx_reviews", "citygov_sc_reviews");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_reviews_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_reviews_reg_shortcodes');
	function citygov_sc_reviews_reg_shortcodes() {
	
		citygov_sc_map("trx_reviews", array(
			"title" => esc_html__("Reviews", "citygov"),
			"desc" => wp_kses_data( __("Insert reviews block in the single post", "citygov") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"align" => array(
					"title" => esc_html__("Alignment", "citygov"),
					"desc" => wp_kses_data( __("Align counter to left, center or right", "citygov") ),
					"divider" => true,
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('align')
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
if ( !function_exists( 'citygov_sc_reviews_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_reviews_reg_shortcodes_vc');
	function citygov_sc_reviews_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_reviews",
			"name" => esc_html__("Reviews", "citygov"),
			"description" => wp_kses_data( __("Insert reviews block in the single post", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_reviews',
			"class" => "trx_sc_single trx_sc_reviews",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "citygov"),
					"description" => wp_kses_data( __("Align counter to left, center or right", "citygov") ),
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('align')),
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
			)
		) );
		
		class WPBakeryShortCode_Trx_Reviews extends CITYGOV_VC_ShortCodeSingle {}
	}
}
?>