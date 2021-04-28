<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'citygov_template_experts_theme_setup' ) ) {
	add_action( 'citygov_action_before_init_theme', 'citygov_template_experts_theme_setup', 1 );
	function citygov_template_experts_theme_setup() {
		citygov_add_template(array(
			'layout' => 'experts',
			'mode'   => 'blog',
			'title'  => esc_html__('Experts', 'citygov'),
			'thumb_title'  => esc_html__('Largest images (crop)', 'citygov'),
			'w'		 => 740,
			'h'		 => 528
		));
	}
}

// Template output
if ( !function_exists( 'citygov_template_experts_output' ) ) {
	function citygov_template_experts_output($post_options, $post_data) {
		$show_title = true;
		$tag = citygov_in_shortcode_blogger(true) ? 'div' : 'article';
		?>
		<<?php echo trim($tag); ?> <?php post_class('post_item post_item_experts post_featured_' . esc_attr($post_options['post_class']) . ' post_format_'.esc_attr($post_data['post_format']) . ($post_options['number']%2==0 ? ' even' : ' odd') . ($post_options['number']==0 ? ' first' : '') . ($post_options['number']==$post_options['posts_on_page']? ' last' : '') . ($post_options['add_view_more'] ? ' viewmore' : '')); ?>>
			<?php
			if ($post_data['post_flags']['sticky']) {
				?><span class="sticky_label"></span><?php
			}

			if (!$post_data['post_protected'] && (!empty($post_options['dedicated']) || $post_data['post_thumb'] || $post_data['post_gallery'] || $post_data['post_video'] || $post_data['post_audio'])) {
				?>
				<div class="post_featured">
				<?php
				if (!empty($post_options['dedicated'])) {
					echo trim($post_options['dedicated']);
				} else if ($post_data['post_thumb'] || $post_data['post_gallery'] || $post_data['post_video'] || $post_data['post_audio']) {
					citygov_template_set_args('post-featured', array(
						'post_options' => $post_options,
						'post_data' => $post_data
					));
					get_template_part(citygov_get_file_slug('templates/_parts/post-featured.php'));
				}
				?>
				</div>
			<?php
			}
			?>
	
			<div class="post_content clearfix">
                <?php

                if (!$post_data['post_protected'] && $post_options['info']) {
                    $post_options['info_parts'] = array('counters' => false, 'terms' => false, 'author' => false);
                    citygov_template_set_args('post-info', array(
                        'post_options' => $post_options,
                        'post_data' => $post_data
                    ));
                    get_template_part(citygov_get_file_slug('templates/_parts/post-info.php'));
                }

                if ($show_title) {
                    ?><h5 class="post_title"><a href="<?php echo esc_url($post_data['post_link']); ?>"><?php echo trim($post_data['post_title']); ?></a></h5><?php
                }
                ?>

				<div class="post_descr">
				<?php
					if ($post_data['post_protected']) {
                        echo trim($post_data['post_excerpt']);
					} else {
						if ($post_data['post_excerpt']) {
							echo in_array($post_data['post_format'], array('quote', 'link', 'chat', 'aside', 'status')) ? $post_data['post_excerpt'] : '<p>'.trim(citygov_strshort($post_data['post_excerpt'], isset($post_options['descr']) ? $post_options['descr'] : citygov_get_custom_option('post_excerpt_maxlength'))).'</p>';
						}
					}

                if (!$post_data['post_protected'] && $post_options['info']) {
                    $post_options['info_parts'] = array('counters'=>true, 'terms'=>false, 'author'=>true, 'date'=>false);
                    citygov_template_set_args('post-info', array(
                        'post_options' => $post_options,
                        'post_data' => $post_data
                    ));
                    get_template_part(citygov_get_file_slug('templates/_parts/post-info.php'));
                }
				?>
				</div>

			</div>	<!-- /.post_content -->

		</<?php echo trim($tag); ?>>	<!-- /.post_item -->

	<?php
	}
}
?>