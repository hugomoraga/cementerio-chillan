<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_columns_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_columns_theme_setup' );
	function citygov_sc_columns_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_columns_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_columns_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_columns id="unique_id" count="number"]
	[trx_column_item id="unique_id" span="2 - number_columns"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta, odio arcu vut natoque dolor ut, enim etiam vut augue. Ac augue amet quis integer ut dictumst? Elit, augue vut egestas! Tristique phasellus cursus egestas a nec a! Sociis et? Augue velit natoque, amet, augue. Vel eu diam, facilisis arcu.[/trx_column_item]
	[trx_column_item]A pulvinar ut, parturient enim porta ut sed, mus amet nunc, in. Magna eros hac montes, et velit. Odio aliquam phasellus enim platea amet. Turpis dictumst ultrices, rhoncus aenean pulvinar? Mus sed rhoncus et cras egestas, non etiam a? Montes? Ac aliquam in nec nisi amet eros! Facilisis! Scelerisque in.[/trx_column_item]
	[trx_column_item]Duis sociis, elit odio dapibus nec, dignissim purus est magna integer eu porta sagittis ut, pid rhoncus facilisis porttitor porta, et, urna parturient mid augue a, in sit arcu augue, sit lectus, natoque montes odio, enim. Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus, vut enim habitasse cum magna.[/trx_column_item]
	[trx_column_item]Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus, vut enim habitasse cum magna. Duis sociis, elit odio dapibus nec, dignissim purus est magna integer eu porta sagittis ut, pid rhoncus facilisis porttitor porta, et, urna parturient mid augue a, in sit arcu augue, sit lectus, natoque montes odio, enim.[/trx_column_item]
[/trx_columns]
*/

if (!function_exists('citygov_sc_columns')) {	
	function citygov_sc_columns($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"count" => "2",
            "style_col" => "",
			"fluid" => "no",
			"margins" => "yes",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= citygov_get_css_dimensions_from_values($width, $height);
		$count = max(1, min(12, (int) $count));
		citygov_storage_set('sc_columns_data', array(
			'counter' => 1,
            'after_span2' => false,
            'after_span3' => false,
            'after_span4' => false,
            'count' => $count
            )
        );
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="columns_wrap sc_columns'
					. ' columns_' . (citygov_param_is_on($fluid) ? 'fluid' : 'nofluid') 
					. (!empty($margins) && citygov_param_is_off($margins) ? ' no_margins' : '') 
					. ' sc_columns_count_' . esc_attr($count)
					. (!empty($class) ? ' '.esc_attr($class) : '')
                    . (!empty($style_col) ? ' style_col_'.esc_attr($style_col) : '')
            . '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
				. '>'
					. do_shortcode($content)
				. '</div>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_columns', $atts, $content);
	}
	citygov_require_shortcode('trx_columns', 'citygov_sc_columns');
}


