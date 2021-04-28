<?php
/* Calculated fields form support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('citygov_calcfields_form_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_calcfields_form_theme_setup', 1 );
	function citygov_calcfields_form_theme_setup() {
		// Register shortcode in the shortcodes list
		if (citygov_exists_calcfields_form()) {
			add_action('citygov_action_shortcodes_list',				'citygov_calcfields_form_reg_shortcodes');
			if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
				add_action('citygov_action_shortcodes_list_vc',		'citygov_calcfields_form_reg_shortcodes_vc');
			if (is_admin()) {
				add_filter( 'citygov_filter_importer_options',			'citygov_calcfields_form_importer_set_options', 10, 1 );
				add_action( 'citygov_action_importer_params',			'citygov_calcfields_form_importer_show_params', 10, 1 );
				add_action( 'citygov_action_importer_import',			'citygov_calcfields_form_importer_import', 10, 2 );
				add_action( 'citygov_action_importer_import_fields',	'citygov_calcfields_form_importer_import_fields', 10, 1 );
				add_action( 'citygov_action_importer_export',			'citygov_calcfields_form_importer_export', 10, 1 );
				add_action( 'citygov_action_importer_export_fields',	'citygov_calcfields_form_importer_export_fields', 10, 1 );
			}
			add_action('wp_enqueue_scripts', 							'citygov_calcfields_form_frontend_scripts');
		}
		if (is_admin()) {
			add_filter( 'citygov_filter_importer_required_plugins',	'citygov_calcfields_form_importer_required_plugins', 10, 2 );
			add_filter( 'citygov_filter_required_plugins',				'citygov_calcfields_form_required_plugins' );
		}
	}
}

// Check if plugin installed and activated
if ( !function_exists( 'citygov_exists_calcfields_form' ) ) {
	function citygov_exists_calcfields_form() {
		return defined('CP_SCHEME');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'citygov_calcfields_form_required_plugins' ) ) {
	function citygov_calcfields_form_required_plugins($list=array()) {
		if (in_array('calcfields', citygov_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> 'Calculated Fields Form',
					'slug' 		=> 'calculated-fields-form',
					'required' 	=> false
					);
		return $list;
	}
}

// Remove jquery_ui from frontend
if ( !function_exists( 'citygov_calcfields_form_frontend_scripts' ) ) {
	function citygov_calcfields_form_frontend_scripts() {
		// Disable loading JQuery UI CSS

	}
}


// One-click import support
//------------------------------------------------------------------------

// Check in the required plugins
if ( !function_exists( 'citygov_calcfields_form_importer_required_plugins' ) ) {
	function citygov_calcfields_form_importer_required_plugins($not_installed='', $list='') {
		if (citygov_strpos($list, 'calcfields')!==false && !citygov_exists_calcfields_form() )
			$not_installed .= '<br>Calculated Fields Form';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'citygov_calcfields_form_importer_set_options' ) ) {
	function citygov_calcfields_form_importer_set_options($options=array()) {
		if ( in_array('calcfields', citygov_storage_get('required_plugins')) && citygov_exists_calcfields_form() ) {
			$options['file_with_calcfields_form'] = 'demo/calcfields_form.txt';			// Name of the file with Calculated Fields Form data
		}
		return $options;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'citygov_calcfields_form_importer_show_params' ) ) {
	function citygov_calcfields_form_importer_show_params($importer) {
		?>
		<input type="checkbox" <?php echo in_array('calcfields', citygov_storage_get('required_plugins')) && $importer->options['plugins_initial_state'] 
											? 'checked="checked"' 
											: ''; ?> value="1" name="import_calcfields_form" id="import_calcfields_form" /> <label for="import_calcfields_form"><?php esc_html_e('Import Calculated Fields Form', 'citygov'); ?></label><br>
		<?php
	}
}

// Import posts
if ( !function_exists( 'citygov_calcfields_form_importer_import' ) ) {
	function citygov_calcfields_form_importer_import($importer, $action) {
		if ( $action == 'import_calcfields_form' ) {
			$importer->import_dump('calcfields_form', esc_html__('Calculated Fields Form', 'citygov'));
		}
	}
}

// Display import progress
if ( !function_exists( 'citygov_calcfields_form_importer_import_fields' ) ) {
	function citygov_calcfields_form_importer_import_fields($importer) {
		?>
		<tr class="import_calcfields_form">
			<td class="import_progress_item"><?php esc_html_e('Calculated Fields Form', 'citygov'); ?></td>
			<td class="import_progress_status"></td>
		</tr>
		<?php
	}
}

// Export posts
if ( !function_exists( 'citygov_calcfields_form_importer_export' ) ) {
	function citygov_calcfields_form_importer_export($importer) {
		citygov_storage_set('export_calcfields_form', serialize( array(
			CP_CALCULATEDFIELDSF_FORMS_TABLE => $importer->export_dump(CP_CALCULATEDFIELDSF_FORMS_TABLE)
			) )
		);
	}
}

// Display exported data in the fields
if ( !function_exists( 'citygov_calcfields_form_importer_export_fields' ) ) {
	function citygov_calcfields_form_importer_export_fields($importer) {
		?>
		<tr>
			<th align="left"><?php esc_html_e('Calculated Fields Form', 'citygov'); ?></th>
			<td><?php citygov_fpc(citygov_get_file_dir('core/core.importer/export/calcfields_form.txt'), citygov_storage_get('export_calcfields_form')); ?>
				<a download="calcfields_form.txt" href="<?php echo esc_url(citygov_get_file_url('core/core.importer/export/calcfields_form.txt')); ?>"><?php esc_html_e('Download', 'citygov'); ?></a>
			</td>
		</tr>
		<?php
	}
}


// Lists
//------------------------------------------------------------------------

// Return Calculated forms list list, prepended inherit (if need)
if ( !function_exists( 'citygov_get_list_calcfields_form' ) ) {
	function citygov_get_list_calcfields_form($prepend_inherit=false) {
		if (($list = citygov_storage_get('list_calcfields_form'))=='') {
			$list = array();
			if (citygov_exists_calcfields_form()) {
				global $wpdb;
				$rows = $wpdb->get_results( "SELECT id, form_name FROM " . esc_sql($wpdb->prefix . CP_CALCULATEDFIELDSF_FORMS_TABLE) );
				if (is_array($rows) && count($rows) > 0) {
					foreach ($rows as $row) {
						$list[$row->id] = $row->form_name;
					}
				}
			}
			$list = apply_filters('citygov_filter_list_calcfields_form', $list);
			if (citygov_get_theme_setting('use_list_cache')) citygov_storage_set('list_calcfields_form', $list); 
		}
		return $prepend_inherit ? citygov_array_merge(array('inherit' => esc_html__("Inherit", 'citygov')), $list) : $list;
	}
}



// Shortcodes
//------------------------------------------------------------------------

// Register shortcode in the shortcodes list
if (!function_exists('citygov_calcfields_form_reg_shortcodes')) {
	function citygov_calcfields_form_reg_shortcodes() {
		if (citygov_storage_isset('shortcodes')) {

			$forms_list = citygov_get_list_calcfields_form();

			citygov_sc_map_after( 'trx_button', 'CP_CALCULATED_FIELDS', array(
					"title" => esc_html__("Calculated fields form", "citygov"),
					"desc" => esc_html__("Insert calculated fields form", "citygov"),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"id" => array(
							"title" => esc_html__("Form ID", "citygov"),
							"desc" => esc_html__("Select Form to insert into current page", "citygov"),
							"value" => "",
							"size" => "medium",
							"options" => $forms_list,
							"type" => "select"
							)
						)
					)
			);
		}
	}
}


// Register shortcode in the VC shortcodes list
if (!function_exists('citygov_calcfields_form_reg_shortcodes_vc')) {
	function citygov_calcfields_form_reg_shortcodes_vc() {

		$forms_list = citygov_get_list_calcfields_form();

		// Calculated fields form
		vc_map( array(
				"base" => "CP_CALCULATED_FIELDS",
				"name" => esc_html__("Calculated fields form", "citygov"),
				"description" => esc_html__("Insert calculated fields form", "citygov"),
				"category" => esc_html__('Content', 'citygov'),
				'icon' => 'icon_trx_calcfields',
				"class" => "trx_sc_single trx_sc_calcfields",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "id",
						"heading" => esc_html__("Form ID", "citygov"),
						"description" => esc_html__("Select Form to insert into current page", "citygov"),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($forms_list),
						"type" => "dropdown"
					)
				)
			) );
			
		class WPBakeryShortCode_Cp_Calculated_Fields extends CITYGOV_VC_ShortCodeSingle {}

	}
}
?>