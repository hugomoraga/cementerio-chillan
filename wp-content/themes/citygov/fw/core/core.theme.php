<?php
/**
 * CityGov Framework: Theme specific actions
 *
 * @package	citygov
 * @since	citygov 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'citygov_core_theme_setup' ) ) {
	add_action( 'citygov_action_before_init_theme', 'citygov_core_theme_setup', 11 );
	function citygov_core_theme_setup() {

		// Add default posts and comments RSS feed links to head 
		add_theme_support( 'automatic-feed-links' );
		
		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );
		
		// Custom header setup
		add_theme_support( 'custom-header', array('header-text'=>false));
		
		// Custom backgrounds setup
		add_theme_support( 'custom-background');
		
		// Supported posts formats
		add_theme_support( 'post-formats', array('gallery', 'video', 'audio', 'link', 'quote', 'image', 'status', 'aside', 'chat') ); 
 
 		// Autogenerate title tag
		add_theme_support('title-tag');
 		
		// Add user menu
		add_theme_support('nav-menus');
		
		// WooCommerce Support
		add_theme_support( 'woocommerce' );
		
		// Editor custom stylesheet - for user
		add_editor_style(citygov_get_file_url('css/editor-style.css'));	
		
		// Make theme available for translation
		// Translations can be filed in the /languages/ directory
		load_theme_textdomain( 'citygov', citygov_get_folder_dir('languages') );


		/* Front and Admin actions and filters:
		------------------------------------------------------------------------ */

		if ( !is_admin() ) {
			
			/* Front actions and filters:
			------------------------------------------------------------------------ */
	
			// Filters wp_title to print a neat <title> tag based on what is being viewed
			if (floatval(get_bloginfo('version')) < "4.1") {
				add_action('wp_head',						'citygov_wp_title_show');
				add_filter('wp_title',						'citygov_wp_title_modify', 10, 2);
			}

			// Add main menu classes
			//add_filter('wp_nav_menu_objects', 			'citygov_add_mainmenu_classes', 10, 2);
	
			// Prepare logo text
			add_filter('citygov_filter_prepare_logo_text',	'citygov_prepare_logo_text', 10, 1);
	
			// Add class "widget_number_#' for each widget
			add_filter('dynamic_sidebar_params', 			'citygov_add_widget_number', 10, 1);

			// Frontend editor: Save post data
			add_action('wp_ajax_frontend_editor_save',		'citygov_callback_frontend_editor_save');
			add_action('wp_ajax_nopriv_frontend_editor_save', 'citygov_callback_frontend_editor_save');

			// Frontend editor: Delete post
			add_action('wp_ajax_frontend_editor_delete', 	'citygov_callback_frontend_editor_delete');
			add_action('wp_ajax_nopriv_frontend_editor_delete', 'citygov_callback_frontend_editor_delete');
	
			// Enqueue scripts and styles
			add_action('wp_enqueue_scripts', 				'citygov_core_frontend_scripts');
			add_action('wp_footer',		 					'citygov_core_frontend_scripts_inline');
			add_action('citygov_action_add_scripts_inline','citygov_core_add_scripts_inline');

			// Prepare theme core global variables
			add_action('citygov_action_prepare_globals',	'citygov_core_prepare_globals');
		}

		// Register theme specific nav menus
		citygov_register_theme_menus();

		// Register theme specific sidebars
		citygov_register_theme_sidebars();
	}
}




/* Theme init
------------------------------------------------------------------------ */

// Init theme template
function citygov_core_init_theme() {
	if (citygov_storage_get('theme_inited')===true) return;
	citygov_storage_set('theme_inited', true);

	if (!is_admin()) citygov_profiler_add_point(esc_html__('After WP INIT actions', 'citygov'), false);

	// Load custom options from GET and post/page/cat options
	if (isset($_GET['set']) && $_GET['set']==1) {
		foreach ($_GET as $k=>$v) {
			if (citygov_get_theme_option($k, null) !== null) {
				setcookie($k, $v, 0, '/');
				$_COOKIE[$k] = $v;
			}
		}
	}

	// Get custom options from current category / page / post / shop / event
	citygov_load_custom_options();

	// Load skin
	$skin = citygov_esc(citygov_get_custom_option('theme_skin'));
	citygov_storage_set('theme_skin', $skin);
	if ( file_exists(citygov_get_file_dir('skins/'.($skin).'/skin.php')) ) {
        require_once get_template_directory().'/skins/'.($skin).'/skin.php';
	}

	// Fire init theme actions (after skin and custom options are loaded)
	do_action('citygov_action_init_theme');

	// Prepare theme core global variables
	do_action('citygov_action_prepare_globals');

	// Fire after init theme actions
	do_action('citygov_action_after_init_theme');
	citygov_profiler_add_point(esc_html__('After Theme Init', 'citygov'));
}


