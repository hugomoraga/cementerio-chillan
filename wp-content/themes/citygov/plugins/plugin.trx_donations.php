<?php
/* CityGov Donations support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('citygov_trx_donations_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_trx_donations_theme_setup', 1 );
	function citygov_trx_donations_theme_setup() {

		// Register shortcode in the shortcodes list
		if (citygov_exists_trx_donations()) {

			// Detect current page type, taxonomy and title (for custom post_types use priority < 10 to fire it handles early, than for standard post types)
			add_filter('citygov_filter_get_blog_type',			'citygov_trx_donations_get_blog_type', 9, 2);
			add_filter('citygov_filter_get_blog_title',		'citygov_trx_donations_get_blog_title', 9, 2);
			add_filter('citygov_filter_get_current_taxonomy',	'citygov_trx_donations_get_current_taxonomy', 9, 2);
			add_filter('citygov_filter_is_taxonomy',			'citygov_trx_donations_is_taxonomy', 9, 2);
			add_filter('citygov_filter_get_stream_page_title',	'citygov_trx_donations_get_stream_page_title', 9, 2);
			add_filter('citygov_filter_get_stream_page_link',	'citygov_trx_donations_get_stream_page_link', 9, 2);
			add_filter('citygov_filter_get_stream_page_id',	'citygov_trx_donations_get_stream_page_id', 9, 2);
			add_filter('citygov_filter_query_add_filters',		'citygov_trx_donations_query_add_filters', 9, 2);
			add_filter('citygov_filter_detect_inheritance_key','citygov_trx_donations_detect_inheritance_key', 9, 1);
			add_filter('citygov_filter_list_post_types',		'citygov_trx_donations_list_post_types');
			// Register shortcodes in the list
			add_action('citygov_action_shortcodes_list',		'citygov_trx_donations_reg_shortcodes');
			if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
				add_action('citygov_action_shortcodes_list_vc','citygov_trx_donations_reg_shortcodes_vc');
			if (is_admin()) {
				add_filter( 'citygov_filter_importer_options',				'citygov_trx_donations_importer_set_options' );
			}
		}
		if (is_admin()) {
			add_filter( 'citygov_filter_importer_required_plugins',	'citygov_trx_donations_importer_required_plugins', 10, 2 );
			add_filter( 'citygov_filter_required_plugins',				'citygov_trx_donations_required_plugins' );
		}
	}
}

if ( !function_exists( 'citygov_trx_donations_settings_theme_setup2' ) ) {
	add_action( 'citygov_action_before_init_theme', 'citygov_trx_donations_settings_theme_setup2', 3 );
	function citygov_trx_donations_settings_theme_setup2() {
		// Add Donations post type and taxonomy into theme inheritance list
		if (citygov_exists_trx_donations()) {
			citygov_add_theme_inheritance( array('donations' => array(
				'stream_template' => 'blog-donations',
				'single_template' => 'single-donation',
				'taxonomy' => array(THEMEREX_Donations::TAXONOMY),
				'taxonomy_tags' => array(),
				'post_type' => array(THEMEREX_Donations::POST_TYPE),
				'override' => 'page'
				) )
			);
		}
	}
}

// Check if CityGov Donations installed and activated
if ( !function_exists( 'citygov_exists_trx_donations' ) ) {
	function citygov_exists_trx_donations() {
		return class_exists('THEMEREX_Donations');
	}
}


// Return true, if current page is donations page
if ( !function_exists( 'citygov_is_trx_donations_page' ) ) {
	function citygov_is_trx_donations_page() {
		$is = false;
		if (citygov_exists_trx_donations()) {
			$is = in_array(citygov_storage_get('page_template'), array('blog-donations', 'single-donation'));
			if (!$is) {
				if (!citygov_storage_empty('pre_query'))
					$is = (citygov_storage_call_obj_method('pre_query', 'is_single') && citygov_storage_call_obj_method('pre_query', 'get', 'post_type') == THEMEREX_Donations::POST_TYPE)
							|| citygov_storage_call_obj_method('pre_query', 'is_post_type_archive', THEMEREX_Donations::POST_TYPE)
							|| citygov_storage_call_obj_method('pre_query', 'is_tax', THEMEREX_Donations::TAXONOMY);
				else
					$is = (is_single() && get_query_var('post_type') == THEMEREX_Donations::POST_TYPE)
							|| is_post_type_archive(THEMEREX_Donations::POST_TYPE)
							|| is_tax(THEMEREX_Donations::TAXONOMY);
			}
		}
		return $is;
	}
}

// Filter to detect current page inheritance key
if ( !function_exists( 'citygov_trx_donations_detect_inheritance_key' ) ) {
	//add_filter('citygov_filter_detect_inheritance_key',	'citygov_trx_donations_detect_inheritance_key', 9, 1);
	function citygov_trx_donations_detect_inheritance_key($key) {
		if (!empty($key)) return $key;
		return citygov_is_trx_donations_page() ? 'donations' : '';
	}
}

// Filter to detect current page slug
if ( !function_exists( 'citygov_trx_donations_get_blog_type' ) ) {
	//add_filter('citygov_filter_get_blog_type',	'citygov_trx_donations_get_blog_type', 9, 2);
	function citygov_trx_donations_get_blog_type($page, $query=null) {
		if (!empty($page)) return $page;
		if ($query && $query->is_tax(THEMEREX_Donations::TAXONOMY) || is_tax(THEMEREX_Donations::TAXONOMY))
			$page = 'donations_category';
		else if ($query && $query->get('post_type')==THEMEREX_Donations::POST_TYPE || get_query_var('post_type')==THEMEREX_Donations::POST_TYPE)
			$page = $query && $query->is_single() || is_single() ? 'donations_item' : 'donations';
		return $page;
	}
}

// Filter to detect current page title
if ( !function_exists( 'citygov_trx_donations_get_blog_title' ) ) {
	//add_filter('citygov_filter_get_blog_title',	'citygov_trx_donations_get_blog_title', 9, 2);
	function citygov_trx_donations_get_blog_title($title, $page) {
		if (!empty($title)) return $title;
		if ( citygov_strpos($page, 'donations')!==false ) {
			if ( $page == 'donations_category' ) {
				$term = get_term_by( 'slug', get_query_var( THEMEREX_Donations::TAXONOMY ), THEMEREX_Donations::TAXONOMY, OBJECT);
				$title = $term->name;
			} else if ( $page == 'donations_item' ) {
				$title = citygov_get_post_title();
			} else {
				$title = esc_html__('All donations', 'citygov');
			}
		}

		return $title;
	}
}

// Filter to detect stream page title
if ( !function_exists( 'citygov_trx_donations_get_stream_page_title' ) ) {
	//add_filter('citygov_filter_get_stream_page_title',	'citygov_trx_donations_get_stream_page_title', 9, 2);
	function citygov_trx_donations_get_stream_page_title($title, $page) {
		if (!empty($title)) return $title;
		if (citygov_strpos($page, 'donations')!==false) {
			if (($page_id = citygov_trx_donations_get_stream_page_id(0, $page=='donations' ? 'blog-donations' : $page)) > 0)
				$title = citygov_get_post_title($page_id);
			else
				$title = esc_html__('All donations', 'citygov');				
		}
		return $title;
	}
}

// Filter to detect stream page ID
if ( !function_exists( 'citygov_trx_donations_get_stream_page_id' ) ) {
	//add_filter('citygov_filter_get_stream_page_id',	'citygov_trx_donations_get_stream_page_id', 9, 2);
	function citygov_trx_donations_get_stream_page_id($id, $page) {
		if (!empty($id)) return $id;
		if (citygov_strpos($page, 'donations')!==false) $id = citygov_get_template_page_id('blog-donations');
		return $id;
	}
}

// Filter to detect stream page URL
if ( !function_exists( 'citygov_trx_donations_get_stream_page_link' ) ) {
	//add_filter('citygov_filter_get_stream_page_link',	'citygov_trx_donations_get_stream_page_link', 9, 2);
	function citygov_trx_donations_get_stream_page_link($url, $page) {
		if (!empty($url)) return $url;
		if (citygov_strpos($page, 'donations')!==false) {
			$id = citygov_get_template_page_id('blog-donations');
			if ($id) $url = get_permalink($id);
		}
		return $url;
	}
}

// Filter to detect current taxonomy
if ( !function_exists( 'citygov_trx_donations_get_current_taxonomy' ) ) {
	//add_filter('citygov_filter_get_current_taxonomy',	'citygov_trx_donations_get_current_taxonomy', 9, 2);
	function citygov_trx_donations_get_current_taxonomy($tax, $page) {
		if (!empty($tax)) return $tax;
		if ( citygov_strpos($page, 'donations')!==false ) {
			$tax = THEMEREX_Donations::TAXONOMY;
		}
		return $tax;
	}
}

// Return taxonomy name (slug) if current page is this taxonomy page
if ( !function_exists( 'citygov_trx_donations_is_taxonomy' ) ) {
	//add_filter('citygov_filter_is_taxonomy',	'citygov_trx_donations_is_taxonomy', 9, 2);
	function citygov_trx_donations_is_taxonomy($tax, $query=null) {
		if (!empty($tax))
			return $tax;
		else 
			return $query && $query->get(THEMEREX_Donations::TAXONOMY)!='' || is_tax(THEMEREX_Donations::TAXONOMY) ? THEMEREX_Donations::TAXONOMY : '';
	}
}

// Add custom post type and/or taxonomies arguments to the query
if ( !function_exists( 'citygov_trx_donations_query_add_filters' ) ) {
	//add_filter('citygov_filter_query_add_filters',	'citygov_trx_donations_query_add_filters', 9, 2);
	function citygov_trx_donations_query_add_filters($args, $filter) {
		if ($filter == 'donations') {
			$args['post_type'] = THEMEREX_Donations::POST_TYPE;
		}
		return $args;
	}
}

// Add custom post type to the list
if ( !function_exists( 'citygov_trx_donations_list_post_types' ) ) {
	//add_filter('citygov_filter_list_post_types',		'citygov_trx_donations_list_post_types');
	function citygov_trx_donations_list_post_types($list) {
		$list[THEMEREX_Donations::POST_TYPE] = esc_html__('Donations', 'citygov');
		return $list;
	}
}


// Register shortcode in the shortcodes list
if (!function_exists('citygov_trx_donations_reg_shortcodes')) {
	//add_filter('citygov_action_shortcodes_list',	'citygov_trx_donations_reg_shortcodes');
	function citygov_trx_donations_reg_shortcodes() {
		if (citygov_storage_isset('shortcodes')) {

			$plugin = THEMEREX_Donations::get_instance();
			$donations_groups = citygov_get_list_terms(false, THEMEREX_Donations::TAXONOMY);

			citygov_sc_map_before('trx_dropcaps', array(

				// CityGov Donations form
				"trx_donations_form" => array(
					"title" => esc_html__("Donations form", "citygov"),
					"desc" => esc_html__("Insert CityGov Donations form", "citygov"),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"title" => array(
							"title" => esc_html__("Title", "citygov"),
							"desc" => esc_html__("Title for the donations form", "citygov"),
							"value" => "",
							"type" => "text"
						),
						"subtitle" => array(
							"title" => esc_html__("Subtitle", "citygov"),
							"desc" => esc_html__("Subtitle for the donations form", "citygov"),
							"value" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => esc_html__("Description", "citygov"),
							"desc" => esc_html__("Short description for the donations form", "citygov"),
							"value" => "",
							"type" => "textarea"
						),
						"align" => array(
							"title" => esc_html__("Alignment", "citygov"),
							"desc" => esc_html__("Alignment of the donations form", "citygov"),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => citygov_get_sc_param('align')
						),
						"account" => array(
							"title" => esc_html__("PayPal account", "citygov"),
							"desc" => esc_html__("PayPal account's e-mail. If empty - used from CityGov Donations settings", "citygov"),
							"divider" => true,
							"value" => "",
							"type" => "text"
						),
						"sandbox" => array(
							"title" => esc_html__("Sandbox mode", "citygov"),
							"desc" => esc_html__("Use PayPal sandbox to test payments", "citygov"),
							"dependency" => array(
								'account' => array('not_empty')
							),
							"value" => "yes",
							"type" => "switch",
							"options" => citygov_get_sc_param('yes_no')
						),
						"amount" => array(
							"title" => esc_html__("Default amount", "citygov"),
							"desc" => esc_html__("Specify amount, initially selected in the form", "citygov"),
							"dependency" => array(
								'account' => array('not_empty')
							),
							"value" => 5,
							"min" => 1,
							"step" => 5,
							"type" => "spinner"
						),
						"currency" => array(
							"title" => esc_html__("Currency", "citygov"),
							"desc" => esc_html__("Select payment's currency", "citygov"),
							"dependency" => array(
								'account' => array('not_empty')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => citygov_array_merge(array(0 => esc_html__('- Select currency -', 'citygov')), $plugin->currency_codes)
						),
						"width" => citygov_shortcodes_width(),
						"top" => citygov_get_sc_param('top'),
						"bottom" => citygov_get_sc_param('bottom'),
						"left" => citygov_get_sc_param('left'),
						"right" => citygov_get_sc_param('right'),
						"id" => citygov_get_sc_param('id'),
						"class" => citygov_get_sc_param('class'),
						"css" => citygov_get_sc_param('css')
					)
				),
				
				
				// CityGov Donations form
				"trx_donations_list" => array(
					"title" => esc_html__("Donations list", "citygov"),
					"desc" => esc_html__("Insert CityGov Doantions list", "citygov"),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"title" => array(
							"title" => esc_html__("Title", "citygov"),
							"desc" => esc_html__("Title for the donations list", "citygov"),
							"value" => "",
							"type" => "text"
						),
						"subtitle" => array(
							"title" => esc_html__("Subtitle", "citygov"),
							"desc" => esc_html__("Subtitle for the donations list", "citygov"),
							"value" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => esc_html__("Description", "citygov"),
							"desc" => esc_html__("Short description for the donations list", "citygov"),
							"value" => "",
							"type" => "textarea"
						),
						"link" => array(
							"title" => esc_html__("Button URL", "citygov"),
							"desc" => esc_html__("Link URL for the button at the bottom of the block", "citygov"),
							"divider" => true,
							"value" => "",
							"type" => "text"
						),
						"link_caption" => array(
							"title" => esc_html__("Button caption", "citygov"),
							"desc" => esc_html__("Caption for the button at the bottom of the block", "citygov"),
							"value" => "",
							"type" => "text"
						),
						"style" => array(
							"title" => esc_html__("List style", "citygov"),
							"desc" => esc_html__("Select style to display donations", "citygov"),
							"value" => "excerpt",
							"type" => "select",
							"options" => array(
								'excerpt' => esc_html__('Excerpt', 'citygov')
							)
						),
						"readmore" => array(
							"title" => esc_html__("Read more text", "citygov"),
							"desc" => esc_html__("Text of the 'Read more' link", "citygov"),
							"value" => esc_html__('Read more', 'citygov'),
							"type" => "text"
						),
						"cat" => array(
							"title" => esc_html__("Categories", "citygov"),
							"desc" => esc_html__("Select categories (groups) to show donations. If empty - select donations from any category (group) or from IDs list", "citygov"),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => citygov_array_merge(array(0 => esc_html__('- Select category -', 'citygov')), $donations_groups)
						),
						"count" => array(
							"title" => esc_html__("Number of donations", "citygov"),
							"desc" => esc_html__("How many donations will be displayed? If used IDs - this parameter ignored.", "citygov"),
							"value" => 3,
							"min" => 1,
							"max" => 100,
							"type" => "spinner"
						),
						"columns" => array(
							"title" => esc_html__("Columns", "citygov"),
							"desc" => esc_html__("How many columns use to show donations list", "citygov"),
							"value" => 3,
							"min" => 2,
							"max" => 6,
							"step" => 1,
							"type" => "spinner"
						),
						"offset" => array(
							"title" => esc_html__("Offset before select posts", "citygov"),
							"desc" => esc_html__("Skip posts before select next part.", "citygov"),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 0,
							"min" => 0,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => esc_html__("Donadions order by", "citygov"),
							"desc" => esc_html__("Select desired sorting method", "citygov"),
							"value" => "date",
							"type" => "select",
							"options" => citygov_get_sc_param('sorting')
						),
						"order" => array(
							"title" => esc_html__("Donations order", "citygov"),
							"desc" => esc_html__("Select donations order", "citygov"),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => citygov_get_sc_param('ordering')
						),
						"ids" => array(
							"title" => esc_html__("Donations IDs list", "citygov"),
							"desc" => esc_html__("Comma separated list of donations ID. If set - parameters above are ignored!", "citygov"),
							"value" => "",
							"type" => "text"
						),
						"top" => citygov_get_sc_param('top'),
						"bottom" => citygov_get_sc_param('bottom'),
						"id" => citygov_get_sc_param('id'),
						"class" => citygov_get_sc_param('class'),
						"css" => citygov_get_sc_param('css')
					)
				)

			));
		}
	}
}


// Register shortcode in the VC shortcodes list
if (!function_exists('citygov_trx_donations_reg_shortcodes_vc')) {
	//add_filter('citygov_action_shortcodes_list_vc',	'citygov_trx_donations_reg_shortcodes_vc');
	function citygov_trx_donations_reg_shortcodes_vc() {

		$plugin = THEMEREX_Donations::get_instance();
		$donations_groups = citygov_get_list_terms(false, THEMEREX_Donations::TAXONOMY);

		// CityGov Donations form
		vc_map( array(
				"base" => "trx_donations_form",
				"name" => esc_html__("Donations form", "citygov"),
				"description" => esc_html__("Insert CityGov Donations form", "citygov"),
				"category" => esc_html__('Content', 'citygov'),
				'icon' => 'icon_trx_donations_form',
				"class" => "trx_sc_single trx_sc_donations_form",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", "citygov"),
						"description" => esc_html__("Title for the donations form", "citygov"),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle",
						"heading" => esc_html__("Subtitle", "citygov"),
						"description" => esc_html__("Subtitle for the donations form", "citygov"),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", "citygov"),
						"description" => esc_html__("Description for the donations form", "citygov"),
						"class" => "",
						"value" => "",
						"type" => "textarea"
					),
					array(
						"param_name" => "align",
						"heading" => esc_html__("Alignment", "citygov"),
						"description" => esc_html__("Alignment of the donations form", "citygov"),
						"class" => "",
						"value" => array_flip(citygov_get_sc_param('align')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "account",
						"heading" => esc_html__("PayPal account", "citygov"),
						"description" => esc_html__("PayPal account's e-mail. If empty - used from CityGov Donations settings", "citygov"),
						"admin_label" => true,
						"group" => esc_html__('PayPal', 'citygov'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "sandbox",
						"heading" => esc_html__("Sandbox mode", "citygov"),
						"description" => esc_html__("Use PayPal sandbox to test payments", "citygov"),
						"admin_label" => true,
						"group" => esc_html__('PayPal', 'citygov'),
						'dependency' => array(
							'element' => 'account',
							'not_empty' => true
						),
						"class" => "",
						"value" => array("Sandbox mode" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "amount",
						"heading" => esc_html__("Default amount", "citygov"),
						"description" => esc_html__("Specify amount, initially selected in the form", "citygov"),
						"admin_label" => true,
						"group" => esc_html__('PayPal', 'citygov'),
						"class" => "",
						"value" => "5",
						"type" => "textfield"
					),
					array(
						"param_name" => "currency",
						"heading" => esc_html__("Currency", "citygov"),
						"description" => esc_html__("Select payment's currency", "citygov"),
						"class" => "",
						"value" => array_flip(citygov_array_merge(array(0 => esc_html__('- Select currency -', 'citygov')), $plugin->currency_codes)),
						"type" => "dropdown"
					),
					citygov_get_vc_param('id'),
					citygov_get_vc_param('class'),
					citygov_get_vc_param('css'),
					citygov_vc_width(),
					citygov_get_vc_param('margin_top'),
					citygov_get_vc_param('margin_bottom'),
					citygov_get_vc_param('margin_left'),
					citygov_get_vc_param('margin_right')
				)
			) );
			
		class WPBakeryShortCode_Trx_Donations_Form extends CITYGOV_VC_ShortCodeSingle {}



		// CityGov Donations list
		vc_map( array(
				"base" => "trx_donations_list",
				"name" => esc_html__("Donations list", "citygov"),
				"description" => esc_html__("Insert CityGov Donations list", "citygov"),
				"category" => esc_html__('Content', 'citygov'),
				'icon' => 'icon_trx_donations_list',
				"class" => "trx_sc_single trx_sc_donations_list",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => esc_html__("List style", "citygov"),
						"description" => esc_html__("Select style to display donations", "citygov"),
						"class" => "",
						"value" => array(
							esc_html__('Excerpt', 'citygov') => 'excerpt'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", "citygov"),
						"description" => esc_html__("Title for the donations form", "citygov"),
						"group" => esc_html__('Captions', 'citygov'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle",
						"heading" => esc_html__("Subtitle", "citygov"),
						"description" => esc_html__("Subtitle for the donations form", "citygov"),
						"group" => esc_html__('Captions', 'citygov'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", "citygov"),
						"description" => esc_html__("Description for the donations form", "citygov"),
						"group" => esc_html__('Captions', 'citygov'),
						"class" => "",
						"value" => "",
						"type" => "textarea"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Button URL", "citygov"),
						"description" => esc_html__("Link URL for the button at the bottom of the block", "citygov"),
						"group" => esc_html__('Captions', 'citygov'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link_caption",
						"heading" => esc_html__("Button caption", "citygov"),
						"description" => esc_html__("Caption for the button at the bottom of the block", "citygov"),
						"group" => esc_html__('Captions', 'citygov'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "readmore",
						"heading" => esc_html__("Read more text", "citygov"),
						"description" => esc_html__("Text of the 'Read more' link", "citygov"),
						"group" => esc_html__('Captions', 'citygov'),
						"class" => "",
						"value" => esc_html__('Read more', 'citygov'),
						"type" => "textfield"
					),
					array(
						"param_name" => "cat",
						"heading" => esc_html__("Categories", "citygov"),
						"description" => esc_html__("Select category to show donations. If empty - select donations from any category (group) or from IDs list", "citygov"),
						"group" => esc_html__('Query', 'citygov'),
						"class" => "",
						"value" => array_flip(citygov_array_merge(array(0 => esc_html__('- Select category -', 'citygov')), $donations_groups)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "citygov"),
						"description" => esc_html__("How many columns use to show donations", "citygov"),
						"group" => esc_html__('Query', 'citygov'),
						"admin_label" => true,
						"class" => "",
						"value" => "3",
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => esc_html__("Number of posts", "citygov"),
						"description" => esc_html__("How many posts will be displayed? If used IDs - this parameter ignored.", "citygov"),
						"group" => esc_html__('Query', 'citygov'),
						"class" => "",
						"value" => "3",
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => esc_html__("Offset before select posts", "citygov"),
						"description" => esc_html__("Skip posts before select next part.", "citygov"),
						"group" => esc_html__('Query', 'citygov'),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Post sorting", "citygov"),
						"description" => esc_html__("Select desired posts sorting method", "citygov"),
						"group" => esc_html__('Query', 'citygov'),
						"class" => "",
						"value" => array_flip(citygov_get_sc_param('sorting')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Post order", "citygov"),
						"description" => esc_html__("Select desired posts order", "citygov"),
						"group" => esc_html__('Query', 'citygov'),
						"class" => "",
						"value" => array_flip(citygov_get_sc_param('ordering')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => esc_html__("client's IDs list", "citygov"),
						"description" => esc_html__("Comma separated list of donation's ID. If set - parameters above (category, count, order, etc.)  are ignored!", "citygov"),
						"group" => esc_html__('Query', 'citygov'),
						'dependency' => array(
							'element' => 'cats',
							'is_empty' => true
						),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),

					citygov_get_vc_param('id'),
					citygov_get_vc_param('class'),
					citygov_get_vc_param('css'),
					citygov_get_vc_param('margin_top'),
					citygov_get_vc_param('margin_bottom')
				)
			) );
			
		class WPBakeryShortCode_Trx_Donations_List extends CITYGOV_VC_ShortCodeSingle {}

	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'citygov_trx_donations_required_plugins' ) ) {
	//add_filter('citygov_filter_required_plugins',	'citygov_trx_donations_required_plugins');
	function citygov_trx_donations_required_plugins($list=array()) {
		if (in_array('trx_donations', citygov_storage_get('required_plugins'))) {
			$path = citygov_get_file_dir('plugins/install/trx_donations.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> 'CityGov Donations',
					'slug' 		=> 'trx_donations',
					'source'	=> $path,
					'required' 	=> false
					);
			}
		}
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check in the required plugins
if ( !function_exists( 'citygov_trx_donations_importer_required_plugins' ) ) {
	//add_filter( 'citygov_filter_importer_required_plugins',	'citygov_trx_donations_importer_required_plugins', 10, 2 );
	function citygov_trx_donations_importer_required_plugins($not_installed='', $list='') {
		if (citygov_strpos($list, 'trx_donations')!==false && !citygov_exists_trx_donations() )
			$not_installed .= '<br>CityGov Donations';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'citygov_trx_donations_importer_set_options' ) ) {
	//add_filter( 'citygov_filter_importer_options',	'citygov_trx_donations_importer_set_options' );
	function citygov_trx_donations_importer_set_options($options=array()) {
		if ( in_array('trx_donations', citygov_storage_get('required_plugins')) && citygov_exists_trx_donations() ) {
			$options['additional_options'][] = 'trx_donations_options';		// Add slugs to export options for this plugin

		}
		return $options;
	}
}
?>