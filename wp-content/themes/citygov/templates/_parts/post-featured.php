<?php
// Get template args
extract(citygov_template_get_args('post-featured'));

if ($post_data['post_video']) {
	echo trim(citygov_get_video_frame($post_data['post_video'], $post_data['post_video_image'] ? $post_data['post_video_image'] : $post_data['post_thumb']));
} else if ($post_data['post_audio']) {
	if (citygov_get_custom_option('substitute_audio')=='no' || !citygov_in_shortcode_blogger(true))
		echo trim(citygov_get_audio_frame($post_data['post_audio'], $post_data['post_audio_image'] ? $post_data['post_audio_image'] : $post_data['post_thumb_url']));
	else
		echo trim($post_data['post_audio']);
} else if ($post_data['post_thumb'] && ($post_data['post_format']!='gallery' || !$post_data['post_gallery'] || citygov_get_custom_option('gallery_instead_image')=='no')) {
	?>
	<div class="post_thumb" data-image="<?php echo esc_url($post_data['post_attachment']); ?>" data-title="<?php echo esc_attr($post_data['post_title']); ?>">
	<?php
	if ($post_data['post_format']=='link' && $post_data['post_url']!='')
		echo '<a class="hover_icon hover_icon_link" href="'.esc_url($post_data['post_url']).'"'.($post_data['post_url_target'] ? ' target="'.esc_attr($post_data['post_url_target']).'"' : '').'>'.($post_data['post_thumb']).'</a>';
	else if ($post_data['post_link']!='')
		echo '<a class="hover_icon hover_icon_link" href="'.esc_url($post_data['post_link']).'">'.($post_data['post_thumb']).'</a>';
	else
		echo trim($post_data['post_thumb']); 
	?>
	</div>
	<?php
} else if ($post_data['post_gallery']) {
	citygov_enqueue_slider();
	echo trim($post_data['post_gallery']);
}
?>