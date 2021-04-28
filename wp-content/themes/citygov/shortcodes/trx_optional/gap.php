<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_gap_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_gap_theme_setup' );
	function citygov_sc_gap_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_gap_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_gap_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_gap]Fullwidth content[/trx_gap]

if (!function_exists('citygov_sc_gap')) {	
	function citygov_sc_gap($atts, $content = null) {
		if (citygov_in_shortcode_blogger()) return '';
		$output = citygov_gap_start() . do_shortcode($content) . citygov_gap_end();
		return apply_filters('citygov_shortcode_output', $output, 'trx_gap', $atts, $content);
	}
	citygov_require_shortcode("trx_gap", "citygov_sc_gap");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_gap_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_gap_reg_shortcodes');
	function citygov_sc_gap_reg_shortcodes() {
	
		citygov_sc_map("trx_gap", array(
			"title" => esc_html__("Gap", "citygov"),
			"desc" => wp_kses_data( __("Insert gap (fullwidth area) in the post content. Attention! Use the gap only in the posts (pages) without left or right sidebar", "citygov") ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Gap content", "citygov"),
					"desc" => wp_kses_data( __("Gap inner content", "citygov") ),
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_gap_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_gap_reg_shortcodes_vc');
	function citygov_sc_gap_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_gap",
			"name" => esc_html__("Gap", "citygov"),
			"description" => wp_kses_data( __("Insert gap (fullwidth area) in the post content", "citygov") ),
			"category" => esc_html__('Structure', 'citygov'),
			'icon' => 'icon_trx_gap',
			"class" => "trx_sc_collection trx_sc_gap",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"params" => array(
				/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("Gap content", "citygov"),
					"description" => wp_kses_data( __("Gap inner content", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				)
				*/
			)
		) );
		
		class WPBakeryShortCode_Trx_Gap extends CITYGOV_VC_ShortCodeCollection {}
	}
}
?>