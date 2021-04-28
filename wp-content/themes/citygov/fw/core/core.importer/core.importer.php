<?php
// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


// Theme init
if (!function_exists('citygov_importer_theme_setup')) {
	add_action( 'citygov_action_after_init_theme', 'citygov_importer_theme_setup' );		// Fire this action after load theme options
	function citygov_importer_theme_setup() {
		if (is_admin() && current_user_can('import') && citygov_get_theme_option('admin_dummy_data')=='yes') {
			new citygov_dummy_data_importer();
		}
	}
}

class citygov_dummy_data_importer {

	// Theme specific settings
	var $options = array(
		'debug'					=> false,						// Enable debug output
		'data_type'				=> 'vc',						// Default dummy data type
		'file_with_posts_vc'	=> 'demo/posts.txt',			// Name of the file with demo content with VC wrappers
		'file_with_posts_no_vc'	=> 'demo/posts_no_vc.txt',		// Name of the file with demo content without VC wrappers
		'file_with_users'		=> 'demo/users.txt',			// Name of the file with users
		'file_with_mods'		=> 'demo/theme_mods.txt',		// Name of the file with theme mods
		'file_with_options'		=> 'demo/theme_options.txt',	// Name of the file with theme options
		'file_with_templates'	=> 'demo/templates_options.txt',// Name of the file with templates options
		'file_with_widgets'		=> 'demo/widgets.txt',			// Name of the file with widgets data
		'file_with_attachments'	=> array(						// Array with names of the attachments											MUST BE SET IN THEME!
//			'http://some.cloud.net/theme_name/uploads.zip',		// Name of the remote file with attachments
//			'demo/uploads.zip',									// Name of the local file with attachments
		),
		'attachments_by_parts'	=> false,						// Files above are parts of single file - large media archive.					MUST BE SET OR CHANGED IN THEME!
																// They are must be concatenated in one file before unpacking
		'ignore_post_types'		=> array(						// Ignore specified post types when export posts and postmeta
			'revision'
		),
		'domain_dev'			=> '',							// Domain on the developer's server. 											MUST BE SET IN THEME!
		'domain_demo'			=> '',							// Domain on the demo-server.													MUST BE SET IN THEME!
		'taxonomies'			=> array(),						// List of the required taxonomies: 'post_type' => 'taxonomy', ...				MUST BE SET OR CHANGED IN THEME!
		'plugins_initial_state'	=> 0,							// The initial state of the plugin's checkboxes: 1 - checked, 0 - unchecked		MUST BE SET OR CHANGED IN THEME!
		'additional_options'	=> array(						// Additional options slugs (for export plugins settings).						MUST BE SET OR CHANGED IN THEME!
			// WP
			'blogname',
			'blogdescription',
			'posts_per_page',
			'show_on_front',
			'page_on_front',
			'page_for_posts'
		)
	);

	var $error    = '';				// Error message
	var $success  = '';				// Success message
	var $result   = 0;				// Import posts percent (if break inside)
	
	var $action 	= '';			// Current AJAX action

	var $last_slider = 0;			// Last imported slider number. 															MUST BE SET OR CHANGED IN THEME!

	var $export_mods = '';
	var $export_options = '';
	var $export_templates = '';
	var $export_widgets = '';
	var $export_posts = '';
	var $export_users = '';

	var $uploads_url = '';
	var $uploads_dir = '';

	var $import_log = '';
	var $import_last_id = 0;
	
	var $start_time = 0;
	var $max_time = 0;

	var	$response = array(
			'action' => '',
			'error' => '',
			'result' => '100'
		);

	//-----------------------------------------------------------------------------------
	// Constuctor
	//-----------------------------------------------------------------------------------
	function __construct() {
		// Add menu item
		add_action('admin_menu', 					array($this, 'admin_menu_item'));
		// Add menu item
		add_action('admin_enqueue_scripts', 		array($this, 'admin_scripts'));
		// AJAX handler
		add_action('wp_ajax_citygov_importer_start_import',		array($this, 'importer'));
		add_action('wp_ajax_nopriv_citygov_importer_start_import',	array($this, 'importer'));
	}
	
	function prepare_vars() {
		// Detect current uploads folder and url
		$uploads_info = wp_upload_dir();
		$this->uploads_dir = $uploads_info['basedir'];
		$this->uploads_url = $uploads_info['baseurl'];
		// Filter importer options
	    $this->options = apply_filters('citygov_filter_importer_options', $this->options);
		// Get allowed execution time
		$this->start_time = time();
		$tm = max(30, ini_get('max_execution_time'));
		$this->max_time = $tm - min(10, round($tm*0.33));
		// Get data from log-file
		$this->import_log = citygov_get_file_dir('core/core.importer/log.posts.txt');
		if (empty($this->import_log)) {
			$this->import_log = get_template_directory().'/fw/core/core.importer/log.posts.txt';
			if (!file_exists($this->import_log)) citygov_fpc($this->import_log, '');
		}
		$log = explode('|', citygov_fgc($this->import_log));
		$this->import_last_id = (int) $log[0];
		$this->result = empty($log[1]) ? 0 : (int) $log[1];
		$this->last_slider = empty($log[2]) ? '' : $log[2];
	}

