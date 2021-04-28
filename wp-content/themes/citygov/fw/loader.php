<?php
/**
 * CityGov Framework
 *
 * @package citygov
 * @since citygov 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Framework directory path from theme root
if ( ! defined( 'CITYGOV_FW_DIR' ) )			define( 'CITYGOV_FW_DIR', 'fw' );

// Theme timing
if ( ! defined( 'CITYGOV_START_TIME' ) )		define( 'CITYGOV_START_TIME', microtime(true));		// Framework start time
if ( ! defined( 'CITYGOV_START_MEMORY' ) )		define( 'CITYGOV_START_MEMORY', memory_get_usage());	// Memory usage before core loading
if ( ! defined( 'CITYGOV_START_QUERIES' ) )	define( 'CITYGOV_START_QUERIES', get_num_queries());	// DB queries used

// Include theme variables storage
require_once get_template_directory().'/fw/core/core.storage.php';

// Theme variables storage
citygov_storage_set('options_prefix', 'citygov');	// Used as prefix for store theme's options in the post meta and wp options
citygov_storage_set('page_template', '');			// Storage for current page template name (used in the inheritance system)
citygov_storage_set('widgets_args', array(			// Arguments to register widgets
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5 class="widget_title">',
		'after_title'   => '</h5>',
	)
);

/* Theme setup section
-------------------------------------------------------------------- */
if ( !function_exists( 'citygov_loader_theme_setup' ) ) {
	add_action( 'after_setup_theme', 'citygov_loader_theme_setup', 20 );
	function citygov_loader_theme_setup() {

		citygov_profiler_add_point(esc_html__('After load theme required files', 'citygov'));

		// Before init theme
		do_action('citygov_action_before_init_theme');

		// Load current values for main theme options
		citygov_load_main_options();

		// Theme core init - only for admin side. In frontend it called from header.php
		if ( is_admin() ) {
			citygov_core_init_theme();
		}
	}
}


/* Include core parts
------------------------------------------------------------------------ */
// Manual load important libraries before load all rest files
// core.strings must be first - we use citygov_str...() in the citygov_get_file_dir()
require_once get_template_directory().'/fw/core/core.strings.php';
// core.files must be first - we use citygov_get_file_dir() to include all rest parts
require_once get_template_directory().'/fw/core/core.files.php';

// Include debug and profiler
require_once get_template_directory().'/fw/core/core.debug.php';

// Include custom theme files
citygov_autoload_folder( 'includes' );

// Include core files
citygov_autoload_folder( 'core' );

// Include theme-specific plugins and post types
citygov_autoload_folder( 'plugins' );

// Include theme templates
citygov_autoload_folder( 'templates' );

// Include theme widgets
citygov_autoload_folder( 'widgets' );
?>