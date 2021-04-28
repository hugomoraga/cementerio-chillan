<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'citygov_template_form_2_theme_setup' ) ) {
	add_action( 'citygov_action_before_init_theme', 'citygov_template_form_2_theme_setup', 1 );
	function citygov_template_form_2_theme_setup() {
		citygov_add_template(array(
			'layout' => 'form_2',
			'mode'   => 'forms',
			'title'  => esc_html__('Contact Form 2', 'citygov')
			));
	}
}

// Template output
if ( !function_exists( 'citygov_template_form_2_output' ) ) {
	function citygov_template_form_2_output($post_options, $post_data) {
		$address_1 = citygov_get_theme_option('contact_address_1');
		$address_2 = citygov_get_theme_option('contact_address_2');
		$phone = citygov_get_theme_option('contact_phone');
		$fax = citygov_get_theme_option('contact_fax');
		$email = citygov_get_theme_option('contact_email');
		$open_hours = citygov_get_theme_option('contact_open_hours');
		?>
		<div class="sc_columns columns_wrap">
			<div class="sc_form_fields column-1_2">
				<form <?php echo !empty($post_options['id']) ? ' id="'.esc_attr($post_options['id']).'_form"' : ''; ?> data-formtype="<?php echo esc_attr($post_options['layout']); ?>" method="post" action="<?php echo esc_url($post_options['action'] ? $post_options['action'] : admin_url('admin-ajax.php')); ?>">
					<?php citygov_sc_form_show_fields($post_options['fields']); ?>
					<div class="sc_form_info">
						<div class="sc_form_item sc_form_field label_over"><label class="required" for="sc_form_username"><?php esc_html_e('Name', 'citygov'); ?></label><input id="sc_form_username" type="text" name="username" placeholder="<?php esc_attr_e('Name *', 'citygov'); ?>"></div>
						<div class="sc_form_item sc_form_field label_over"><label class="required" for="sc_form_email"><?php esc_html_e('E-mail', 'citygov'); ?></label><input id="sc_form_email" type="text" name="email" placeholder="<?php esc_attr_e('E-mail *', 'citygov'); ?>"></div>
					</div>
					<div class="sc_form_item sc_form_message label_over"><label class="required" for="sc_form_message"><?php esc_html_e('Message', 'citygov'); ?></label><textarea id="sc_form_message" name="message" placeholder="<?php esc_attr_e('Message', 'citygov'); ?>"></textarea></div>
					<div class="sc_form_item sc_form_button"><button><?php esc_html_e('Send Message', 'citygov'); ?></button></div>
					<div class="result sc_infobox"></div>
				</form>
			</div><div class="sc_form_address column-1_2">
                <div class="sc_form_address_field">
                    <span class="sc_form_address_label"><?php esc_html_e('Address', 'citygov'); ?></span>
                    <span class="sc_form_address_data"><?php echo trim($address_1) . (!empty($address_1) && !empty($address_2) ? ', ' : '') . $address_2; ?></span>
                </div>
                <div class="sc_form_address_field">
                    <span class="sc_form_address_label"><?php esc_html_e('Phone number', 'citygov'); ?></span>
                    <span class="sc_form_address_data"><?php echo trim($phone) . (!empty($phone) && !empty($fax) ? ', ' : '') . $fax; ?></span>
                </div>
                <div class="sc_form_address_field">
                    <span class="sc_form_address_label"><?php esc_html_e('We are open', 'citygov'); ?></span>
                    <span class="sc_form_address_data"><?php echo trim($open_hours); ?></span>
                </div>
            </div>
		</div>
		<?php
	}
}
?>