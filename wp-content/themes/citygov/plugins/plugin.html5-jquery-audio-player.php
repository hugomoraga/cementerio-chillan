<?php
/* HTML5 jQuery Audio Player support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('citygov_html5_jquery_audio_player_theme_setup')) {
    add_action( 'citygov_action_before_init_theme', 'citygov_html5_jquery_audio_player_theme_setup' );
    function citygov_html5_jquery_audio_player_theme_setup() {
        // Add shortcode in the shortcodes list
        if (citygov_exists_html5_jquery_audio_player()) {
			add_action('citygov_action_add_styles',					'citygov_html5_jquery_audio_player_frontend_scripts' );
            add_action('citygov_action_shortcodes_list',				'citygov_html5_jquery_audio_player_reg_shortcodes');
			if (function_exists('citygov_exists_visual_composer') && citygov_exists_visual_composer())
	            add_action('citygov_action_shortcodes_list_vc',		'citygov_html5_jquery_audio_player_reg_shortcodes_vc');
            if (is_admin()) {
                add_filter( 'citygov_filter_importer_options',			'citygov_html5_jquery_audio_player_importer_set_options', 10, 1 );
                add_action( 'citygov_action_importer_params',			'citygov_html5_jquery_audio_player_importer_show_params', 10, 1 );
                add_action( 'citygov_action_importer_import',			'citygov_html5_jquery_audio_player_importer_import', 10, 2 );
				add_action( 'citygov_action_importer_import_fields',	'citygov_html5_jquery_audio_player_importer_import_fields', 10, 1 );
                add_action( 'citygov_action_importer_export',			'citygov_html5_jquery_audio_player_importer_export', 10, 1 );
                add_action( 'citygov_action_importer_export_fields',	'citygov_html5_jquery_audio_player_importer_export_fields', 10, 1 );
            }
        }
        if (is_admin()) {
            add_filter( 'citygov_filter_importer_required_plugins',	'citygov_html5_jquery_audio_player_importer_required_plugins', 10, 2 );
            add_filter( 'citygov_filter_required_plugins',				'citygov_html5_jquery_audio_player_required_plugins' );
        }
    }
}

// Check if plugin installed and activated
if ( !function_exists( 'citygov_exists_html5_jquery_audio_player' ) ) {
	function citygov_exists_html5_jquery_audio_player() {
		return function_exists('hmp_db_create');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'citygov_html5_jquery_audio_player_required_plugins' ) ) {
	function citygov_html5_jquery_audio_player_required_plugins($list=array()) {
		$list[] = array(
					'name' 		=> 'HTML5 jQuery Audio Player',
					'slug' 		=> 'html5-jquery-audio-player',
					'required' 	=> false
				);
		return $list;
	}
}

// Enqueue custom styles
if ( !function_exists( 'citygov_html5_jquery_audio_player_frontend_scripts' ) ) {
	//add_action( 'citygov_action_add_styles', 'citygov_html5_jquery_audio_player_frontend_scripts' );
	function citygov_html5_jquery_audio_player_frontend_scripts() {
		if (file_exists(citygov_get_file_dir('css/plugin.html5-jquery-audio-player.css'))) {
			citygov_enqueue_style( 'citygov-plugin.html5-jquery-audio-player-style',  citygov_get_file_url('css/plugin.html5-jquery-audio-player.css'), array(), null );
		}
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check HTML5 jQuery Audio Player in the required plugins
if ( !function_exists( 'citygov_html5_jquery_audio_player_importer_required_plugins' ) ) {
	//add_filter( 'citygov_filter_importer_required_plugins',	'citygov_html5_jquery_audio_player_importer_required_plugins', 10, 2 );
	function citygov_html5_jquery_audio_player_importer_required_plugins($not_installed='', $importer=null) {
		if (citygov_strpos($list, 'html5_jquery_audio_player')!==false && !citygov_exists_html5_jquery_audio_player() )
			$not_installed .= '<br>HTML5 jQuery Audio Player';
		return $not_installed;
	}
}


// Set options for one-click importer
if ( !function_exists( 'citygov_html5_jquery_audio_player_importer_set_options' ) ) {
    //add_filter( 'citygov_filter_importer_options',	'citygov_html5_jquery_audio_player_importer_set_options', 10, 1 );
    function citygov_html5_jquery_audio_player_importer_set_options($options=array()) {
		if ( in_array('html5_jquery_audio_player', citygov_storage_get('required_plugins')) && citygov_exists_html5_jquery_audio_player() ) {
            $options['file_with_html5_jquery_audio_player'] = 'demo/html5_jquery_audio_player.txt';			// Name of the file with HTML5 jQuery Audio Player data
            $options['additional_options'][] = 'showbuy';		// Add slugs to export options for this plugin
            $options['additional_options'][] = 'buy_text';		// Add slugs to export options for this plugin
            $options['additional_options'][] = 'showlist';		// Add slugs to export options for this plugin
            $options['additional_options'][] = 'autoplay';		// Add slugs to export options for this plugin
            $options['additional_options'][] = 'tracks';		// Add slugs to export options for this plugin
            $options['additional_options'][] = 'currency';		// Add slugs to export options for this plugin
            $options['additional_options'][] = 'color';		    // Add slugs to export options for this plugin
            $options['additional_options'][] = 'tcolor';		// Add slugs to export options for this plugin
        }
        return $options;
    }
}

// Add checkbox to the one-click importer
if ( !function_exists( 'citygov_html5_jquery_audio_player_importer_show_params' ) ) {
    //add_action( 'citygov_action_importer_params',	'citygov_html5_jquery_audio_player_importer_show_params', 10, 1 );
    function citygov_html5_jquery_audio_player_importer_show_params($importer) {
        ?>
        <input type="checkbox" <?php echo in_array('html5_jquery_audio_player', citygov_storage_get('required_plugins')) && $importer->options['plugins_initial_state']
											? 'checked="checked"' 
											: ''; ?> value="1" name="import_html5_jquery_audio_player" id="import_html5_jquery_audio_player" /> <label for="import_html5_jquery_audio_player"><?php esc_html_e('Import HTML5 jQuery Audio Player', 'citygov'); ?></label><br>
    <?php
    }
}


// Import posts
if ( !function_exists( 'citygov_html5_jquery_audio_player_importer_import' ) ) {
    //add_action( 'citygov_action_importer_import',	'citygov_html5_jquery_audio_player_importer_import', 10, 2 );
    function citygov_html5_jquery_audio_player_importer_import($importer, $action) {
		if ( $action == 'import_html5_jquery_audio_player' ) {
            $importer->import_dump('html5_jquery_audio_player', esc_html__('HTML5 jQuery Audio Player', 'citygov'));
        }
    }
}

// Display import progress
if ( !function_exists( 'citygov_html5_jquery_audio_player_importer_import_fields' ) ) {
	//add_action( 'citygov_action_importer_import_fields',	'citygov_html5_jquery_audio_player_importer_import_fields', 10, 1 );
	function citygov_html5_jquery_audio_player_importer_import_fields($importer) {
		?>
		<tr class="import_html5_jquery_audio_player">
			<td class="import_progress_item"><?php esc_html_e('HTML5 jQuery Audio Player', 'citygov'); ?></td>
			<td class="import_progress_status"></td>
		</tr>
		<?php
	}
}


// Export posts
if ( !function_exists( 'citygov_html5_jquery_audio_player_importer_export' ) ) {
    //add_action( 'citygov_action_importer_export',	'citygov_html5_jquery_audio_player_importer_export', 10, 1 );
    function citygov_html5_jquery_audio_player_importer_export($importer) {
		citygov_storage_set('export_html5_jquery_audio_player', serialize( array(
			'hmp_playlist'	=> $importer->export_dump('hmp_playlist'),
			'hmp_rating'	=> $importer->export_dump('hmp_rating')
			) )
		);
    }
}


// Display exported data in the fields
if ( !function_exists( 'citygov_html5_jquery_audio_player_importer_export_fields' ) ) {
    //add_action( 'citygov_action_importer_export_fields',	'citygov_html5_jquery_audio_player_importer_export_fields', 10, 1 );
    function citygov_html5_jquery_audio_player_importer_export_fields($importer) {
        ?>
        <tr>
            <th align="left"><?php esc_html_e('HTML5 jQuery Audio Player', 'citygov'); ?></th>
            <td><?php citygov_fpc(citygov_get_file_dir('core/core.importer/export/html5_jquery_audio_player.txt'), citygov_storage_get('export_html5_jquery_audio_player')); ?>
                <a download="html5_jquery_audio_player.txt" href="<?php echo esc_url(citygov_get_file_url('core/core.importer/export/html5_jquery_audio_player.txt')); ?>"><?php esc_html_e('Download', 'citygov'); ?></a>
            </td>
        </tr>
    <?php
    }
}





// Shortcodes
//------------------------------------------------------------------------

// Register shortcode in the shortcodes list
if (!function_exists('citygov_html5_jquery_audio_player_reg_shortcodes')) {
    //add_filter('citygov_action_shortcodes_list',	'citygov_html5_jquery_audio_player_reg_shortcodes');
    function citygov_html5_jquery_audio_player_reg_shortcodes() {
		if (citygov_storage_isset('shortcodes')) {
			citygov_sc_map_after('trx_audio', 'hmp_player', array(
                "title" => esc_html__("HTML5 jQuery Audio Player", "citygov"),
                "desc" => esc_html__("Insert HTML5 jQuery Audio Player", "citygov"),
                "decorate" => true,
                "container" => false,
				"params" => array()
				)
            );
        }
    }
}


// Register shortcode in the VC shortcodes list
if (!function_exists('citygov_hmp_player_reg_shortcodes_vc')) {
    add_filter('citygov_action_shortcodes_list_vc',	'citygov_hmp_player_reg_shortcodes_vc');
    function citygov_hmp_player_reg_shortcodes_vc() {

        // CityGov HTML5 jQuery Audio Player
        vc_map( array(
            "base" => "hmp_player",
            "name" => esc_html__("HTML5 jQuery Audio Player", "citygov"),
            "description" => esc_html__("Insert HTML5 jQuery Audio Player", "citygov"),
            "category" => esc_html__('Content', 'citygov'),
            'icon' => 'icon_trx_audio',
            "class" => "trx_sc_single trx_sc_hmp_player",
            "content_element" => true,
            "is_container" => false,
            "show_settings_on_create" => false,
            "params" => array()
        ) );

        class WPBakeryShortCode_Hmp_Player extends CITYGOV_VC_ShortCodeSingle {}

    }
}
?>