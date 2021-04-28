<?php
/**
 * The Header for our theme.
 */

// Theme init - don't remove next row! Load custom options
citygov_core_init_theme();

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1<?php if (citygov_get_theme_option('responsive_layouts')=='yes') echo ', maximum-scale=1'; ?>">
	<meta name="format-detection" content="telephone=no">

	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php
	if (($preloader=citygov_get_theme_option('page_preloader'))!='') {
		$clr = citygov_get_scheme_color('bg_color');
		?>
	   	<style type="text/css">
   		<!--
			#page_preloader { background-color: <?php echo esc_attr($clr); ?>; background-image:url(<?php echo esc_url($preloader); ?>); background-position:center; background-repeat:no-repeat; position:fixed; z-index:1000000; left:0; top:0; right:0; bottom:0; opacity: 0.8; }
	   	-->
   		</style>
   		<?php
   	}
	if ( !function_exists('has_site_icon') || !has_site_icon() ) {
		$favicon = citygov_get_custom_option('favicon');
		if (!$favicon) {
            $theme_skin = citygov_esc(citygov_get_custom_option('theme_skin'));
			if ( file_exists(citygov_get_file_dir('skins/'.($theme_skin).'/images/favicon.ico')) )
				$favicon = citygov_get_file_url('skins/'.($theme_skin).'/images/favicon.ico');
			if ( !$favicon && file_exists(citygov_get_file_dir('favicon.ico')) )
				$favicon = citygov_get_file_url('favicon.ico');
		}
		if ($favicon) {
			?><link rel="icon" type="image/x-icon" href="<?php echo esc_url($favicon); ?>" /><?php
		}
	}

	wp_head();
	?>
</head>

