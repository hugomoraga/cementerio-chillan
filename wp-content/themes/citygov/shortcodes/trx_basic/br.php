<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_br_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_br_theme_setup' );
	function citygov_sc_br_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_br_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_br_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_br clear="left|right|both"]
*/

if (!function_exists('citygov_sc_br')) {	
	function citygov_sc_br($atts, $content = null) {
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			"clear" => ""
		), $atts)));
		$output = in_array($clear, array('left', 'right', 'both', 'all')) 
			? '<div class="clearfix" style="clear:' . str_replace('all', 'both', $clear) . '"></div>'
			: '<br />';
		return apply_filters('citygov_shortcode_output', $output, 'trx_br', $atts, $content);
	}
	citygov_require_shortcode("trx_br", "citygov_sc_br");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_br_reg_shortcodes' ) ) {
	function citygov_sc_br_reg_shortcodes() {
	
		citygov_sc_map("trx_br", array(
			"title" => esc_html__("Break", "citygov"),
			"desc" => wp_kses_data( __("Line break with clear floating (if need)", "citygov") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"clear" => 	array(
					"title" => esc_html__("Clear floating", "citygov"),
					"desc" => wp_kses_data( __("Clear floating (if need)", "citygov") ),
					"value" => "",
					"type" => "checklist",
					"options" => array(
						'none' => esc_html__('None', 'citygov'),
						'left' => esc_html__('Left', 'citygov'),
						'right' => esc_html__('Right', 'citygov'),
						'both' => esc_html__('Both', 'citygov')
					)
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_br_reg_shortcodes_vc' ) ) {
	function citygov_sc_br_reg_shortcodes_vc() {
/*
		vc_map( array(
			"base" => "trx_br",
			"name" => esc_html__("Line break", "citygov"),
			"description" => wp_kses_data( __("Line break or Clear Floating", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_br',
			"class" => "trx_sc_single trx_sc_br",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "clear",
					"heading" => esc_html__("Clear floating", "citygov"),
					"description" => wp_kses_data( __("Select clear side (if need)", "citygov") ),
					"class" => "",
					"value" => "",
					"value" => array(
						esc_html__('None', 'citygov') => 'none',
						esc_html__('Left', 'citygov') => 'left',
						esc_html__('Right', 'citygov') => 'right',
						esc_html__('Both', 'citygov') => 'both'
					),
					"type" => "dropdown"
				)
			)
		) );
		
		class WPBakeryShortCode_Trx_Br extends CITYGOV_VC_ShortCodeSingle {}
*/
	}
}
?>