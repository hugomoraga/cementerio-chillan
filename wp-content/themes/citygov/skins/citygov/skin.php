<?php
/**
 * Skin file for the theme.
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Theme init
if (!function_exists('citygov_action_skin_theme_setup')) {
    add_action( 'citygov_action_init_theme', 'citygov_action_skin_theme_setup', 1 );
    function citygov_action_skin_theme_setup() {

        // Add skin fonts in the used fonts list
        add_filter('citygov_filter_used_fonts',			'citygov_filter_skin_used_fonts');
        // Add skin fonts (from Google fonts) in the main fonts list (if not present).
        add_filter('citygov_filter_list_fonts',			'citygov_filter_skin_list_fonts');

        // Add skin stylesheets
        add_action('citygov_action_add_styles',			'citygov_action_skin_add_styles');
        // Add skin inline styles
        add_filter('citygov_filter_add_styles_inline',		'citygov_filter_skin_add_styles_inline');
        // Add skin responsive styles
        add_action('citygov_action_add_responsive',		'citygov_action_skin_add_responsive');
        // Add skin responsive inline styles
        add_filter('citygov_filter_add_responsive_inline',	'citygov_filter_skin_add_responsive_inline');

        // Add skin scripts
        add_action('citygov_action_add_scripts',			'citygov_action_skin_add_scripts');
        // Add skin scripts inline
        add_action('citygov_action_add_scripts_inline',	'citygov_action_skin_add_scripts_inline');

        // Add skin less files into list for compilation
        add_filter('citygov_filter_compile_less',			'citygov_filter_skin_compile_less');


        /* Color schemes

        // Accenterd colors
        accent1			- theme accented color 1
        accent1_hover	- theme accented color 1 (hover state)
        accent2			- theme accented color 2
        accent2_hover	- theme accented color 2 (hover state)
        accent3			- theme accented color 3
        accent3_hover	- theme accented color 3 (hover state)

        // Headers, text and links
        text			- main content
        text_light		- post info
        text_dark		- headers
        inverse_text	- text on accented background
        inverse_light	- post info on accented background
        inverse_dark	- headers on accented background
        inverse_link	- links on accented background
        inverse_hover	- hovered links on accented background

        // Block's border and background
        bd_color		- border for the entire block
        bg_color		- background color for the entire block
        bg_image, bg_image_position, bg_image_repeat, bg_image_attachment  - first background image for the entire block
        bg_image2,bg_image2_position,bg_image2_repeat,bg_image2_attachment - second background image for the entire block

        // Alternative colors - highlight blocks, form fields, etc.
        alter_text		- text on alternative background
        alter_light		- post info on alternative background
        alter_dark		- headers on alternative background
        alter_link		- links on alternative background
        alter_hover		- hovered links on alternative background
        alter_bd_color	- alternative border
        alter_bd_hover	- alternative border for hovered state or active field
        alter_bg_color	- alternative background
        alter_bg_hover	- alternative background for hovered state or active field
        alter_bg_image, alter_bg_image_position, alter_bg_image_repeat, alter_bg_image_attachment - background image for the alternative block

        */

        // Add color schemes
        citygov_add_color_scheme('original', array(

                'title'					=> esc_html__('Original', 'citygov'),

                // Accent colors
                'accent1'				=> '#104382',
                'accent1_hover'			=> '#cc123f',


                // Headers, text and links colors
                'text'					=> '#616161',
                'text_light'			=> '#75777a',
                'text_dark'				=> '#1f252b',
                'inverse_text'			=> '#ffffff',
                'inverse_light'			=> '#b5133a',       //
                'inverse_dark'			=> '#012655',       //
                'inverse_link'			=> '#d4d5d5',       //
                'inverse_hover'			=> '#c9cacb',       //

                // Whole block border and background
                'bd_color'				=> '#dfdfdf',
                'bg_color'				=> '#f3f3f3',
                'bg_image'				=> '',
                'bg_image_position'		=> 'left top',
                'bg_image_repeat'		=> 'repeat',
                'bg_image_attachment'	=> 'scroll',
                'bg_image2'				=> '',
                'bg_image2_position'	=> 'left top',
                'bg_image2_repeat'		=> 'repeat',
                'bg_image2_attachment'	=> 'scroll',

                // Alternative blocks (submenu items, form's fields, etc.)
                'alter_text'			=> '#919191',       //
                'alter_light'			=> '#c2c2c2',       //
                'alter_dark'			=> '#053775',       //
                'alter_link'			=> '#efefef',       //
                'alter_hover'			=> '#ebedef',       //
                'alter_bd_color'		=> '#dcdcdc',       //input border
                'alter_bd_hover'		=> '#7c7c7c',       //socilas
                'alter_bg_color'		=> '#f8f8f8',
                'alter_bg_hover'		=> '#f0f0f0',
                'alter_bg_image'			=> '',
                'alter_bg_image_position'	=> 'left top',
                'alter_bg_image_repeat'		=> 'repeat',
                'alter_bg_image_attachment'	=> 'scroll',
            )
        );


        citygov_add_color_scheme('yellow', array(

                'title'					=> esc_html__('Yellow', 'citygov'),

                // Accent colors
                'accent1'				=> '#011424',
                'accent1_hover'			=> '#fabd27',


                // Headers, text and links colors
                'text'					=> '#616161',
                'text_light'			=> '#75777a',
                'text_dark'				=> '#1f252b',
                'inverse_text'			=> '#ffffff',
                'inverse_light'			=> '#fabd27',       //
                'inverse_dark'			=> '#000b14',       //
                'inverse_link'			=> '#d4d5d5',       //
                'inverse_hover'			=> '#c9cacb',       //

                // Whole block border and background
                'bd_color'				=> '#dfdfdf',
                'bg_color'				=> '#f3f3f3',
                'bg_image'				=> '',
                'bg_image_position'		=> 'left top',
                'bg_image_repeat'		=> 'repeat',
                'bg_image_attachment'	=> 'scroll',
                'bg_image2'				=> '',
                'bg_image2_position'	=> 'left top',
                'bg_image2_repeat'		=> 'repeat',
                'bg_image2_attachment'	=> 'scroll',

                // Alternative blocks (submenu items, form's fields, etc.)
                'alter_text'			=> '#919191',       //
                'alter_light'			=> '#c2c2c2',       //
                'alter_dark'			=> '#000b14',       //
                'alter_link'			=> '#efefef',       //
                'alter_hover'			=> '#ebedef',       //
                'alter_bd_color'		=> '#dcdcdc',       //input border
                'alter_bd_hover'		=> '#7c7c7c',       //socilas
                'alter_bg_color'		=> '#f8f8f8',
                'alter_bg_hover'		=> '#f0f0f0',
                'alter_bg_image'			=> '',
                'alter_bg_image_position'	=> 'left top',
                'alter_bg_image_repeat'		=> 'repeat',
                'alter_bg_image_attachment'	=> 'scroll',
            )
        );

        citygov_add_color_scheme('blue', array(

                'title'					=> esc_html__('Blue', 'citygov'),

                // Accent colors
                'accent1'				=> '#4b91c2',
                'accent1_hover'			=> '#fc8800',


                // Headers, text and links colors
                'text'					=> '#616161',
                'text_light'			=> '#75777a',
                'text_dark'				=> '#1f252b',
                'inverse_text'			=> '#ffffff',
                'inverse_light'			=> '#fc8800',       //
                'inverse_dark'			=> '#1e6ea5',       //
                'inverse_link'			=> '#d4d5d5',       //
                'inverse_hover'			=> '#c9cacb',       //

                // Whole block border and background
                'bd_color'				=> '#dfdfdf',
                'bg_color'				=> '#f3f3f3',
                'bg_image'				=> '',
                'bg_image_position'		=> 'left top',
                'bg_image_repeat'		=> 'repeat',
                'bg_image_attachment'	=> 'scroll',
                'bg_image2'				=> '',
                'bg_image2_position'	=> 'left top',
                'bg_image2_repeat'		=> 'repeat',
                'bg_image2_attachment'	=> 'scroll',

                // Alternative blocks (submenu items, form's fields, etc.)
                'alter_text'			=> '#919191',       //
                'alter_light'			=> '#c2c2c2',       //
                'alter_dark'			=> '#1e6ea5',       //
                'alter_link'			=> '#efefef',       //
                'alter_hover'			=> '#ebedef',       //
                'alter_bd_color'		=> '#dcdcdc',       //input border
                'alter_bd_hover'		=> '#7c7c7c',       //socilas
                'alter_bg_color'		=> '#f8f8f8',
                'alter_bg_hover'		=> '#f0f0f0',
                'alter_bg_image'			=> '',
                'alter_bg_image_position'	=> 'left top',
                'alter_bg_image_repeat'		=> 'repeat',
                'alter_bg_image_attachment'	=> 'scroll',
            )
        );


        citygov_add_color_scheme('black', array(

                'title'					=> esc_html__('Black', 'citygov'),

                // Accent colors
                'accent1'				=> '#484848',
                'accent1_hover'			=> '#0ba3c7',


                // Headers, text and links colors
                'text'					=> '#616161',
                'text_light'			=> '#75777a',
                'text_dark'				=> '#1f252b',
                'inverse_text'			=> '#ffffff',
                'inverse_light'			=> '#0ba3c7',       //
                'inverse_dark'			=> '#1e6ea5',       //
                'inverse_link'			=> '#d4d5d5',       //
                'inverse_hover'			=> '#c9cacb',       //

                // Whole block border and background
                'bd_color'				=> '#dfdfdf',
                'bg_color'				=> '#f3f3f3',
                'bg_image'				=> '',
                'bg_image_position'		=> 'left top',
                'bg_image_repeat'		=> 'repeat',
                'bg_image_attachment'	=> 'scroll',
                'bg_image2'				=> '',
                'bg_image2_position'	=> 'left top',
                'bg_image2_repeat'		=> 'repeat',
                'bg_image2_attachment'	=> 'scroll',

                // Alternative blocks (submenu items, form's fields, etc.)
                'alter_text'			=> '#919191',       //
                'alter_light'			=> '#c2c2c2',       //
                'alter_dark'			=> '#292929',       //
                'alter_link'			=> '#efefef',       //
                'alter_hover'			=> '#ebedef',       //
                'alter_bd_color'		=> '#dcdcdc',       //input border
                'alter_bd_hover'		=> '#7c7c7c',       //socilas
                'alter_bg_color'		=> '#f8f8f8',
                'alter_bg_hover'		=> '#f0f0f0',
                'alter_bg_image'			=> '',
                'alter_bg_image_position'	=> 'left top',
                'alter_bg_image_repeat'		=> 'repeat',
                'alter_bg_image_attachment'	=> 'scroll',
            )
        );

        /* Font slugs:
        h1 ... h6	- headers
        p			- plain text
        link		- links
        info		- info blocks (Posted 15 May, 2015 by John Doe)
        menu		- main menu
        submenu		- dropdown menus
        logo		- logo text
        button		- button's caption
        input		- input fields
        */

        // Add Custom fonts
        citygov_add_custom_font('h1', array(
                'title'			=> esc_html__('Heading 1', 'citygov'),
                'description'	=> '',
                'font-family'	=> 'Montserrat',
                'font-size' 	=> '4.118em',
                'font-weight'	=> '400',
                'font-style'	=> '',
                'line-height'	=> '1.3em',
                'margin-top'	=> '0.5em',
                'margin-bottom'	=> '0.4em'
            )
        );
        citygov_add_custom_font('h2', array(
                'title'			=> esc_html__('Heading 2', 'citygov'),
                'description'	=> '',
                'font-family'	=> 'Montserrat',
                'font-size' 	=> '2.941em',
                'font-weight'	=> '400',
                'font-style'	=> '',
                'line-height'	=> '1.2em',
                'margin-top'	=> '0',
                'margin-bottom'	=> '0.08em'
            )
        );
        citygov_add_custom_font('h3', array(
                'title'			=> esc_html__('Heading 3', 'citygov'),
                'description'	=> '',
                'font-family'	=> 'Montserrat',
                'font-size' 	=> '2.353em',
                'font-weight'	=> '700',
                'font-style'	=> '',
                'line-height'	=> '1.3em',
                'margin-top'	=> '2.6em',
                'margin-bottom'	=> '1.02em'
            )
        );
        citygov_add_custom_font('h4', array(
                'title'			=> esc_html__('Heading 4', 'citygov'),
                'description'	=> '',
                'font-family'	=> '',
                'font-size' 	=> '2.059em',
                'font-weight'	=> '500',
                'font-style'	=> '',
                'line-height'	=> '1.3em',
                'margin-top'	=> '1.2em',
                'margin-bottom'	=> '0.28em'
            )
        );
        citygov_add_custom_font('h5', array(
                'title'			=> esc_html__('Heading 5', 'citygov'),
                'description'	=> '',
                'font-family'	=> '',
                'font-size' 	=> '1.353em',
                'font-weight'	=> '500',
                'font-style'	=> '',
                'line-height'	=> '1.3em',
                'margin-top'	=> '1.2em',
                'margin-bottom'	=> '0.62em'
            )
        );
        citygov_add_custom_font('h6', array(
                'title'			=> esc_html__('Heading 6', 'citygov'),
                'description'	=> '',
                'font-family'	=> 'Montserrat',
                'font-size' 	=> '1.176em',
                'font-weight'	=> '700',
                'font-style'	=> '',
                'line-height'	=> '1.3em',
                'margin-top'	=> '1.25em',
                'margin-bottom'	=> '0.65em'
            )
        );
        citygov_add_custom_font('p', array(
                'title'			=> esc_html__('Text', 'citygov'),
                'description'	=> '',
                'font-family'	=> 'Hind',
                'font-size' 	=> '17px',
                'font-weight'	=> '400',
                'font-style'	=> '',
                'line-height'	=> '1.53em',
                'margin-top'	=> '',
                'margin-bottom'	=> '1em'
            )
        );
        citygov_add_custom_font('link', array(
                'title'			=> esc_html__('Links', 'citygov'),
                'description'	=> '',
                'font-family'	=> '',
                'font-size' 	=> '',
                'font-weight'	=> '',
                'font-style'	=> ''
            )
        );
        citygov_add_custom_font('info', array(
                'title'			=> esc_html__('Post info', 'citygov'),
                'description'	=> '',
                'font-family'	=> '',
                'font-size' 	=> '0.8235em',
                'font-weight'	=> '',
                'font-style'	=> '',
                'line-height'	=> '1.2857em',
                'margin-top'	=> '',
                'margin-bottom'	=> '2.4em'
            )
        );
        citygov_add_custom_font('menu', array(
                'title'			=> esc_html__('Main menu items', 'citygov'),
                'description'	=> '',
                'font-family'	=> 'Montserrat',
                'font-size' 	=> '0.882em',
                'font-weight'	=> '700',
                'font-style'	=> '',
                'line-height'	=> '2em',
                'margin-top'	=> '1.8em',
                'margin-bottom'	=> '2.2em'
            )
        );

        citygov_add_custom_font('submenu', array(
                'title'			=> esc_html__('Dropdown menu items', 'citygov'),
                'description'	=> '',
                'font-family'	=> 'Hind',
                'font-size' 	=> '0.8823em',
                'font-weight'	=> '',
                'font-style'	=> '',
                'line-height'	=> '3em',
                'margin-top'	=> '',
                'margin-bottom'	=> ''
            )
        );
        citygov_add_custom_font('logo', array(
                'title'			=> esc_html__('Logo', 'citygov'),
                'description'	=> '',
                'font-family'	=> 'Montserrat',
                'font-size' 	=> '0.647em',
                'font-weight'	=> '700',
                'font-style'	=> '',
                'line-height'	=> '1.092em',
                'margin-top'	=> '2.05em',
                'margin-bottom'	=> '0.9em'
            )
        );
        citygov_add_custom_font('button', array(
                'title'			=> esc_html__('Buttons', 'citygov'),
                'description'	=> '',
                'font-family'	=> 'Montserrat',
                'font-size' 	=> '0.7647em',
                'font-weight'	=> '700',
                'font-style'	=> '',
                'line-height'	=> '1.2857em'
            )
        );
        citygov_add_custom_font('input', array(
                'title'			=> esc_html__('Input fields', 'citygov'),
                'description'	=> '',
                'font-family'	=> '',
                'font-size' 	=> '',
                'font-weight'	=> '',
                'font-style'	=> '',
                'line-height'	=> '1.2857em'
            )
        );

    }
}





