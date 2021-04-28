// Init scripts
jQuery(document).ready(function(){
	"use strict";
	
	// Settings and constants
	CITYGOV_STORAGE['shortcodes_delimiter'] = ',';		// Delimiter for multiple values
	CITYGOV_STORAGE['shortcodes_popup'] = null;		// Popup with current shortcode settings
	CITYGOV_STORAGE['shortcodes_current_idx'] = '';	// Current shortcode's index
	CITYGOV_STORAGE['shortcodes_tab_clone_tab'] = '<li id="citygov_shortcodes_tab_{id}" data-id="{id}"><a href="#citygov_shortcodes_tab_{id}_content"><span class="iconadmin-{icon}"></span>{title}</a></li>';
	CITYGOV_STORAGE['shortcodes_tab_clone_content'] = '';

	// Shortcode selector - "change" event handler - add selected shortcode in editor
	jQuery('body').on('change', ".sc_selector", function() {
		"use strict";
		CITYGOV_STORAGE['shortcodes_current_idx'] = jQuery(this).find(":selected").val();
		if (CITYGOV_STORAGE['shortcodes_current_idx'] == '') return;
		var sc = citygov_clone_object(CITYGOV_STORAGE['shortcodes'][CITYGOV_STORAGE['shortcodes_current_idx']]);
		var hdr = sc.title;
		var content = "";
		try {
			content = tinyMCE.activeEditor ? tinyMCE.activeEditor.selection.getContent({format : 'raw'}) : jQuery('#wp-content-editor-container textarea').selection();
		} catch(e) {};
		if (content) {
			for (var i in sc.params) {
				if (i == '_content_') {
					sc.params[i].value = content;
					break;
				}
			}
		}
		var html = (!citygov_empty(sc.desc) ? '<p>'+sc.desc+'</p>' : '')
			+ citygov_shortcodes_prepare_layout(sc);


		// Show Dialog popup
		CITYGOV_STORAGE['shortcodes_popup'] = citygov_message_dialog(html, hdr,
			function(popup) {
				"use strict";
				citygov_options_init(popup);
				popup.find('.citygov_options_tab_content').css({
					maxHeight: jQuery(window).height() - 300 + 'px',
					overflow: 'auto'
				});
			},
			function(btn, popup) {
				"use strict";
				if (btn != 1) return;
				var sc = citygov_shortcodes_get_code(CITYGOV_STORAGE['shortcodes_popup']);
				if (tinyMCE.activeEditor) {
					if ( !tinyMCE.activeEditor.isHidden() )
						tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, sc );
					//else if (typeof wpActiveEditor != 'undefined' && wpActiveEditor != '') {
					//	document.getElementById( wpActiveEditor ).value += sc;
					else
						send_to_editor(sc);
				} else
					send_to_editor(sc);
			});

		// Set first item active
		jQuery(this).get(0).options[0].selected = true;

		// Add new child tab
		CITYGOV_STORAGE['shortcodes_popup'].find('.citygov_shortcodes_tab').on('tabsbeforeactivate', function (e, ui) {
			if (ui.newTab.data('id')=='add') {
				citygov_shortcodes_add_tab(ui.newTab);
				e.stopImmediatePropagation();
				e.preventDefault();
				return false;
			}
		});

		// Delete child tab
		CITYGOV_STORAGE['shortcodes_popup'].find('.citygov_shortcodes_tab > ul').on('click', '> li+li > a > span', function (e) {
			var tab = jQuery(this).parents('li');
			var idx = tab.data('id');
			if (parseInt(idx) > 1) {
				if (tab.hasClass('ui-state-active')) {
					tab.prev().find('a').trigger('click');
				}
				tab.parents('.citygov_shortcodes_tab').find('.citygov_options_tab_content').eq(idx).remove();
				tab.remove();
				e.preventDefault();
				return false;
			}
		});

		return false;
	});

});



