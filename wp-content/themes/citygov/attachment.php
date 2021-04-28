<?php
/**
Template Name: Attachment page
 */
get_header(); 

while ( have_posts() ) { the_post();

	// Move citygov_set_post_views to the javascript - counter will work under cache system
	if (citygov_get_custom_option('use_ajax_views_counter')=='no') {
		citygov_set_post_views(get_the_ID());
	}

	citygov_show_post_layout(
		array(
			'layout' => 'attachment',
			'sidebar' => !citygov_param_is_off(citygov_get_custom_option('show_sidebar_main'))
		)
	);

}

get_footer();
?>