<?php
/* LearnDash LMS support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('citygov_learndash_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_learndash_theme_setup', 1 );
	function citygov_learndash_theme_setup() {

		// Register shortcode in the shortcodes list
		if (citygov_exists_learndash()) {
			// Detect current page type, taxonomy and title (for custom post_types use priority < 10 to fire it handles early, than for standard post types)
			add_filter('citygov_filter_get_blog_type',			'citygov_learndash_get_blog_type', 9, 2);
			add_filter('citygov_filter_get_blog_title',		'citygov_learndash_get_blog_title', 9, 2);
			add_filter('citygov_filter_get_current_taxonomy',	'citygov_learndash_get_current_taxonomy', 9, 2);
			add_filter('citygov_filter_is_taxonomy',			'citygov_learndash_is_taxonomy', 9, 2);
			add_filter('citygov_filter_get_stream_page_title',	'citygov_learndash_get_stream_page_title', 9, 2);
			add_filter('citygov_filter_get_stream_page_link',	'citygov_learndash_get_stream_page_link', 9, 2);
			add_filter('citygov_filter_get_stream_page_id',	'citygov_learndash_get_stream_page_id', 9, 2);
			add_filter('citygov_filter_query_add_filters',		'citygov_learndash_query_add_filters', 9, 2);
			add_filter('citygov_filter_detect_inheritance_key','citygov_learndash_detect_inheritance_key', 9, 1);

			add_action('citygov_action_add_styles',			'citygov_learndash_frontend_scripts');

			// One-click importer support
			add_filter( 'citygov_filter_importer_options',		'citygov_learndash_importer_set_options' );

			add_filter('citygov_filter_list_post_types', 		'citygov_learndash_list_post_types', 10, 1);

			// Register shortcodes in the list
			//add_action('citygov_action_shortcodes_list',		'citygov_learndash_reg_shortcodes');
			//if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			//	add_action('citygov_action_shortcodes_list_vc','citygov_learndash_reg_shortcodes_vc');

			// Get list post_types and taxonomies
			citygov_storage_set('learndash_post_types', array('sfwd-courses', 'sfwd-lessons', 'sfwd-quiz', 'sfwd-topic', 'sfwd-certificates', 'sfwd-transactions'));
			citygov_storage_set('learndash_taxonomies', array('category'));
		}
		if (is_admin()) {
			add_filter( 'citygov_filter_importer_required_plugins',	'citygov_learndash_importer_required_plugins', 10, 2 );
			add_filter( 'citygov_filter_required_plugins',				'citygov_learndash_required_plugins' );
		}
	}
}

// Attention! Add action on 'init' instead 'before_init_theme' because LearnDash add post_types and taxonomies on this action
if ( !function_exists( 'citygov_learndash_settings_theme_setup2' ) ) {
	add_action( 'citygov_action_before_init_theme', 'citygov_learndash_settings_theme_setup2', 3 );
	//add_action( 'init', 'citygov_learndash_settings_theme_setup2', 20 );
	function citygov_learndash_settings_theme_setup2() {
		// Add LearnDash post type and taxonomy into theme inheritance list
		if (citygov_exists_learndash()) {
			// Get list post_types and taxonomies
			if (!empty(SFWD_CPT_Instance::$instances) && count(SFWD_CPT_Instance::$instances) > 0) {
				$post_types = array();
				foreach (SFWD_CPT_Instance::$instances as $pt=>$data)
					$post_types[] = $pt;
				if (count($post_types) > 0)
					citygov_storage_set('learndash_post_types', $post_types);
			}
			// Add in the inheritance list
			citygov_add_theme_inheritance( array('learndash' => array(
				'stream_template' => 'blog-learndash',
				'single_template' => 'single-learndash',
				'taxonomy' => citygov_storage_get('learndash_taxonomies'),
				'taxonomy_tags' => array('post_tag'),
				'post_type' => citygov_storage_get('learndash_post_types'),
				'override' => 'page'
				) )
			);
		}
	}
}



// Check if CityGov Donations installed and activated
if ( !function_exists( 'citygov_exists_learndash' ) ) {
	function citygov_exists_learndash() {
		return class_exists('SFWD_LMS');
	}
}


// Return true, if current page is donations page
if ( !function_exists( 'citygov_is_learndash_page' ) ) {
	function citygov_is_learndash_page() {
		$is = false;
		if (citygov_exists_learndash()) {
			$is = in_array(citygov_storage_get('page_template'), array('blog-learndash', 'single-learndash'));
			if (!$is) {
				$is = !citygov_storage_empty('pre_query')
							? citygov_storage_call_obj_method('pre_query', 'is_single') && in_array(citygov_storage_call_obj_method('pre_query', 'get', 'post_type'), citygov_storage_get('learndash_post_types'))
							: is_single() && in_array(get_query_var('post_type'), citygov_storage_get('learndash_post_types'));
			}
			if (!$is) {
				$post_types = citygov_storage_get('learndash_post_types');
				if (count($post_types) > 0) {
					foreach ($post_types as $pt) {
						if (!citygov_storage_empty('pre_query') ? citygov_storage_call_obj_method('pre_query', 'is_post_type_archive', $pt) : is_post_type_archive($pt)) {
							$is = true;
							break;
						}
					}
				}
			}
			if (!$is) {
				$taxes = citygov_storage_get('learndash_taxonomies');
				if (count($taxes) > 0) {
					foreach ($taxes as $pt) {
						if (!citygov_storage_empty('pre_query') ? citygov_storage_call_obj_method('pre_query', 'is_tax', $pt) : is_tax($pt)) {
							$is = true;
							break;
						}
					}
				}
			}
		}
		return $is;
	}
}

// Filter to detect current page inheritance key
if ( !function_exists( 'citygov_learndash_detect_inheritance_key' ) ) {
	//add_filter('citygov_filter_detect_inheritance_key',	'citygov_learndash_detect_inheritance_key', 9, 1);
	function citygov_learndash_detect_inheritance_key($key) {
		if (!empty($key)) return $key;
		return citygov_is_learndash_page() ? 'learndash' : '';
	}
}

// Filter to detect current page slug
if ( !function_exists( 'citygov_learndash_get_blog_type' ) ) {
	//add_filter('citygov_filter_get_blog_type',	'citygov_learndash_get_blog_type', 9, 2);
	function citygov_learndash_get_blog_type($page, $query=null) {
		if (!empty($page)) return $page;
		$taxes = citygov_storage_get('learndash_taxonomies');
		if (count($taxes) > 0) {
			foreach ($taxes as $pt) {
				if ($query && $query->is_tax($pt) || is_tax($pt)) {
					$page = 'learndash_'.$pt;
					break;
				}
			}
		}
		if (empty($page)) {
			$pt = $query ? $query->get('post_type') : get_query_var('post_type');
			if (in_array($pt, citygov_storage_get('learndash_post_types'))) {
				$page = $query && $query->is_single() || is_single() ? 'learndash_item' : 'learndash';
			}
		}
		return $page;
	}
}

// Filter to detect current page title
if ( !function_exists( 'citygov_learndash_get_blog_title' ) ) {
	//add_filter('citygov_filter_get_blog_title',	'citygov_learndash_get_blog_title', 9, 2);
	function citygov_learndash_get_blog_title($title, $page) {
		if (!empty($title)) return $title;
		if ( citygov_strpos($page, 'learndash')!==false ) {
			if ( $page == 'learndash_item' ) {
				$title = citygov_get_post_title();
			} else if ( citygov_strpos($page, 'learndash_')!==false ) {
				$parts = explode('_', $page);
				$term = get_term_by( 'slug', get_query_var( $parts[1] ), $parts[1], OBJECT);
				$title = $term->name;
			} else {
				$title = esc_html__('All courses', 'citygov');
			}
		}

		return $title;
	}
}

// Filter to detect stream page title
if ( !function_exists( 'citygov_learndash_get_stream_page_title' ) ) {
	//add_filter('citygov_filter_get_stream_page_title',	'citygov_learndash_get_stream_page_title', 9, 2);
	function citygov_learndash_get_stream_page_title($title, $page) {
		if (!empty($title)) return $title;
		if (citygov_strpos($page, 'learndash')!==false) {
			if (($page_id = citygov_learndash_get_stream_page_id(0, $page=='learndash' ? 'blog-learndash' : $page)) > 0)
				$title = citygov_get_post_title($page_id);
			else
				$title = esc_html__('All courses', 'citygov');				
		}
		return $title;
	}
}

// Filter to detect stream page ID
if ( !function_exists( 'citygov_learndash_get_stream_page_id' ) ) {
	//add_filter('citygov_filter_get_stream_page_id',	'citygov_learndash_get_stream_page_id', 9, 2);
	function citygov_learndash_get_stream_page_id($id, $page) {
		if (!empty($id)) return $id;
		if (citygov_strpos($page, 'learndash')!==false) $id = citygov_get_template_page_id('blog-learndash');
		return $id;
	}
}

// Filter to detect stream page URL
if ( !function_exists( 'citygov_learndash_get_stream_page_link' ) ) {
	//add_filter('citygov_filter_get_stream_page_link',	'citygov_learndash_get_stream_page_link', 9, 2);
	function citygov_learndash_get_stream_page_link($url, $page) {
		if (!empty($url)) return $url;
		if (citygov_strpos($page, 'learndash')!==false) {
			$id = citygov_get_template_page_id('blog-learndash');
			if ($id) $url = get_permalink($id);
		}
		return $url;
	}
}

// Filter to detect current taxonomy
if ( !function_exists( 'citygov_learndash_get_current_taxonomy' ) ) {
	//add_filter('citygov_filter_get_current_taxonomy',	'citygov_learndash_get_current_taxonomy', 9, 2);
	function citygov_learndash_get_current_taxonomy($tax, $page) {
		if (!empty($tax)) return $tax;
		if ( citygov_strpos($page, 'learndash')!==false ) {
			$taxes = citygov_storage_get('learndash_taxonomies');
			if (count($taxes) > 0) {
				$tax = $taxes[0];
			}
		}
		return $tax;
	}
}

// Return taxonomy name (slug) if current page is this taxonomy page
if ( !function_exists( 'citygov_learndash_is_taxonomy' ) ) {
	//add_filter('citygov_filter_is_taxonomy',	'citygov_learndash_is_taxonomy', 9, 2);
	function citygov_learndash_is_taxonomy($tax, $query=null) {
		if (!empty($tax))
			return $tax;
		else {
			$taxes = citygov_storage_get('learndash_taxonomies');
			if (count($taxes) > 0) {
				foreach ($taxes as $pt) {
					if ($query && ($query->get($pt)!='' || $query->is_tax($pt)) || is_tax($pt)) {
						$tax = $pt;
						break;
					}
				}
			}
			return $tax;
		}
	}
}

// Add custom post type and/or taxonomies arguments to the query
if ( !function_exists( 'citygov_learndash_query_add_filters' ) ) {
	//add_filter('citygov_filter_query_add_filters',	'citygov_learndash_query_add_filters', 9, 2);
	function citygov_learndash_query_add_filters($args, $filter) {
		if ($filter == 'learndash') {
			$args['post_type'] = 'sfwd-courses';	//citygov_storage_get('learndash_post_types');
		}
		return $args;
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'citygov_learndash_required_plugins' ) ) {
	//add_filter('citygov_filter_required_plugins',	'citygov_learndash_required_plugins');
	function citygov_learndash_required_plugins($list=array()) {
		if (in_array('learndash', citygov_storage_get('required_plugins'))) {
			$path = citygov_get_file_dir('plugins/install/sfwd-lms.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> 'LearnDash LMS',
					'slug' 		=> 'sfwd-lms',
					'source'	=> $path,
					'required' 	=> false
					);
			}
		}
		return $list;
	}
}

// Add custom post type into list
if ( !function_exists( 'citygov_learndash_list_post_types' ) ) {
	//add_filter('citygov_filter_list_post_types', 	'citygov_learndash_list_post_types', 10, 1);
	function citygov_learndash_list_post_types($list) {
		$list['sfwd-courses'] = esc_html__('Courses (LearnDash)', 'citygov');
		return $list;
	}
}

// Enqueue custom styles
if ( !function_exists( 'citygov_learndash_frontend_scripts' ) ) {
	//add_action( 'citygov_action_add_styles', 'citygov_learndash_frontend_scripts' );
	function citygov_learndash_frontend_scripts() {
		if (file_exists(citygov_get_file_dir('css/plugin.learndash.css')))
			citygov_enqueue_style( 'citygov-plugin.learndash-style',  citygov_get_file_url('css/plugin.learndash.css'), array(), null );
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check in the required plugins
if ( !function_exists( 'citygov_learndash_importer_required_plugins' ) ) {
	//add_filter( 'citygov_filter_importer_required_plugins',	'citygov_learndash_importer_required_plugins', 10, 2 );
	function citygov_learndash_importer_required_plugins($not_installed='', $list='') {
		//if (in_array('learndash', citygov_storage_get('required_plugins')) && !citygov_exists_learndash() )
		if (citygov_strpos($list, 'learndash')!==false && !citygov_exists_learndash() )
			$not_installed .= '<br>LearnDash LMS';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'citygov_learndash_importer_set_options' ) ) {
	function citygov_learndash_importer_set_options($options=array()) {
		if ( in_array('learndash', citygov_storage_get('required_plugins')) && citygov_exists_learndash() ) {
			$options['additional_options'][] = 'sfwd_cpt_options';		// Add slugs to export options for this plugin
		}
		return $options;
	}
}
?>