// Return result code
//------------------------------------------------------------------------------------------
function citygov_shortcodes_get_code(popup) {
	CITYGOV_STORAGE['sc_custom'] = '';
	
	var sc_name = CITYGOV_STORAGE['shortcodes_current_idx'];
	var sc = CITYGOV_STORAGE['shortcodes'][sc_name];
	var tabs = popup.find('.citygov_shortcodes_tab > ul > li');
	var decor = !citygov_isset(sc.decorate) || sc.decorate;
	var rez = '[' + sc_name + citygov_shortcodes_get_code_from_tab(popup.find('#citygov_shortcodes_tab_0_content').eq(0)) + ']'
			// + (decor ? '\n' : '')
			;
	if (citygov_isset(sc.children)) {
		if (CITYGOV_STORAGE['sc_custom']!='no') {
			var decor2 = !citygov_isset(sc.children.decorate) || sc.children.decorate;
			for (var i=0; i<tabs.length; i++) {
				var tab = tabs.eq(i);
				var idx = tab.data('id');
				if (isNaN(idx) || parseInt(idx) < 1) continue;
				var content = popup.find('#citygov_shortcodes_tab_' + idx + '_content').eq(0);
				rez += (decor2 ? '\n\t' : '') + '[' + sc.children.name + citygov_shortcodes_get_code_from_tab(content) + ']';	// + (decor2 ? '\n' : '');
				if (citygov_isset(sc.children.container) && sc.children.container) {
					if (content.find('[data-param="_content_"]').length > 0) {
						rez += 
							//(decor2 ? '\t\t' : '') + 
							content.find('[data-param="_content_"]').val()
							// + (decor2 ? '\n' : '')
							;
					}
					rez += 
						//(decor2 ? '\t' : '') + 
						'[/' + sc.children.name + ']'
						// + (decor ? '\n' : '')
						;
				}
			}
		}
	} else if (citygov_isset(sc.container) && sc.container && popup.find('#citygov_shortcodes_tab_0_content [data-param="_content_"]').length > 0) {
		rez += 
			//(decor ? '\t' : '') + 
			popup.find('#citygov_shortcodes_tab_0_content [data-param="_content_"]').val()
			// + (decor ? '\n' : '')
			;
	}
	if (citygov_isset(sc.container) && sc.container || citygov_isset(sc.children))
		rez += 
			(citygov_isset(sc.children) && decor && CITYGOV_STORAGE['sc_custom']!='no' ? '\n' : '')
			+ '[/' + sc_name + ']'
			 //+ (decor ? '\n' : '')
			 ;
	return rez;
}

// Collect all parameters from tab into string
function citygov_shortcodes_get_code_from_tab(tab) {
	var rez = ''
	var mainTab = tab.attr('id').indexOf('tab_0') > 0;
	tab.find('[data-param]').each(function () {
		var field = jQuery(this);
		var param = field.data('param');
		if (!field.parents('.citygov_options_field').hasClass('citygov_options_no_use') && param.substr(0, 1)!='_' && !citygov_empty(field.val()) && field.val()!='none' && (field.attr('type') != 'checkbox' || field.get(0).checked)) {
			rez += ' '+param+'="'+citygov_shortcodes_prepare_value(field.val())+'"';
		}
		// On main tab detect param "custom"
		if (mainTab && param=='custom') {
			CITYGOV_STORAGE['sc_custom'] = field.val();
		}
	});
	// Get additional params for general tab from items tabs
	if (CITYGOV_STORAGE['sc_custom']!='no' && mainTab) {
		var sc = CITYGOV_STORAGE['shortcodes'][CITYGOV_STORAGE['shortcodes_current_idx']];
		var sc_name = CITYGOV_STORAGE['shortcodes_current_idx'];
		if (sc_name == 'trx_columns' || sc_name == 'trx_skills' || sc_name == 'trx_team' || sc_name == 'trx_price_table') {	// Determine "count" parameter
			var cnt = 0;
			tab.siblings('div').each(function() {
				var item_tab = jQuery(this);
				var merge = parseInt(item_tab.find('[data-param="span"]').val());
				cnt += !isNaN(merge) && merge > 0 ? merge : 1;
			});
			rez += ' count="'+cnt+'"';
		}
	}
	return rez;
}


// Shortcode parameters builder
//-------------------------------------------------------------------------------------------

