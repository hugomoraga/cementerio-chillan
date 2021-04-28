<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'citygov_template_clients_1_theme_setup' ) ) {
	add_action( 'citygov_action_before_init_theme', 'citygov_template_clients_1_theme_setup', 1 );
	function citygov_template_clients_1_theme_setup() {
		citygov_add_template(array(
			'layout' => 'clients-1',
			'template' => 'clients-1',
			'mode'   => 'clients',
			'title'  => esc_html__('Clients /Style 1/', 'citygov'),
			'thumb_title'  => esc_html__('Original image', 'citygov'),
			'w'		 => null,
			'h_crop' => null,
			'h'      => null
		));
	}
}

// Template output
if ( !function_exists( 'citygov_template_clients_1_output' ) ) {
	function citygov_template_clients_1_output($post_options, $post_data) {
		$show_title = !empty($post_data['post_title']);
		$parts = explode('_', $post_options['layout']);
		$style = $parts[0];
		$columns = max(1, min(12, empty($parts[1]) ? (!empty($post_options['columns_count']) ? $post_options['columns_count'] : 1) : (int) $parts[1]));
		if (citygov_param_is_on($post_options['slider'])) {
			?><div class="swiper-slide" data-style="<?php echo esc_attr($post_options['tag_css_wh']); ?>" style="<?php echo esc_attr($post_options['tag_css_wh']); ?>"><?php
		} else if ($columns > 1) {
			?><div class="column-1_<?php echo esc_attr($columns); ?> column_padding_bottom"><?php
		}
		?>
			<div<?php echo !empty($post_options['tag_id']) ? ' id="'.esc_attr($post_options['tag_id']).'"' : ''; ?> class="sc_clients_item sc_clients_item_<?php echo esc_attr($post_options['number']) . ($post_options['number'] % 2 == 1 ? ' odd' : ' even') . ($post_options['number'] == 1 ? ' first' : '').(!empty($post_options['tag_class']) ? ' '.esc_attr($post_options['tag_class']) : ''); ?>"<?php echo (!empty($post_options['tag_css']) ? ' style="'.esc_attr($post_options['tag_css']).'"' : '') . (!citygov_param_is_off($post_options['tag_animation']) ? ' data-animation="'.esc_attr(citygov_get_animation_classes($post_options['tag_animation'])).'"' : '');?>>
				<?php if ($post_options['client_image']) { ?>
				<div class="sc_client_image"><?php
					echo (!empty($post_options['client_link']) ? '<a href="'.esc_url($post_options['client_link']).'">'	: '')
						. trim($post_options['client_image'])
						. (!empty($post_options['client_link']) ? '</a>' : ''); 
				?></div>
				<?php } ?>
			</div>
		<?php
		if (citygov_param_is_on($post_options['slider']) || $columns > 1) {
			?></div><?php
		}
	}
}
?>