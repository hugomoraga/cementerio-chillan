<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_emailer_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_emailer_theme_setup' );
	function citygov_sc_emailer_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_emailer_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_emailer_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_emailer group=""]

if (!function_exists('citygov_sc_emailer')) {	
	function citygov_sc_emailer($atts, $content = null) {
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"group" => "",
			"open" => "yes",
			"align" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "",
			"height" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= citygov_get_css_dimensions_from_values($width, $height);
		// Load core messages
		citygov_enqueue_messages();
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
					. ' class="sc_emailer' . ($align && $align!='none' ? ' align' . esc_attr($align) : '') . (citygov_param_is_on($open) ? ' sc_emailer_opened' : '') . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
					. ($css ? ' style="'.esc_attr($css).'"' : '') 
					. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
					. '>'
				. '<form class="sc_emailer_form">'
				. '<input type="text" class="sc_emailer_input" name="email" value="" placeholder="'.esc_attr__('Enter Your Email', 'citygov').'">'
				. '<a href="#" class="sc_emailer_button icon-mail" title="'.esc_attr__('Submit', 'citygov').'" data-group="'.esc_attr($group ? $group : esc_html__('E-mailer subscription', 'citygov')).'"></a>'
				. '</form>'
			. '</div>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_emailer', $atts, $content);
	}
	citygov_require_shortcode("trx_emailer", "citygov_sc_emailer");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_emailer_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_emailer_reg_shortcodes');
	function citygov_sc_emailer_reg_shortcodes() {
	
		citygov_sc_map("trx_emailer", array(
			"title" => esc_html__("E-mail collector", "citygov"),
			"desc" => wp_kses_data( __("Collect the e-mail address into specified group", "citygov") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"group" => array(
					"title" => esc_html__("Group", "citygov"),
					"desc" => wp_kses_data( __("The name of group to collect e-mail address", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"open" => array(
					"title" => esc_html__("Open", "citygov"),
					"desc" => wp_kses_data( __("Initially open the input field on show object", "citygov") ),
					"divider" => true,
					"value" => "yes",
					"type" => "switch",
					"options" => citygov_get_sc_param('yes_no')
				),
				"align" => array(
					"title" => esc_html__("Alignment", "citygov"),
					"desc" => wp_kses_data( __("Align object to left, center or right", "citygov") ),
					"divider" => true,
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('align')
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
if ( !function_exists( 'citygov_sc_emailer_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_emailer_reg_shortcodes_vc');
	function citygov_sc_emailer_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_emailer",
			"name" => esc_html__("E-mail collector", "citygov"),
			"description" => wp_kses_data( __("Collect e-mails into specified group", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_emailer',
			"class" => "trx_sc_single trx_sc_emailer",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "group",
					"heading" => esc_html__("Group", "citygov"),
					"description" => wp_kses_data( __("The name of group to collect e-mail address", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "open",
					"heading" => esc_html__("Opened", "citygov"),
					"description" => wp_kses_data( __("Initially open the input field on show object", "citygov") ),
					"class" => "",
					"value" => array(esc_html__('Initially opened', 'citygov') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "citygov"),
					"description" => wp_kses_data( __("Align field to left, center or right", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('align')),
					"type" => "dropdown"
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
			)
		) );
		
		class WPBakeryShortCode_Trx_Emailer extends CITYGOV_VC_ShortCodeSingle {}
	}
}
?>