// Prepare layout from shortcode object (array)
function citygov_shortcodes_prepare_layout(field) {
	"use strict";
	// Make params cloneable
	field['params'] = [field['params']];
	if (!citygov_empty(field.children)) {
		field.children['params'] = [field.children['params']];
	}
	// Prepare output
	var output = '<div class="citygov_shortcodes_body citygov_options_body"><form>';
	output += citygov_shortcodes_show_tabs(field);
	output += citygov_shortcodes_show_field(field, 0);
	if (!citygov_empty(field.children)) {
		CITYGOV_STORAGE['shortcodes_tab_clone_content'] = citygov_shortcodes_show_field(field.children, 1);
		output += CITYGOV_STORAGE['shortcodes_tab_clone_content'];
	}
	output += '</div></form></div>';
	return output;
}



// Show tabs
function citygov_shortcodes_show_tabs(field) {
	"use strict";
	// html output
	var output = '<div class="citygov_shortcodes_tab citygov_options_container citygov_options_tab">'
		+ '<ul>'
		+ CITYGOV_STORAGE['shortcodes_tab_clone_tab'].replace(/{id}/g, 0).replace('{icon}', 'cog').replace('{title}', 'General');
	if (citygov_isset(field.children)) {
		for (var i=0; i<field.children.params.length; i++)
			output += CITYGOV_STORAGE['shortcodes_tab_clone_tab'].replace(/{id}/g, i+1).replace('{icon}', 'cancel').replace('{title}', field.children.title + ' ' + (i+1));
		output += CITYGOV_STORAGE['shortcodes_tab_clone_tab'].replace(/{id}/g, 'add').replace('{icon}', 'list-add').replace('{title}', '');
	}
	output += '</ul>';
	return output;
}

// Add new tab
function citygov_shortcodes_add_tab(tab) {
	"use strict";
	var idx = 0;
	tab.siblings().each(function () {
		"use strict";
		var i = parseInt(jQuery(this).data('id'));
		if (i > idx) idx = i;
	});
	idx++;
	tab.before( CITYGOV_STORAGE['shortcodes_tab_clone_tab'].replace(/{id}/g, idx).replace('{icon}', 'cancel').replace('{title}', CITYGOV_STORAGE['shortcodes'][CITYGOV_STORAGE['shortcodes_current_idx']].children.title + ' ' + idx) );
	tab.parents('.citygov_shortcodes_tab').append(CITYGOV_STORAGE['shortcodes_tab_clone_content'].replace(/tab_1_/g, 'tab_' + idx + '_'));
	tab.parents('.citygov_shortcodes_tab').tabs('refresh');
	citygov_options_init(tab.parents('.citygov_shortcodes_tab').find('.citygov_options_tab_content').eq(idx));
	tab.prev().find('a').trigger('click');
}



