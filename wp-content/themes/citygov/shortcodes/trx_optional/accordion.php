<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('citygov_sc_accordion_theme_setup')) {
    add_action( 'citygov_action_before_init_theme', 'citygov_sc_accordion_theme_setup' );
    function citygov_sc_accordion_theme_setup() {
        add_action('citygov_action_shortcodes_list', 		'citygov_sc_accordion_reg_shortcodes');
        if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
            add_action('citygov_action_shortcodes_list_vc','citygov_sc_accordion_reg_shortcodes_vc');
    }
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_accordion counter="off" initial="1"]
	[trx_accordion_item title="Accordion Title 1"]Lorem ipsum dolor sit amet, consectetur adipisicing elit[/trx_accordion_item]
	[trx_accordion_item title="Accordion Title 2"]Proin dignissim commodo magna at luctus. Nam molestie justo augue, nec eleifend urna laoreet non.[/trx_accordion_item]
	[trx_accordion_item title="Accordion Title 3 with custom icons" icon_closed="icon-check" icon_opened="icon-delete"]Curabitur tristique tempus arcu a placerat.[/trx_accordion_item]
[/trx_accordion]
*/
if (!function_exists('citygov_sc_accordion')) {
    function citygov_sc_accordion($atts, $content=null){
        if (citygov_in_shortcode_blogger()) return '';
        extract(citygov_html_decode(shortcode_atts(array(
            // Individual params
            "initial" => "1",
            "counter" => "off",
            "icon_closed" => "icon-plus",
            "icon_opened" => "icon-minus",
            // Common params
            "id" => "",
            "class" => "",
            "css" => "",
            "animation" => "",
            "top" => "",
            "bottom" => "",
            "left" => "",
            "right" => ""
        ), $atts)));
        $class .= ($class ? ' ' : '') . citygov_get_css_position_as_classes($top, $right, $bottom, $left);
        $initial = max(0, (int) $initial);
        citygov_storage_set('sc_accordion_data', array(
                'counter' => 0,
                'show_counter' => citygov_param_is_on($counter),
                'icon_closed' => empty($icon_closed) || citygov_param_is_inherit($icon_closed) ? "icon-plus" : $icon_closed,
                'icon_opened' => empty($icon_opened) || citygov_param_is_inherit($icon_opened) ? "icon-minus" : $icon_opened
            )
        );
        citygov_enqueue_script('jquery-ui-accordion', false, array('jquery','jquery-ui-core'), null, true);
        $output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
            . ' class="sc_accordion'
            . (!empty($class) ? ' '.esc_attr($class) : '')
            . (citygov_param_is_on($counter) ? ' sc_show_counter' : '')
            . '"'
            . ($css!='' ? ' style="'.esc_attr($css).'"' : '')
            . ' data-active="' . ($initial-1) . '"'
            . (!citygov_param_is_off($animation) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($animation)).'"' : '')
            . '>'
            . do_shortcode($content)
            . '</div>';
        return apply_filters('citygov_shortcode_output', $output, 'trx_accordion', $atts, $content);
    }
    citygov_require_shortcode('trx_accordion', 'citygov_sc_accordion');
}