// Prepare theme global variables
if ( !function_exists( 'citygov_core_prepare_globals' ) ) {
	function citygov_core_prepare_globals() {
		if (!is_admin()) {
			// Logo text and slogan
			citygov_storage_set('logo_text', apply_filters('citygov_filter_prepare_logo_text', citygov_get_custom_option('logo_text')));
			citygov_storage_set('logo_slogan', get_bloginfo('description'));
			
			// Logo image and icons from skin
			$logo        = citygov_get_logo_icon('logo');
			$logo_side   = citygov_get_logo_icon('logo_side');
			$logo_fixed  = citygov_get_logo_icon('logo_fixed');
			$logo_footer = citygov_get_logo_icon('logo_footer');
			citygov_storage_set('logo', $logo);
			citygov_storage_set('logo_icon',   citygov_get_logo_icon('logo_icon'));
			citygov_storage_set('logo_side',   $logo_side   ? $logo_side   : $logo);
			citygov_storage_set('logo_fixed',  $logo_fixed  ? $logo_fixed  : $logo);
			citygov_storage_set('logo_footer', $logo_footer ? $logo_footer : $logo);
	
			$shop_mode = '';
			if (citygov_get_custom_option('show_mode_buttons')=='yes')
				$shop_mode = citygov_get_value_gpc('citygov_shop_mode');
			if (empty($shop_mode))
				$shop_mode = citygov_get_custom_option('shop_mode', '');
			if (empty($shop_mode) || !is_archive())
				$shop_mode = 'thumbs';
			citygov_storage_set('shop_mode', $shop_mode);
		}
	}
}


// Return url for the uploaded logo image or (if not uploaded) - to image from skin folder
if ( !function_exists( 'citygov_get_logo_icon' ) ) {
	function citygov_get_logo_icon($slug) {
		$mult = citygov_get_retina_multiplier();
		$logo_icon = '';
		if ($mult > 1) 			$logo_icon = citygov_get_custom_option($slug.'_retina');
		if (empty($logo_icon))	$logo_icon = citygov_get_custom_option($slug);
		return $logo_icon;
	}
}