// Show one field layout
function citygov_shortcodes_show_field(field, tab_idx) {
	"use strict";
	
	// html output
	var output = '';

	// Parse field params
	for (var clone_num in field['params']) {
		var tab_id = 'tab_' + (parseInt(tab_idx) + parseInt(clone_num));
		output += '<div id="citygov_shortcodes_' + tab_id + '_content" class="citygov_options_content citygov_options_tab_content">';

		for (var param_num in field['params'][clone_num]) {
			
			var param = field['params'][clone_num][param_num];
			var id = tab_id + '_' + param_num;
	
			// Divider after field
			var divider = citygov_isset(param['divider']) && param['divider'] ? ' citygov_options_divider' : '';
		
			// Setup default parameters
			if (param['type']=='media') {
				if (!citygov_isset(param['before'])) param['before'] = {};
				param['before'] = citygov_merge_objects({
						'title': 'Choose image',
						'action': 'media_upload',
						'type': 'image',
						'multiple': false,
						'sizes': false,
						'linked_field': '',
						'captions': { 	
							'choose': 'Choose image',
							'update': 'Select image'
							}
					}, param['before']);
				if (!citygov_isset(param['after'])) param['after'] = {};
				param['after'] = citygov_merge_objects({
						'icon': 'iconadmin-cancel',
						'action': 'media_reset'
					}, param['after']);
			}
			if (param['type']=='color' && (CITYGOV_STORAGE['shortcodes_cp']=='tiny' || (citygov_isset(param['style']) && param['style']!='wp'))) {
				if (!citygov_isset(param['after'])) param['after'] = {};
				param['after'] = citygov_merge_objects({
						'icon': 'iconadmin-cancel',
						'action': 'color_reset'
					}, param['after']);
			}
		
			// Buttons before and after field
			var before = '', after = '', buttons_classes = '', rez, rez2, i, key, opt;
			
			if (citygov_isset(param['before'])) {
				rez = citygov_shortcodes_action_button(param['before'], 'before');
				before = rez[0];
				buttons_classes += rez[1];
			}
			if (citygov_isset(param['after'])) {
				rez = citygov_shortcodes_action_button(param['after'], 'after');
				after = rez[0];
				buttons_classes += rez[1];
			}
			if (citygov_in_array(param['type'], ['list', 'select', 'fonts']) || (param['type']=='socials' && (citygov_empty(param['style']) || param['style']=='icons'))) {
				buttons_classes += ' citygov_options_button_after_small';
			}

			if (param['type'] != 'hidden') {
				output += '<div class="citygov_options_field'
					+ ' citygov_options_field_' + (citygov_in_array(param['type'], ['list','fonts']) ? 'select' : param['type'])
					+ (citygov_in_array(param['type'], ['media', 'fonts', 'list', 'select', 'socials', 'date', 'time']) ? ' citygov_options_field_text'  : '')
					+ (param['type']=='socials' && !citygov_empty(param['style']) && param['style']=='images' ? ' citygov_options_field_images'  : '')
					+ (param['type']=='socials' && (citygov_empty(param['style']) || param['style']=='icons') ? ' citygov_options_field_icons'  : '')
					+ (citygov_isset(param['dir']) && param['dir']=='vertical' ? ' citygov_options_vertical' : '')
					+ (!citygov_empty(param['multiple']) ? ' citygov_options_multiple' : '')
					+ (citygov_isset(param['size']) ? ' citygov_options_size_'+param['size'] : '')
					+ (citygov_isset(param['class']) ? ' ' + param['class'] : '')
					+ divider 
					+ '">' 
					+ "\n"
					+ '<label class="citygov_options_field_label" for="' + id + '">' + param['title']
					+ '</label>'
					+ "\n"
					+ '<div class="citygov_options_field_content'
					+ buttons_classes
					+ '">'
					+ "\n";
			}
			
			if (!citygov_isset(param['value'])) {
				param['value'] = '';
			}
			

			switch ( param['type'] ) {
	
			case 'hidden':
				output += '<input class="citygov_options_input citygov_options_input_hidden" name="' + id + '" id="' + id + '" type="hidden" value="' + citygov_shortcodes_prepare_value(param['value']) + '" data-param="' + citygov_shortcodes_prepare_value(param_num) + '" />';
			break;

			case 'date':
				if (citygov_isset(param['style']) && param['style']=='inline') {
					output += '<div class="citygov_options_input_date"'
						+ ' id="' + id + '_calendar"'
						+ ' data-format="' + (!citygov_empty(param['format']) ? param['format'] : 'yy-mm-dd') + '"'
						+ ' data-months="' + (!citygov_empty(param['months']) ? max(1, min(3, param['months'])) : 1) + '"'
						+ ' data-linked-field="' + (!citygov_empty(data['linked_field']) ? data['linked_field'] : id) + '"'
						+ '></div>'
						+ '<input id="' + id + '"'
							+ ' name="' + id + '"'
							+ ' type="hidden"'
							+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"'
							+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
							+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
							+ ' />';
				} else {
					output += '<input class="citygov_options_input citygov_options_input_date' + (!citygov_empty(param['mask']) ? ' citygov_options_input_masked' : '') + '"'
						+ ' name="' + id + '"'
						+ ' id="' + id + '"'
						+ ' type="text"'
						+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"'
						+ ' data-format="' + (!citygov_empty(param['format']) ? param['format'] : 'yy-mm-dd') + '"'
						+ ' data-months="' + (!citygov_empty(param['months']) ? max(1, min(3, param['months'])) : 1) + '"'
						+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
						+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
						+ ' />'
						+ before 
						+ after;
				}
			break;

			case 'text':
				output += '<input class="citygov_options_input citygov_options_input_text' + (!citygov_empty(param['mask']) ? ' citygov_options_input_masked' : '') + '"'
					+ ' name="' + id + '"'
					+ ' id="' + id + '"'
					+ ' type="text"'
					+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"'
					+ (!citygov_empty(param['mask']) ? ' data-mask="'+param['mask']+'"' : '') 
					+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
					+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
					+ ' />'
				+ before 
				+ after;
			break;
		
			case 'textarea':
				var cols = citygov_isset(param['cols']) && param['cols'] > 10 ? param['cols'] : '40';
				var rows = citygov_isset(param['rows']) && param['rows'] > 1 ? param['rows'] : '8';
				output += '<textarea class="citygov_options_input citygov_options_input_textarea"'
					+ ' name="' + id + '"'
					+ ' id="' + id + '"'
					+ ' cols="' + cols + '"'
					+ ' rows="' + rows + '"'
					+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
					+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
					+ '>'
					+ param['value']
					+ '</textarea>';
			break;

			case 'spinner':
				output += '<input class="citygov_options_input citygov_options_input_spinner' + (!citygov_empty(param['mask']) ? ' citygov_options_input_masked' : '') + '"'
					+ ' name="' + id + '"'
					+ ' id="' + id + '"'
					+ ' type="text"'
					+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"' 
					+ (!citygov_empty(param['mask']) ? ' data-mask="'+param['mask']+'"' : '') 
					+ (citygov_isset(param['min']) ? ' data-min="'+param['min']+'"' : '') 
					+ (citygov_isset(param['max']) ? ' data-max="'+param['max']+'"' : '') 
					+ (!citygov_empty(param['step']) ? ' data-step="'+param['step']+'"' : '') 
					+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
					+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
					+ ' />' 
					+ '<span class="citygov_options_arrows"><span class="citygov_options_arrow_up iconadmin-up-dir"></span><span class="citygov_options_arrow_down iconadmin-down-dir"></span></span>';
			break;

			case 'tags':
				var tags = param['value'].split(CITYGOV_STORAGE['shortcodes_delimiter']);
				if (tags.length > 0) {
					for (i=0; i<tags.length; i++) {
						if (citygov_empty(tags[i])) continue;
						output += '<span class="citygov_options_tag iconadmin-cancel">' + tags[i] + '</span>';
					}
				}
				output += '<input class="citygov_options_input_tags"'
					+ ' type="text"'
					+ ' value=""'
					+ ' />'
					+ '<input name="' + id + '"'
						+ ' type="hidden"'
						+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"'
						+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
						+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
						+ ' />';
			break;
		
			case "checkbox": 
				output += '<input type="checkbox" class="citygov_options_input citygov_options_input_checkbox"'
					+ ' name="' + id + '"'
					+ ' id="' + id + '"'
					+ ' value="true"' 
					+ (param['value'] == 'true' ? ' checked="checked"' : '') 
					+ (!citygov_empty(param['disabled']) ? ' readonly="readonly"' : '') 
					+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
					+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
					+ ' />'
					+ '<label for="' + id + '" class="' + (!citygov_empty(param['disabled']) ? 'citygov_options_state_disabled' : '') + (param['value']=='true' ? ' citygov_options_state_checked' : '') + '"><span class="citygov_options_input_checkbox_image iconadmin-check"></span>' + (!citygov_empty(param['label']) ? param['label'] : param['title']) + '</label>';
			break;
		
			case "radio":
				for (key in param['options']) { 
					output += '<span class="citygov_options_radioitem"><input class="citygov_options_input citygov_options_input_radio" type="radio"'
						+ ' name="' + id + '"'
						+ ' value="' + citygov_shortcodes_prepare_value(key) + '"'
						+ ' data-value="' + citygov_shortcodes_prepare_value(key) + '"'
						+ (param['value'] == key ? ' checked="checked"' : '') 
						+ ' id="' + id + '_' + key + '"'
						+ ' />'
						+ '<label for="' + id + '_' + key + '"' + (param['value'] == key ? ' class="citygov_options_state_checked"' : '') + '><span class="citygov_options_input_radio_image iconadmin-circle-empty' + (param['value'] == key ? ' iconadmin-dot-circled' : '') + '"></span>' + param['options'][key] + '</label></span>';
				}
				output += '<input type="hidden"'
						+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"'
						+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
						+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
						+ ' />';

			break;
		
			case "switch":
				opt = [];
				i = 0;
				for (key in param['options']) {
					opt[i++] = {'key': key, 'title': param['options'][key]};
					if (i==2) break;
				}
				output += '<input name="' + id + '"'
					+ ' type="hidden"'
					+ ' value="' + citygov_shortcodes_prepare_value(citygov_empty(param['value']) ? opt[0]['key'] : param['value']) + '"'
					+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
					+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
					+ ' />'
					+ '<span class="citygov_options_switch' + (param['value']==opt[1]['key'] ? ' citygov_options_state_off' : '') + '"><span class="citygov_options_switch_inner iconadmin-circle"><span class="citygov_options_switch_val1" data-value="' + opt[0]['key'] + '">' + opt[0]['title'] + '</span><span class="citygov_options_switch_val2" data-value="' + opt[1]['key'] + '">' + opt[1]['title'] + '</span></span></span>';
			break;

			case 'media':
				output += '<input class="citygov_options_input citygov_options_input_text citygov_options_input_media"'
					+ ' name="' + id + '"'
					+ ' id="' + id + '"'
					+ ' type="text"'
					+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"'
					+ (!citygov_isset(param['readonly']) || param['readonly'] ? ' readonly="readonly"' : '')
					+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
					+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
					+ ' />'
					+ before 
					+ after;
				if (!citygov_empty(param['value'])) {
					var fname = citygov_get_file_name(param['value']);
					var fext  = citygov_get_file_ext(param['value']);
					output += '<a class="citygov_options_image_preview" rel="prettyPhoto" target="_blank" href="' + param['value'] + '">' + (fext!='' && citygov_in_list('jpg,png,gif', fext, ',') ? '<img src="'+param['value']+'" alt="" />' : '<span>'+fname+'</span>') + '</a>';
				}
			break;
		
			case 'button':
				rez = citygov_shortcodes_action_button(param, 'button');
				output += rez[0];
			break;

			case 'range':
				output += '<div class="citygov_options_input_range" data-step="'+(!citygov_empty(param['step']) ? param['step'] : 1) + '">'
					+ '<span class="citygov_options_range_scale"><span class="citygov_options_range_scale_filled"></span></span>';
				if (param['value'].toString().indexOf(CITYGOV_STORAGE['shortcodes_delimiter']) == -1)
					param['value'] = Math.min(param['max'], Math.max(param['min'], param['value']));
				var sliders = param['value'].toString().split(CITYGOV_STORAGE['shortcodes_delimiter']);
				for (i=0; i<sliders.length; i++) {
					output += '<span class="citygov_options_range_slider"><span class="citygov_options_range_slider_value">' + sliders[i] + '</span><span class="citygov_options_range_slider_button"></span></span>';
				}
				output += '<span class="citygov_options_range_min">' + param['min'] + '</span><span class="citygov_options_range_max">' + param['max'] + '</span>'
					+ '<input name="' + id + '"'
						+ ' type="hidden"'
						+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"'
						+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
						+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
						+ ' />'
					+ '</div>';			
			break;
		
			case "checklist":
				for (key in param['options']) { 
					output += '<span class="citygov_options_listitem'
						+ (citygov_in_list(param['value'], key, CITYGOV_STORAGE['shortcodes_delimiter']) ? ' citygov_options_state_checked' : '') + '"'
						+ ' data-value="' + citygov_shortcodes_prepare_value(key) + '"'
						+ '>'
						+ param['options'][key]
						+ '</span>';
				}
				output += '<input name="' + id + '"'
					+ ' type="hidden"'
					+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"'
					+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
					+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
					+ ' />';
			break;
		
			case 'fonts':
				for (key in param['options']) {
					param['options'][key] = key;
				}
			case 'list':
			case 'select':
				if (!citygov_isset(param['options']) && !citygov_empty(param['from']) && !citygov_empty(param['to'])) {
					param['options'] = [];
					for (i = param['from']; i <= param['to']; i+=(!citygov_empty(param['step']) ? param['step'] : 1)) {
						param['options'][i] = i;
					}
				}
				rez = citygov_shortcodes_menu_list(param);
				if (citygov_empty(param['style']) || param['style']=='select') {
					output += '<input class="citygov_options_input citygov_options_input_select" type="text" value="' + citygov_shortcodes_prepare_value(rez[1]) + '"'
						+ ' readonly="readonly"'
						//+ (!citygov_empty(param['mask']) ? ' data-mask="'+param['mask']+'"' : '') 
						+ ' />'
						+ '<span class="citygov_options_field_after citygov_options_with_action iconadmin-down-open" onchange="citygov_options_action_show_menu(this);return false;"></span>';
				}
				output += rez[0]
					+ '<input name="' + id + '"'
						+ ' type="hidden"'
						+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"'
						+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
						+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
						+ ' />';
			break;

			case 'images':
				rez = citygov_shortcodes_menu_list(param);
				if (citygov_empty(param['style']) || param['style']=='select') {
					output += '<div class="citygov_options_caption_image iconadmin-down-open">'
						//+'<img src="' + rez[1] + '" alt="" />'
						+'<span style="background-image: url(' + rez[1] + ')"></span>'
						+'</div>';
				}
				output += rez[0]
					+ '<input name="' + id + '"'
						+ ' type="hidden"'
						+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"'
						+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
						+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
						+ ' />';
			break;
		
			case 'icons':
				rez = citygov_shortcodes_menu_list(param);
				if (citygov_empty(param['style']) || param['style']=='select') {
					output += '<div class="citygov_options_caption_icon iconadmin-down-open"><span class="' + rez[1] + '"></span></div>';
				}
				output += rez[0]
					+ '<input name="' + id + '"'
						+ ' type="hidden"'
						+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"'
						+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
						+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
						+ ' />';
			break;

			case 'socials':
				if (!citygov_is_object(param['value'])) param['value'] = {'url': '', 'icon': ''};
				rez = citygov_shortcodes_menu_list(param);
				if (citygov_empty(param['style']) || param['style']=='icons') {
					rez2 = citygov_shortcodes_action_button({
						'action': citygov_empty(param['style']) || param['style']=='icons' ? 'select_icon' : '',
						'icon': (citygov_empty(param['style']) || param['style']=='icons') && !citygov_empty(param['value']['icon']) ? param['value']['icon'] : 'iconadmin-users'
						}, 'after');
				} else
					rez2 = ['', ''];
				output += '<input class="citygov_options_input citygov_options_input_text citygov_options_input_socials' 
					+ (!citygov_empty(param['mask']) ? ' citygov_options_input_masked' : '') + '"'
					+ ' name="' + id + '"'
					+ ' id="' + id + '"'
					+ ' type="text" value="' + citygov_shortcodes_prepare_value(param['value']['url']) + '"' 
					+ (!citygov_empty(param['mask']) ? ' data-mask="'+param['mask']+'"' : '') 
					+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
					+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
					+ ' />'
					+ rez2[0];
				if (!citygov_empty(param['style']) && param['style']=='images') {
					output += '<div class="citygov_options_caption_image iconadmin-down-open">'
						//+'<img src="' + rez[1] + '" alt="" />'
						+'<span style="background-image: url(' + rez[1] + ')"></span>'
						+'</div>';
				}
				output += rez[0]
					+ '<input name="' + id + '_icon' + '" type="hidden" value="' + citygov_shortcodes_prepare_value(param['value']['icon']) + '" />';
			break;

			case "color":
				var cp_style = citygov_isset(param['style']) ? param['style'] : CITYGOV_STORAGE['shortcodes_cp'];
				output += '<input class="citygov_options_input citygov_options_input_color citygov_options_input_color_'+cp_style +'"'
					+ ' name="' + id + '"'
					+ ' id="' + id + '"'
					+ ' data-param="' + citygov_shortcodes_prepare_value(param_num) + '"'
					+ ' type="text"'
					+ ' value="' + citygov_shortcodes_prepare_value(param['value']) + '"'
					+ (!citygov_empty(param['action']) ? ' onchange="citygov_options_action_'+param['action']+'(this);return false;"' : '')
					+ ' />'
					+ before;
				if (cp_style=='custom')
					output += '<span class="citygov_options_input_colorpicker iColorPicker"></span>';
				else if (cp_style=='tiny')
					output += after;
			break;   
	
			}

			if (param['type'] != 'hidden') {
				output += '</div>';
				if (!citygov_empty(param['desc']))
					output += '<div class="citygov_options_desc">' + param['desc'] + '</div>' + "\n";
				output += '</div>' + "\n";
			}

		}

		output += '</div>';
	}

	
	return output;
}