<body <?php body_class();?>>
	<?php 
	citygov_profiler_add_point(esc_html__('BODY start', 'citygov'));
	
	echo force_balance_tags(citygov_get_custom_option('gtm_code'));

	// Page preloader
	if ($preloader!='') {
		?><div id="page_preloader"></div><?php
	}

	do_action( 'before' );

	// Add TOC items 'Home' and "To top"
	if (citygov_get_custom_option('menu_toc_home')=='yes')
		echo trim(citygov_sc_anchor(array(
			'id' => "toc_home",
			'title' => esc_html__('Home', 'citygov'),
			'description' => esc_html__('{{Return to Home}} - ||navigate to home page of the site', 'citygov'),
			'icon' => "icon-home",
			'separator' => "yes",
			'url' => esc_url(home_url('/'))
			)
		)); 
	if (citygov_get_custom_option('menu_toc_top')=='yes')
		echo trim(citygov_sc_anchor(array(
			'id' => "toc_top",
			'title' => esc_html__('To Top', 'citygov'),
			'description' => esc_html__('{{Back to top}} - ||scroll to top of the page', 'citygov'),
			'icon' => "icon-double-up",
			'separator' => "yes")
			)); 
	?>

	<?php if ( !citygov_param_is_off(citygov_get_custom_option('show_sidebar_outer')) ) { ?>
	<div class="outer_wrap">
	<?php } ?>

	<?php get_template_part(citygov_get_file_slug('sidebar_outer.php')); ?>

	<?php
		$class = $style = '';
		if (citygov_get_custom_option('body_style')=='boxed' || citygov_get_custom_option('bg_image_load')=='always') {
			if (($img = (int) citygov_get_custom_option('bg_image', 0)) > 0)
				$class = 'bg_image_'.($img);
			else if (($img = (int) citygov_get_custom_option('bg_pattern', 0)) > 0)
				$class = 'bg_pattern_'.($img);
			else if (($img = citygov_get_custom_option('bg_color', '')) != '')
				$style = 'background-color: '.($img).';';
			else if (citygov_get_custom_option('bg_custom')=='yes') {
				if (($img = citygov_get_custom_option('bg_image_custom')) != '')
					$style = 'background: url('.esc_url($img).') ' . str_replace('_', ' ', citygov_get_custom_option('bg_image_custom_position')) . ' no-repeat fixed;';
				else if (($img = citygov_get_custom_option('bg_pattern_custom')) != '')
					$style = 'background: url('.esc_url($img).') 0 0 repeat fixed;';
				else if (($img = citygov_get_custom_option('bg_image')) > 0)
					$class = 'bg_image_'.($img);
				else if (($img = citygov_get_custom_option('bg_pattern')) > 0)
					$class = 'bg_pattern_'.($img);
				if (($img = citygov_get_custom_option('bg_color')) != '')
					$style .= 'background-color: '.($img).';';
			}
		}
	?>

	<div class="body_wrap<?php echo !empty($class) ? ' '.esc_attr($class) : ''; ?>"<?php echo !empty($style) ? ' style="'.esc_attr($style).'"' : ''; ?>>

		<?php

		if (citygov_get_custom_option('show_video_bg')=='yes' && (citygov_get_custom_option('video_bg_youtube_code')!='' || citygov_get_custom_option('video_bg_url')!='')) {
			$youtube = citygov_get_custom_option('video_bg_youtube_code');
			$video   = citygov_get_custom_option('video_bg_url');
			$overlay = citygov_get_custom_option('video_bg_overlay')=='yes';
			if (!empty($youtube)) {
				?>
				<div class="video_bg<?php echo !empty($overlay) ? ' video_bg_overlay' : ''; ?>" data-youtube-code="<?php echo esc_attr($youtube); ?>"></div>
				<?php
			} else if (!empty($video)) {
				$info = pathinfo($video);
				$ext = !empty($info['extension']) ? $info['extension'] : 'src';
				?>
				<div class="video_bg<?php echo !empty($overlay) ? ' video_bg_overlay' : ''; ?>"><video class="video_bg_tag" width="1280" height="720" data-width="1280" data-height="720" data-ratio="16:9" preload="metadata" autoplay loop src="<?php echo esc_url($video); ?>"><source src="<?php echo esc_url($video); ?>" type="video/<?php echo esc_attr($ext); ?>"></source></video></div>
				<?php
			}
		}
		?>

		<div class="page_wrap">

			<?php
            $top_panel_scheme = citygov_get_custom_option('top_panel_scheme');
			citygov_profiler_add_point(esc_html__('Before Page Header', 'citygov'));
			// Top panel 'Above' or 'Over'
            $top_panel_style = citygov_get_custom_option('top_panel_style');
            $top_panel_position = citygov_get_custom_option('top_panel_position');
			if (in_array($top_panel_position, array('above', 'over'))) {
				citygov_show_post_layout(array(
					'layout' => $top_panel_style,
					'position' => $top_panel_position,
					'scheme' => $top_panel_scheme
					), false);
				citygov_profiler_add_point(esc_html__('After show menu', 'citygov'));
			}
			// Slider
			get_template_part(citygov_get_file_slug('templates/headers/_parts/slider.php'));
			// Top panel 'Below'
			if (citygov_get_custom_option('top_panel_position') == 'below') {
				citygov_show_post_layout(array(
					'layout' => $top_panel_style,
					'position' => $top_panel_position,
					'scheme' => $top_panel_scheme
					), false);
				citygov_profiler_add_point(esc_html__('After show menu', 'citygov'));
			}

			// Top of page section: page title and breadcrumbs
			$show_title = citygov_get_custom_option('show_page_title')=='yes';
			$show_navi = $show_title && is_single() && citygov_is_woocommerce_page();
			$show_breadcrumbs = citygov_get_custom_option('show_breadcrumbs')=='yes';
			if ($show_title || $show_breadcrumbs) {
				?>
				<div class="top_panel_title top_panel_style_<?php echo esc_attr(str_replace('header_', '', $top_panel_style)); ?> <?php echo (!empty($show_title) ? ' title_present'.  ($show_navi ? ' navi_present' : '') : '') . (!empty($show_breadcrumbs) ? ' breadcrumbs_present' : ''); ?> scheme_<?php echo esc_attr($top_panel_scheme); ?>">
					<div class="top_panel_title_inner top_panel_inner_style_<?php echo esc_attr(str_replace('header_', '', $top_panel_style)); ?> <?php echo (!empty($show_title) ? ' title_present_inner' : '') . (!empty($show_breadcrumbs) ? ' breadcrumbs_present_inner' : ''); ?>">
						<div class="content_wrap">
							<?php
							if ($show_title) {
								if ($show_navi) {
									?><div class="post_navi"><?php 
										previous_post_link( '<span class="post_navi_item post_navi_prev">%link</span>', '%title', true, '', 'product_cat' );
										echo '<span class="post_navi_delimiter"></span>';
										next_post_link( '<span class="post_navi_item post_navi_next">%link</span>', '%title', true, '', 'product_cat' );
									?></div><?php
								} else {
									?><h1 class="page_title"><?php echo strip_tags(citygov_get_blog_title()); ?></h1><?php
								}
							}
							if ($show_breadcrumbs) {
								?><div class="breadcrumbs"><?php if (!is_404()) citygov_show_breadcrumbs(); ?></div><?php
							}
							?>
						</div>
					</div>
				</div>
				<?php
			}
			?>

			<div class="page_content_wrap page_paddings_<?php echo esc_attr(citygov_get_custom_option('body_paddings')); ?>">

				<?php
				citygov_profiler_add_point(esc_html__('Before Page content', 'citygov'));
				// Content and sidebar wrapper
				if (citygov_get_custom_option('body_style')!='fullscreen') citygov_open_wrapper('<div class="content_wrap">');
				
				// Main content wrapper
				citygov_open_wrapper('<div class="content">');

				?>