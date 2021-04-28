<?php
/**
 * Theme sprecific functions and definitions
 */

/* Theme setup section
------------------------------------------------------------------- */

// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) ) $content_width = 1170; /* pixels */

// Add theme specific actions and filters
// Attention! Function were add theme specific actions and filters handlers must have priority 1
if ( !function_exists( 'citygov_theme_setup' ) ) {
	add_action( 'citygov_action_before_init_theme', 'citygov_theme_setup', 1 );
	function citygov_theme_setup() {

		// Register theme menus
		add_filter( 'citygov_filter_add_theme_menus',		'citygov_add_theme_menus' );

		// Register theme sidebars
		add_filter( 'citygov_filter_add_theme_sidebars',	'citygov_add_theme_sidebars' );

		// Set options for importer
		add_filter( 'citygov_filter_importer_options',		'citygov_set_importer_options' );

        // Add theme specified classes into the body
        add_filter( 'body_class', 'citygov_body_classes' );

		// Set list of the theme required plugins
		citygov_storage_set('required_plugins', array(
			'booked',
			'buddypress',		// Attention! This slug used to install both BuddyPress and bbPress
			'essgrids',
			'instagram_widget',
			'revslider',
			'tribe_events',
			'trx_donations',
			'trx_utils',
			'visual_composer',
            'wordpress-social-login',
			'html5_jquery_audio_player'
			)
		);
		
	}
}


function citygov_disable_admin_bar() {
    if ( ! current_user_can('edit_posts') ) {
        add_filter('show_admin_bar', '__return_false');
    }
}
add_action( 'after_setup_theme', 'citygov_disable_admin_bar' );

// Add/Remove theme nav menus
if ( !function_exists( 'citygov_add_theme_menus' ) ) {
	//add_filter( 'citygov_filter_add_theme_menus', 'citygov_add_theme_menus' );
	function citygov_add_theme_menus($menus) {
		return $menus;
	}
}


// Add theme specific widgetized areas
if ( !function_exists( 'citygov_add_theme_sidebars' ) ) {
	function citygov_add_theme_sidebars($sidebars=array()) {
		if (is_array($sidebars)) {
			$theme_sidebars = array(
				'sidebar_main'		=> esc_html__( 'Main Sidebar', 'citygov' ),
				'sidebar_footer'	=> esc_html__( 'Footer Sidebar', 'citygov' )
			);
			if (function_exists('citygov_exists_woocommerce') && citygov_exists_woocommerce()) {
				$theme_sidebars['sidebar_cart']  = esc_html__( 'WooCommerce Cart Sidebar', 'citygov' );
			}
			$sidebars = array_merge($theme_sidebars, $sidebars);
		}
		return $sidebars;
	}
}


// Add theme specified classes into the body
if ( !function_exists('citygov_body_classes') ) {
    function citygov_body_classes( $classes ) {

        $classes[] = 'citygov_body';
        $classes[] = 'body_style_' . trim(citygov_get_custom_option('body_style'));
        $classes[] = 'body_' . (citygov_get_custom_option('body_filled')=='yes' ? 'filled' : 'transparent');
        $classes[] = 'theme_skin_' . trim(citygov_get_custom_option('theme_skin'));
        $classes[] = 'article_style_' . trim(citygov_get_custom_option('article_style'));

        $blog_style = citygov_get_custom_option(is_singular() && !citygov_storage_get('blog_streampage') ? 'single_style' : 'blog_style');
        $classes[] = 'layout_' . trim($blog_style);
        $classes[] = 'template_' . trim(citygov_get_template_name($blog_style));

        $body_scheme = citygov_get_custom_option('body_scheme');
        if (empty($body_scheme)  || citygov_is_inherit_option($body_scheme)) $body_scheme = 'original';
        $classes[] = 'scheme_' . $body_scheme;

        $top_panel_position = citygov_get_custom_option('top_panel_position');
        if (!citygov_param_is_off($top_panel_position)) {
            $classes[] = 'top_panel_show';
            $classes[] = 'top_panel_' . trim($top_panel_position);
        } else
            $classes[] = 'top_panel_hide';
        $classes[] = citygov_get_sidebar_class();

        if (citygov_get_custom_option('show_video_bg')=='yes' && (citygov_get_custom_option('video_bg_youtube_code')!='' || citygov_get_custom_option('video_bg_url')!=''))
            $classes[] = 'video_bg_show';

        if (citygov_get_theme_option('page_preloader')!='')
            $classes[] = 'preloader';

        return $classes;
    }
} 

// Set theme specific importer options
if ( !function_exists( 'citygov_set_importer_options' ) ) {
	//add_filter( 'citygov_filter_importer_options',	'citygov_set_importer_options' );
	function citygov_set_importer_options($options=array()) {
		if (is_array($options)) {
			$options['debug'] = citygov_get_theme_option('debug_mode')=='yes';
			$options['domain_dev'] = 'citygov.dv.ancorathemes.com';
			$options['domain_demo'] = 'citygov.ancorathemes.com';
			$options['menus'] = array(
				'menu-main'	  => esc_html__('Main menu', 'citygov'),
				'menu-user'	  => esc_html__('User menu', 'citygov'),
				'menu-footer' => esc_html__('Footer menu', 'citygov'),
				'menu-outer'  => esc_html__('Main menu', 'citygov')
			);
			$options['file_with_attachments'] = array(				// Array with names of the attachments
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.001',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.002',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.003',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.004',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.005',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.006',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.007',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.008',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.009',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.010',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.011',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.012',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.013',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.014',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.015',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.016',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.017',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.018',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.019',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.020',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.021',
                'http://citygov.ancorathemes.com/wp-content/imports/uploads.022'
			);

			$options['attachments_by_parts'] = true;				// Files above are parts of single file - large media archive. They are must be concatenated in one file before unpacking
		}

		return $options;
	}
}


/* Include framework core files
------------------------------------------------------------------- */
// If now is WP Heartbeat call - skip loading theme core files (to reduce server and DB uploads)
// Remove comments below only if your theme not work with own post types and/or taxonomies
//if (!isset($_POST['action']) || $_POST['action']!="heartbeat") {
    require_once get_template_directory().'/fw/loader.php';
//}
?>