// Return menu items list (menu, images or icons)
function citygov_shortcodes_menu_list(field) {
	"use strict";
	if (field['type'] == 'socials') field['value'] = field['value']['icon'];
	var list = '<div class="citygov_options_input_menu ' + (citygov_empty(field['style']) ? '' : ' citygov_options_input_menu_' + field['style']) + '">';
	var caption = '';
	for (var key in field['options']) {
		var value = field['options'][key];
		if (citygov_in_array(field['type'], ['list', 'icons', 'socials'])) key = value;
		var selected = '';
		if (citygov_in_list(field['value'], key, CITYGOV_STORAGE['shortcodes_delimiter'])) {
			caption = value;
			selected = ' citygov_options_state_checked';
		}
		list += '<span class="citygov_options_menuitem' 
			+ selected 
			+ '" data-value="' + citygov_shortcodes_prepare_value(key) + '"'
			+ '>';
		if (citygov_in_array(field['type'], ['list', 'select', 'fonts']))
			list += value;
		else if (field['type'] == 'icons' || (field['type'] == 'socials' && field['style'] == 'icons'))
			list += '<span class="' + value + '"></span>';
		else if (field['type'] == 'images' || (field['type'] == 'socials' && field['style'] == 'images'))
			//list += '<img src="' + value + '" data-icon="' + key + '" alt="" class="citygov_options_input_image" />';
			list += '<span style="background-image:url(' + value + ')" data-src="' + value + '" data-icon="' + key + '" class="citygov_options_input_image"></span>';
		list += '</span>';
	}
	list += '</div>';
	return [list, caption];
}



