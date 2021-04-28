<?php
/**
Template Name: Single post
 */
get_header(); 

$single_style = citygov_storage_get('single_style');
if (empty($single_style)) $single_style = citygov_get_custom_option('single_style');

while ( have_posts() ) { the_post();
	citygov_show_post_layout(
		array(
			'layout' => $single_style,
			'sidebar' => !citygov_param_is_off(citygov_get_custom_option('show_sidebar_main')),
			'content' => citygov_get_template_property($single_style, 'need_content'),
			'terms_list' => citygov_get_template_property($single_style, 'need_terms')
		)
	);
}

get_footer();
?>