//------------------------------------------------------------------------------
// Skin's fonts
//------------------------------------------------------------------------------

// Add skin fonts in the used fonts list
if (!function_exists('citygov_filter_skin_used_fonts')) {
    function citygov_filter_skin_used_fonts($theme_fonts) {
        $theme_fonts['Hind'] = 1;
        $theme_fonts['Montserrat'] = 1;
        $theme_fonts['Damion'] = 1;
        return $theme_fonts;
    }
}

// Add skin fonts (from Google fonts) in the main fonts list (if not present).
// To use custom font-face you not need add it into list in this function
// How to install custom @font-face fonts into the theme?
// All @font-face fonts are located in "theme_name/css/font-face/" folder in the separate subfolders for the each font. Subfolder name is a font-family name!
// Place full set of the font files (for each font style and weight) and css-file named stylesheet.css in the each subfolder.
// Create your @font-face kit by using Fontsquirrel @font-face Generator (http://www.fontsquirrel.com/fontface/generator)
// and then extract the font kit (with folder in the kit) into the "theme_name/css/font-face" folder to install
if (!function_exists('citygov_filter_skin_list_fonts')) {
    function citygov_filter_skin_list_fonts($list) {
        if (!isset($list['Hind']))	$list['Hind'] = array('family'=>'sans-serif', 'link' => 'Hind:400,500');
        if (!isset($list['Montserrat']))	$list['Montserrat'] = array('family'=>'sans-serif', 'link' => 'Montserrat:400,700');
        if (!isset($list['Damion']))	$list['Damion'] = array('family'=>'cursive', 'link' => 'Damion');

        return $list;
    }
}