// Display logo image with text and slogan (if specified)
if ( !function_exists( 'citygov_show_logo' ) ) {
	function citygov_show_logo($logo_main=true, $logo_fixed=false, $logo_footer=false, $logo_side=false, $logo_text=true, $logo_slogan=true) {
		if ($logo_main===true)		$logo_main   = citygov_storage_get('logo');
		if ($logo_fixed===true)		$logo_fixed  = citygov_storage_get('logo_fixed');
		if ($logo_footer===true)	$logo_footer = citygov_storage_get('logo_footer');
		if ($logo_side===true)		$logo_side   = citygov_storage_get('logo_side');
		if ($logo_text===true)		$logo_text   = citygov_storage_get('logo_text');
		if ($logo_slogan===true)	$logo_slogan = citygov_storage_get('logo_slogan');
		if ($logo_main || $logo_fixed || $logo_footer || $logo_side || $logo_text) {
		?>
		<div class="logo">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php
				if (!empty($logo_main)) {
					$attr = citygov_getimagesize($logo_main);
					echo '<img src="'.esc_url($logo_main).'" class="logo_main" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				if (!empty($logo_fixed)) {
					$attr = citygov_getimagesize($logo_fixed);
					echo '<img src="'.esc_url($logo_fixed).'" class="logo_fixed" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				if (!empty($logo_footer)) {
					$attr = citygov_getimagesize($logo_footer);
					echo '<img src="'.esc_url($logo_footer).'" class="logo_footer" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				if (!empty($logo_side)) {
					$attr = citygov_getimagesize($logo_side);
					echo '<img src="'.esc_url($logo_side).'" class="logo_side" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				echo !empty($logo_text) ? '<div class="logo_text">'.trim($logo_text).'</div>' : '';
				echo !empty($logo_slogan) ? '<div class="logo_slogan">' . esc_html($logo_slogan) . '</div>' : '';
			?></a>
		</div>
		<?php 
		}
	} 
}


// Add menu locations
if ( !function_exists( 'citygov_register_theme_menus' ) ) {
	function citygov_register_theme_menus() {
		register_nav_menus(apply_filters('citygov_filter_add_theme_menus', array(
			'menu_main'		=> esc_html__('Main Menu', 'citygov'),
			'menu_user'		=> esc_html__('User Menu', 'citygov'),
			'menu_footer'	=> esc_html__('Footer Menu', 'citygov'),
			'menu_side'		=> esc_html__('Side Menu', 'citygov')
		)));
	}
}


// Register widgetized area
if ( !function_exists( 'citygov_register_theme_sidebars' ) ) {
	function citygov_register_theme_sidebars($sidebars=array()) {
		if (!is_array($sidebars)) $sidebars = array();
		// Custom sidebars
		$custom = citygov_get_theme_option('custom_sidebars');
		if (is_array($custom) && count($custom) > 0) {
			foreach ($custom as $i => $sb) {
				if (trim(chop($sb))=='') continue;
				$sidebars['sidebar_custom_'.($i)]  = $sb;
			}
		}
		$sidebars = apply_filters( 'citygov_filter_add_theme_sidebars', $sidebars );
		citygov_storage_set('registered_sidebars', $sidebars);
		if (is_array($sidebars) && count($sidebars) > 0) {
			foreach ($sidebars as $id=>$name) {
				register_sidebar( array_merge( array(
													'name'          => $name,
													'id'            => $id
												),
												citygov_storage_get('widgets_args')
									)
				);
			}
		}
	}
}





/* Front actions and filters:
------------------------------------------------------------------------ */

//  Enqueue scripts and styles
if ( !function_exists( 'citygov_core_frontend_scripts' ) ) {
	function citygov_core_frontend_scripts() {
		
		// Modernizr will load in head before other scripts and styles
		// Use older version (from photostack)
		citygov_enqueue_script( 'citygov-core-modernizr-script', citygov_get_file_url('js/photostack/modernizr.min.js'), array(), null, false );
		
		// Enqueue styles
		//-----------------------------------------------------------------------------------------------------
		
		// Prepare custom fonts
		$fonts = citygov_get_list_fonts(false);
		$theme_fonts = array();
		$custom_fonts = citygov_get_custom_fonts();
		if (is_array($custom_fonts) && count($custom_fonts) > 0) {
			foreach ($custom_fonts as $s=>$f) {
				if (!empty($f['font-family']) && !citygov_is_inherit_option($f['font-family'])) $theme_fonts[$f['font-family']] = 1;
			}
		}
		// Prepare current skin fonts
		$theme_fonts = apply_filters('citygov_filter_used_fonts', $theme_fonts);
		// Link to selected fonts
		if (is_array($theme_fonts) && count($theme_fonts) > 0) {
			$google_fonts = '';
			foreach ($theme_fonts as $font=>$v) {
				if (isset($fonts[$font])) {
					$font_name = ($pos=citygov_strpos($font,' ('))!==false ? citygov_substr($font, 0, $pos) : $font;
					if (!empty($fonts[$font]['css'])) {
						$css = $fonts[$font]['css'];
						citygov_enqueue_style( 'citygov-font-'.str_replace(' ', '-', $font_name).'-style', $css, array(), null );
					} else {
						$google_fonts .= ($google_fonts ? '|' : '') 
							. (!empty($fonts[$font]['link']) ? $fonts[$font]['link'] : str_replace(' ', '+', $font_name).':300,300italic,400,400italic,700,700italic');
					}
				}
			}
			if ($google_fonts)
				citygov_enqueue_style( 'citygov-font-google_fonts-style', citygov_get_protocol() . '://fonts.googleapis.com/css?family=' . $google_fonts . '&subset=' . citygov_get_theme_option('fonts_subset'), array(), null );
		}
		
		// Fontello styles must be loaded before main stylesheet
		citygov_enqueue_style( 'citygov-fontello-style',  citygov_get_file_url('css/fontello/css/fontello.css'),  array(), null);
		//citygov_enqueue_style( 'citygov-fontello-animation-style', citygov_get_file_url('css/fontello/css/animation.css'), array(), null);

		// Main stylesheet
		citygov_enqueue_style( 'citygov-main-style', get_stylesheet_uri(), array(), null );
		
		// Animations
		if (citygov_get_theme_option('css_animation')=='yes' && (citygov_get_theme_option('animation_on_mobile')=='yes' || !wp_is_mobile()) && !citygov_vc_is_frontend())
			citygov_enqueue_style( 'citygov-animation-style',	citygov_get_file_url('css/core.animation.css'), array(), null );

		// Theme skin stylesheet
		do_action('citygov_action_add_styles');
		
		// Theme customizer stylesheet and inline styles
		citygov_enqueue_custom_styles();

		// Responsive
		if (citygov_get_theme_option('responsive_layouts') == 'yes') {
			$suffix = citygov_param_is_off(citygov_get_custom_option('show_sidebar_outer')) ? '' : '-outer';
			citygov_enqueue_style( 'citygov-responsive-style', citygov_get_file_url('css/responsive'.($suffix).'.css'), array(), null );
			do_action('citygov_action_add_responsive');
			if (citygov_get_custom_option('theme_skin')!='') {
				$css = apply_filters('citygov_filter_add_responsive_inline', '');
				if (!empty($css)) wp_add_inline_style( 'citygov-responsive-style', $css );
			}
		}

		// Disable loading JQuery UI CSS
		//global $wp_styles, $wp_scripts;
		//$wp_styles->done[]	= 'jquery-ui';
		//$wp_styles->done[]	= 'date-picker-css';
		wp_deregister_style('jquery_ui');
		wp_deregister_style('date-picker-css');


		// Enqueue scripts	
		//----------------------------------------------------------------------------------------------------------------------------
		
		// Load separate theme scripts
		citygov_enqueue_script( 'superfish', citygov_get_file_url('js/superfish.js'), array('jquery'), null, true );
		if (citygov_get_theme_option('menu_slider')=='yes') {
			citygov_enqueue_script( 'citygov-slidemenu-script', citygov_get_file_url('js/jquery.slidemenu.js'), array('jquery'), null, true );
			//citygov_enqueue_script( 'citygov-jquery-easing-script', citygov_get_file_url('js/jquery.easing.js'), array('jquery'), null, true );
		}

		if ( is_single() && citygov_get_custom_option('show_reviews')=='yes' ) {
			citygov_enqueue_script( 'citygov-core-reviews-script', citygov_get_file_url('js/core.reviews.js'), array('jquery'), null, true );
		}

		citygov_enqueue_script( 'citygov-core-utils-script',	citygov_get_file_url('js/core.utils.js'), array('jquery'), null, true );
		citygov_enqueue_script( 'citygov-core-init-script',	citygov_get_file_url('js/core.init.js'), array('jquery'), null, true );	
		citygov_enqueue_script( 'citygov-theme-init-script',	citygov_get_file_url('js/theme.init.js'), array('jquery'), null, true );	

		// Media elements library	
		if (citygov_get_theme_option('use_mediaelement')=='yes') {
			wp_enqueue_style ( 'mediaelement' );
			wp_enqueue_style ( 'wp-mediaelement' );
			wp_enqueue_script( 'mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		} else {
			wp_deregister_style('mediaelement');
			wp_deregister_style('wp-mediaelement');
		}
		
		// Video background
		if (citygov_get_custom_option('show_video_bg') == 'yes' && citygov_get_custom_option('video_bg_youtube_code') != '') {
			citygov_enqueue_script( 'citygov-video-bg-script', citygov_get_file_url('js/jquery.tubular.1.0.js'), array('jquery'), null, true );
		}

        // Google map
        if ( citygov_get_custom_option('show_googlemap')=='yes' ) {
            $api_key = citygov_get_theme_option('api_google');
            citygov_enqueue_script( 'googlemap', citygov_get_protocol().'://maps.google.com/maps/api/js'.($api_key ? '?key='.$api_key : ''), array(), null, true );
            citygov_enqueue_script( 'citygov-googlemap-script', citygov_get_file_url('js/core.googlemap.js'), array(), null, true );
        }

			
		// Social share buttons
		if (is_singular() && !citygov_storage_get('blog_streampage') && citygov_get_custom_option('show_share')!='hide') {
			citygov_enqueue_script( 'citygov-social-share-script', citygov_get_file_url('js/social/social-share.js'), array('jquery'), null, true );
		}

		// Comments
		if ( is_singular() && !citygov_storage_get('blog_streampage') && comments_open() && get_option( 'thread_comments' ) ) {
            wp_enqueue_script( 'comment-reply', false, array(), null, true );
		}

		// Custom panel
		if (citygov_get_theme_option('show_theme_customizer') == 'yes') {
			if (file_exists(citygov_get_file_dir('core/core.customizer/front.customizer.css')))
				citygov_enqueue_style(  'citygov-customizer-style',  citygov_get_file_url('core/core.customizer/front.customizer.css'), array(), null );
			if (file_exists(citygov_get_file_dir('core/core.customizer/front.customizer.js')))
				citygov_enqueue_script( 'citygov-customizer-script', citygov_get_file_url('core/core.customizer/front.customizer.js'), array(), null, true );	
		}
		
		//Debug utils
		if (citygov_get_theme_option('debug_mode')=='yes') {
			citygov_enqueue_script( 'citygov-core-debug-script', citygov_get_file_url('js/core.debug.js'), array(), null, true );
		}

		// Theme skin script
		do_action('citygov_action_add_scripts');
	}
}

//  Enqueue Swiper Slider scripts and styles
if ( !function_exists( 'citygov_enqueue_slider' ) ) {
	function citygov_enqueue_slider($engine='all') {
		if ($engine=='all' || $engine=='swiper') {
			citygov_enqueue_style(  'citygov-swiperslider-style', 			citygov_get_file_url('js/swiper/swiper.css'), array(), null );
			citygov_enqueue_script( 'citygov-swiperslider-script', 			citygov_get_file_url('js/swiper/swiper.js'), array(), null, true );
			// jQuery version conflict with Revolution Slider
			//citygov_enqueue_script( 'citygov-swiperslider-script', 			citygov_get_file_url('js/swiper/swiper.jquery.js'), array(), null, true );
		}
	}
}

//  Enqueue Photostack gallery
if ( !function_exists( 'citygov_enqueue_polaroid' ) ) {
	function citygov_enqueue_polaroid() {
		citygov_enqueue_style(  'citygov-polaroid-style', 	citygov_get_file_url('js/photostack/component.css'), array(), null );
		citygov_enqueue_script( 'citygov-classie-script',		citygov_get_file_url('js/photostack/classie.js'), array(), null, true );
		citygov_enqueue_script( 'citygov-polaroid-script',	citygov_get_file_url('js/photostack/photostack.js'), array(), null, true );
	}
}

//  Enqueue Messages scripts and styles
if ( !function_exists( 'citygov_enqueue_messages' ) ) {
	function citygov_enqueue_messages() {
		citygov_enqueue_style(  'citygov-messages-style',		citygov_get_file_url('js/core.messages/core.messages.css'), array(), null );
		citygov_enqueue_script( 'citygov-messages-script',	citygov_get_file_url('js/core.messages/core.messages.js'),  array('jquery'), null, true );
	}
}

//  Enqueue Portfolio hover scripts and styles
if ( !function_exists( 'citygov_enqueue_portfolio' ) ) {
	function citygov_enqueue_portfolio($hover='') {
		citygov_enqueue_style( 'citygov-portfolio-style',  citygov_get_file_url('css/core.portfolio.css'), array(), null );
		if (citygov_strpos($hover, 'effect_dir')!==false)
			citygov_enqueue_script( 'hoverdir', citygov_get_file_url('js/hover/jquery.hoverdir.js'), array(), null, true );
	}
}

//  Enqueue Charts and Diagrams scripts and styles
if ( !function_exists( 'citygov_enqueue_diagram' ) ) {
	function citygov_enqueue_diagram($type='all') {
		if ($type=='all' || $type=='pie') citygov_enqueue_script( 'citygov-diagram-chart-script',	citygov_get_file_url('js/diagram/chart.min.js'), array(), null, true );
		if ($type=='all' || $type=='arc') citygov_enqueue_script( 'citygov-diagram-raphael-script',	citygov_get_file_url('js/diagram/diagram.raphael.min.js'), array(), 'no-compose', true );
	}
}

// Enqueue Theme Popup scripts and styles
// Link must have attribute: data-rel="popup" or data-rel="popup[gallery]"
if ( !function_exists( 'citygov_enqueue_popup' ) ) {
	function citygov_enqueue_popup($engine='') {
		if ($engine=='pretty' || (empty($engine) && citygov_get_theme_option('popup_engine')=='pretty')) {
			citygov_enqueue_style(  'citygov-prettyphoto-style',	citygov_get_file_url('js/prettyphoto/css/prettyPhoto.css'), array(), null );
			citygov_enqueue_script( 'citygov-prettyphoto-script',	citygov_get_file_url('js/prettyphoto/jquery.prettyPhoto.min.js'), array('jquery'), 'no-compose', true );
		} else if ($engine=='magnific' || (empty($engine) && citygov_get_theme_option('popup_engine')=='magnific')) {
			citygov_enqueue_style(  'citygov-magnific-style',	citygov_get_file_url('js/magnific/magnific-popup.css'), array(), null );
			citygov_enqueue_script( 'citygov-magnific-script',citygov_get_file_url('js/magnific/jquery.magnific-popup.min.js'), array('jquery'), '', true );
		} else if ($engine=='internal' || (empty($engine) && citygov_get_theme_option('popup_engine')=='internal')) {
			citygov_enqueue_messages();
		}
	}
}

//  Add inline scripts in the footer hook
if ( !function_exists( 'citygov_core_frontend_scripts_inline' ) ) {
	function citygov_core_frontend_scripts_inline() {
		do_action('citygov_action_add_scripts_inline');
	}
}

//  Add inline scripts in the footer
if (!function_exists('citygov_core_add_scripts_inline')) {
	function citygov_core_add_scripts_inline() {

		$msg = citygov_get_system_message(true); 
		if (!empty($msg['message'])) citygov_enqueue_messages();

		echo "<script type=\"text/javascript\">"
			
			. "if (typeof CITYGOV_STORAGE == 'undefined') var CITYGOV_STORAGE = {};"
			
			// AJAX parameters
			. "CITYGOV_STORAGE['ajax_url']			 = '" . esc_url(admin_url('admin-ajax.php')) . "';"
			. "CITYGOV_STORAGE['ajax_nonce']		 = '" . esc_attr(wp_create_nonce(admin_url('admin-ajax.php'))) . "';"
			
			// Site base url
			. "CITYGOV_STORAGE['site_url']			= '" . get_site_url() . "';"
			
			// VC frontend edit mode
			. "CITYGOV_STORAGE['vc_edit_mode']		= " . (function_exists('citygov_vc_is_frontend') && citygov_vc_is_frontend() ? 'true' : 'false') . ";"
			
			// Theme base font
			. "CITYGOV_STORAGE['theme_font']		= '" . citygov_get_custom_font_settings('p', 'font-family') . "';"
			
			// Theme skin
			. "CITYGOV_STORAGE['theme_skin']			= '" . esc_attr(citygov_get_custom_option('theme_skin')) . "';"
			. "CITYGOV_STORAGE['theme_skin_color']		= '" . citygov_get_scheme_color('text_dark') . "';"
			. "CITYGOV_STORAGE['theme_skin_bg_color']	= '" . citygov_get_scheme_color('bg_color') . "';"
			
			// Slider height
			. "CITYGOV_STORAGE['slider_height']	= " . max(100, citygov_get_custom_option('slider_height')) . ";"
			
			// System message
			. "CITYGOV_STORAGE['system_message']	= {"
				. "message: '" . addslashes($msg['message']) . "',"
				. "status: '"  . addslashes($msg['status'])  . "',"
				. "header: '"  . addslashes($msg['header'])  . "'"
				. "};"
			
			// User logged in
			. "CITYGOV_STORAGE['user_logged_in']	= " . (is_user_logged_in() ? 'true' : 'false') . ";"
			
			// Show table of content for the current page
			. "CITYGOV_STORAGE['toc_menu']		= '" . esc_attr(citygov_get_custom_option('menu_toc')) . "';"
			. "CITYGOV_STORAGE['toc_menu_home']	= " . (citygov_get_custom_option('menu_toc')!='hide' && citygov_get_custom_option('menu_toc_home')=='yes' ? 'true' : 'false') . ";"
			. "CITYGOV_STORAGE['toc_menu_top']	= " . (citygov_get_custom_option('menu_toc')!='hide' && citygov_get_custom_option('menu_toc_top')=='yes' ? 'true' : 'false') . ";"
			
			// Fix main menu
			. "CITYGOV_STORAGE['menu_fixed']		= " . (citygov_get_theme_option('menu_attachment')=='fixed' ? 'true' : 'false') . ";"
			
			// Use responsive version for main menu
			. "CITYGOV_STORAGE['menu_relayout']	= " . max(0, (int) citygov_get_theme_option('menu_relayout')) . ";"
			. "CITYGOV_STORAGE['menu_responsive']	= " . (citygov_get_theme_option('responsive_layouts') == 'yes' ? max(0, (int) citygov_get_theme_option('menu_responsive')) : 0) . ";"
			. "CITYGOV_STORAGE['menu_slider']     = " . (citygov_get_theme_option('menu_slider')=='yes' ? 'true' : 'false') . ";"
			
			// Menu cache is used
			. "CITYGOV_STORAGE['menu_cache']	= " . (citygov_get_theme_option('use_menu_cache')=='yes' ? 'true' : 'false') . ";"

			// Right panel demo timer
			. "CITYGOV_STORAGE['demo_time']		= " . (citygov_get_theme_option('show_theme_customizer')=='yes' ? max(0, (int) citygov_get_theme_option('customizer_demo')) : 0) . ";"

			// Video and Audio tag wrapper
			. "CITYGOV_STORAGE['media_elements_enabled'] = " . (citygov_get_theme_option('use_mediaelement')=='yes' ? 'true' : 'false') . ";"
			
			// Use AJAX search
			. "CITYGOV_STORAGE['ajax_search_enabled'] 	= " . (citygov_get_theme_option('use_ajax_search')=='yes' ? 'true' : 'false') . ";"
			. "CITYGOV_STORAGE['ajax_search_min_length']	= " . min(3, citygov_get_theme_option('ajax_search_min_length')) . ";"
			. "CITYGOV_STORAGE['ajax_search_delay']		= " . min(200, max(1000, citygov_get_theme_option('ajax_search_delay'))) . ";"

			// Use CSS animation
			. "CITYGOV_STORAGE['css_animation']      = " . (citygov_get_theme_option('css_animation')=='yes' ? 'true' : 'false') . ";"
			. "CITYGOV_STORAGE['menu_animation_in']  = '" . esc_attr(citygov_get_theme_option('menu_animation_in')) . "';"
			. "CITYGOV_STORAGE['menu_animation_out'] = '" . esc_attr(citygov_get_theme_option('menu_animation_out')) . "';"

			// Popup windows engine
			. "CITYGOV_STORAGE['popup_engine']	= '" . esc_attr(citygov_get_theme_option('popup_engine')) . "';"

			// E-mail mask
			. "CITYGOV_STORAGE['email_mask']		= '^([a-zA-Z0-9_\\-]+\\.)*[a-zA-Z0-9_\\-]+@[a-z0-9_\\-]+(\\.[a-z0-9_\\-]+)*\\.[a-z]{2,6}$';"
			
			// Messages max length
			. "CITYGOV_STORAGE['contacts_maxlength']	= " . intval(citygov_get_theme_option('message_maxlength_contacts')) . ";"
			. "CITYGOV_STORAGE['comments_maxlength']	= " . intval(citygov_get_theme_option('message_maxlength_comments')) . ";"

			// Remember visitors settings
			. "CITYGOV_STORAGE['remember_visitors_settings']	= " . (citygov_get_theme_option('remember_visitors_settings')=='yes' ? 'true' : 'false') . ";"

			// Internal vars - do not change it!
			// Flag for review mechanism
			. "CITYGOV_STORAGE['admin_mode']			= false;"
			// Max scale factor for the portfolio and other isotope elements before relayout
			. "CITYGOV_STORAGE['isotope_resize_delta']	= 0.3;"
			// jQuery object for the message box in the form
			. "CITYGOV_STORAGE['error_message_box']	= null;"
			// Waiting for the viewmore results
			. "CITYGOV_STORAGE['viewmore_busy']		= false;"
			. "CITYGOV_STORAGE['video_resize_inited']	= false;"
			. "CITYGOV_STORAGE['top_panel_height']		= 0;"
			
			. "</script>";
	}
}


//  Enqueue Custom styles (main Theme options settings)
if ( !function_exists( 'citygov_enqueue_custom_styles' ) ) {
	function citygov_enqueue_custom_styles() {
		// Custom stylesheet
		$custom_css = '';	//citygov_get_custom_option('custom_stylesheet_url');
		citygov_enqueue_style( 'citygov-custom-style', $custom_css ? $custom_css : citygov_get_file_url('css/custom-style.css'), array(), null );
		// Custom inline styles
		wp_add_inline_style( 'citygov-custom-style', citygov_prepare_custom_styles() );
	}
}

// Add class "widget_number_#' for each widget
if ( !function_exists( 'citygov_add_widget_number' ) ) {
	//add_filter('dynamic_sidebar_params', 'citygov_add_widget_number', 10, 1);
	function citygov_add_widget_number($prm) {
		if (is_admin()) return $prm;
		static $num=0, $last_sidebar='', $last_sidebar_id='', $last_sidebar_columns=0, $last_sidebar_count=0, $sidebars_widgets=array();
		$cur_sidebar = citygov_storage_get('current_sidebar');
		if (empty($cur_sidebar)) $cur_sidebar = 'undefined';
		if (count($sidebars_widgets) == 0)
			$sidebars_widgets = wp_get_sidebars_widgets();
		if ($last_sidebar != $cur_sidebar) {
			$num = 0;
			$last_sidebar = $cur_sidebar;
			$last_sidebar_id = $prm[0]['id'];
			$last_sidebar_columns = max(1, (int) citygov_get_custom_option('sidebar_'.($cur_sidebar).'_columns'));
			$last_sidebar_count = count($sidebars_widgets[$last_sidebar_id]);
		}
		$num++;
		$prm[0]['before_widget'] = str_replace(' class="', ' class="widget_number_'.esc_attr($num).($last_sidebar_columns > 1 ? ' column-1_'.esc_attr($last_sidebar_columns) : '').' ', $prm[0]['before_widget']);
		return $prm;
	}
}


// Show <title> tag under old WP (version < 4.1)
if ( !function_exists( 'citygov_wp_title_show' ) ) {
	// add_action('wp_head', 'citygov_wp_title_show');
	function citygov_wp_title_show() {
		?><title><?php wp_title( '|', true, 'right' ); ?></title><?php
	}
}

// Filters wp_title to print a neat <title> tag based on what is being viewed.
if ( !function_exists( 'citygov_wp_title_modify' ) ) {
	// add_filter( 'wp_title', 'citygov_wp_title_modify', 10, 2 );
	function citygov_wp_title_modify( $title, $sep ) {
		global $page, $paged;
		if ( is_feed() ) return $title;
		// Add the blog name
		$title .= get_bloginfo( 'name' );
		// Add the blog description for the home/front page.
		if ( is_home() || is_front_page() ) {
			$site_description = get_bloginfo( 'description', 'display' );
			if ( $site_description )
				$title .= " $sep $site_description";
		}
		// Add a page number if necessary:
		if ( $paged >= 2 || $page >= 2 )
			$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'citygov' ), max( $paged, $page ) );
		return $title;
	}
}

