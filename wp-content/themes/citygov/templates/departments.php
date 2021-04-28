<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'citygov_template_departments_theme_setup' ) ) {
	add_action( 'citygov_action_before_init_theme', 'citygov_template_departments_theme_setup', 1 );
	function citygov_template_departments_theme_setup() {
        citygov_add_template(array(
            'layout' => 'departments_1',
            'template' => 'departments',
            'mode'   => 'blog',
            'need_isotope' => true,
            'title'  => esc_html__('Departments tile /1 column/', 'citygov'),
            'thumb_title'  => esc_html__('Medium images', 'citygov'),
            'w'		 => 740,
            'h' => 460
        ));
		citygov_add_template(array(
			'layout' => 'departments_2',
			'template' => 'departments',
			'mode'   => 'blog',
			'need_isotope' => true,
			'title'  => esc_html__('Departments tile /2 columns/', 'citygov'),
			'thumb_title'  => esc_html__('Medium images', 'citygov'),
			'w'		 => 740,
			'h' => 460
		));
		citygov_add_template(array(
			'layout' => 'departments_3',
			'template' => 'departments',
			'mode'   => 'blog',
			'need_isotope' => true,
			'title'  => esc_html__('Departments tile /3 columns/', 'citygov'),
			'thumb_title'  => esc_html__('Medium images', 'citygov'),
			'w'		 => 740,
			'h' => 460
		));
		citygov_add_template(array(
			'layout' => 'departments_4',
			'template' => 'departments',
			'mode'   => 'blog',
			'need_isotope' => true,
			'title'  => esc_html__('Departments tile /4 columns/', 'citygov'),
			'thumb_title'  => esc_html__('Medium images', 'citygov'),
			'w'		 => 740,
			'h' => 460
		));
        citygov_add_template(array(
            'layout' => 'opportuniti_1',
            'template' => 'departments',
            'mode'   => 'blog',
            'need_isotope' => true,
            'title'  => esc_html__('Opportunities tile /1 columns/', 'citygov'),
            'thumb_title'  => esc_html__('Square images (no-crop)', 'citygov'),
            'w'		 => 540,
            'h' => 504
        ));
        citygov_add_template(array(
            'layout' => 'opportuniti_3',
            'template' => 'departments',
            'mode'   => 'blog',
            'need_isotope' => true,
            'title'  => esc_html__('Opportunities tile /3 columns/', 'citygov'),
            'thumb_title'  => esc_html__('Square images (crop)', 'citygov'),
            'w'		 => 740,
            'h' => 584
        ));
		// Add template specific scripts
		add_action('citygov_action_blog_scripts', 'citygov_template_departments_add_scripts');
	}
}

// Add template specific scripts
if (!function_exists('citygov_template_departments_add_scripts')) {
	function citygov_template_departments_add_scripts($style) {
		if (in_array(citygov_substr($style, 0, 12), array('departments_', 'opportuniti_'))) {
			citygov_enqueue_script( 'isotope', citygov_get_file_url('js/jquery.isotope.min.js'), array(), null, true );
		}
	}
}

// Template output
if ( !function_exists( 'citygov_template_departments_output' ) ) {
	function citygov_template_departments_output($post_options, $post_data) {
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
					if ($show_title) {
						if (!isset($post_options['links']) || $post_options['links']) {
							?>
							<h4 class="post_title"><a href="<?php echo esc_url($post_data['post_link']); ?>"><?php echo trim($post_data['post_title']); ?></a></h4>
							<?php
						} else {
							?>
							<h4 class="post_title"><?php echo trim($post_data['post_title']); ?></h4>
							<?php
						}
					}
					
					?>

					<div class="post_descr">
						<?php
						if ($post_data['post_protected']) {
							echo trim($post_data['post_excerpt']); 
						} else {
							if ($post_data['post_excerpt']) {
								echo in_array($post_data['post_format'], array('quote', 'link', 'chat', 'aside', 'status')) ? $post_data['post_excerpt'] : '<p>'.trim(citygov_strshort($post_data['post_excerpt'], isset($post_options['descr']) ? $post_options['descr'] : citygov_get_custom_option('post_excerpt_maxlength_departments'))).'</p>';
							}
							if (empty($post_options['readmore'])) $post_options['readmore'] = esc_html__('Read more', 'citygov');
							if (!citygov_param_is_off($post_options['readmore']) && !in_array($post_data['post_format'], array('quote', 'link', 'chat', 'aside', 'status'))) {
								?><a href="<?php echo esc_url($post_data['post_link']); ?>" class="post_readmore"><span class="post_readmore_label"><?php echo trim($post_options['readmore']); ?></span></a><?php
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