if (!function_exists('citygov_sc_column_item')) {	
	function citygov_sc_column_item($atts, $content=null) {
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts( array(
			// Individual params
			"span" => "1",
			"align" => "",
			"color" => "",
			"bg_color" => "",
			"bg_image" => "",
			"bg_tile" => "no",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => ""
		), $atts)));
		$css .= ($align !== '' ? 'text-align:' . esc_attr($align) . ';' : '') 
			. ($color !== '' ? 'color:' . esc_attr($color) . ';' : '');
		$span = max(1, min(11, (int) $span));
		if (!empty($bg_image)) {
			if ($bg_image > 0) {
				$attach = wp_get_attachment_image_src( $bg_image, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$bg_image = $attach[0];
			}
		}
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') . ' class="column-'.($span > 1 ? esc_attr($span) : 1).'_'.esc_attr(citygov_storage_get_array('sc_columns_data', 'count')).' sc_column_item sc_column_item_'.esc_attr(citygov_storage_get_array('sc_columns_data', 'counter')) 
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. (citygov_storage_get_array('sc_columns_data', 'counter') % 2 == 1 ? ' odd' : ' even') 
					. (citygov_storage_get_array('sc_columns_data', 'counter') == 1 ? ' first' : '') 
					. ($span > 1 ? ' span_'.esc_attr($span) : '') 
					. (citygov_storage_get_array('sc_columns_data', 'after_span2') ? ' after_span_2' : '') 
					. (citygov_storage_get_array('sc_columns_data', 'after_span3') ? ' after_span_3' : '') 
					. (citygov_storage_get_array('sc_columns_data', 'after_span4') ? ' after_span_4' : '') 
					. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
					. '>'
					. ($bg_color!=='' || $bg_image !== '' ? '<div class="sc_column_item_inner" style="'
							. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) . ';' : '')
							. ($bg_image !== '' ? 'background-image:url(' . esc_url($bg_image) . ');'.(citygov_param_is_on($bg_tile) ? 'background-repeat:repeat;' : 'background-repeat:no-repeat;background-size:cover;') : '')
							. '">' : '')
						. do_shortcode($content)
					. ($bg_color!=='' || $bg_image !== '' ? '</div>' : '')
					. '</div>';
		citygov_storage_inc_array('sc_columns_data', 'counter', $span);
		citygov_storage_set_array('sc_columns_data', 'after_span2', $span==2);
		citygov_storage_set_array('sc_columns_data', 'after_span3', $span==3);
		citygov_storage_set_array('sc_columns_data', 'after_span4', $span==4);
		return apply_filters('citygov_shortcode_output', $output, 'trx_column_item', $atts, $content);
	}
	citygov_require_shortcode('trx_column_item', 'citygov_sc_column_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_columns_reg_shortcodes' ) ) {
	function citygov_sc_columns_reg_shortcodes() {
	
		citygov_sc_map("trx_columns", array(
			"title" => esc_html__("Columns", "citygov"),
			"desc" => wp_kses_data( __("Insert up to 5 columns in your page (post)", "citygov") ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"fluid" => array(
					"title" => esc_html__("Fluid columns", "citygov"),
					"desc" => wp_kses_data( __("To squeeze the columns when reducing the size of the window (fluid=yes) or to rebuild them (fluid=no)", "citygov") ),
					"value" => "no",
					"type" => "switch",
					"options" => citygov_get_sc_param('yes_no')
				), 
				"margins" => array(
					"title" => esc_html__("Margins between columns", "citygov"),
					"desc" => wp_kses_data( __("Add margins between columns", "citygov") ),
					"value" => "yes",
					"type" => "switch",
					"options" => citygov_get_sc_param('yes_no')
				),
                "style_col" => array(
                    "title" => esc_html__("Style of columns", "citygov"),
                    "desc" => wp_kses_data( __("Style of columns", "citygov") ),
                    "value" => "original",
                    "dir" => "horizontal",
                    "options" => array(
                        'original' => esc_html__('Original', 'citygov'),
                        'delimiter' => esc_html__('With delimiter', 'citygov')
                    ),
                    "type" => "checklist"
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
			),
			"children" => array(
				"name" => "trx_column_item",
				"title" => esc_html__("Column", "citygov"),
				"desc" => wp_kses_data( __("Column item", "citygov") ),
				"container" => true,
				"params" => array(
					"span" => array(
						"title" => esc_html__("Merge columns", "citygov"),
						"desc" => wp_kses_data( __("Count merged columns from current", "citygov") ),
						"value" => "",
						"type" => "text"
					),
					"align" => array(
						"title" => esc_html__("Alignment", "citygov"),
						"desc" => wp_kses_data( __("Alignment text in the column", "citygov") ),
						"value" => "",
						"type" => "checklist",
						"dir" => "horizontal",
						"options" => citygov_get_sc_param('align')
					),
					"color" => array(
						"title" => esc_html__("Fore color", "citygov"),
						"desc" => wp_kses_data( __("Any color for objects in this column", "citygov") ),
						"value" => "",
						"type" => "color"
					),
					"bg_color" => array(
						"title" => esc_html__("Background color", "citygov"),
						"desc" => wp_kses_data( __("Any background color for this column", "citygov") ),
						"value" => "",
						"type" => "color"
					),
					"bg_image" => array(
						"title" => esc_html__("URL for background image file", "citygov"),
						"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the background", "citygov") ),
						"readonly" => false,
						"value" => "",
						"type" => "media"
					),
					"bg_tile" => array(
						"title" => esc_html__("Tile background image", "citygov"),
						"desc" => wp_kses_data( __("Do you want tile background image or image cover whole column?", "citygov") ),
						"value" => "no",
						"dependency" => array(
							'bg_image' => array('not_empty')
						),
						"type" => "switch",
						"options" => citygov_get_sc_param('yes_no')
					),
					"_content_" => array(
						"title" => esc_html__("Column item content", "citygov"),
						"desc" => wp_kses_data( __("Current column item content", "citygov") ),
						"divider" => true,
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
					),
					"id" => citygov_get_sc_param('id'),
					"class" => citygov_get_sc_param('class'),
					"animation" => citygov_get_sc_param('animation'),
					"css" => citygov_get_sc_param('css')
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_columns_reg_shortcodes_vc' ) ) {
	function citygov_sc_columns_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_columns",
			"name" => esc_html__("Columns", "citygov"),
			"description" => wp_kses_data( __("Insert columns with margins", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_columns',
			"class" => "trx_sc_columns",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"as_parent" => array('only' => 'trx_column_item'),
			"params" => array(
				array(
					"param_name" => "count",
					"heading" => esc_html__("Columns count", "citygov"),
					"description" => wp_kses_data( __("Number of the columns in the container.", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "2",
					"type" => "textfield"
				),
				array(
					"param_name" => "fluid",
					"heading" => esc_html__("Fluid columns", "citygov"),
					"description" => wp_kses_data( __("To squeeze the columns when reducing the size of the window (fluid=yes) or to rebuild them (fluid=no)", "citygov") ),
					"class" => "",
					"value" => array(esc_html__('Fluid columns', 'citygov') => 'yes'),
					"type" => "checkbox"
				),
                array(
                    "param_name" => "style_col",
                    "heading" => esc_html__("Style of columns", "citygov"),
                    "description" => wp_kses_data( __("Style of columns", "citygov") ),
                    "class" => "",
                    "value" => array(
                        esc_html__('Original', 'citygov') => 'original',
                        esc_html__('With delimiter', 'citygov') => 'delimiter'
                    ),
                    "type" => "dropdown"
                ),
				array(
					"param_name" => "margins",
					"heading" => esc_html__("Margins between columns", "citygov"),
					"description" => wp_kses_data( __("Add margins between columns", "citygov") ),
					"class" => "",
					"std" => "yes",
					"value" => array(esc_html__('Disable margins between columns', 'citygov') => 'no'),
					"type" => "checkbox"
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
			'default_content' => '
				[trx_column_item][/trx_column_item]
				[trx_column_item][/trx_column_item]
			',
			'js_view' => 'VcTrxColumnsView'
		) );
		
		
		vc_map( array(
			"base" => "trx_column_item",
			"name" => esc_html__("Column", "citygov"),
			"description" => wp_kses_data( __("Column item", "citygov") ),
			"show_settings_on_create" => true,
			"class" => "trx_sc_collection trx_sc_column_item",
			"content_element" => true,
			"is_container" => true,
			'icon' => 'icon_trx_column_item',
			"as_child" => array('only' => 'trx_columns'),
			"as_parent" => array('except' => 'trx_columns'),
			"params" => array(
				array(
					"param_name" => "span",
					"heading" => esc_html__("Merge columns", "citygov"),
					"description" => wp_kses_data( __("Count merged columns from current", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "citygov"),
					"description" => wp_kses_data( __("Alignment text in the column", "citygov") ),
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Fore color", "citygov"),
					"description" => wp_kses_data( __("Any color for objects in this column", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "citygov"),
					"description" => wp_kses_data( __("Any background color for this column", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_image",
					"heading" => esc_html__("URL for background image file", "citygov"),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for the background", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_tile",
					"heading" => esc_html__("Tile background image", "citygov"),
					"description" => wp_kses_data( __("Do you want tile background image or image cover whole column?", "citygov") ),
					"class" => "",
					'dependency' => array(
						'element' => 'bg_image',
						'not_empty' => true
					),
					"std" => "no",
					"value" => array(esc_html__('Tile background image', 'citygov') => 'yes'),
					"type" => "checkbox"
				),
				/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("Column's content", "citygov"),
					"description" => wp_kses_data( __("Content of the current column", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				*/
				citygov_get_vc_param('id'),
				citygov_get_vc_param('class'),
				citygov_get_vc_param('animation'),
				citygov_get_vc_param('css')
			),
			'js_view' => 'VcTrxColumnItemView'
		) );
		
		class WPBakeryShortCode_Trx_Columns extends CITYGOV_VC_ShortCodeColumns {}
		class WPBakeryShortCode_Trx_Column_Item extends CITYGOV_VC_ShortCodeCollection {}
	}
}
?>