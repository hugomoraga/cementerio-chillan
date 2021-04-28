<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'citygov_template_masonews_theme_setup' ) ) {
	add_action( 'citygov_action_before_init_theme', 'citygov_template_masonews_theme_setup', 1 );
	function citygov_template_masonews_theme_setup() {
		citygov_add_template(array(
			'layout' => 'masonews_2',
			'template' => 'masonews',
			'mode'   => 'blog',
			'need_isotope' => true,
			'title'  => esc_html__('Masonews tile (different height) /2 columns/', 'citygov'),
			'thumb_title'  => esc_html__('Medium image', 'citygov'),
			'w'		 => 370,
			'h' => 190
		));
		citygov_add_template(array(
			'layout' => 'masonews_3',
			'template' => 'masonews',
			'mode'   => 'blog',
			'need_isotope' => true,
			'title'  => esc_html__('Masonews tile /3 columns/', 'citygov'),
			'thumb_title'  => esc_html__('Medium image', 'citygov'),
			'w'		 => 370,
			'h' => 190
		));
		citygov_add_template(array(
			'layout' => 'masonews_4',
			'template' => 'masonews',
			'mode'   => 'blog',
			'need_isotope' => true,
			'title'  => esc_html__('Masonews tile /4 columns/', 'citygov'),
			'thumb_title'  => esc_html__('Medium image', 'citygov'),
			'w'		 => 370,
			'h' => 190
		));
		// Add template specific scripts
		add_action('citygov_action_blog_scripts', 'citygov_template_masonews_add_scripts');
	}
}

// Add template specific scripts
if (!function_exists('citygov_template_masonews_add_scripts')) {
	function citygov_template_masonews_add_scripts($style) {
		if (in_array(citygov_substr($style, 0, 9), array('masonews_'))) {
			citygov_enqueue_script( 'isotope', citygov_get_file_url('js/jquery.isotope.min.js'), array(), null, true );
		}
	}
}

// Template output
if ( !function_exists( 'citygov_template_masonews_output' ) ) {
	function citygov_template_masonews_output($post_options, $post_data) {
		$show_title = !in_array($post_data['post_format'], array('aside', 'chat', 'status', 'link', 'quote'));
		$parts = explode('_', $post_options['layout']);
		$style = $parts[0];
		$columns = max(1, min(12, empty($post_options['columns_count']) 
									? (empty($parts[1]) ? 1 : (int) $parts[1])
									: $post_options['columns_count']
									));
		$tag = citygov_in_shortcode_blogger(true) ? 'div' : 'article';
		?>
		<div class="isotope_item isotope_item_<?php echo esc_attr($style); ?> isotope_item_<?php echo esc_attr($post_options['layout']); ?> isotope_column_<?php echo esc_attr($columns); ?>
					<?php
					if ($post_options['filters'] != '') {
						if ($post_options['filters']=='categories' && !empty($post_data['post_terms'][$post_data['post_taxonomy']]->terms_ids))
							echo ' flt_' . join(' flt_', $post_data['post_terms'][$post_data['post_taxonomy']]->terms_ids);
						else if ($post_options['filters']=='tags' && !empty($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms_ids))
							echo ' flt_' . join(' flt_', $post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms_ids);
					}
					?>">
			<<?php echo trim($tag); ?> class="post_item post_item_<?php echo esc_attr($style); ?> post_item_<?php echo esc_attr($post_options['layout']); ?>
				 <?php echo ' post_format_'.esc_attr($post_data['post_format']) 
					. ($post_options['number']%2==0 ? ' even' : ' odd') 
					. ($post_options['number']==0 ? ' first' : '') 
					. ($post_options['number']==$post_options['posts_on_page'] ? ' last' : '');
				?>">
				
				<?php if ($post_data['post_video'] || $post_data['post_audio'] || $post_data['post_thumb'] ||  $post_data['post_gallery']) { ?>
					<div class="post_featured">
						<?php
						citygov_template_set_args('post-featured', array(
							'post_options' => $post_options,
							'post_data' => $post_data
						));
						get_template_part(citygov_get_file_slug('templates/_parts/post-featured.php'));
						?>
					</div>
				<?php } ?>

				<div class="post_content isotope_item_content">
					
					<?php
                    if (!$post_data['post_protected'] && $post_options['info']) {
                        $post_options['info_parts'] = array('counters'=>false, 'terms'=>false, 'author'=>false);
                        citygov_template_set_args('post-info', array(
                            'post_options' => $post_options,
                            'post_data' => $post_data
                        ));
                        get_template_part(citygov_get_file_slug('templates/_parts/post-info.php'));
                    }

					if ($show_title) {
						if (!isset($post_options['links']) || $post_options['links']) {
							?>
							<h5 class="post_title"><a href="<?php echo esc_url($post_data['post_link']); ?>"><?php echo trim($post_data['post_title']); ?></a></h5>
							<?php
						} else {
							?>
							<h5 class="post_title"><?php echo trim($post_data['post_title']); ?></h5>
							<?php
						}
					}
					

					?>

					<div class="post_descr">
						<?php
						if ($post_data['post_protected']) {
						} else {
                        if (!$post_data['post_protected'] && $post_options['info']) {
                            $post_options['info_parts'] = array('counters'=>true, 'terms'=>false, 'author'=>true, 'date'=>false);
                            citygov_template_set_args('post-info', array(
                                'post_options' => $post_options,
                                'post_data' => $post_data
                            ));
                            get_template_part(citygov_get_file_slug('templates/_parts/post-info.php'));
                        }
						}
						?>
					</div>

				</div>				<!-- /.post_content -->
			</<?php echo trim($tag); ?>>	<!-- /.post_item -->
		</div>						<!-- /.isotope_item -->
		<?php
	}
}
?>