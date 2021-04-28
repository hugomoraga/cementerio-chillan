<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'citygov_template_ekcerpt_theme_setup' ) ) {
	add_action( 'citygov_action_before_init_theme', 'citygov_template_ekcerpt_theme_setup', 1 );
	function citygov_template_ekcerpt_theme_setup() {
		citygov_add_template(array(
			'layout' => 'ekcerpt',
			'mode'   => 'blog',
			'title'  => esc_html__('Ekcerpt', 'citygov'),
			'thumb_title'  => esc_html__('Smaller image (crop)', 'citygov'),
			'w'		 => 170,
			'h'		 => 170
		));
	}
}

// Template output
if ( !function_exists( 'citygov_template_ekcerpt_output' ) ) {
	function citygov_template_ekcerpt_output($post_options, $post_data) {
		$show_title = true;
		$tag = citygov_in_shortcode_blogger(true) ? 'div' : 'article';
		?>
		<<?php echo trim($tag); ?> <?php post_class('post_item post_item_ekcerpt post_featured_' . esc_attr($post_options['post_class']) . ' post_format_'.esc_attr($post_data['post_format']) . ($post_options['number']%2==0 ? ' even' : ' odd') . ($post_options['number']==0 ? ' first' : '') . ($post_options['number']==$post_options['posts_on_page']? ' last' : '') . ($post_options['add_view_more'] ? ' viewmore' : '')); ?>>
			<?php
			if ($post_data['post_flags']['sticky']) {
				?><span class="sticky_label"></span><?php
			}

			if ($show_title && $post_options['location'] == 'center' && !empty($post_data['post_title'])) {
				?><h5 class="post_title"><a href="<?php echo esc_url($post_data['post_link']); ?>"><?php echo trim($post_data['post_title']); ?></a></h5><?php
			}
			
			if (!$post_data['post_protected'] && (!empty($post_options['dedicated']) || $post_data['post_thumb'] || $post_data['post_gallery'] || $post_data['post_video'] || $post_data['post_audio'])) {
				?>
				<div class="post_featured">
				<?php
				if (!empty($post_options['dedicated'])) {
					echo trim($post_options['dedicated']);
				} else if ($post_data['post_thumb'] || $post_data['post_gallery'] || $post_data['post_video'] || $post_data['post_audio']) {
					require citygov_get_file_dir('templates/_parts/post-featured.php');
				}
				?>
				</div>
			<?php
			}
			?>
	
			<div class="post_content clearfix">
				<?php
				if ($show_title && $post_options['location'] != 'center' && !empty($post_data['post_title'])) {
					?><h5 class="post_title"><a href="<?php echo esc_url($post_data['post_link']); ?>"><?php echo trim($post_data['post_title']); ?></a></h5><?php
				}
				?>
		
				<div class="post_descr">
				<?php

					if (empty($post_options['readmore'])) $post_options['readmore'] = esc_html__('more', 'citygov');
                if (!citygov_param_is_off($post_options['readmore']) && !in_array($post_data['post_format'], array('quote', 'link', 'chat', 'aside', 'status'))) {
                    ?><a href="<?php echo esc_url($post_data['post_link']); ?>" class="post_readmore"><?php echo trim($post_options['readmore']); ?></a><?php
                }
				?>
				</div>

			</div>	<!-- /.post_content -->

		</<?php echo trim($tag); ?>>	<!-- /.post_item -->

	<?php
	}
}
?>