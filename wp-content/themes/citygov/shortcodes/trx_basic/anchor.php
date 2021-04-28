<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_anchor_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_anchor_theme_setup' );
	function citygov_sc_anchor_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_anchor_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_anchor_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_anchor id="unique_id" description="Anchor description" title="Short Caption" icon="icon-class"]
*/

if (!function_exists('citygov_sc_anchor')) {	
	function citygov_sc_anchor($atts, $content = null) {
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"description" => '',
			"icon" => '',
			"url" => "",
			"separator" => "no",
			// Common params
			"id" => ""
		), $atts)));
		$output = $id 
			? '<a id="'.esc_attr($id).'"'
				. ' class="sc_anchor"' 
				. ' title="' . ($title ? esc_attr($title) : '') . '"'
				. ' data-description="' . ($description ? esc_attr(citygov_strmacros($description)) : ''). '"'
				. ' data-icon="' . ($icon ? $icon : '') . '"' 
				. ' data-url="' . ($url ? esc_attr($url) : '') . '"' 
				. ' data-separator="' . (citygov_param_is_on($separator) ? 'yes' : 'no') . '"'
				. '></a>'
			: '';
		return apply_filters('citygov_shortcode_output', $output, 'trx_anchor', $atts, $content);
	}
	citygov_require_shortcode("trx_anchor", "citygov_sc_anchor");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_anchor_reg_shortcodes' ) ) {
	function citygov_sc_anchor_reg_shortcodes() {
	
		citygov_sc_map("trx_anchor", array(
			"title" => esc_html__("Anchor", "citygov"),
			"desc" => wp_kses_data( __("Insert anchor for the TOC (table of content)", "citygov") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"icon" => array(
					"title" => esc_html__("Anchor's icon",  'citygov'),
					"desc" => wp_kses_data( __('Select icon for the anchor from Fontello icons set',  'citygov') ),
					"value" => "",
					"type" => "icons",
					"options" => citygov_get_sc_param('icons')
				),
				"title" => array(
					"title" => esc_html__("Short title", "citygov"),
					"desc" => wp_kses_data( __("Short title of the anchor (for the table of content)", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"description" => array(
					"title" => esc_html__("Long description", "citygov"),
					"desc" => wp_kses_data( __("Description for the popup (then hover on the icon). You can use:<br>'{{' and '}}' - to make the text italic,<br>'((' and '))' - to make the text bold,<br>'||' - to insert line break", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"url" => array(
					"title" => esc_html__("External URL", "citygov"),
					"desc" => wp_kses_data( __("External URL for this TOC item", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"separator" => array(
					"title" => esc_html__("Add separator", "citygov"),
					"desc" => wp_kses_data( __("Add separator under item in the TOC", "citygov") ),
					"value" => "no",
					"type" => "switch",
					"options" => citygov_get_sc_param('yes_no')
				),
				"id" => citygov_get_sc_param('id')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_anchor_reg_shortcodes_vc' ) ) {
	function citygov_sc_anchor_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_anchor",
			"name" => esc_html__("Anchor", "citygov"),
			"description" => wp_kses_data( __("Insert anchor for the TOC (table of content)", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_anchor',
			"class" => "trx_sc_single trx_sc_anchor",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Anchor's icon", "citygov"),
					"description" => wp_kses_data( __("Select icon for the anchor from Fontello icons set", "citygov") ),
					"class" => "",
					"value" => citygov_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Short title", "citygov"),
					"description" => wp_kses_data( __("Short title of the anchor (for the table of content)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "description",
					"heading" => esc_html__("Long description", "citygov"),
					"description" => wp_kses_data( __("Description for the popup (then hover on the icon). You can use:<br>'{{' and '}}' - to make the text italic,<br>'((' and '))' - to make the text bold,<br>'||' - to insert line break", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("External URL", "citygov"),
					"description" => wp_kses_data( __("External URL for this TOC item", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "separator",
					"heading" => esc_html__("Add separator", "citygov"),
					"description" => wp_kses_data( __("Add separator under item in the TOC", "citygov") ),
					"class" => "",
					"value" => array("Add separator" => "yes" ),
					"type" => "checkbox"
				),
				citygov_get_vc_param('id')
			),
		) );
		
		class WPBakeryShortCode_Trx_Anchor extends CITYGOV_VC_ShortCodeSingle {}
	}
}
?>