// Return action button
function citygov_shortcodes_action_button(data, type) {
	"use strict";
	var class_name = ' citygov_options_button_' + type + (citygov_empty(data['title']) ? ' citygov_options_button_'+type+'_small' : '');
	var output = '<span class="' 
				+ (type == 'button' ? 'citygov_options_input_button'  : 'citygov_options_field_'+type)
				+ (!citygov_empty(data['action']) ? ' citygov_options_with_action' : '')
				+ (!citygov_empty(data['icon']) ? ' '+data['icon'] : '')
				+ '"'
				+ (!citygov_empty(data['icon']) && !citygov_empty(data['title']) ? ' title="'+citygov_shortcodes_prepare_value(data['title'])+'"' : '')
				+ (!citygov_empty(data['action']) ? ' onclick="citygov_options_action_'+data['action']+'(this);return false;"' : '')
				+ (!citygov_empty(data['type']) ? ' data-type="'+data['type']+'"' : '')
				+ (!citygov_empty(data['multiple']) ? ' data-multiple="'+data['multiple']+'"' : '')
				+ (!citygov_empty(data['sizes']) ? ' data-sizes="'+data['sizes']+'"' : '')
				+ (!citygov_empty(data['linked_field']) ? ' data-linked-field="'+data['linked_field']+'"' : '')
				+ (!citygov_empty(data['captions']) && !citygov_empty(data['captions']['choose']) ? ' data-caption-choose="'+citygov_shortcodes_prepare_value(data['captions']['choose'])+'"' : '')
				+ (!citygov_empty(data['captions']) && !citygov_empty(data['captions']['update']) ? ' data-caption-update="'+citygov_shortcodes_prepare_value(data['captions']['update'])+'"' : '')
				+ '>'
				+ (type == 'button' || (citygov_empty(data['icon']) && !citygov_empty(data['title'])) ? data['title'] : '')
				+ '</span>';
	return [output, class_name];
}

// Prepare string to insert as parameter's value
function citygov_shortcodes_prepare_value(val) {
	return typeof val == 'string' ? val.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#039;').replace(/</g, '&lt;').replace(/>/g, '&gt;') : val;
}