	//-----------------------------------------------------------------------------------
	// Admin Interface
	//-----------------------------------------------------------------------------------
	
	// Add menu item
	function admin_menu_item() {
		if ( current_user_can( 'manage_options' ) ) {
			// Add in admin menu 'Theme Options'
			citygov_admin_add_menu_item('theme', array(
				'page_title' => esc_html__('Install Dummy Data', 'citygov'),
				'menu_title' => esc_html__('Install Dummy Data', 'citygov'),
				'capability' => 'manage_options',
				'menu_slug'  => 'trx_importer',
				'callback'   => array($this, 'build_page'),
				'icon'		 => ''
				)
			);
		}
	}
	
	// Add script
	function admin_scripts() {
		citygov_enqueue_style(  'citygov-importer-style',  citygov_get_file_url('core/core.importer/core.importer.css'), array(), null );
		citygov_enqueue_script( 'citygov-importer-script', citygov_get_file_url('core/core.importer/core.importer.js'), array('jquery'), null, true );	
	}
	
	
	//-----------------------------------------------------------------------------------
	// Build the Main Page
	//-----------------------------------------------------------------------------------
	function build_page() {
		$this->prepare_vars();
		
		// Export data
		if ( isset($_POST['exporter_action']) ) {
			if ( !wp_verify_nonce( citygov_get_value_gp('nonce'), admin_url() ) )
				$this->error = esc_html__('Incorrect WP-nonce data! Operation canceled!', 'citygov');
						
			else
				$this->exporter();
		}
		?>

		<div class="trx_importer">
			<div class="trx_importer_section">
				<h2 class="trx_title"><?php esc_html_e('CityGov Importer', 'citygov'); ?></h2>
				<p><b><?php esc_html_e('Attention! Important info:', 'citygov'); ?></b></p>
				<ol>
					<li><?php esc_html_e('Data import will replace all existing content - so you get a complete copy of our demo site', 'citygov'); ?></li>
					<li><?php esc_html_e('Data import can take a long time (sometimes more than 10 minutes) - please wait until the end of the procedure, do not navigate away from the page.', 'citygov'); ?></li>
					<li><?php esc_html_e('Web-servers set the time limit for the execution of php-scripts. Therefore, the import process will be split into parts. Upon completion of each part - the import will resume automatically!', 'citygov'); ?></li>
				</ol>

				<form id="trx_importer_form">

					<p><b><?php esc_html_e('Select the data to import:', 'citygov'); ?></b></p>

					<p>
					<input type="checkbox" checked="checked" value="1" name="import_posts" id="import_posts" /> <label for="import_posts"><?php esc_html_e('Import posts', 'citygov'); ?></label><br>
					<span class="import_posts_params">
						<?php
						$checked = 'checked="checked"';
						if (!empty($this->options['file_with_posts_vc']) && file_exists(citygov_get_file_dir($this->options['file_with_posts_vc']))) {
							?>
							<input type="radio" <?php echo ('vc' == $this->options['data_type'] ? trim($checked) : ''); ?> value="vc" name="data_type" id="data_type_vc" /><label for="data_type_vc"><?php esc_html_e('Import data to edit with the Visual Composer', 'citygov'); ?></label><br>
							<?php
							if ($this->options['data_type']=='vc') $checked = '';
						}
						if (!empty($this->options['file_with_posts_no_vc']) && file_exists(citygov_get_file_dir($this->options['file_with_posts_no_vc']))) {
							?>
							<input type="radio" <?php echo ('no_vc'==$this->options['data_type'] || $checked ? trim($checked) : ''); ?> value="no_vc" name="data_type" id="data_type_no_vc" /><label for="data_type_no_vc"><?php esc_html_e('Import data without Visual Composer wrappers', 'citygov'); ?></label><br>
							<?php
						}
						?>
					</span>
					</p>

					<p>
					<input type="checkbox" checked="checked" value="1" name="import_attachments" id="import_attachments" /> <label for="import_attachments"><?php esc_html_e('Import media', 'citygov'); ?></label><br><br>
					<input type="checkbox" checked="checked" value="1" name="import_tm" id="import_tm" /> <label for="import_tm"><?php esc_html_e('Import Theme Mods', 'citygov'); ?></label><br>
					<input type="checkbox" checked="checked" value="1" name="import_to" id="import_to" /> <label for="import_to"><?php esc_html_e('Import Theme Options', 'citygov'); ?></label><br>
					<input type="checkbox" checked="checked" value="1" name="import_tpl" id="import_tpl" /> <label for="import_tpl"><?php esc_html_e('Import Templates Options', 'citygov'); ?></label><br>
					<input type="checkbox" checked="checked" value="1" name="import_widgets" id="import_widgets" /> <label for="import_widgets"><?php esc_html_e('Import Widgets', 'citygov'); ?></label><br><br>

					<?php do_action('citygov_action_importer_params', $this); ?>
					</p>

					<div class="trx_buttons">
						<?php if ($this->import_last_id > 0 || !empty($this->last_slider)) { ?>
							<h4 class="trx_importer_complete"><?php sprintf(esc_html__('Import posts completed by %s', 'citygov'), $this->result.'%'); ?></h4>
							<input type="button" value="<?php
								if ($this->import_last_id > 0)
									printf(esc_html__('Continue import (from ID=%s)', 'citygov'), $this->import_last_id);
								else
									esc_html_e('Continue import sliders', 'citygov');
								?>" data-last_id="<?php echo esc_attr($this->import_last_id); ?>" data-last_slider="<?php echo esc_attr($this->last_slider); ?>">
							<input type="button" value="<?php esc_attr_e('Start import again', 'citygov'); ?>">
						<?php } else { ?>
							<input type="button" value="<?php esc_attr_e('Start import', 'citygov'); ?>">
						<?php } ?>
					</div>

				</form>
				
				<div id="trx_importer_progress" class="notice notice-info style_<?php echo esc_attr(citygov_get_theme_setting('admin_dummy_style')); ?>">
					<h4 class="trx_importer_progress_title"><?php esc_html_e('Import demo data', 'citygov'); ?></h4>
					<table border="0" cellpadding="4">
					<tr class="import_posts">
						<td class="import_progress_item"><?php esc_html_e('Posts', 'citygov'); ?></td>
						<td class="import_progress_status"></td>
					</tr>
					<tr class="import_attachments">
						<td class="import_progress_item"><?php esc_html_e('Media', 'citygov'); ?></td>
						<td class="import_progress_status"></td>
					</tr>
					<tr class="import_tm">
						<td class="import_progress_item"><?php esc_html_e('Theme Mods', 'citygov'); ?></td>
						<td class="import_progress_status"></td>
					</tr>
					<tr class="import_to">
						<td class="import_progress_item"><?php esc_html_e('Theme Options', 'citygov'); ?></td>
						<td class="import_progress_status"></td>
					</tr>
					<tr class="import_tpl">
						<td class="import_progress_item"><?php esc_html_e('Templates Options', 'citygov'); ?></td>
						<td class="import_progress_status"></td>
					</tr>
					<tr class="import_widgets">
						<td class="import_progress_item"><?php esc_html_e('Widgets', 'citygov'); ?></td>
						<td class="import_progress_status"></td>
					</tr>
					<?php do_action('citygov_action_importer_import_fields', $this); ?>
					</table>
					<h4 class="trx_importer_progress_complete"><?php esc_html_e('Congratulations! Data import complete!', 'citygov'); ?> <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('View site', 'citygov'); ?></a></h4>
				</div>
				
			</div>

			<div class="trx_exporter_section">
				<h2 class="trx_title"><?php esc_html_e('CityGov Exporter', 'citygov'); ?></h2>
				<?php 
				if ($this->error) {
					?><div class="trx_exporter_error notice notice-error"><?php echo trim($this->error); ?></div><?php
				}
				?>
				<form id="trx_exporter_form" action="#" method="post">

					<input type="hidden" value="<?php echo esc_attr(wp_create_nonce(admin_url())); ?>" name="nonce" />
					<input type="hidden" value="all" name="exporter_action" />

					<div class="trx_buttons">
						<?php if ($this->export_options!='') { ?>

							<table border="0" cellpadding="6">
							<tr>
								<th align="left"><?php esc_html_e('Users', 'citygov'); ?></th>
								<td><?php citygov_fpc(citygov_get_file_dir('core/core.importer/export/users.txt'), $this->export_users); ?>
									<a download="users.txt" href="<?php echo esc_url(citygov_get_file_url('core/core.importer/export/users.txt')); ?>"><?php esc_html_e('Download', 'citygov'); ?></a>
								</td>
							</tr>
							<tr>
								<th align="left"><?php esc_html_e('Posts', 'citygov'); ?></th>
								<td><?php citygov_fpc(citygov_get_file_dir('core/core.importer/export/posts.txt'), $this->export_posts); ?>
									<a download="posts.txt" href="<?php echo esc_url(citygov_get_file_url('core/core.importer/export/posts.txt')); ?>"><?php esc_html_e('Download', 'citygov'); ?></a>
								</td>
							</tr>
							<tr>
								<th align="left"><?php esc_html_e('Theme Mods', 'citygov'); ?></th>
								<td><?php citygov_fpc(citygov_get_file_dir('core/core.importer/export/theme_mods.txt'), $this->export_mods); ?>
									<a download="theme_mods.txt" href="<?php echo esc_url(citygov_get_file_url('core/core.importer/export/theme_mods.txt')); ?>"><?php esc_html_e('Download', 'citygov'); ?></a>
								</td>
							</tr>
							<tr>
								<th align="left"><?php esc_html_e('Theme Options', 'citygov'); ?></th>
								<td><?php citygov_fpc(citygov_get_file_dir('core/core.importer/export/theme_options.txt'), $this->export_options); ?>
									<a download="theme_options.txt" href="<?php echo esc_url(citygov_get_file_url('core/core.importer/export/theme_options.txt')); ?>"><?php esc_html_e('Download', 'citygov'); ?></a>
								</td>
							</tr>
							<tr>
								<th align="left"><?php esc_html_e('Templates Options', 'citygov'); ?></th>
								<td><?php citygov_fpc(citygov_get_file_dir('core/core.importer/export/templates_options.txt'), $this->export_templates); ?>
									<a download="templates_options.txt" href="<?php echo esc_url(citygov_get_file_url('core/core.importer/export/templates_options.txt')); ?>"><?php esc_html_e('Download', 'citygov'); ?></a>
								</td>
							</tr>
							<tr>
								<th align="left"><?php esc_html_e('Widgets', 'citygov'); ?></th>
								<td><?php citygov_fpc(citygov_get_file_dir('core/core.importer/export/widgets.txt'), $this->export_widgets); ?>
									<a download="widgets.txt" href="<?php echo esc_url(citygov_get_file_url('core/core.importer/export/widgets.txt')); ?>"><?php esc_html_e('Download', 'citygov'); ?></a>
								</td>
							</tr>
							
							<?php do_action('citygov_action_importer_export_fields', $this); ?>

							</table>

						<?php } else { ?>

							<input type="submit" value="<?php esc_attr_e('Export Demo Data', 'citygov'); ?>">

						<?php } ?>
					</div>

				</form>
			</div>
		</div>
		<?php
	}

