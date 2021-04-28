<?php
/**
 * Theme Widget: Recent posts
 */

// Theme init
if (!function_exists('citygov_widget_recent_posts_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_widget_recent_posts_theme_setup', 1 );
	function citygov_widget_recent_posts_theme_setup() {

		// Register shortcodes in the shortcodes list
		//add_action('citygov_action_shortcodes_list',	'citygov_widget_recent_posts_reg_shortcodes');
		if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
			add_action('citygov_action_shortcodes_list_vc','citygov_widget_recent_posts_reg_shortcodes_vc');
	}
}

// Load widget
if (!function_exists('citygov_widget_recent_posts_load')) {
	add_action( 'widgets_init', 'citygov_widget_recent_posts_load' );
	function citygov_widget_recent_posts_load() {
		register_widget('citygov_widget_recent_posts');
	}
}

// Widget Class
class citygov_widget_recent_posts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_recent_posts', 'description' => esc_html__('The recent blog posts (extended)', 'citygov'));
		parent::__construct( 'citygov_widget_recent_posts', esc_html__('CityGov - Recent Posts', 'citygov'), $widget_ops );

		// Add thumb sizes into list
		citygov_add_thumb_sizes( array( 'layout' => 'widgets', 'w' => 75, 'h' => 75, 'title'=>esc_html__('Widgets', 'citygov') ) );
	}

	// Show widget
	function widget($args, $instance) {
		extract($args);

		global $post;

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');

		$post_type = isset($instance['post_type']) ? $instance['post_type'] : 'post';
		$category = isset($instance['category']) ? (int) $instance['category'] : 0;
		$taxonomy = citygov_get_taxonomy_categories_by_post_type($post_type);

		$number = isset($instance['number']) ? (int) $instance['number'] : '';

		$show_date = isset($instance['show_date']) ? (int) $instance['show_date'] : 0;
		$show_image = isset($instance['show_image']) ? (int) $instance['show_image'] : 0;
		$show_author = isset($instance['show_author']) ? (int) $instance['show_author'] : 0;
		$show_counters = isset($instance['show_counters']) ? $instance['show_counters'] : '';
		
		$output = '';

		$args = array(
			'numberposts' => $number,
			'offset' => 0,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'post_type' => $post_type,
			'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
			'ignore_sticky_posts' => true,
			'suppress_filters' => true 
    	);
		if ($category > 0) {
			if ($taxonomy=='category')
				$args['category'] = $category;
			else {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $taxonomy,
						'field' => 'id',
						'terms' => $category
					)
				);
			}
		}
		$ex = citygov_get_theme_option('exclude_cats');
		if (!empty($ex)) {
			$args['category__not_in'] = explode(',', $ex);
		}

    	$recent_posts = wp_get_recent_posts($args, OBJECT);
			
		$post_number = 0;

		if (is_array($recent_posts) && count($recent_posts) > 0) {
			foreach ($recent_posts as $post) {
				$post_number++;
				citygov_template_set_args('widgets-posts', array(
					'post_number' => $post_number,
					'show_date' => $show_date,
					'show_image' => $show_image,
					'show_author' => $show_author,
					'show_counters' => $show_counters
				));
				get_template_part(citygov_get_file_slug('templates/_parts/widgets-posts.php'));
				$output .= citygov_storage_get('widgets_posts_output');
				if ($post_number >= $number) break;
			}
			wp_reset_postdata();
		}

		if (!empty($output)) {
	
			// Before widget (defined by themes)
			echo trim($before_widget);
			
			// Display the widget title if one was input (before and after defined by themes)
			if ($title) echo trim($before_title . $title . $after_title);
	
			echo trim($output);
			
			// After widget (defined by themes)
			echo trim($after_widget);
		}
	}

	// Update the widget settings.
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = (int) $new_instance['show_date'];
		$instance['show_image'] = (int) $new_instance['show_image'];
		$instance['show_author'] = (int) $new_instance['show_author'];
		$instance['show_counters'] = strip_tags($new_instance['show_counters']);
		$instance['category'] = (int) $new_instance['category'];
		$instance['post_type'] = strip_tags( $new_instance['post_type'] );
		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'number' => '4',
			'show_date' => '1',
			'show_image' => '1',
			'show_author' => '1',
			'show_counters' => 'hide',
			'category'=>'0',
			'post_type' => 'post'
			)
		);
		$title = $instance['title'];
		$number = (int) $instance['number'];
		$show_date = (int) $instance['show_date'];
		$show_image = (int) $instance['show_image'];
		$show_author = (int) $instance['show_author'];
		$show_counters = $instance['show_counters'];
		$post_type = $instance['post_type'];
		$category = (int) $instance['category'];

		$posts_types = citygov_get_list_posts_types(false);
		$categories = citygov_get_list_terms(false, citygov_get_taxonomy_categories_by_post_type($post_type));
		$counters = array_merge(array('hide' => esc_html__('Hide', 'citygov')), citygov_get_list_blog_counters(false));
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'citygov'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('post_type')); ?>"><?php esc_html_e('Post type:', 'citygov'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('post_type')); ?>" name="<?php echo esc_attr($this->get_field_name('post_type')); ?>" class="widgets_param_fullwidth widgets_param_post_type_selector">
			<?php
				if (is_array($posts_types) && count($posts_types) > 0) {
					foreach ($posts_types as $type => $type_name) {
						echo '<option value="'.esc_attr($type).'"'.($post_type==$type ? ' selected="selected"' : '').'>'.esc_html($type_name).'</option>';
					}
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e('Category:', 'citygov'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('category')); ?>" name="<?php echo esc_attr($this->get_field_name('category')); ?>" class="widgets_param_fullwidth">
				<option value="0"><?php esc_html_e('-- Any category --', 'citygov'); ?></option> 
			<?php
				if (is_array($categories) && count($categories) > 0) {
					foreach ($categories as $cat_id => $cat_name) {
						echo '<option value="'.esc_attr($cat_id).'"'.($category==$cat_id ? ' selected="selected"' : '').'>'.esc_html($cat_name).'</option>';
					}
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number posts to show:', 'citygov'); ?></label>
			<input type="text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" value="<?php echo esc_attr($number); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>_1"><?php esc_html_e('Show post image:', 'citygov'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_image')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_image')); ?>" value="1" <?php echo (1==$show_image ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>_1"><?php esc_html_e('Show', 'citygov'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_image')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_image')); ?>" value="0" <?php echo (0==$show_image ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>_0"><?php esc_html_e('Hide', 'citygov'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>_1"><?php esc_html_e('Show post author:', 'citygov'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_author')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_author')); ?>" value="1" <?php echo (1==$show_author ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>_1"><?php esc_html_e('Show', 'citygov'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_author')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_author')); ?>" value="0" <?php echo (0==$show_author ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>_0"><?php esc_html_e('Hide', 'citygov'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>_1"><?php esc_html_e('Show post date:', 'citygov'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_date')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>" value="1" <?php echo (1==$show_date ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>_1"><?php esc_html_e('Show', 'citygov'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_date')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>" value="0" <?php echo (0==$show_date ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>_0"><?php esc_html_e('Hide', 'citygov'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_1"><?php esc_html_e('Show post counters:', 'citygov'); ?></label><br />
			<?php
				if (is_array($counters) && count($counters) > 0) {
					foreach ($counters as $id => $title) {
						?>
						<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($this->get_field_name('show_counters')); ?>" value="<?php echo esc_attr($id); ?>" <?php if ($show_counters==$id) echo ' checked="checked"'; ?> />
						<label for="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></label>
						<?php
					}
				}
			?>
		</p>

	<?php
	}
}



// trx_widget_recent_posts
//-------------------------------------------------------------
/*
[trx_widget_recent_posts id="unique_id" title="Widget title" number="4" show_date="0|1" show_image="0|1" show_author="0|1" show_counters="0|1"]
*/
if ( !function_exists( 'citygov_sc_widget_recent_posts' ) ) {
	function citygov_sc_widget_recent_posts($atts, $content=null){	
		$atts = citygov_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"number" => 4,
			"show_date" => 1,
			"show_image" => 1,
			"show_author" => 1,
			"show_counters" => '',
			'category' 		=> '',
			'cat' 			=> 0,
			'post_type'		=> 'post',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts));
		if ($atts['post_type']=='') $atts['post_type'] = 'post';
		if ($atts['cat']!='' && $atts['category']=='') $atts['category'] = $atts['cat'];
		if ($atts['show_date']=='') $atts['show_date'] = 0;
		if ($atts['show_image']=='') $atts['show_image'] = 0;
		if ($atts['show_author']=='') $atts['show_author'] = 0;
		if ($atts['show_counters']=='') $atts['show_counters'] = 'hide';
		extract($atts);
		$type = 'citygov_widget_recent_posts';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_recent_posts' 
								. (citygov_exists_visual_composer() ? ' vc_widget_recent_posts wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
						. '">';
			ob_start();
			the_widget( $type, $atts, citygov_prepare_widgets_args(citygov_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_recent_posts', 'widget_recent_posts') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('citygov_shortcode_output', $output, 'trx_widget_recent_posts', $atts, $content);
	}
	citygov_require_shortcode("trx_widget_recent_posts", "citygov_sc_widget_recent_posts");
}


// Add [trx_widget_recent_posts] in the VC shortcodes list
if (!function_exists('citygov_widget_recent_posts_reg_shortcodes_vc')) {
	function citygov_widget_recent_posts_reg_shortcodes_vc() {
		
		$posts_types = citygov_get_list_posts_types(false);
		$categories = citygov_get_list_terms(false, citygov_get_taxonomy_categories_by_post_type('post'));
		$counters = array_merge(array('hide' => esc_html__('Hide', 'citygov')), citygov_get_list_blog_counters(false));

		vc_map( array(
				"base" => "trx_widget_recent_posts",
				"name" => esc_html__("Widget Recent Posts", "citygov"),
				"description" => wp_kses_data( __("Insert recent posts list with thumbs and post's meta", "citygov") ),
				"category" => esc_html__('Content', 'citygov'),
				"icon" => 'icon_trx_widget_recent_posts',
				"class" => "trx_widget_recent_posts",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => esc_html__("Widget title", "citygov"),
						"description" => wp_kses_data( __("Title of the widget", "citygov") ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "number",
						"heading" => esc_html__("Number posts to show", "citygov"),
						"description" => wp_kses_data( __("How many posts display in widget?", "citygov") ),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "post_type",
						"heading" => esc_html__("Post type", "citygov"),
						"description" => wp_kses_data( __("Select post type to show", "citygov") ),
						"class" => "",
						"std" => "post",
						"value" => array_flip($posts_types),
						"type" => "dropdown"
					),
					array(
						"param_name" => "cat",
						"heading" => esc_html__("Parent category", "citygov"),
						"description" => wp_kses_data( __("Select parent category. If empty - show posts from any category", "citygov") ),
						"class" => "",
						"value" => array_flip(citygov_array_merge(array(0 => esc_html__('- Select category -', 'citygov')), $categories)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "show_image",
						"heading" => esc_html__("Show post's image", "citygov"),
						"description" => wp_kses_data( __("Do you want display post's featured image?", "citygov") ),
						"group" => esc_html__('Details', 'citygov'),
						"class" => "",
						"std" => 1,
						"value" => array("Show image" => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "show_author",
						"heading" => esc_html__("Show post's author", "citygov"),
						"description" => wp_kses_data( __("Do you want display post's author?", "citygov") ),
						"group" => esc_html__('Details', 'citygov'),
						"class" => "",
						"std" => 1,
						"value" => array("Show author" => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "show_date",
						"heading" => esc_html__("Show post's date", "citygov"),
						"description" => wp_kses_data( __("Do you want display post's publish date?", "citygov") ),
						"group" => esc_html__('Details', 'citygov'),
						"class" => "",
						"std" => 1,
						"value" => array("Show date" => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "show_counters",
						"heading" => esc_html__("Show post's counters", "citygov"),
						"description" => wp_kses_data( __("Do you want display post's counters?", "citygov") ),
						"admin_label" => true,
						"group" => esc_html__('Details', 'citygov'),
						"class" => "",
						"value" => array_flip($counters),
						"type" => "dropdown"
					),
					citygov_get_vc_param('id'),
					citygov_get_vc_param('class'),
					citygov_get_vc_param('css')
				)
			) );
			
		class WPBakeryShortCode_Trx_Widget_Recent_Posts extends WPBakeryShortCode {}

	}
}
?>