<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_tooltip_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_tooltip_theme_setup' );
	function citygov_sc_tooltip_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_tooltip_reg_shortcodes');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_tooltip id="unique_id" title="Tooltip text here"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/tooltip]
*/

if (!function_exists('citygov_sc_tooltip')) {	
	function citygov_sc_tooltip($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		$output = '<span' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_tooltip_parent'. (!empty($class) ? ' '.esc_attr($class) : '').'"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					. '>'
						. do_shortcode($content)
						. '<span class="sc_tooltip">' . ($title) . '</span>'
					. '</span>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_tooltip', $atts, $content);
	}
	citygov_require_shortcode('trx_tooltip', 'citygov_sc_tooltip');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_tooltip_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_tooltip_reg_shortcodes');
	function citygov_sc_tooltip_reg_shortcodes() {
	
		citygov_sc_map("trx_tooltip", array(
			"title" => esc_html__("Tooltip", "citygov"),
			"desc" => wp_kses_data( __("Create tooltip for selected text", "citygov") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"title" => array(
					"title" => esc_html__("Title", "citygov"),
					"desc" => wp_kses_data( __("Tooltip title (required)", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"_content_" => array(
					"title" => esc_html__("Tipped content", "citygov"),
					"desc" => wp_kses_data( __("Highlighted content with tooltip", "citygov") ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"id" => citygov_get_sc_param('id'),
				"class" => citygov_get_sc_param('class'),
				"css" => citygov_get_sc_param('css')
			)
		));
	}
}
?>