	// Check for required plugings
	function check_required_plugins($list='') {
		$not_installed = '';
		if (in_array('trx_utils', citygov_storage_get('required_plugins')) && !defined('TRX_UTILS_VERSION') )
			$not_installed .= 'CityGov Utilities';
		$not_installed = apply_filters('citygov_filter_importer_required_plugins', $not_installed, $list);
		if ($not_installed) {
			$this->error = '<b>'.esc_html__('Attention! For correct installation of the selected demo data, you must install and activate the following plugins: ', 'citygov').'</b><br>'.($not_installed);
			return false;
		}
		return true;
	}
	
	
	//-----------------------------------------------------------------------------------
	// Export dummy data
	//-----------------------------------------------------------------------------------
	function exporter() {
		global $wpdb;
		$suppress = $wpdb->suppress_errors();

		// Export theme mods
		$this->export_mods = serialize($this->prepare_data(get_theme_mods()));

		// Export theme, templates and categories options and VC templates
		$rows = $wpdb->get_results( "SELECT option_name, option_value FROM " . esc_sql($wpdb->options) . " WHERE option_name LIKE '" . esc_sql(citygov_storage_get('options_prefix')) . "_options%'" );
		$options = array();
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				$options[$row->option_name] = citygov_unserialize($row->option_value);
			}
		}
		// Export additional options
		if (is_array($this->options['additional_options']) && count($this->options['additional_options']) > 0) {
			foreach ($this->options['additional_options'] as $opt) {
				$rows = $wpdb->get_results( "SELECT option_name, option_value FROM " . esc_sql($wpdb->options) . " WHERE option_name LIKE '" . esc_sql($opt) . "'" );
				if (is_array($rows) && count($rows) > 0) {
					foreach ($rows as $row) {
						$options[$row->option_name] = citygov_unserialize($row->option_value);
					}
				}
			}
		}
		$this->export_options = serialize($this->prepare_data($options));

		// Export templates options
		$rows = $wpdb->get_results( "SELECT option_name, option_value FROM " . esc_sql($wpdb->options) . " WHERE option_name LIKE '".esc_sql(citygov_storage_get('options_prefix'))."_options_template_%'" );
		$options = array();
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				$options[$row->option_name] = citygov_unserialize($row->option_value);
			}
		}
		$this->export_templates = serialize($this->prepare_data($options));

		// Export widgets
		$rows = $wpdb->get_results( "SELECT option_name, option_value FROM " . esc_sql($wpdb->options) . " WHERE option_name = 'sidebars_widgets' OR option_name LIKE 'widget_%'" );
		$options = array();
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				$options[$row->option_name] = citygov_unserialize($row->option_value);
			}
		}
		$this->export_widgets = serialize($this->prepare_data($options));

		// Export posts
		$this->export_posts = serialize(array(
			"posts"					=> $this->export_dump("posts"),
			"postmeta"				=> $this->export_dump("postmeta"),
			"comments"				=> $this->export_dump("comments"),
			"commentmeta"			=> $this->export_dump("commentmeta"),
			"terms"					=> $this->export_dump("terms"),
			"term_taxonomy"			=> $this->export_dump("term_taxonomy"),
			"term_relationships"	=> $this->export_dump("term_relationships")
			)
        );
		
		// Expost WP Users
		$users = array();
		$rows = $this->export_dump("users");
		if (is_array($rows) && count($rows)>0) {
			foreach ($rows as $k=>$v) {
				$rows[$k]['user_login']	= $rows[$k]['user_nicename'] = 'user'.$v['ID'];
				$rows[$k]['user_pass']		= '';
				$rows[$k]['display_name']	= sprintf(esc_html__('User %d', 'citygov'), $v['ID']);
				$rows[$k]['user_email']	= 'user'.$v['ID'].'@user-mail.net';
			}
		}
		$users['users'] = $rows;
		$rows = $this->export_dump("usermeta");
		if (is_array($rows) && count($rows)>0) {
			foreach ($rows as $k=>$v) {
				if      ($v['meta_key'] == 'nickname')				$rows[$k]['meta_value'] = 'user'.$v['user_id'];
				else if ($v['meta_key'] == 'first_name')			$rows[$k]['meta_value'] = sprintf(esc_html__('FName%d', 'citygov'), $v['user_id']);
				else if ($v['meta_key'] == 'last_name')				$rows[$k]['meta_value'] = sprintf(esc_html__('LName%d', 'citygov'), $v['user_id']);
				else if ($v['meta_key'] == 'billing_first_name')	$rows[$k]['meta_value'] = sprintf(esc_html__('FName%d', 'citygov'), $v['user_id']);
				else if ($v['meta_key'] == 'billing_last_name')		$rows[$k]['meta_value'] = sprintf(esc_html__('LName%d', 'citygov'), $v['user_id']);
				else if ($v['meta_key'] == 'billing_email')			$rows[$k]['meta_value'] = 'user'.$v['user_id'].'@user-mail.net';
			}
		}
		$users['usermeta'] = $rows;
		$this->export_users = serialize($users);

		// Export Theme specific post types
		do_action('citygov_action_importer_export', $this);

		$wpdb->suppress_errors( $suppress );
	}
	
	
	//-----------------------------------------------------------------------------------
	// Export specified table
	//-----------------------------------------------------------------------------------
	function export_dump($table) {
		global $wpdb;
		$rows = array();
		if ( count($wpdb->get_results( "SHOW TABLES LIKE '".esc_sql($wpdb->prefix . $table)."'", ARRAY_A )) == 1 ) {
			$where = '';
			if (count($this->options['ignore_post_types'])>0) {
				if ($table=='posts') {
					$where = " WHERE t.post_type NOT IN ('" . join("','", array_map("esc_sql", $this->options['ignore_post_types'])) . "')";
				}
			}
			// Attention! All parts of $where clause processed with esc_sql() in the condition above
			$query = "SELECT t.* FROM " . esc_sql($wpdb->prefix.$table) . " AS t" . $where;
			$rows = $this->prepare_data( $wpdb->get_results( $query, ARRAY_A ) );
			if ($this->options['debug']) dfl(sprintf(__("Export %d rows from table '%s'. Used query: %s", 'citygov'), count($rows), $table, $query));
		}
		return $rows;
	}
	
	
	//-----------------------------------------------------------------------------------
	// Import dummy data
	//-----------------------------------------------------------------------------------
	//add_action('wp_ajax_citygov_importer_start_import',			array($this, 'importer'));
	//add_action('wp_ajax_nopriv_citygov_importer_start_import',	array($this, 'importer'));
	function importer() {

		if ($this->options['debug']) dfl(__('AJAX handler for importer', 'citygov'));

		if ( !isset($_POST['importer_action']) || !wp_verify_nonce( citygov_get_value_gp('ajax_nonce'), admin_url('admin-ajax.php') ) )
			die();
		
		$this->prepare_vars();

		$this->action = $this->response['action'] = $_POST['importer_action'];

		if ($this->options['debug']) dfl( sprintf(__('Dispatch action: %s', 'citygov'), $this->action) );
		
		global $wpdb;
		$suppress = $wpdb->suppress_errors();

		ob_start();

		// Change max_execution_time (if allowed by server)
		$admin_tm = max(0, min(1800, (int) citygov_get_theme_option('admin_dummy_timeout')));
		$tm = max(30, (int) ini_get('max_execution_time'));
		if ($tm < $admin_tm) {
			@set_time_limit($admin_tm);
			$tm = max(30, ini_get('max_execution_time'));
			$this->max_time = $tm - min(10, round($tm*0.33));
		}

		// Start import - clear tables, etc.
		if ($this->action == 'import_start') {
			if (!$this->check_required_plugins($_POST['clear_tables']))
				$this->response['error'] = $this->error;
			else
				if (!empty($_POST['clear_tables'])) $this->clear_tables();

		// Import posts and users
		} else if ($this->action == 'import_posts') {
			$result = $this->import_posts();
			if ($result >= 100) {
				$this->import_users();
				do_action('citygov_action_importer_after_import_posts', $this);
			}
			$this->response['result'] = $result;

		// Import attachments
		} else if ($this->action == 'import_attachments') {
			$result = $this->import_attachments();
			$this->response['result'] = $result;

		// Import Theme Mods
		} else if ($this->action == 'import_tm') {
			$this->import_theme_mods();

		// Import Theme Options
		} else if ($this->action == 'import_to') {
			$this->import_theme_options();

		// Import Templates Options
		} else if ($this->action == 'import_tpl') {
			$this->import_templates_options();

		// Import Widgets
		} else if ($this->action == 'import_widgets') {
			$this->import_widgets();

		// End import - clear cache, flush rules, etc.
		} else if ($this->action == 'import_end') {
			citygov_clear_cache('all');

		// Import Theme specific posts
		} else {
			do_action('citygov_action_importer_import', $this, $this->action);
		}

		ob_end_clean();

		$wpdb->suppress_errors($suppress);

		if ($this->options['debug']) dfl( sprintf(__("AJAX handler finished - send results to client: %s", 'citygov'), json_encode($this->response)) );

		echo json_encode($this->response);
		die();
	}
	
	
	// Delete all data from tables
	function clear_tables() {
		global $wpdb;
		if (citygov_strpos($_POST['clear_tables'], 'posts')!==false && $this->import_last_id==0) {
			if ($this->options['debug']) dfl( __('Clear posts tables', 'citygov') );
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->posts));
			if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "posts".', 'citygov' ) . ' ' . ($res->get_error_message()) );
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->postmeta));
			if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "postmeta".', 'citygov' ) . ' ' . ($res->get_error_message()) );
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->comments));
			if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "comments".', 'citygov' ) . ' ' . ($res->get_error_message()) );
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->commentmeta));
			if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "commentmeta".', 'citygov' ) . ' ' . ($res->get_error_message()) );
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->terms));
			if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "terms".', 'citygov' ) . ' ' . ($res->get_error_message()) );
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->term_relationships));
			if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "term_relationships".', 'citygov' ) . ' ' . ($res->get_error_message()) );
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->term_taxonomy));
			if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "term_taxonomy".', 'citygov' ) . ' ' . ($res->get_error_message()) );
		}
		do_action('citygov_action_importer_clear_tables', $this, $_POST['clear_tables']);
	}


	// Import users
	function import_users() {
		if ($this->options['debug']) 
			dfl(__('Import users', 'citygov'));
		$result = $this->import_dump('users', esc_html__('Users', 'citygov'));
	}

	// Import posts, terms and comments
	function import_posts() {
		if ($this->options['debug']) 
			dfl((int) $_POST['last_id'] > 0 ? sprintf(__('Continue import posts from ID=%d', 'citygov'), (int) $_POST['last_id']) : __('Import posts, terms and comments', 'citygov'));
		$result = $this->import_dump('posts_'.trim($_POST['data_type']), esc_html__('Posts', 'citygov'));
		if ($result>=100) citygov_fpc($this->import_log, '');
		return $result;
	}

	// Import attachments
	function import_attachments() {
		$result = 100;
		if ($this->options['debug']) 
			dfl(__('Import media (attachments)', 'citygov'));
		if (empty($this->options['file_with_attachments'])) return;
		// Get log
		$log = citygov_get_file_dir('core/core.importer/log.media.txt');
		if (empty($log)) {
			$log = get_template_directory().'/fw/core/core.importer/log.media.txt';
			if (!file_exists($log)) citygov_fpc($log, '');
		}
		$last_arh = citygov_fgc($log);
		// Process files
		$files = $this->options['file_with_attachments'];
		if (!is_array($files)) $files = array($files);
		$counter = 0;
		foreach ($files as $file) {
			$counter++;
			if (!empty($last_arh)) {
				if ($file==$last_arh) $last_arh = '';
				continue;
			}
			$need_del = false;
			$need_extract = true;
			$need_exit = false;		// Break process when critical error is appear
			$zip = $file;
			// Download remote file
			if (citygov_substr($zip, 0, 4)=='http') {
				if (!$this->options['attachments_by_parts']) {			// Method 1: WP download_url() load single file into system temp folder
					$response = download_url($zip, $this->max_time);
					if (is_string($response)) {
						$zip = $response;
						$need_del = true;
					} else {
						$this->response['error'] = sprintf(__("Error download remote archive with media '%s'.\nWP Error:\n%s", 'citygov'), $zip, citygov_debug_dump_var($response));
						if ($this->options['debug']) 
							dfl($this->response['error']);
						$need_exit = true;
						$need_extract = false;
						$result = 100;
						$zip = '';
					}
				} else {												// Method 2: Load file in the memory and save it into WP uploads dir - used to load many parts of file
					$response = wp_remote_get($zip, array(
									'timeout'     => $this->max_time,
									'redirection' => $this->max_time
									)
								);
					if (is_array($response)) {
						$zip = $this->uploads_dir.'/import_media.tmp';
						citygov_fpc($zip, $response['body'], $file==$files[0] ? 0 : FILE_APPEND);
						$need_extract = ($counter == count($files));
						$need_del = $need_extract;
					} else {
						$this->response['error'] = sprintf(__("Error download remote archive with media '%s'.\nWP Error:\n%s", 'citygov'), $zip, citygov_debug_dump_var($response));
						if ($this->options['debug']) 
							dfl($this->response['error']);
						$need_exit = true;
						$need_extract = false;
						$result = 100;
						$zip = '';
					}
				}
			} else {
				// Archive packed with theme
				$zip = citygov_get_file_dir($zip);
			}
			// Unrecoverable error is appear
			if ($need_exit) break;
			// Unzip file
			$success = !$need_extract;
			if ($need_extract) {
				if (!empty($zip) && file_exists($zip)) {
					WP_Filesystem();
					if ( !unzip_file($zip, $this->uploads_dir) ) {
						if ($this->options['debug']) 
							dfl(sprintf(__('Error when unzip file "%s"', 'citygov'), $zip));
					} else
						$success = true;
					if ($need_del) unlink($zip);
				} else {
					if ($this->options['debug']) 
						dfl(sprintf(__('File "%s" not found', 'citygov'), $zip));
				}
			}
			// Save to log last processed file
			citygov_fpc($log, $file);
			// Check time
			$result = $counter < count($files) ? round($counter / count($files) * 100) : 100;
			if ($this->options['debug']) 
					dfl(sprintf( __('File %s imported. Current import progress: %s. Time limit: %s sec. Elapsed time: %s sec.', 'citygov'), $file, $result.'%', $this->max_time, time() - $this->start_time));
			// Break import after timeout or if attachments loading from parts - to show percent loading after each part
			//if (time() - $this->start_time >= $this->max_time)
				break;
		}
		// Clear log with last processed file
		if ($result>=100)
			citygov_fpc($log, '');
		return $result;
	}

	// Import theme mods
	function import_theme_mods() {
		if (empty($this->options['file_with_mods'])) return;
		if ($this->options['debug']) dfl(__('Import Theme Mods', 'citygov'));
		$txt = citygov_fgc(citygov_get_file_dir($this->options['file_with_mods']));
		$data = citygov_unserialize($txt);
		// Replace upload url in options
		if (is_array($data) && count($data) > 0) {
			foreach ($data as $k=>$v) {
				$data[$k] = $this->replace_uploads($v);
			}
			$theme = get_option( 'stylesheet' );
			update_option( "theme_mods_$theme", $data );
		}
	}


	// Import theme options
	function import_theme_options() {
		if (empty($this->options['file_with_options'])) return;
		if ($this->options['debug']) dfl(__('Reset Theme Options', 'citygov'));
		citygov_options_reset();
		if ($this->options['debug']) dfl(__('Import Theme Options', 'citygov'));
		$txt = citygov_fgc(citygov_get_file_dir($this->options['file_with_options']));
		$data = citygov_unserialize($txt);
		// Replace upload url in options
		if (is_array($data) && count($data) > 0) {
			foreach ($data as $k=>$v) {
				$v = $this->replace_uploads($v);
				if ($k == 'mega_main_menu_options' && isset($v['last_modified']))
					$v['last_modified'] = time()+30;
				update_option( $k, $v );
			}
		}
		citygov_load_main_options();
	}


	// Import templates options
	function import_templates_options() {
		if (empty($this->options['file_with_templates'])) return;
		if ($this->options['debug']) dfl(__('Import Templates Options', 'citygov'));
		$txt = citygov_fgc(citygov_get_file_dir($this->options['file_with_templates']));
		$data = citygov_unserialize($txt);
		// Replace upload url in options
		if (is_array($data) && count($data) > 0) {
			foreach ($data as $k=>$v) {
				update_option( $k, $this->replace_uploads($v) );
			}
		}
	}


	// Import widgets
	function import_widgets() {
		if (empty($this->options['file_with_widgets'])) return;
		if ($this->options['debug']) dfl(__('Import Widgets', 'citygov'));
		$txt = citygov_fgc(citygov_get_file_dir($this->options['file_with_widgets']));
		$data = citygov_unserialize($txt);
		if (is_array($data) && count($data) > 0) {
			foreach ($data as $k=>$v) {
				update_option( $k, $this->replace_uploads($v) );
			}
		}
	}


	// Import any SQL dump
	function import_dump($slug, $title) {
		$result = 100;
		if (empty($this->options['file_with_'.$slug])) return $result;
		if ($this->options['debug']) dfl(sprintf(__('Import dump of "%s"', 'citygov'), $title));
		$txt = citygov_fgc(citygov_get_file_dir($this->options['file_with_'.$slug]));
		$data = citygov_unserialize($txt);
		if (is_array($data) && count($data) > 0) {
			global $wpdb;
			foreach ($data as $table=>$rows) {
				if ($this->options['debug']) dfl(sprintf(__('Process table "%s"', 'citygov'), $table));
				// Clear table, if it is not 'users' or 'usermeta' amd not any posts, terms or comments table
				if (!in_array($table, array('users', 'usermeta')) && $this->action!='import_posts')
					$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix . $table));
				$values = $fields = '';
				$break = false;
				if (is_array($rows) && ($posts_all=count($rows)) > 0) {
					$posts_counter = $posts_imported = 0;
					$start_from_id = (int) $_POST['last_id'] > 0 ? $this->import_last_id : 0;
					foreach ($rows as $row) {
						$posts_counter++;
						if ($table=='posts' && !empty($row['ID']) && $row['ID'] <= $start_from_id) continue;
						// Replace demo URL to current site URL
						$row = citygov_replace_site_url($row, $this->options['domain_demo']);
						$f = '';
						$v = '';
						if (is_array($row) && count($row) > 0) {
							foreach ($row as $field => $value) {
								$f .= ($f ? ',' : '') . "'" . esc_sql($field) . "'";
								$v .= ($v ? ',' : '') . "'" . esc_sql($value) . "'";
							}
						}
						if ($fields == '') $fields = '(' . $f . ')';
						$values .= ($values ? ',' : '') . '(' . $v . ')';
						// If query length exceed 64K - run query, because MySQL not accept long query string
						// If current table 'users' or 'usermeta' - run queries row by row, because we append data
						if (citygov_strlen($values) > 64000 || in_array($table, array('users', 'usermeta'))) {
							// Attention! All items in the variable $values escaped on the loop above - esc_sql($value)
							$q = "INSERT INTO ".esc_sql($wpdb->prefix . $table)." VALUES {$values}";
							$wpdb->query($q);
							$values = $fields = '';
						}
						
						// Save into log last
						if ($table=='posts') {
							$result = $posts_counter < $posts_all ? round($posts_counter / $posts_all * 100) : 100;
							citygov_fpc($this->import_log, trim(max($row['ID'], $start_from_id)) . '|' . trim($result));
							if ($this->debug) dfl( sprintf( __('Post (ID=%s) imported. Current import progress: %s. Time limit: %s sec. Elapsed time: %s sec.', 'citygov'), $row['ID'], $result.'%', $this->max_time, time() - $this->start_time) );
							// Break import after timeout or if leave one post and execution time > half of max_time
							if (time() - $this->start_time >= $this->max_time) {
								$break = true;
								break;
							}
						}
					}
				}
				if (!empty($values)) {
					// Attention! All items in the variable $values escaped on the loop above - esc_sql($value)
					$q = "INSERT INTO ".esc_sql($wpdb->prefix . $table)." VALUES {$values}";
					$wpdb->query($q);
				}
				if ($this->options['debug']) dfl(sprintf(__('Imported %s. Elapsed time %s sec. of %s sec.', 'citygov'), $result.'%', time() - $this->start_time, $this->max_time));
				if ($break) break;
			}
		} else
			if ($this->options['debug']) dfl(sprintf(__('Error unserialize data from the file %s', 'citygov'), $this->options['file_with_'.$slug]));
		return $result;
	}

	
	// Replace uploads dir to new url
	function replace_uploads($str) {
		return citygov_replace_uploads_url($str);
	}

	
	// Replace strings then export data
	function prepare_data($str) {
		$need_ser = false;
		if (is_string($str) && citygov_substr($str, 0, 2)=='a:') {
			$str = citygov_unserialize($str);
			$need_ser = is_array($str);
		}
		if (is_array($str) && count($str) > 0) {
			foreach ($str as $k=>$v) {
				$str[$k] = $this->prepare_data($v);
			}
		} else if (is_string($str)) {
			// Replace developers domain to demo domain
			if ($this->options['domain_dev']!=$this->options['domain_demo'])
				$str = str_replace($this->options['domain_dev'], $this->options['domain_demo'], $str);
			// Replace DOS-style line endings to UNIX-style
			$str = str_replace("\r\n", "\n", $str);
		}
		if ($need_ser) $str = serialize($str);
		return $str;
	}
}
?>