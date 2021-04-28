<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_hide_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_hide_theme_setup' );
	function citygov_sc_hide_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_hide_reg_shortcodes');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_hide selector="unique_id"]
*/

if (!function_exists('citygov_sc_hide')) {	
	function citygov_sc_hide($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"selector" => "",
			"hide" => "on",
			"delay" => 0
		), $atts)));
		$selector = trim(chop($selector));
		$output = $selector == '' ? '' : 
			'<script type="text/javascript">
				jQuery(document).ready(function() {
					'.($delay>0 ? 'setTimeout(function() {' : '').'
					jQuery("'.esc_attr($selector).'").' . ($hide=='on' ? 'hide' : 'show') . '();
					'.($delay>0 ? '},'.($delay).');' : '').'
				});
			</script>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_hide', $atts, $content);
	}
	citygov_require_shortcode('trx_hide', 'citygov_sc_hide');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_hide_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_hide_reg_shortcodes');
	function citygov_sc_hide_reg_shortcodes() {
	
		citygov_sc_map("trx_hide", array(
			"title" => esc_html__("Hide/Show any block", "citygov"),
			"desc" => wp_kses_data( __("Hide or Show any block with desired CSS-selector", "citygov") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"selector" => array(
					"title" => esc_html__("Selector", "citygov"),
					"desc" => wp_kses_data( __("Any block's CSS-selector", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"hide" => array(
					"title" => esc_html__("Hide or Show", "citygov"),
					"desc" => wp_kses_data( __("New state for the block: hide or show", "citygov") ),
					"value" => "yes",
					"size" => "small",
					"options" => citygov_get_sc_param('yes_no'),
					"type" => "switch"
				)
			)
		));
	}
}
?>