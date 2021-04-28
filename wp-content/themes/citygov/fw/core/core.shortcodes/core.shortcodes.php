<?php
/**
 * CityGov Framework: shortcodes manipulations
 *
 * @package	citygov
 * @since	citygov 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Theme init
if (!function_exists('citygov_sc_theme_setup')) {
	add_action( 'citygov_action_init_theme', 'citygov_sc_theme_setup', 1 );
	function citygov_sc_theme_setup() {
		// Add sc stylesheets
		add_action('citygov_action_add_styles', 'citygov_sc_add_styles', 1);
	}
}

if (!function_exists('citygov_sc_theme_setup2')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_sc_theme_setup2' );
	function citygov_sc_theme_setup2() {

		if ( !is_admin() || isset($_POST['action']) ) {
			// Enable/disable shortcodes in excerpt
			add_filter('the_excerpt', 					'citygov_sc_excerpt_shortcodes');
	
			// Prepare shortcodes in the content
			if (function_exists('citygov_sc_prepare_content')) citygov_sc_prepare_content();
		}

		// Add init script into shortcodes output in VC frontend editor
		add_filter('citygov_shortcode_output', 'citygov_sc_add_scripts', 10, 4);

		// AJAX: Send contact form data
		add_action('wp_ajax_send_form',			'citygov_sc_form_send');
		add_action('wp_ajax_nopriv_send_form',	'citygov_sc_form_send');

		// Show shortcodes list in admin editor
		add_action('media_buttons',				'citygov_sc_selector_add_in_toolbar', 11);

	}
}


// Register shortcodes styles
if ( !function_exists( 'citygov_sc_add_styles' ) ) {
	//add_action('citygov_action_add_styles', 'citygov_sc_add_styles', 1);
	function citygov_sc_add_styles() {
		// Shortcodes
		citygov_enqueue_style( 'citygov-shortcodes-style',	citygov_get_file_url('shortcodes/theme.shortcodes.css'), array(), null );
	}
}


// Register shortcodes init scripts
if ( !function_exists( 'citygov_sc_add_scripts' ) ) {
	//add_filter('citygov_shortcode_output', 'citygov_sc_add_scripts', 10, 4);
	function citygov_sc_add_scripts($output, $tag='', $atts=array(), $content='') {

		if (citygov_storage_empty('shortcodes_scripts_added')) {
			citygov_storage_set('shortcodes_scripts_added', true);
			//citygov_enqueue_style( 'citygov-shortcodes-style', citygov_get_file_url('shortcodes/theme.shortcodes.css'), array(), null );
			citygov_enqueue_script( 'citygov-shortcodes-script', citygov_get_file_url('shortcodes/theme.shortcodes.js'), array('jquery'), null, true );	
		}
		
		return $output;
	}
}


/* Prepare text for shortcodes
-------------------------------------------------------------------------------- */

// Prepare shortcodes in content
if (!function_exists('citygov_sc_prepare_content')) {
	function citygov_sc_prepare_content() {
		if (function_exists('citygov_sc_clear_around')) {
			$filters = array(
				array('citygov', 'sc', 'clear', 'around'),
				array('widget', 'text'),
				array('the', 'excerpt'),
				array('the', 'content')
			);
			if (function_exists('citygov_exists_woocommerce') && citygov_exists_woocommerce()) {
				$filters[] = array('woocommerce', 'template', 'single', 'excerpt');
				$filters[] = array('woocommerce', 'short', 'description');
			}
			if (is_array($filters) && count($filters) > 0) {
				foreach ($filters as $flt)
					add_filter(join('_', $flt), 'citygov_sc_clear_around', 1);	// Priority 1 to clear spaces before do_shortcodes()
			}
		}
	}
}

// Enable/Disable shortcodes in the excerpt
if (!function_exists('citygov_sc_excerpt_shortcodes')) {
	function citygov_sc_excerpt_shortcodes($content) {
		if (!empty($content)) {
			$content = do_shortcode($content);
			//$content = strip_shortcodes($content);
		}
		return $content;
	}
}



if (!function_exists('citygov_sc_clear_around')) {
	function citygov_sc_clear_around($content) {
		if (!empty($content)) $content = preg_replace("/\](\s|\n|\r)*\[/", "][", $content);
		return $content;
	}
}






/* Shortcodes support utils
---------------------------------------------------------------------- */

// CityGov shortcodes load scripts
if (!function_exists('citygov_sc_load_scripts')) {
	function citygov_sc_load_scripts() {
		citygov_enqueue_script( 'citygov-shortcodes-script', citygov_get_file_url('core/core.shortcodes/shortcodes_admin.js'), array('jquery'), null, true );
		citygov_enqueue_script( 'citygov-selection-script',  citygov_get_file_url('js/jquery.selection.js'), array('jquery'), null, true );
	}
}

// CityGov shortcodes prepare scripts
if (!function_exists('citygov_sc_prepare_scripts')) {
	function citygov_sc_prepare_scripts() {
		if (!citygov_storage_isset('shortcodes_prepared')) {
			citygov_storage_set('shortcodes_prepared', true);
			$json_parse_func = 'eval';	// 'JSON.parse'
			?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					try {
						CITYGOV_STORAGE['shortcodes'] = <?php echo trim($json_parse_func); ?>(<?php echo json_encode( citygov_array_prepare_to_json(citygov_storage_get('shortcodes')) ); ?>);
					} catch (e) {}
					CITYGOV_STORAGE['shortcodes_cp'] = '<?php echo is_admin() ? (!citygov_storage_empty('to_colorpicker') ? citygov_storage_get('to_colorpicker') : 'wp') : 'custom'; ?>';	// wp | tiny | custom
				});
			</script>
			<?php
		}
	}
}

// Show shortcodes list in admin editor
if (!function_exists('citygov_sc_selector_add_in_toolbar')) {
	//add_action('media_buttons','citygov_sc_selector_add_in_toolbar', 11);
	function citygov_sc_selector_add_in_toolbar(){

		if ( !citygov_options_is_used() ) return;

		citygov_sc_load_scripts();
		citygov_sc_prepare_scripts();

		$shortcodes = citygov_storage_get('shortcodes');
		$shortcodes_list = '<select class="sc_selector"><option value="">&nbsp;'.esc_html__('- Select Shortcode -', 'citygov').'&nbsp;</option>';

		if (is_array($shortcodes) && count($shortcodes) > 0) {
			foreach ($shortcodes as $idx => $sc) {
				$shortcodes_list .= '<option value="'.esc_attr($idx).'" title="'.esc_attr($sc['desc']).'">'.esc_html($sc['title']).'</option>';
			}
		}

		$shortcodes_list .= '</select>';

		echo trim($shortcodes_list);
	}
}

// CityGov shortcodes builder settings
require_once get_template_directory().'/fw/core/core.shortcodes/shortcodes_settings.php';

// VC shortcodes settings
if ( class_exists('WPBakeryShortCode') ) {
    require_once get_template_directory().'/fw/core/core.shortcodes/shortcodes_vc.php';
}

// CityGov shortcodes implementation
citygov_autoload_folder( 'shortcodes/trx_basic' );
citygov_autoload_folder( 'shortcodes/trx_optional' );
?>