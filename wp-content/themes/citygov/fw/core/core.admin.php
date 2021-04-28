<?php
/**
 * CityGov Framework: Admin functions
 *
 * @package	citygov
 * @since	citygov 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Admin actions and filters:
------------------------------------------------------------------------ */

if (is_admin()) {

	/* Theme setup section
	-------------------------------------------------------------------- */
	
	if ( !function_exists( 'citygov_admin_theme_setup' ) ) {
		add_action( 'citygov_action_before_init_theme', 'citygov_admin_theme_setup', 11 );
		function citygov_admin_theme_setup() {
			if ( is_admin() ) {
				add_action("admin_head",			'citygov_admin_prepare_scripts');
				add_action("admin_enqueue_scripts",	'citygov_admin_load_scripts');
				add_action('tgmpa_register',		'citygov_admin_register_plugins');

				// AJAX: Get terms for specified post type
				add_action('wp_ajax_citygov_admin_change_post_type', 		'citygov_callback_admin_change_post_type');
				add_action('wp_ajax_nopriv_citygov_admin_change_post_type','citygov_callback_admin_change_post_type');
			}
		}
	}
	
	// Load required styles and scripts for admin mode
	if ( !function_exists( 'citygov_admin_load_scripts' ) ) {
		//add_action("admin_enqueue_scripts", 'citygov_admin_load_scripts');
		function citygov_admin_load_scripts() {
			citygov_enqueue_script( 'citygov-debug-script', citygov_get_file_url('js/core.debug.js'), array('jquery'), null, true );
			//if (citygov_options_is_used()) {
				citygov_enqueue_style( 'citygov-admin-style', citygov_get_file_url('css/core.admin.css'), array(), null );
				citygov_enqueue_script( 'citygov-admin-script', citygov_get_file_url('js/core.admin.js'), array('jquery'), null, true );
			//}
			if (citygov_strpos($_SERVER['REQUEST_URI'], 'widgets.php')!==false) {
				citygov_enqueue_style( 'citygov-fontello-style', citygov_get_file_url('css/fontello-admin/css/fontello-admin.css'), array(), null );
				citygov_enqueue_style( 'citygov-animations-style', citygov_get_file_url('css/fontello-admin/css/animation.css'), array(), null );
			}
		}
	}
	
	// Prepare required styles and scripts for admin mode
	if ( !function_exists( 'citygov_admin_prepare_scripts' ) ) {
		//add_action("admin_head", 'citygov_admin_prepare_scripts');
		function citygov_admin_prepare_scripts() {
			?>
			<script>
				if (typeof CITYGOV_STORAGE == 'undefined') var CITYGOV_STORAGE = {};
				CITYGOV_STORAGE['admin_mode']	= true;
				CITYGOV_STORAGE['ajax_nonce'] 	= "<?php echo esc_attr(wp_create_nonce(admin_url('admin-ajax.php'))); ?>";
				CITYGOV_STORAGE['ajax_url']	= "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
				CITYGOV_STORAGE['ajax_error']	= "<?php esc_html_e('Invalid server answer', 'citygov'); ?>";
				CITYGOV_STORAGE['user_logged_in'] = true;
			</script>
			<?php
		}
	}
	
	// AJAX: Get terms for specified post type
	if ( !function_exists( 'citygov_callback_admin_change_post_type' ) ) {
		//add_action('wp_ajax_citygov_admin_change_post_type', 		'citygov_callback_admin_change_post_type');
		//add_action('wp_ajax_nopriv_citygov_admin_change_post_type',	'citygov_callback_admin_change_post_type');
		function citygov_callback_admin_change_post_type() {
			if ( !wp_verify_nonce( citygov_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
				die();
			$post_type = $_REQUEST['post_type'];
			$terms = citygov_get_list_terms(false, citygov_get_taxonomy_categories_by_post_type($post_type));
			$terms = citygov_array_merge(array(0 => esc_html__('- Select category -', 'citygov')), $terms);
			$response = array(
				'error' => '',
				'data' => array(
					'ids' => array_keys($terms),
					'titles' => array_values($terms)
				)
			);
			echo json_encode($response);
			die();
		}
	}

	// Return current post type in dashboard
	if ( !function_exists( 'citygov_admin_get_current_post_type' ) ) {
		function citygov_admin_get_current_post_type() {
			global $post, $typenow, $current_screen;
			if ( $post && $post->post_type )							//we have a post so we can just get the post type from that
				return $post->post_type;
			else if ( $typenow )										//check the global $typenow — set in admin.php
				return $typenow;
			else if ( $current_screen && $current_screen->post_type )	//check the global $current_screen object — set in sceen.php
				return $current_screen->post_type;
			else if ( isset( $_REQUEST['post_type'] ) )					//check the post_type querystring
				return sanitize_key( $_REQUEST['post_type'] );
			else if ( isset( $_REQUEST['post'] ) ) {					//lastly check the post id querystring
				$post = get_post( sanitize_key( $_REQUEST['post'] ) );
				return !empty($post->post_type) ? $post->post_type : '';
			} else														//we do not know the post type!
				return '';
		}
	}

	// Add admin menu pages
	if ( !function_exists( 'citygov_admin_add_menu_item' ) ) {
		function citygov_admin_add_menu_item($mode, $item, $pos='100') {
			static $shift = 0;
			if ($pos=='100') $pos .= '.'.$shift++;
			$fn = join('_', array('add', $mode, 'page'));
			if (empty($item['parent']))
				$fn($item['page_title'], $item['menu_title'], $item['capability'], $item['menu_slug'], $item['callback'], $item['icon'], $pos);
			else
				$fn($item['parent'], $item['page_title'], $item['menu_title'], $item['capability'], $item['menu_slug'], $item['callback'], $item['icon'], $pos);
		}
	}
	
	// Register optional plugins
	if ( !function_exists( 'citygov_admin_register_plugins' ) ) {
		function citygov_admin_register_plugins() {

			$plugins = apply_filters('citygov_filter_required_plugins', array(
				array(
					'name' 		=> esc_html__('CityGov Utilities', 'citygov'),
					'version'	=> '2.6',				// Minimal required version
					'slug' 		=> 'trx_utils',
					'source'	=> citygov_get_file_dir('plugins/install/trx_utils.zip'),
					'required' 	=> true
				),
                array(
                    'name'   => esc_html__('WordPress Social Login', 'citygov'),
                    'slug'   => 'wordpress-social-login',
                    'required'  => false
                )
			));
			$config = array(
				'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
				'default_path' => '',                      // Default absolute path to bundled plugins.
				'menu'         => 'tgmpa-install-plugins', // Menu slug.
				'parent_slug'  => 'themes.php',            // Parent menu slug.
				'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
				'has_notices'  => true,                    // Show admin notices or not.
				'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
				'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
				'is_automatic' => true,                    // Automatically activate plugins after installation or not.
				'message'      => ''                       // Message to output right before the plugins table.
			);
	
			tgmpa( $plugins, $config );
		}
	}

    require_once get_template_directory().'/fw/lib/tgm/class-tgm-plugin-activation.php';

    require_once get_template_directory().'/fw/tools/emailer/emailer.php';
    require_once get_template_directory().'/fw/tools/po_composer/po_composer.php';
}

?>