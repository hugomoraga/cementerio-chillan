<?php
/**
 * CityGov Framework: Theme options custom fields
 *
 * @package	citygov
 * @since	citygov 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'citygov_options_custom_theme_setup' ) ) {
	add_action( 'citygov_action_before_init_theme', 'citygov_options_custom_theme_setup' );
	function citygov_options_custom_theme_setup() {

		if ( is_admin() ) {
			add_action("admin_enqueue_scripts",	'citygov_options_custom_load_scripts');
		}
		
	}
}

// Load required styles and scripts for custom options fields
if ( !function_exists( 'citygov_options_custom_load_scripts' ) ) {
	//add_action("admin_enqueue_scripts", 'citygov_options_custom_load_scripts');
	function citygov_options_custom_load_scripts() {
		citygov_enqueue_script( 'citygov-options-custom-script',	citygov_get_file_url('core/core.options/js/core.options-custom.js'), array(), null, true );	
	}
}


// Show theme specific fields in Post (and Page) options
if ( !function_exists( 'citygov_show_custom_field' ) ) {
	function citygov_show_custom_field($id, $field, $value) {
		$output = '';
		switch ($field['type']) {
			case 'reviews':
				$output .= '<div class="reviews_block">' . trim(citygov_reviews_get_markup($field, $value, true)) . '</div>';
				break;
	
			case 'mediamanager':
				wp_enqueue_media( );
				$output .= '<a id="'.esc_attr($id).'" class="button mediamanager citygov_media_selector"
					data-param="' . esc_attr($id) . '"
					data-choose="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Choose Images', 'citygov') : esc_html__( 'Choose Image', 'citygov')).'"
					data-update="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Add to Gallery', 'citygov') : esc_html__( 'Choose Image', 'citygov')).'"
					data-multiple="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? 'true' : 'false').'"
					data-linked-field="'.esc_attr($field['media_field_id']).'"
					>' . (isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Choose Images', 'citygov') : esc_html__( 'Choose Image', 'citygov')) . '</a>';
				break;
		}
		return apply_filters('citygov_filter_show_custom_field', $output, $id, $field, $value);
	}
}
?>