// Add main menu classes
if ( !function_exists( 'citygov_add_mainmenu_classes' ) ) {
	// add_filter('wp_nav_menu_objects', 'citygov_add_mainmenu_classes', 10, 2);
	function citygov_add_mainmenu_classes($items, $args) {
		if (is_admin()) return $items;
		if ($args->menu_id == 'mainmenu' && citygov_get_theme_option('menu_colored')=='yes' && is_array($items) && count($items) > 0) {
			foreach($items as $k=>$item) {
				if ($item->menu_item_parent==0) {
					if ($item->type=='taxonomy' && $item->object=='category') {
						$cur_tint = citygov_taxonomy_get_inherited_property('category', $item->object_id, 'bg_tint');
						if (!empty($cur_tint) && !citygov_is_inherit_option($cur_tint))
							$items[$k]->classes[] = 'bg_tint_'.esc_attr($cur_tint);
					}
				}
			}
		}
		return $items;
	}
}


// Save post data from frontend editor
if ( !function_exists( 'citygov_callback_frontend_editor_save' ) ) {
	function citygov_callback_frontend_editor_save() {

		if ( !wp_verify_nonce( citygov_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();

		$response = array('error'=>'');

		parse_str($_REQUEST['data'], $output);
		$post_id = $output['frontend_editor_post_id'];

		if ( citygov_get_theme_option("allow_editor")=='yes' && (current_user_can('edit_posts', $post_id) || current_user_can('edit_pages', $post_id)) ) {
			if ($post_id > 0) {
				$title   = stripslashes($output['frontend_editor_post_title']);
				$content = stripslashes($output['frontend_editor_post_content']);
				$excerpt = stripslashes($output['frontend_editor_post_excerpt']);
				$rez = wp_update_post(array(
					'ID'           => $post_id,
					'post_content' => $content,
					'post_excerpt' => $excerpt,
					'post_title'   => $title
				));
				if ($rez == 0) 
					$response['error'] = esc_html__('Post update error!', 'citygov');
			} else {
				$response['error'] = esc_html__('Post update error!', 'citygov');
			}
		} else
			$response['error'] = esc_html__('Post update denied!', 'citygov');
		
		echo json_encode($response);
		die();
	}
}

// Delete post from frontend editor
if ( !function_exists( 'citygov_callback_frontend_editor_delete' ) ) {
	function citygov_callback_frontend_editor_delete() {

		if ( !wp_verify_nonce( citygov_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();

		$response = array('error'=>'');
		
		$post_id = $_REQUEST['post_id'];

		if ( citygov_get_theme_option("allow_editor")=='yes' && (current_user_can('delete_posts', $post_id) || current_user_can('delete_pages', $post_id)) ) {
			if ($post_id > 0) {
				$rez = wp_delete_post($post_id);
				if ($rez === false) 
					$response['error'] = esc_html__('Post delete error!', 'citygov');
			} else {
				$response['error'] = esc_html__('Post delete error!', 'citygov');
			}
		} else
			$response['error'] = esc_html__('Post delete denied!', 'citygov');

		echo json_encode($response);
		die();
	}
}


// Prepare logo text
if ( !function_exists( 'citygov_prepare_logo_text' ) ) {
	function citygov_prepare_logo_text($text) {
		$text = str_replace(array('[', ']'), array('<span class="theme_accent">', '</span>'), $text);
		$text = str_replace(array('{', '}'), array('<strong>', '</strong>'), $text);
		return $text;
	}
}
?>