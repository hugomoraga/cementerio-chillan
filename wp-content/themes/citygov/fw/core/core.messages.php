<?php
/**
 * CityGov Framework: messages subsystem
 *
 * @package	citygov
 * @since	citygov 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Theme init
if (!function_exists('citygov_messages_theme_setup')) {
	add_action( 'citygov_action_before_init_theme', 'citygov_messages_theme_setup' );
	function citygov_messages_theme_setup() {
		// Core messages strings
		add_action('citygov_action_add_scripts_inline', 'citygov_messages_add_scripts_inline');
	}
}


/* Session messages
------------------------------------------------------------------------------------- */

if (!function_exists('citygov_get_error_msg')) {
	function citygov_get_error_msg() {
		return citygov_storage_get('error_msg');
	}
}

if (!function_exists('citygov_set_error_msg')) {
	function citygov_set_error_msg($msg) {
		$msg2 = citygov_get_error_msg();
		citygov_storage_set('error_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}

if (!function_exists('citygov_get_success_msg')) {
	function citygov_get_success_msg() {
		return citygov_storage_get('success_msg');
	}
}

if (!function_exists('citygov_set_success_msg')) {
	function citygov_set_success_msg($msg) {
		$msg2 = citygov_get_success_msg();
		citygov_storage_set('success_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}

if (!function_exists('citygov_get_notice_msg')) {
	function citygov_get_notice_msg() {
		return citygov_storage_get('notice_msg');
	}
}

if (!function_exists('citygov_set_notice_msg')) {
	function citygov_set_notice_msg($msg) {
		$msg2 = citygov_get_notice_msg();
		citygov_storage_set('notice_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}


/* System messages (save when page reload)
------------------------------------------------------------------------------------- */
if (!function_exists('citygov_set_system_message')) {
	function citygov_set_system_message($msg, $status='info', $hdr='') {
		update_option('citygov_message', array('message' => $msg, 'status' => $status, 'header' => $hdr));
	}
}

if (!function_exists('citygov_get_system_message')) {
	function citygov_get_system_message($del=false) {
		$msg = get_option('citygov_message', false);
		if (!$msg)
			$msg = array('message' => '', 'status' => '', 'header' => '');
		else if ($del)
			citygov_del_system_message();
		return $msg;
	}
}

if (!function_exists('citygov_del_system_message')) {
	function citygov_del_system_message() {
		delete_option('citygov_message');
	}
}


/* Messages strings
------------------------------------------------------------------------------------- */

if (!function_exists('citygov_messages_add_scripts_inline')) {
	function citygov_messages_add_scripts_inline() {
		echo '<script type="text/javascript">'
			
			. "if (typeof CITYGOV_STORAGE == 'undefined') var CITYGOV_STORAGE = {};"
			
			// Strings for translation
			. 'CITYGOV_STORAGE["strings"] = {'
				. 'ajax_error: 			"' . addslashes(esc_html__('Invalid server answer', 'citygov')) . '",'
				. 'bookmark_add: 		"' . addslashes(esc_html__('Add the bookmark', 'citygov')) . '",'
				. 'bookmark_added:		"' . addslashes(esc_html__('Current page has been successfully added to the bookmarks. You can see it in the right panel on the tab \'Bookmarks\'', 'citygov')) . '",'
				. 'bookmark_del: 		"' . addslashes(esc_html__('Delete this bookmark', 'citygov')) . '",'
				. 'bookmark_title:		"' . addslashes(esc_html__('Enter bookmark title', 'citygov')) . '",'
				. 'bookmark_exists:		"' . addslashes(esc_html__('Current page already exists in the bookmarks list', 'citygov')) . '",'
				. 'search_error:		"' . addslashes(esc_html__('Error occurs in AJAX search! Please, type your query and press search icon for the traditional search way.', 'citygov')) . '",'
				. 'email_confirm:		"' . addslashes(esc_html__('On the e-mail address "%s" we sent a confirmation email. Please, open it and click on the link.', 'citygov')) . '",'
				. 'reviews_vote:		"' . addslashes(esc_html__('Thanks for your vote! New average rating is:', 'citygov')) . '",'
				. 'reviews_error:		"' . addslashes(esc_html__('Error saving your vote! Please, try again later.', 'citygov')) . '",'
				. 'error_like:			"' . addslashes(esc_html__('Error saving your like! Please, try again later.', 'citygov')) . '",'
				. 'error_global:		"' . addslashes(esc_html__('Global error text', 'citygov')) . '",'
				. 'name_empty:			"' . addslashes(esc_html__('The name can\'t be empty', 'citygov')) . '",'
				. 'name_long:			"' . addslashes(esc_html__('Too long name', 'citygov')) . '",'
				. 'email_empty:			"' . addslashes(esc_html__('Too short (or empty) email address', 'citygov')) . '",'
				. 'email_long:			"' . addslashes(esc_html__('Too long email address', 'citygov')) . '",'
				. 'email_not_valid:		"' . addslashes(esc_html__('Invalid email address', 'citygov')) . '",'
				. 'subject_empty:		"' . addslashes(esc_html__('The subject can\'t be empty', 'citygov')) . '",'
				. 'subject_long:		"' . addslashes(esc_html__('Too long subject', 'citygov')) . '",'
				. 'text_empty:			"' . addslashes(esc_html__('The message text can\'t be empty', 'citygov')) . '",'
				. 'text_long:			"' . addslashes(esc_html__('Too long message text', 'citygov')) . '",'
				. 'send_complete:		"' . addslashes(esc_html__("Send message complete!", 'citygov')) . '",'
				. 'send_error:			"' . addslashes(esc_html__('Transmit failed!', 'citygov')) . '",'
				. 'login_empty:			"' . addslashes(esc_html__('The Login field can\'t be empty', 'citygov')) . '",'
				. 'login_long:			"' . addslashes(esc_html__('Too long login field', 'citygov')) . '",'
				. 'login_success:		"' . addslashes(esc_html__('Login success! The page will be reloaded in 3 sec.', 'citygov')) . '",'
				. 'login_failed:		"' . addslashes(esc_html__('Login failed!', 'citygov')) . '",'
				. 'password_empty:		"' . addslashes(esc_html__('The password can\'t be empty and shorter then 4 characters', 'citygov')) . '",'
				. 'password_long:		"' . addslashes(esc_html__('Too long password', 'citygov')) . '",'
				. 'password_not_equal:	"' . addslashes(esc_html__('The passwords in both fields are not equal', 'citygov')) . '",'
				. 'registration_success:"' . addslashes(esc_html__('Registration success! Please log in!', 'citygov')) . '",'
				. 'registration_failed:	"' . addslashes(esc_html__('Registration failed!', 'citygov')) . '",'
				. 'geocode_error:		"' . addslashes(esc_html__('Geocode was not successful for the following reason:', 'citygov')) . '",'
				. 'googlemap_not_avail:	"' . addslashes(esc_html__('Google map API not available!', 'citygov')) . '",'
				. 'editor_save_success:	"' . addslashes(esc_html__("Post content saved!", 'citygov')) . '",'
				. 'editor_save_error:	"' . addslashes(esc_html__("Error saving post data!", 'citygov')) . '",'
				. 'editor_delete_post:	"' . addslashes(esc_html__("You really want to delete the current post?", 'citygov')) . '",'
				. 'editor_delete_post_header:"' . addslashes(esc_html__("Delete post", 'citygov')) . '",'
				. 'editor_delete_success:	"' . addslashes(esc_html__("Post deleted!", 'citygov')) . '",'
				. 'editor_delete_error:		"' . addslashes(esc_html__("Error deleting post!", 'citygov')) . '",'
				. 'editor_caption_cancel:	"' . addslashes(esc_html__('Cancel', 'citygov')) . '",'
				. 'editor_caption_close:	"' . addslashes(esc_html__('Close', 'citygov')) . '"'
				. '};'
			
			. '</script>';
	}
}
?>