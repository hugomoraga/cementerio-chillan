<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_skills_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_skills_theme_setup' );
	function citygov_sc_skills_theme_setup() {
		add_action('citygov_action_shortcodes_list', 		'citygov_sc_skills_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_sc_skills_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_skills id="unique_id" type="bar|pie|arc|counter" dir="horizontal|vertical" layout="rows|columns" count="" max_value="100" align="left|right"]
	[trx_skills_item title="Scelerisque pid" value="50%"]
	[trx_skills_item title="Scelerisque pid" value="50%"]
	[trx_skills_item title="Scelerisque pid" value="50%"]
[/trx_skills]
*/

if (!function_exists('citygov_sc_skills')) {	
	function citygov_sc_skills($atts, $content=null){	
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts(array(
			// Individual params
			"max_value" => "100",
			"type" => "bar",
			"layout" => "",
			"dir" => "",
			"style" => "1",
			"columns" => "",
			"align" => "",
			"color" => "",
			"bg_color" => "",
			"border_color" => "",
			"arc_caption" => esc_html__("Skills", "citygov"),
			"pie_compact" => "on",
			"pie_cutout" => 0,
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link_caption" => esc_html__('Learn more', 'citygov'),
			"link" => '',
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		citygov_storage_set('sc_skills_data', array(
			'counter' => 0,
            'columns' => 0,
            'height'  => 0,
            'type'    => $type,
            'pie_compact' => citygov_param_is_on($pie_compact) ? 'on' : 'off',
            'pie_cutout'  => max(0, min(99, $pie_cutout)),
            'color'   => $color,
            'bg_color'=> $bg_color,
            'border_color'=> $border_color,
            'legend'  => '',
            'data'    => ''
			)
		);
		citygov_enqueue_diagram($type);
		if ($type!='arc') {
			if ($layout=='' || ($layout=='columns' && $columns<1)) $layout = 'rows';
			if ($layout=='columns') citygov_storage_set_array('sc_skills_data', 'columns', $columns);
			if ($type=='bar') {
				if ($dir == '') $dir = 'horizontal';
				if ($dir == 'vertical' && $height < 1) $height = 300;
			}
		}
		if (empty($id)) $id = 'sc_skills_diagram_'.str_replace('.','',mt_rand());
		if ($max_value < 1) $max_value = 100;
		if ($style) {
			$style = max(1, min(4, $style));
			citygov_storage_set_array('sc_skills_data', 'style', $style);
		}
		citygov_storage_set_array('sc_skills_data', 'max', $max_value);
		citygov_storage_set_array('sc_skills_data', 'dir', $dir);
		citygov_storage_set_array('sc_skills_data', 'height', citygov_prepare_css_value($height));
		$class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= citygov_get_css_dimensions_from_values($width);
		if (!citygov_storage_empty('sc_skills_data', 'height') && (citygov_storage_get_array('sc_skills_data', 'type') == 'arc' || (citygov_storage_get_array('sc_skills_data', 'type') == 'pie' && citygov_param_is_on(citygov_storage_get_array('sc_skills_data', 'pie_compact')))))
			$css .= 'height: '.citygov_storage_get_array('sc_skills_data', 'height');
		$content = do_shortcode($content);
		$output = '<div id="'.esc_attr($id).'"' 
					. ' class="sc_skills sc_skills_' . esc_attr($type) 
						. ($type=='bar' ? ' sc_skills_'.esc_attr($dir) : '') 
						. ($type=='pie' ? ' sc_skills_compact_'.esc_attr(citygov_storage_get_array('sc_skills_data', 'pie_compact')) : '') 
						. (!empty($class) ? ' '.esc_attr($class) : '') 
						. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
						. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
					. ' data-type="'.esc_attr($type).'"'
					. ' data-caption="'.esc_attr($arc_caption).'"'
					. ($type=='bar' ? ' data-dir="'.esc_attr($dir).'"' : '')
				. '>'
					. (!empty($subtitle) ? '<h6 class="sc_skills_subtitle sc_item_subtitle">' . esc_html($subtitle) . '</h6>' : '')
					. (!empty($title) ? '<h2 class="sc_skills_title sc_item_title">' . esc_html($title) . '</h2>' : '')
					. (!empty($description) ? '<div class="sc_skills_descr sc_item_descr">' . trim($description) . '</div>' : '')
					. ($layout == 'columns' ? '<div class="columns_wrap sc_skills_'.esc_attr($layout).' sc_skills_columns_'.esc_attr($columns).'">' : '')
					. ($type=='arc' 
						? ('<div class="sc_skills_legend">'.(citygov_storage_get_array('sc_skills_data', 'legend')).'</div>'
							. '<div id="'.esc_attr($id).'_diagram" class="sc_skills_arc_canvas"></div>'
							. '<div class="sc_skills_data" style="display:none;">' . (citygov_storage_get_array('sc_skills_data', 'data')) . '</div>'
						  )
						: '')
					. ($type=='pie' && citygov_param_is_on(citygov_storage_get_array('sc_skills_data', 'pie_compact'))
						? ('<div class="sc_skills_legend">'.(citygov_storage_get_array('sc_skills_data', 'legend')).'</div>'
							. '<div id="'.esc_attr($id).'_pie" class="sc_skills_item">'
								. '<canvas id="'.esc_attr($id).'_pie" class="sc_skills_pie_canvas"></canvas>'
								. '<div class="sc_skills_data" style="display:none;">' . (citygov_storage_get_array('sc_skills_data', 'data')) . '</div>'
							. '</div>'
						  )
						: '')
					. ($content)
					. ($layout == 'columns' ? '</div>' : '')
					. (!empty($link) ? '<div class="sc_skills_button sc_item_button">'.do_shortcode('[trx_button link="'.esc_url($link).'" icon="icon-right"]'.esc_html($link_caption).'[/trx_button]').'</div>' : '')
				. '</div>';
		return apply_filters('citygov_shortcode_output', $output, 'trx_skills', $atts, $content);
	}
	citygov_require_shortcode('trx_skills', 'citygov_sc_skills');
}


if (!function_exists('citygov_sc_skills_item')) {	
	function citygov_sc_skills_item($atts, $content=null) {
		if (citygov_in_shortcode_blogger()) return '';
		extract(citygov_html_decode(shortcode_atts( array(
			// Individual params
			"title" => "",
			"value" => "",
			"color" => "",
			"bg_color" => "",
			"border_color" => "",
			"style" => "",
			"icon" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		citygov_storage_inc_array('sc_skills_data', 'counter');
		$ed = citygov_substr($value, -1)=='%' ? '%' : '';
		$value = str_replace('%', '', $value);
		if (citygov_storage_get_array('sc_skills_data', 'max') < $value) citygov_storage_set_array('sc_skills_data', 'max', $value);
		$percent = round($value / citygov_storage_get_array('sc_skills_data', 'max') * 100);
		$start = 0;
		$stop = $value;
		$steps = 100;
		$step = max(1, round(citygov_storage_get_array('sc_skills_data', 'max')/$steps));
		$speed = mt_rand(10,40);
		$animation = round(($stop - $start) / $step * $speed);
		$title_block = '<div class="sc_skills_info"><div class="sc_skills_label">' . ($title) . '</div></div>';
		$old_color = $color;
		if (empty($color)) $color = citygov_storage_get_array('sc_skills_data', 'color');
		if (empty($color)) $color = citygov_get_scheme_color('accent1_hover', $color);
		if (empty($bg_color)) $bg_color = citygov_storage_get_array('sc_skills_data', 'bg_color');
		if (empty($bg_color)) $bg_color = citygov_get_scheme_color('bg_color', $bg_color);
		if (empty($border_color)) $border_color = citygov_storage_get_array('sc_skills_data', 'border_color');
		if (empty($border_color)) $border_color = citygov_get_scheme_color('bd_color', $border_color);;
		if (empty($style)) $style = citygov_storage_get_array('sc_skills_data', 'style');
		$style = max(1, min(4, $style));
		$output = '';
		if (citygov_storage_get_array('sc_skills_data', 'type') == 'arc' || (citygov_storage_get_array('sc_skills_data', 'type') == 'pie' && citygov_param_is_on(citygov_storage_get_array('sc_skills_data', 'pie_compact')))) {
			if (citygov_storage_get_array('sc_skills_data', 'type') == 'arc' && empty($old_color)) {
				$rgb = citygov_hex2rgb($color);
				$color = 'rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.(1 - 0.1*(citygov_storage_get_array('sc_skills_data', 'counter')-1)).')';
			}
			citygov_storage_concat_array('sc_skills_data', 'legend', 
				'<div class="sc_skills_legend_item"><span class="sc_skills_legend_marker" style="background-color:'.esc_attr($color).'"></span><span class="sc_skills_legend_title">' . ($title) . '</span><span class="sc_skills_legend_value">' . ($value) . '</span></div>'
			);
			citygov_storage_concat_array('sc_skills_data', 'data', 
				'<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
					. ' class="'.esc_attr(citygov_storage_get_array('sc_skills_data', 'type')).'"'
					. (citygov_storage_get_array('sc_skills_data', 'type')=='pie'
						? ( ' data-start="'.esc_attr($start).'"'
							. ' data-stop="'.esc_attr($stop).'"'
							. ' data-step="'.esc_attr($step).'"'
							. ' data-steps="'.esc_attr($steps).'"'
							. ' data-max="'.esc_attr(citygov_storage_get_array('sc_skills_data', 'max')).'"'
							. ' data-speed="'.esc_attr($speed).'"'
							. ' data-duration="'.esc_attr($animation).'"'
							. ' data-color="'.esc_attr($color).'"'
							. ' data-bg_color="'.esc_attr($bg_color).'"'
							. ' data-border_color="'.esc_attr($border_color).'"'
							. ' data-cutout="'.esc_attr(citygov_storage_get_array('sc_skills_data', 'pie_cutout')).'"'
							. ' data-easing="easeOutCirc"'
							. ' data-ed="'.esc_attr($ed).'"'
							)
						: '')
					. '><input type="hidden" class="text" value="'.esc_attr($title).'" /><input type="hidden" class="percent" value="'.esc_attr($percent).'" /><input type="hidden" class="color" value="'.esc_attr($color).'" /></div>'
			);
		} else {
			$output .= (citygov_storage_get_array('sc_skills_data', 'columns') > 0 
							? '<div class="sc_skills_column column-1_'.esc_attr(citygov_storage_get_array('sc_skills_data', 'columns')).'">' 
							: '')
					. (citygov_storage_get_array('sc_skills_data', 'type')=='bar' && citygov_storage_get_array('sc_skills_data', 'dir')=='horizontal' ? $title_block : '')
					. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_skills_item' . ($style ? ' sc_skills_style_'.esc_attr($style) : '') 
							. (!empty($class) ? ' '.esc_attr($class) : '')
							. (citygov_storage_get_array('sc_skills_data', 'counter') % 2 == 1 ? ' odd' : ' even') 
							. (citygov_storage_get_array('sc_skills_data', 'counter') == 1 ? ' first' : '') 
							. '"'
						. (citygov_storage_get_array('sc_skills_data', 'height') !='' || $css 
							? ' style="' 
								. (citygov_storage_get_array('sc_skills_data', 'height') !='' 
										? 'height: '.esc_attr(citygov_storage_get_array('sc_skills_data', 'height')).';' 
										: '') 
								. ($css) 
								. '"' 
							: '')
					. '>'
					. (!empty($icon) ? '<div class="sc_skills_icon '.esc_attr($icon).'"></div>' : '');
			if (in_array(citygov_storage_get_array('sc_skills_data', 'type'), array('bar', 'counter'))) {
				$output .= '<div class="margin_class"><div class="sc_skills_count"' . (citygov_storage_get_array('sc_skills_data', 'type')=='bar' && $color ? ' style="background-color:' . esc_attr($color) . '; border-color:' . esc_attr($color) . '"' : '') . '>'
							. '</div><div class="sc_skills_total"'
								. ' data-start="'.esc_attr($start).'"'
								. ' data-stop="'.esc_attr($stop).'"'
								. ' data-step="'.esc_attr($step).'"'
								. ' data-max="'.esc_attr(citygov_storage_get_array('sc_skills_data', 'max')).'"'
								. ' data-speed="'.esc_attr($speed).'"'
								. ' data-duration="'.esc_attr($animation).'"'
								. ' data-ed="'.esc_attr($ed).'">'
								. ($start) . ($ed)
							.'</div>'
						. '</div>';
			} else if (citygov_storage_get_array('sc_skills_data', 'type')=='pie') {
				if (empty($id)) $id = 'sc_skills_canvas_'.str_replace('.','',mt_rand());
				$output .= '<canvas id="'.esc_attr($id).'"></canvas>'
					. '<div class="sc_skills_total"'
						. ' data-start="'.esc_attr($start).'"'
						. ' data-stop="'.esc_attr($stop).'"'
						. ' data-step="'.esc_attr($step).'"'
						. ' data-steps="'.esc_attr($steps).'"'
						. ' data-max="'.esc_attr(citygov_storage_get_array('sc_skills_data', 'max')).'"'
						. ' data-speed="'.esc_attr($speed).'"'
						. ' data-duration="'.esc_attr($animation).'"'
						. ' data-color="'.esc_attr($color).'"'
						. ' data-bg_color="'.esc_attr($bg_color).'"'
						. ' data-border_color="'.esc_attr($border_color).'"'
						. ' data-cutout="'.esc_attr(citygov_storage_get_array('sc_skills_data', 'pie_cutout')).'"'
						. ' data-easing="easeOutCirc"'
						. ' data-ed="'.esc_attr($ed).'">'
						. ($start) . ($ed)
					.'</div>';
			}
			$output .= 
					  (citygov_storage_get_array('sc_skills_data', 'type')=='counter' ? $title_block : '')
					. '</div>'
					. (citygov_storage_get_array('sc_skills_data', 'type')=='bar' && citygov_storage_get_array('sc_skills_data', 'dir')=='vertical' || citygov_storage_get_array('sc_skills_data', 'type') == 'pie' ? $title_block : '')
					. (citygov_storage_get_array('sc_skills_data', 'columns') > 0 ? '</div>' : '');
		}
		return apply_filters('citygov_shortcode_output', $output, 'trx_skills_item', $atts, $content);
	}
	citygov_require_shortcode('trx_skills_item', 'citygov_sc_skills_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_skills_reg_shortcodes' ) ) {
	//add_action('citygov_action_shortcodes_list', 'citygov_sc_skills_reg_shortcodes');
	function citygov_sc_skills_reg_shortcodes() {
	
		citygov_sc_map("trx_skills", array(
			"title" => esc_html__("Skills", "citygov"),
			"desc" => wp_kses_data( __("Insert skills diagramm in your page (post)", "citygov") ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"max_value" => array(
					"title" => esc_html__("Max value", "citygov"),
					"desc" => wp_kses_data( __("Max value for skills items", "citygov") ),
					"value" => 100,
					"min" => 1,
					"type" => "spinner"
				),
				"type" => array(
					"title" => esc_html__("Skills type", "citygov"),
					"desc" => wp_kses_data( __("Select type of skills block", "citygov") ),
					"value" => "bar",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						'bar' => esc_html__('Bar', 'citygov'),
						'pie' => esc_html__('Pie chart', 'citygov'),
						'counter' => esc_html__('Counter', 'citygov'),
						'arc' => esc_html__('Arc', 'citygov')
					)
				), 
				"layout" => array(
					"title" => esc_html__("Skills layout", "citygov"),
					"desc" => wp_kses_data( __("Select layout of skills block", "citygov") ),
					"dependency" => array(
						'type' => array('counter','pie','bar')
					),
					"value" => "rows",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						'rows' => esc_html__('Rows', 'citygov'),
						'columns' => esc_html__('Columns', 'citygov')
					)
				),
				"dir" => array(
					"title" => esc_html__("Direction", "citygov"),
					"desc" => wp_kses_data( __("Select direction of skills block", "citygov") ),
					"dependency" => array(
						'type' => array('counter','pie','bar')
					),
					"value" => "horizontal",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('dir')
				), 
				"style" => array(
					"title" => esc_html__("Counters style", "citygov"),
					"desc" => wp_kses_data( __("Select style of skills items (only for type=counter)", "citygov") ),
					"dependency" => array(
						'type' => array('counter')
					),
					"value" => 1,
					"options" => citygov_get_list_styles(1, 4),
					"type" => "checklist"
				), 
				// "columns" - autodetect, not set manual
				"color" => array(
					"title" => esc_html__("Skills items color", "citygov"),
					"desc" => wp_kses_data( __("Color for all skills items", "citygov") ),
					"divider" => true,
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", "citygov"),
					"desc" => wp_kses_data( __("Background color for all skills items (only for type=pie)", "citygov") ),
					"dependency" => array(
						'type' => array('pie')
					),
					"value" => "",
					"type" => "color"
				),
				"border_color" => array(
					"title" => esc_html__("Border color", "citygov"),
					"desc" => wp_kses_data( __("Border color for all skills items (only for type=pie)", "citygov") ),
					"dependency" => array(
						'type' => array('pie')
					),
					"value" => "",
					"type" => "color"
				),
				"align" => array(
					"title" => esc_html__("Align skills block", "citygov"),
					"desc" => wp_kses_data( __("Align skills block to left or right side", "citygov") ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => citygov_get_sc_param('float')
				), 
				"arc_caption" => array(
					"title" => esc_html__("Arc Caption", "citygov"),
					"desc" => wp_kses_data( __("Arc caption - text in the center of the diagram", "citygov") ),
					"dependency" => array(
						'type' => array('arc')
					),
					"value" => "",
					"type" => "text"
				),
				"pie_compact" => array(
					"title" => esc_html__("Pie compact", "citygov"),
					"desc" => wp_kses_data( __("Show all skills in one diagram or as separate diagrams", "citygov") ),
					"dependency" => array(
						'type' => array('pie')
					),
					"value" => "yes",
					"type" => "switch",
					"options" => citygov_get_sc_param('yes_no')
				),
				"pie_cutout" => array(
					"title" => esc_html__("Pie cutout", "citygov"),
					"desc" => wp_kses_data( __("Pie cutout (0-99). 0 - without cutout, 99 - max cutout", "citygov") ),
					"dependency" => array(
						'type' => array('pie')
					),
					"value" => 0,
					"min" => 0,
					"max" => 99,
					"type" => "spinner"
				),
				"title" => array(
					"title" => esc_html__("Title", "citygov"),
					"desc" => wp_kses_data( __("Title for the block", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"subtitle" => array(
					"title" => esc_html__("Subtitle", "citygov"),
					"desc" => wp_kses_data( __("Subtitle for the block", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"description" => array(
					"title" => esc_html__("Description", "citygov"),
					"desc" => wp_kses_data( __("Short description for the block", "citygov") ),
					"value" => "",
					"type" => "textarea"
				),
				"link" => array(
					"title" => esc_html__("Button URL", "citygov"),
					"desc" => wp_kses_data( __("Link URL for the button at the bottom of the block", "citygov") ),
					"value" => "",
					"type" => "text"
				),
				"link_caption" => array(
					"title" => esc_html__("Button caption", "citygov"),
					"desc" => wp_kses_data( __("Caption for the button at the bottom of the block", "citygov") ),
					"value" => "",
					"type" => "text"
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
				"name" => "trx_skills_item",
				"title" => esc_html__("Skill", "citygov"),
				"desc" => wp_kses_data( __("Skills item", "citygov") ),
				"container" => false,
				"params" => array(
					"title" => array(
						"title" => esc_html__("Title", "citygov"),
						"desc" => wp_kses_data( __("Current skills item title", "citygov") ),
						"value" => "",
						"type" => "text"
					),
					"value" => array(
						"title" => esc_html__("Value", "citygov"),
						"desc" => wp_kses_data( __("Current skills level", "citygov") ),
						"value" => 50,
						"min" => 0,
						"step" => 1,
						"type" => "spinner"
					),
					"color" => array(
						"title" => esc_html__("Color", "citygov"),
						"desc" => wp_kses_data( __("Current skills item color", "citygov") ),
						"value" => "",
						"type" => "color"
					),
					"bg_color" => array(
						"title" => esc_html__("Background color", "citygov"),
						"desc" => wp_kses_data( __("Current skills item background color (only for type=pie)", "citygov") ),
						"value" => "",
						"type" => "color"
					),
					"border_color" => array(
						"title" => esc_html__("Border color", "citygov"),
						"desc" => wp_kses_data( __("Current skills item border color (only for type=pie)", "citygov") ),
						"value" => "",
						"type" => "color"
					),
					"style" => array(
						"title" => esc_html__("Counter style", "citygov"),
						"desc" => wp_kses_data( __("Select style for the current skills item (only for type=counter)", "citygov") ),
						"value" => 1,
						"options" => citygov_get_list_styles(1, 4),
						"type" => "checklist"
					), 
					"icon" => array(
						"title" => esc_html__("Counter icon",  'citygov'),
						"desc" => wp_kses_data( __('Select icon from Fontello icons set, placed above counter (only for type=counter)',  'citygov') ),
						"value" => "",
						"type" => "icons",
						"options" => citygov_get_sc_param('icons')
					),
					"id" => citygov_get_sc_param('id'),
					"class" => citygov_get_sc_param('class'),
					"css" => citygov_get_sc_param('css')
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_skills_reg_shortcodes_vc' ) ) {
	//add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_skills_reg_shortcodes_vc');
	function citygov_sc_skills_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_skills",
			"name" => esc_html__("Skills", "citygov"),
			"description" => wp_kses_data( __("Insert skills diagramm", "citygov") ),
			"category" => esc_html__('Content', 'citygov'),
			'icon' => 'icon_trx_skills',
			"class" => "trx_sc_collection trx_sc_skills",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"as_parent" => array('only' => 'trx_skills_item'),
			"params" => array(
				array(
					"param_name" => "max_value",
					"heading" => esc_html__("Max value", "citygov"),
					"description" => wp_kses_data( __("Max value for skills items", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "100",
					"type" => "textfield"
				),
				array(
					"param_name" => "type",
					"heading" => esc_html__("Skills type", "citygov"),
					"description" => wp_kses_data( __("Select type of skills block", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Bar', 'citygov') => 'bar',
						esc_html__('Pie chart', 'citygov') => 'pie',
						esc_html__('Counter', 'citygov') => 'counter',
						esc_html__('Arc', 'citygov') => 'arc'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "layout",
					"heading" => esc_html__("Skills layout", "citygov"),
					"description" => wp_kses_data( __("Select layout of skills block", "citygov") ),
					"admin_label" => true,
					'dependency' => array(
						'element' => 'type',
						'value' => array('counter','bar','pie')
					),
					"class" => "",
					"value" => array(
						esc_html__('Rows', 'citygov') => 'rows',
						esc_html__('Columns', 'citygov') => 'columns'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "dir",
					"heading" => esc_html__("Direction", "citygov"),
					"description" => wp_kses_data( __("Select direction of skills block", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('dir')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Counters style", "citygov"),
					"description" => wp_kses_data( __("Select style of skills items (only for type=counter)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(citygov_get_list_styles(1, 4)),
					'dependency' => array(
						'element' => 'type',
						'value' => array('counter')
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "columns",
					"heading" => esc_html__("Columns count", "citygov"),
					"description" => wp_kses_data( __("Skills columns count (required)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Color", "citygov"),
					"description" => wp_kses_data( __("Color for all skills items", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "citygov"),
					"description" => wp_kses_data( __("Background color for all skills items (only for type=pie)", "citygov") ),
					'dependency' => array(
						'element' => 'type',
						'value' => array('pie')
					),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "border_color",
					"heading" => esc_html__("Border color", "citygov"),
					"description" => wp_kses_data( __("Border color for all skills items (only for type=pie)", "citygov") ),
					'dependency' => array(
						'element' => 'type',
						'value' => array('pie')
					),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "citygov"),
					"description" => wp_kses_data( __("Align skills block to left or right side", "citygov") ),
					"class" => "",
					"value" => array_flip(citygov_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "arc_caption",
					"heading" => esc_html__("Arc caption", "citygov"),
					"description" => wp_kses_data( __("Arc caption - text in the center of the diagram", "citygov") ),
					'dependency' => array(
						'element' => 'type',
						'value' => array('arc')
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "pie_compact",
					"heading" => esc_html__("Pie compact", "citygov"),
					"description" => wp_kses_data( __("Show all skills in one diagram or as separate diagrams", "citygov") ),
					'dependency' => array(
						'element' => 'type',
						'value' => array('pie')
					),
					"class" => "",
					"value" => array(esc_html__('Show separate skills', 'citygov') => 'no'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "pie_cutout",
					"heading" => esc_html__("Pie cutout", "citygov"),
					"description" => wp_kses_data( __("Pie cutout (0-99). 0 - without cutout, 99 - max cutout", "citygov") ),
					'dependency' => array(
						'element' => 'type',
						'value' => array('pie')
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "citygov"),
					"description" => wp_kses_data( __("Title for the block", "citygov") ),
					"admin_label" => true,
					"group" => esc_html__('Captions', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "subtitle",
					"heading" => esc_html__("Subtitle", "citygov"),
					"description" => wp_kses_data( __("Subtitle for the block", "citygov") ),
					"group" => esc_html__('Captions', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "description",
					"heading" => esc_html__("Description", "citygov"),
					"description" => wp_kses_data( __("Description for the block", "citygov") ),
					"group" => esc_html__('Captions', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textarea"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Button URL", "citygov"),
					"description" => wp_kses_data( __("Link URL for the button at the bottom of the block", "citygov") ),
					"group" => esc_html__('Captions', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link_caption",
					"heading" => esc_html__("Button caption", "citygov"),
					"description" => wp_kses_data( __("Caption for the button at the bottom of the block", "citygov") ),
					"group" => esc_html__('Captions', 'citygov'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
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
		
		
		vc_map( array(
			"base" => "trx_skills_item",
			"name" => esc_html__("Skill", "citygov"),
			"description" => wp_kses_data( __("Skills item", "citygov") ),
			"show_settings_on_create" => true,
			'icon' => 'icon_trx_skills_item',
			"class" => "trx_sc_single trx_sc_skills_item",
			"content_element" => true,
			"is_container" => false,
			"as_child" => array('only' => 'trx_skills'),
			"as_parent" => array('except' => 'trx_skills'),
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "citygov"),
					"description" => wp_kses_data( __("Title for the current skills item", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "value",
					"heading" => esc_html__("Value", "citygov"),
					"description" => wp_kses_data( __("Value for the current skills item", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Color", "citygov"),
					"description" => wp_kses_data( __("Color for current skills item", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "citygov"),
					"description" => wp_kses_data( __("Background color for current skills item (only for type=pie)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "border_color",
					"heading" => esc_html__("Border color", "citygov"),
					"description" => wp_kses_data( __("Border color for current skills item (only for type=pie)", "citygov") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Counter style", "citygov"),
					"description" => wp_kses_data( __("Select style for the current skills item (only for type=counter)", "citygov") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(citygov_get_list_styles(1, 4)),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Counter icon", "citygov"),
					"description" => wp_kses_data( __("Select icon from Fontello icons set, placed before counter (only for type=counter)", "citygov") ),
					"class" => "",
					"value" => citygov_get_sc_param('icons'),
					"type" => "dropdown"
				),
				citygov_get_vc_param('id'),
				citygov_get_vc_param('class'),
				citygov_get_vc_param('css'),
			)
		) );
		
		class WPBakeryShortCode_Trx_Skills extends CITYGOV_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Skills_Item extends CITYGOV_VC_ShortCodeSingle {}
	}
}
?>