<?php
/*
 * The template for displaying "Page 404"
*/

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'citygov_template_404_theme_setup' ) ) {
	add_action( 'citygov_action_before_init_theme', 'citygov_template_404_theme_setup', 1 );
	function citygov_template_404_theme_setup() {
		citygov_add_template(array(
			'layout' => '404',
			'mode'   => 'internal',
			'title'  => 'Page 404',
			'theme_options' => array(
				'article_style' => 'stretch'
			)
		));
	}
}

// Template output
if ( !function_exists( 'citygov_template_404_output' ) ) {
	function citygov_template_404_output() {
		?>
		<article class="post_item post_item_404">
			<div class="post_content">
                <img class="image-404" src="http://citygov.ancorathemes.com/wp-content/themes/citygov/images/404.jpg" alt="">
				<h3 class="page_title"><?php esc_html_e( 'We Are Sorry! Error 404!', 'citygov' ); ?></h3>
				<p class="page_description"><?php echo wp_kses_data( sprintf( __('Can\'t find what you need? Take a moment and do a search below or start from our <a href="%s">homepage</a>.', 'citygov'), esc_url(home_url('/')) ) ); ?></p>
				<div class="page_search"><?php echo trim(citygov_sc_search(array('state'=>'fixed', 'title'=>__('To search type and hit enter', 'citygov')))); ?></div>
			</div>
		</article>
		<?php
	}
}
?>