if (!function_exists('citygov_sc_accordion_item')) {
    function citygov_sc_accordion_item($atts, $content=null) {
        if (citygov_in_shortcode_blogger()) return '';
        extract(citygov_html_decode(shortcode_atts( array(
            // Individual params
            "icon_closed" => "",
            "icon_opened" => "",
            "icon_global" => "",
            "title" => "",
            // Common params
            "id" => "",
            "class" => "",
            "css" => ""
        ), $atts)));
        citygov_storage_inc_array('sc_accordion_data', 'counter');
        if (empty($icon_closed) || citygov_param_is_inherit($icon_closed)) $icon_closed = citygov_storage_get_array('sc_accordion_data', 'icon_closed', '', "icon-plus");
        if (empty($icon_opened) || citygov_param_is_inherit($icon_opened)) $icon_opened = citygov_storage_get_array('sc_accordion_data', 'icon_opened', '', "icon-minus");
        $output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
            . ' class="sc_accordion_item'
            . (!empty($class) ? ' '.esc_attr($class) : '')
            . (citygov_storage_get_array('sc_accordion_data', 'counter') % 2 == 1 ? ' odd' : ' even')
            . (citygov_storage_get_array('sc_accordion_data', 'counter') == 1 ? ' first' : '')
            . '">'
            . '<h5 class="sc_accordion_title">'
            . '<span class="sc_accordion_icon '.esc_attr($icon_global).' in_begin"></span>'
            . (!citygov_param_is_off($icon_closed) ? '<span class="sc_accordion_icon in_end sc_accordion_icon_closed '.esc_attr($icon_closed).'"></span>' : '')
            . (!citygov_param_is_off($icon_opened) ? '<span class="sc_accordion_icon in_end sc_accordion_icon_opened '.esc_attr($icon_opened).'"></span>' : '')
            . (citygov_storage_get_array('sc_accordion_data', 'show_counter') ? '<span class="sc_items_counter">'.(citygov_storage_get_array('sc_accordion_data', 'counter')).'</span>' : '')
            . ($title)
            . '</h5>'
            . '<div class="sc_accordion_content"'
            . ($css!='' ? ' style="'.esc_attr($css).'"' : '')
            . '>'
            . do_shortcode($content)
            . '</div>'
            . '</div>';
        return apply_filters('citygov_shortcode_output', $output, 'trx_accordion_item', $atts, $content);
    }
    citygov_require_shortcode('trx_accordion_item', 'citygov_sc_accordion_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_sc_accordion_reg_shortcodes' ) ) {
    //add_action('citygov_action_shortcodes_list', 'citygov_sc_accordion_reg_shortcodes');
    function citygov_sc_accordion_reg_shortcodes() {

        citygov_sc_map("trx_accordion", array(
            "title" => esc_html__("Accordion", "citygov"),
            "desc" => wp_kses( __("Accordion items", "citygov"), citygov_storage_get('allowed_tags') ),
            "decorate" => true,
            "container" => false,
            "params" => array(
                "counter" => array(
                    "title" => esc_html__("Counter", "citygov"),
                    "desc" => wp_kses( __("Display counter before each accordion title", "citygov"), citygov_storage_get('allowed_tags') ),
                    "value" => "off",
                    "type" => "switch",
                    "options" => citygov_get_sc_param('on_off')
                ),
                "initial" => array(
                    "title" => esc_html__("Initially opened item", "citygov"),
                    "desc" => wp_kses( __("Number of initially opened item", "citygov"), citygov_storage_get('allowed_tags') ),
                    "value" => 1,
                    "min" => 0,
                    "type" => "spinner"
                ),
                "icon_closed" => array(
                    "title" => esc_html__("Icon while closed",  'citygov'),
                    "desc" => wp_kses( __('Select icon for the closed accordion item from Fontello icons set',  'citygov'), citygov_storage_get('allowed_tags') ),
                    "value" => "",
                    "type" => "icons",
                    "options" => citygov_get_sc_param('icons')
                ),
                "icon_opened" => array(
                    "title" => esc_html__("Icon while opened",  'citygov'),
                    "desc" => wp_kses( __('Select icon for the opened accordion item from Fontello icons set',  'citygov'), citygov_storage_get('allowed_tags') ),
                    "value" => "",
                    "type" => "icons",
                    "options" => citygov_get_sc_param('icons')
                ),
                "icon_global" => array(
                    "title" => esc_html__("Icon of title",  'citygov'),
                    "desc" => wp_kses( __('Select icon for the title accordion item from Fontello icons set',  'citygov'), citygov_storage_get('allowed_tags') ),
                    "value" => "",
                    "type" => "icons",
                    "options" => citygov_get_sc_param('icons')
                ),
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
                "name" => "trx_accordion_item",
                "title" => esc_html__("Item", "citygov"),
                "desc" => wp_kses( __("Accordion item", "citygov"), citygov_storage_get('allowed_tags') ),
                "container" => true,
                "params" => array(
                    "title" => array(
                        "title" => esc_html__("Accordion item title", "citygov"),
                        "desc" => wp_kses( __("Title for current accordion item", "citygov"), citygov_storage_get('allowed_tags') ),
                        "value" => "",
                        "type" => "text"
                    ),
                    "icon_closed" => array(
                        "title" => esc_html__("Icon while closed",  'citygov'),
                        "desc" => wp_kses( __('Select icon for the closed accordion item from Fontello icons set',  'citygov'), citygov_storage_get('allowed_tags') ),
                        "value" => "",
                        "type" => "icons",
                        "options" => citygov_get_sc_param('icons')
                    ),
                    "icon_opened" => array(
                        "title" => esc_html__("Icon while opened",  'citygov'),
                        "desc" => wp_kses( __('Select icon for the opened accordion item from Fontello icons set',  'citygov'), citygov_storage_get('allowed_tags') ),
                        "value" => "",
                        "type" => "icons",
                        "options" => citygov_get_sc_param('icons')
                    ),
                    "icon_global" => array(
                        "title" => esc_html__("Icon of title",  'citygov'),
                        "desc" => wp_kses( __('Select icon for the title accordion item from Fontello icons set',  'citygov'), citygov_storage_get('allowed_tags') ),
                        "value" => "",
                        "type" => "icons",
                        "options" => citygov_get_sc_param('icons')
                    ),
                    "_content_" => array(
                        "title" => esc_html__("Accordion item content", "citygov"),
                        "desc" => wp_kses( __("Current accordion item content", "citygov"), citygov_storage_get('allowed_tags') ),
                        "rows" => 4,
                        "value" => "",
                        "type" => "textarea"
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
if ( !function_exists( 'citygov_sc_accordion_reg_shortcodes_vc' ) ) {
    //add_action('citygov_action_shortcodes_list_vc', 'citygov_sc_accordion_reg_shortcodes_vc');
    function citygov_sc_accordion_reg_shortcodes_vc() {

        vc_map( array(
            "base" => "trx_accordion",
            "name" => esc_html__("Accordion", "citygov"),
            "description" => wp_kses( __("Accordion items", "citygov"), citygov_storage_get('allowed_tags') ),
            "category" => esc_html__('Content', 'citygov'),
            'icon' => 'icon_trx_accordion',
            "class" => "trx_sc_collection trx_sc_accordion",
            "content_element" => true,
            "is_container" => true,
            "show_settings_on_create" => false,
            "as_parent" => array('only' => 'trx_accordion_item'),	// Use only|except attributes to limit child shortcodes (separate multiple values with comma)
            "params" => array(
                array(
                    "param_name" => "counter",
                    "heading" => esc_html__("Counter", "citygov"),
                    "description" => wp_kses( __("Display counter before each accordion title", "citygov"), citygov_storage_get('allowed_tags') ),
                    "class" => "",
                    "value" => array("Add item numbers before each element" => "on" ),
                    "type" => "checkbox"
                ),
                array(
                    "param_name" => "initial",
                    "heading" => esc_html__("Initially opened item", "citygov"),
                    "description" => wp_kses( __("Number of initially opened item", "citygov"), citygov_storage_get('allowed_tags') ),
                    "class" => "",
                    "value" => 1,
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "icon_closed",
                    "heading" => esc_html__("Icon while closed", "citygov"),
                    "description" => wp_kses( __("Select icon for the closed accordion item from Fontello icons set", "citygov"), citygov_storage_get('allowed_tags') ),
                    "class" => "",
                    "value" => citygov_get_sc_param('icons'),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "icon_opened",
                    "heading" => esc_html__("Icon while opened", "citygov"),
                    "description" => wp_kses( __("Select icon for the opened accordion item from Fontello icons set", "citygov"), citygov_storage_get('allowed_tags') ),
                    "class" => "",
                    "value" => citygov_get_sc_param('icons'),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "icon_global",
                    "heading" => esc_html__("Icon for title", "citygov"),
                    "description" => wp_kses( __("Select icon for the title accordion item from Fontello icons set", "citygov"), citygov_storage_get('allowed_tags') ),
                    "class" => "",
                    "value" => citygov_get_sc_param('icons'),
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
            ),
            'default_content' => '
				[trx_accordion_item title="' . esc_html__( 'Item 1 title', 'citygov' ) . '"][/trx_accordion_item]
				[trx_accordion_item title="' . esc_html__( 'Item 2 title', 'citygov' ) . '"][/trx_accordion_item]
			',
            "custom_markup" => '
				<div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
					%content%
				</div>
				<div class="tab_controls">
					<button class="add_tab" title="'.esc_attr__("Add item", "citygov").'">'.esc_html__("Add item", "citygov").'</button>
				</div>
			',
            'js_view' => 'VcTrxAccordionView'
        ) );


        vc_map( array(
            "base" => "trx_accordion_item",
            "name" => esc_html__("Accordion item", "citygov"),
            "description" => wp_kses( __("Inner accordion item", "citygov"), citygov_storage_get('allowed_tags') ),
            "show_settings_on_create" => true,
            "content_element" => true,
            "is_container" => true,
            'icon' => 'icon_trx_accordion_item',
            "as_child" => array('only' => 'trx_accordion'), 	// Use only|except attributes to limit parent (separate multiple values with comma)
            "as_parent" => array('except' => 'trx_accordion'),
            "params" => array(
                array(
                    "param_name" => "title",
                    "heading" => esc_html__("Title", "citygov"),
                    "description" => wp_kses( __("Title for current accordion item", "citygov"), citygov_storage_get('allowed_tags') ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "icon_closed",
                    "heading" => esc_html__("Icon while closed", "citygov"),
                    "description" => wp_kses( __("Select icon for the closed accordion item from Fontello icons set", "citygov"), citygov_storage_get('allowed_tags') ),
                    "class" => "",
                    "value" => citygov_get_sc_param('icons'),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "icon_opened",
                    "heading" => esc_html__("Icon while opened", "citygov"),
                    "description" => wp_kses( __("Select icon for the opened accordion item from Fontello icons set", "citygov"), citygov_storage_get('allowed_tags') ),
                    "class" => "",
                    "value" => citygov_get_sc_param('icons'),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "icon_global",
                    "heading" => esc_html__("Icon for title", "citygov"),
                    "description" => wp_kses( __("Select icon for the title accordion item from Fontello icons set", "citygov"), citygov_storage_get('allowed_tags') ),
                    "class" => "",
                    "value" => citygov_get_sc_param('icons'),
                    "type" => "dropdown"
                ),
                citygov_get_vc_param('id'),
                citygov_get_vc_param('class'),
                citygov_get_vc_param('css')
            ),
            'js_view' => 'VcTrxAccordionTabView'
        ) );

        class WPBakeryShortCode_Trx_Accordion extends citygov_VC_ShortCodeAccordion {}
        class WPBakeryShortCode_Trx_Accordion_Item extends citygov_VC_ShortCodeAccordionItem {}
    }
}
?>