//------------------------------------------------------------------------------
// Skin's stylesheets
//------------------------------------------------------------------------------
// Add skin stylesheets
if (!function_exists('citygov_action_skin_add_styles')) {
    function citygov_action_skin_add_styles() {
        citygov_enqueue_style( 'citygov-skin-style', citygov_get_file_url('skin.css'), array(), null );
        if (file_exists(citygov_get_file_dir('skin.customizer.css')))
            citygov_enqueue_style( 'citygov-skin-customizer-style', citygov_get_file_url('skin.customizer.css'), array(), null );
    }
}

// Add skin inline styles
if (!function_exists('citygov_filter_skin_add_styles_inline')) {
    function citygov_filter_skin_add_styles_inline($custom_style) {
        return $custom_style;
    }
}

// Add skin responsive styles
if (!function_exists('citygov_action_skin_add_responsive')) {
    function citygov_action_skin_add_responsive() {
        $suffix = citygov_param_is_off(citygov_get_custom_option('show_sidebar_outer')) ? '' : '-outer';
        if (file_exists(citygov_get_file_dir('skin.responsive'.($suffix).'.css')))
            citygov_enqueue_style( 'theme-skin-responsive-style', citygov_get_file_url('skin.responsive'.($suffix).'.css'), array(), null );
    }
}

