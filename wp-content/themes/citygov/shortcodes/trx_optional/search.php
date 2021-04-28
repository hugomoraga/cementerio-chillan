<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_search_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_search_theme_setup' );
	function citygov_sc_search_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_search_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_search_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_search id="unique_id" open="yes|no"]
*/

if (!function_exists('citygov_sc_search')) {	
	function citygov_sc_search($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "regular",
			"state" => "fixed",
			"scheme" => "original",
			"ajax" => "",
			"title" => "",
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
		if (empty($ajax)) $ajax = citygov_get_theme_option('use_ajax_search');
		// Load core messages
		citygov_enqueue_messages();
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') . ' class="search_wrap search_style_'.esc_attr($style).' search_state_'.esc_attr($state)
						. (citygov_param_is_on($ajax) ? ' search_ajax' : '')
						. ($class ? ' '.esc_attr($class) : '')
						. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
					. '>
						<div class="search_form_wrap">
							<form role="search" method="get" class="search_form" action="' . esc_url(home_url('/')) . '">
								<button type="submit" class="search_submit icon-search" title="' . ($state=='closed' ? esc_attr__('Open search', 'citygov') : esc_attr__('Start search', 'citygov')) . '"></button>
								<input type="text" class="search_field" placeholder="' . esc_attr($title) . '" value="' . esc_attr(get_search_query()) . '" name="s" />
							</form>
						</div>
						<div class="search_results widget_area' . ($scheme && !citygov_param_is_off($scheme) && !citygov_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') . '"><a class="search_results_close icon-cancel"></a><div class="search_results_content"></div></div>
				</div>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_search', $atts, $content);
	}
	citygov_require_shortcode('trx_search', 'citygov_sc_search');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_search_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_search_reg_shortcodes');
	function citygov_sc_search_reg_shortcodes() {
	
		citygov_sc_map("trx_search", array(
			"title" => esc_html__("Search", "citygov"),
			"desc" => wp_kses_data( __("Show search form", "citygov") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", "citygov"),
					"desc" => wp_kses_data( __("Select style to display search field", "citygov") ),
					"value" => "regular",
					"options" => array(
						"regular" => esc_html__('Regular', 'citygov'),
						"rounded" => esc_html__('Rounded', 'citygov')
					),
					"type" => "checklist"
				),
				"state" => array(
					"title" => esc_html__("State", "citygov"),
					"desc" => wp_kses_data( __("Select search field initial state", "citygov") ),
					"value" => "fixed",
					"options" => array(
						"fixed"  => esc_html__('Fixed',  'citygov'),
						"opened" => esc_html__('Opened', 'citygov'),
						"closed" => esc_html__('Closed', 'citygov')
					),
					"type" => "checklist"
				),
				"title" => array(
					"title" => esc_html__("Title", "citygov"),
					"desc" => wp_kses_data( __("Title (placeholder) for the search field", "citygov") ),
					"value" => esc_html__("Search &hellip;", 'citygov'),
					"type" => "text"
				),
				"ajax" => array(
					"title" => esc_html__("AJAX", "citygov"),
					"desc" => wp_kses_data( __("Search via AJAX or reload page", "citygov") ),
					"value" => "yes",
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
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_search_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_search_reg_shortcodes_vc');
	function citygov_sc_search_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_search",
			"name" => esc_html__("Search form", "citygov"),
			"description" => wp_kses_data( __("Insert search form", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_search',
			"class" => "trx_sc_single trx_sc_search",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", "citygov"),
					"description" => wp_kses_data( __("Select style to display search field", "citygov") ),
					"class" => "",
					"value" => array(
						esc_html__('Regular', 'citygov') => "regular",
						esc_html__('Flat', 'citygov') => "flat"
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "state",
					"heading" => esc_html__("State", "citygov"),
					"description" => wp_kses_data( __("Select search field initial state", "citygov") ),
					"class" => "",
					"value" => array(
						esc_html__('Fixed', 'citygov')  => "fixed",
						esc_html__('Opened', 'citygov') => "opened",
						esc_html__('Closed', 'citygov') => "closed"
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "citygov"),
					"description" => wp_kses_data( __("Title (placeholder) for the search field", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => esc_html__("Search &hellip;", 'citygov'),
					"type" => "textfield"
				),
				array(
					"param_name" => "ajax",
					"heading" => esc_html__("AJAX", "citygov"),
					"description" => wp_kses_data( __("Search via AJAX or reload page", "citygov") ),
					"class" => "",
					"value" => array(esc_html__('Use AJAX search', 'citygov') => 'yes'),
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
			)
		) );
		
		class WPBakeryShortCode_Trx_Search extends CITYGOV_VC_ShortCodeSingle {}
	}
}
?>