// Add skin responsive inline styles
if (!function_exists('citygov_filter_skin_add_responsive_inline')) {
    function citygov_filter_skin_add_responsive_inline($custom_style) {
        return $custom_style;
    }
}

// Add skin.less into list files for compilation
if (!function_exists('citygov_filter_skin_compile_less')) {
    function citygov_filter_skin_compile_less($files) {
        if (file_exists(citygov_get_file_dir('skin.less'))) {
            $files[] = citygov_get_file_dir('skin.less');
        }
        return $files;
    }
}



//------------------------------------------------------------------------------
// Skin's scripts
//------------------------------------------------------------------------------

// Add skin scripts
if (!function_exists('citygov_action_skin_add_scripts')) {
    //add_action('citygov_action_add_scripts', 'citygov_action_skin_add_scripts');
    function citygov_action_skin_add_scripts() {
        if (file_exists(citygov_get_file_dir('skin.js')))
            citygov_enqueue_script( 'theme-skin-script', citygov_get_file_url('skin.js'), array(), null );
        if (citygov_get_theme_option('show_theme_customizer') == 'yes' && file_exists(citygov_get_file_dir('skin.customizer.js')))
            citygov_enqueue_script( 'theme-skin-customizer-script', citygov_get_file_url('skin.customizer.js'), array(), null );
    }
}

// Add skin scripts inline
if (!function_exists('citygov_action_skin_add_scripts_inline')) {
    function citygov_action_skin_add_scripts_inline() {
    }
}
?>