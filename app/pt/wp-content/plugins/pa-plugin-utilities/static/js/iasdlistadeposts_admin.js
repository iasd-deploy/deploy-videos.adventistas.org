if (!Array.prototype.indexOf) {
	Array.prototype.indexOf = function(obj, start) {
		for (var i = (start || 0), j = this.length; i < j; i++) {
			if (this[i] === obj) {
				return i;
			}
		}
		return -1;
	}
}

(function($){
	var iasdRules = [];
	var checkContentForm = null;

	var onClick_ContentSourceExtraCheck = function(event) {
		var buttonSourceCheck = $(this);
		var form = buttonSourceCheck.closest('form');
		var inputSourceExtra = form.find('.iasd-widget-content-source_extra-field');
		inputSourceExtra.data('verified', 0);
		var newSourceExtra = inputSourceExtra.val();

		var spinner = form.find('.iasd-widget-content-source_extra-container .spinner');

		var widgetId = form.find('.widget-id').val();

		if(newSourceExtra) {
			if(newSourceExtra.indexOf(':') == -1)
				newSourceExtra = 'http://' + newSourceExtra;

			if(newSourceExtra.substr(newSourceExtra.length - 1) != '/')
				newSourceExtra += '/';

			inputSourceExtra.val(newSourceExtra);

			spinner.show();

			$.post(ajaxurl, { action: buttonSourceCheck.data('action'), source: newSourceExtra, fieldId: buttonSourceCheck.attr('id') },
				onClick_ContentSourceExtraCheck_Ajax);
		} else {
			alert(iasdRules['translations']['source-extra-invalid']);
		}
	}

	var onClick_ContentSourceExtraCheck_Ajax = function(response) {
		var response = response.substring(0, response.length - 1);
		var decoded_response = jQuery.parseJSON( response );
		var buttonSourceCheck = $('#' + decoded_response['field']);
		var form = buttonSourceCheck.closest('form');

		var spinner = form.find('.iasd-widget-content-source_extra-container .spinner');

		if(decoded_response['status']) {

			var widgetId = form.find('.widget-id').val();

			iasdRules['sources'][widgetId] = [];
			iasdRules['sources'][widgetId]['post_type'] = decoded_response['post_type'];
			iasdRules['sources'][widgetId]['authors'] = decoded_response['authors'];

			onChange_ContentSourceId_ReplacePostTypes(form, widgetId);
			onChange_ContentSourceId_ReplaceAuthors(form, widgetId);

			form.find('.iasd-widget-content-source_extra-field').data('verified', 1);


			var fieldsetContentAuthors = form.find('.iasdlistadeposts-content-authors');
			fieldsetContentAuthors.attr('disabled', true);

		} else {
			alert(iasdRules['translations']['source-extra-not-compatible']);
		}
		spinner.hide();
	}

	var onChange_ContentSourceId = function(event) {
		var selectSourceId = $(this);
		var newSourceId = selectSourceId.val();		

		var form = selectSourceId.closest('form');
		var widgetId = form.find('.widget-id').val();

			var source = '#widget-'+widgetId+'-source_id';
			var res = jQuery(source).val();
			
			var strcategory = '.widget-'+widgetId+'-category';
			var strtags = '.widget-'+widgetId+'-post_tag';

			if (res != 'local') {

				jQuery(strcategory).hide();
				jQuery(strtags).hide();


			} else {
				jQuery(strcategory).show();
				jQuery(strtags).show();
			}


		jQuery('.post_format').hide();


		var fieldsetContentPostType = form.find('.iasd-widget-content-post_type-container');
		fieldsetContentPostType.attr('disabled', true);
		var fieldsetContentAuthors = form.find('.iasdlistadeposts-content-authors');
		fieldsetContentAuthors.attr('disabled', true);
		var fieldsetContentTaxonomy = form.find('.iasd-widget-content-taxonomies-container');
		fieldsetContentTaxonomy.attr('disabled', true);

		var fieldsetAppearance = form.find('.iasdlistadeposts-appearance');
		fieldsetAppearance.attr('disabled', true);

		var containerSourceExtra = form.find('.iasd-widget-content-source_extra-container');

		var confirmed = confirm( (newSourceId == 'outra') ? iasdRules['translations']['may-lose-data-check'] : iasdRules['translations']['may-lose-data']);

		if(confirmed) {
			if(newSourceId == 'outra') {
				containerSourceExtra.removeAttr('disabled');
			} else {
				containerSourceExtra.attr('disabled', 'disabled');
				onChange_ContentSourceId_ReplacePostTypes(form, widgetId);
				onChange_ContentSourceId_ReplaceAuthors(form, widgetId);
			}
		} else {
			selectSourceId.val(iasdRules['defaults'][widgetId]['source_id']);

			fieldsetContentPostType.removeAttr('disabled');
			fieldsetContentAuthors.removeAttr('disabled');
			fieldsetContentTaxonomy.removeAttr('disabled');
			fieldsetAppearance.removeAttr('disabled');
		}
	}


	var onChange_ContentSourceId_ReplacePostTypes = function(form, widgetId) {
		var fieldsetContentPostType = form.find('.iasd-widget-content-post_type-container');
		fieldsetContentPostType.removeAttr('disabled');

		var selectSourceId = form.find('.iasd-widget-content-source_id');
		var newSourceId = selectSourceId.val();

		if(newSourceId == 'outra')
			newSourceId = widgetId;

		var sourcePostTypes = iasdRules['sources'][newSourceId]['post_type'];

		var newOptions = '';

		var selectPostType = form.find('.iasd-widget-content-post_type');
		var previous_value = selectPostType.val();

		for(post_type in sourcePostTypes) {
			var is_selected = (previous_value == post_type) ? 'selected="selected"' : '';
			newOptions += '<option value="' + post_type + '" '+is_selected+'>' + sourcePostTypes[post_type]['name'] + '</option>';
		}

		selectPostType.html(newOptions);
		selectPostType.removeAttr('disabled', true);

		selectPostType.trigger( 'change', ['forced'] );
	}

	var onChange_ContentSourceId_ReplaceAuthors = function(form, widgetId) {
		var selectSourceId = form.find('.iasd-widget-content-source_id');
		var newSourceId = selectSourceId.val();

		if(newSourceId == 'outra')
			newSourceId = widgetId;

		var sourceAuthors = iasdRules['sources'][newSourceId]['authors'];

		var widget_number = form.find('.widget_number');

		var html = '';
		for(author in sourceAuthors) {
			var part_html = '';
			part_html += '<li>';
			part_html += '<label class="selectit">';
			part_html += '<input value="' + author + '" type="checkbox" name="widget-iasd_lista-de-posts['+widget_number+'][authors][]" id="widget-iasd_lista-de-posts-'+widget_number+'-authors-admin">';
			part_html += sourceAuthors[author]['name'];
			part_html += '</label>';
			part_html += '</li>';
			html += part_html;
		}

		var ulAuthors = form.find('.authors-checkbox-list');
		ulAuthors.html(html);
		if(html)
			form.find('.iasdlistadeposts-content-authors').removeAttr('disabled');
	}

	var onChange_ContentPostType = function(event, forced) {
		if(forced == undefined)
			if(!confirm(iasdRules['translations']['may-lose-data']))
				return false;


		var selectPostType = $(this);
		var newPostType = selectPostType.val();
		var form = selectPostType.closest('form');
		var saveButton = form.find('.widget-control-save');

		var fieldsetContentTaxonomy = form.find('.iasd-widget-content-taxonomies-container');
		fieldsetContentTaxonomy.removeAttr('disabled');

		var sourceId = form.find('.iasd-widget-content-source_id');

		var widgetId = form.find('.widget-id').val();
		var source = (sourceId.val() == 'outra') ? widgetId : sourceId.val();

		var postType = iasdRules['sources'][source]['post_type'][newPostType];
		if(postType != undefined) {
			onChange_ContentPostType_TaxonomiesEnableDisable(form, widgetId);

			onChange_ContentPostType_OrderByRefresh(form, widgetId);

			onChange_ContentPostType_ViewsRefresh(form, widgetId);
		} else {
			console.log("DEBUG: " + newPostType + " não existe em " + source);
		}
	};

	var onChange_AppearanceGroupingContextual = function() {
		var groupinContextual = $(this);
		var form = groupinContextual.closest('form');
		var widgetId = form.find('.widget-id').val();
		onChange_ContentPostType_TaxonomiesEnableDisable(form, widgetId);
	}

	var onChange_AppearanceAuthorContextual = function() {
		var contextual = $(this);
		var fieldset = contextual.closest('fieldset');
		if(contextual.is(":checked"))
			fieldset.find('.iasd-widget-content-authors-container').attr('disabled', true);
		else
			fieldset.find('.iasd-widget-content-authors-container').removeAttr('disabled');
	}


	var onChange_ContentPostType_TaxonomiesEnableDisable = function(form, widgetId) {
		var selectSourceId = form.find('.iasd-widget-content-source_id');
		var sourceId = selectSourceId.val();

		var selectConfigOptions = form.find('.iasd-widget-appearance-config');

		var widgetId = form.find('.widget-id').val();

		var groupinContextualIsEnabled = form.find('.iasd-widget-appearance-grouping_contextual').is(':checked');

		if(sourceId == 'outra')
			sourceId = widgetId;

		var selectPostType = form.find('.iasd-widget-content-post_type');
		postType = selectPostType.val();

		var selectedPostType = iasdRules['sources'][sourceId]['post_type'][postType];
		var availableTaxonomies = selectedPostType['taxonomy'];

		var configOptions = '<option value="">' + iasdRules['translations']['not-allowed'] + '</option>';

		for(i in iasdRules['taxonomies']) {
			var taxonomy = iasdRules['taxonomies'][i];
			if(typeof(taxonomy) == 'string') {
				var taxonomyFieldset = form.find('.iasd-widget-content-' + taxonomy + '-container');
				if($.inArray(taxonomy, availableTaxonomies) == -1 || groupinContextualIsEnabled || (sourceId != 'local' && taxonomyFieldset.hasClass('iasd-ldp-localonly'))) {
					taxonomyFieldset.attr('disabled', true);
				} else{
					taxonomyFieldset.removeAttr('disabled');
					configOptions  += '<option value="' + taxonomy + '">' + iasdRules['taxonomies_names'][taxonomy] + '</option>';
				}
			}
		};

		selectConfigOptions.html(configOptions);

		form.find('.iasdlistadeposts-appearance').removeAttr('disabled');

		var fieldsetTaxonomies = form.find('.iasdlistadeposts-content-taxonomies');
		fieldsetTaxonomies.removeAttr('disabled');
	}

	var onChange_ContentPostType_OrderByRefresh = function(form, widgetId) {
		var selectSourceId = form.find('.iasd-widget-content-source_id');
		var sourceId = selectSourceId.val();

		var widgetId = form.find('.widget-id').val();

		if(sourceId == 'outra')
			sourceId = widgetId;

		var selectPostType = form.find('.iasd-widget-content-post_type');
		postType = selectPostType.val();

		var selectedPostType = iasdRules['sources'][sourceId]['post_type'][postType];
		var availablePostmeta = selectedPostType['postmeta'];

		var html = '';
		for(name in availablePostmeta) {
			var desc = availablePostmeta[name];
			if(typeof(desc) == 'string') {
				html += '<option value="'+name+'">'+desc+'</option>';
			}
		};

		form.find('.iasd-widget-appearance-orderby').html(html);
	};

	var onChange_ContentPostType_ViewsRefresh = function(form, widgetId) {
		var fieldsetAppearance = form.find('.iasdlistadeposts-appearance');
		fieldsetAppearance.removeAttr('disabled');

		var selectSourceId = form.find('.iasd-widget-content-source_id');
		var sourceId = selectSourceId.val();

		var widgetId = form.find('.widget-id').val();

		if(sourceId == 'outra')
			sourceId = widgetId;

		var postType = form.find('.iasd-widget-content-post_type').val();

		var selectedPostType = iasdRules['sources'][sourceId]['post_type'][postType];
		var allViews = iasdRules['views'];
		var allGroups = iasdRules['groups'];

		var html = '<option value="">'+iasdRules['translations']['select-one']+'</option>';;

		var selectView = form.find('.iasd-widget-appearance-view');
		var previous_value = selectView.val();
		for(group in allGroups) {
			var viewsInGroup = allGroups[group];
			var pre_html = '';
			if(typeof(viewsInGroup) == 'object') {
				for(idx in viewsInGroup) {
					var view_name = viewsInGroup[idx];
					var view = allViews[view_name];

					if(typeof(view) == 'object') {
						var allowed = true;
						//If there's no rule, force to be allowed
						if(view['post_type'].length > 0)
							if(view['post_type'].indexOf(postType) == -1)
								allowed = false;
						var is_selected = (previous_value == view_name) ? 'selected="selected"' : '';
						if(allowed)
							pre_html += '<option value="'+view_name+'"'+is_selected+'>'+view['description']+'</option>';
					}
				}
			}
			if(typeof(group) == 'string' && group != 'all')
				pre_html = "<optgroup label=\"" + group + "\">"+pre_html+"</optgroup>";
			html += pre_html;
		}

		selectView.html(html);
		selectView.trigger('change');
	};

	var onChange_ContentTaxonomy = function() {
		var checkbox = $(this);
		var form = checkbox.closest('form');
		var selectedCheckboxes = form.find('.taxonomy-checkbox-list input:checked');

		var fieldset = checkbox.closest('fieldset');
		var countSelected = fieldset.find('.taxonomy-checkbox-list input:checked').length;
		if(countSelected)
			fieldset.find('.count').html('('+countSelected+')').show();
		else
			fieldset.find('.count').hide();

		var markedTaxonomies = [];

		for(i = 0; i < selectedCheckboxes.length; i++) {
			var selectedCheckbox = $(selectedCheckboxes[i]);
			var taxonomyList = selectedCheckbox.closest('ul.taxonomy-checkbox-list');

			if(taxonomyList.find('input:checked').length > 1) {
				var taxonomyName = taxonomyList.data('taxonomy');
				if(markedTaxonomies.indexOf(taxonomyName) < 0) {
					markedTaxonomies.push(taxonomyName);
				}
			}
		}

		if(markedTaxonomies.length > 0) {
			form.find('.fieldset-grouping_taxonomy').removeAttr('disabled');

			var selectSourceId = form.find('.iasd-widget-content-source_id');
			var sourceId = selectSourceId.val();
			var widgetId = form.find('.widget-id').val();

			if(sourceId == 'outra')
				sourceId = widgetId;

			var html = '<option value="">'+iasdRules['translations']['not-allowed']+'</option>';
			var selectGrouping = form.find('.iasd-widget-appearance-grouping_taxonomy');
			var previous_value = selectGrouping.val();

			for(i = 0; i < markedTaxonomies.length; i++) {
				markedTaxonomy = markedTaxonomies[i];
				var is_selected = (previous_value == markedTaxonomy) ? 'selected="selected"' : '';

				html += '<option value="' + markedTaxonomy + '" ' + is_selected + '>' + iasdRules['taxonomies_names'][markedTaxonomy] + '</option>';
			}
			selectGrouping.html(html);

			if(previous_value) {
				AppearanceGroupingTaxonomyInternal(form, selectGrouping);
			}
		} else {
			form.find('.fieldset-grouping_taxonomy').attr('disabled', true);
			form.find('.fieldset-grouping_slug').attr('disabled', true);
		}

	}
	/**
	APPEARANCE
	*/

	var onChange_AppearanceSeeMore = function() {
		var checkbox = $(this);
		var form = checkbox.closest('form');

		if(!checkbox.is(":checked")) {
			form.find('.iasd-widget-appearance-seemore_text').attr('readonly', true);
			form.find('.iasd-widget-appearance-seemore_title').attr('readonly', true);
		} else {
			form.find('.iasd-widget-appearance-seemore_text').removeAttr('readonly');
			form.find('.iasd-widget-appearance-seemore_title').removeAttr('readonly');
		}
	}

	var onChange_AppearanceGroupingTaxonomy = function() {
		var select = $(this);
		var form = select.closest('form');

		AppearanceGroupingTaxonomyInternal(form, select);
	}

	var AppearanceGroupingTaxonomyInternal = function(form, selectGroupingTaxonomy) {
		if(!selectGroupingTaxonomy.val()) {
			form.find('.fieldset-grouping_slug').attr('disabled', true);
		} else {
			var taxonomySlug = selectGroupingTaxonomy.val();
			var ulClass = '.iasdlistadeposts-content-taxonomy-' + taxonomySlug;

			var selectedTerms = form.find(ulClass + ' input:checked');

			var selectGroupingSlug = form.find('.iasd-widget-appearance-grouping_slug');
			var previous_value = selectGroupingSlug.val();

			var html = '';
			html += '<option value="forced">'+iasdRules['translations']['grouping_forced']+'</option>';

			var is_selected = (previous_value == "default") ? 'selected="selected"' : '';
			html += '<option value="default" '+is_selected+'>'+iasdRules['translations']['grouping_default']+'</option>';

			html += '<optgroup label="'+iasdRules['translations']['grouping_terms']+'">';
			console.log(previous_value);
			for (var i = selectedTerms.length - 1; i >= 0; i--) {
				var selectedTerm = $(selectedTerms[i]);
				var is_selected = (previous_value == selectedTerm.val()) ? 'selected="selected"' : '';
				html += '<option value="'+selectedTerm.val()+'" '+is_selected+'>'+selectedTerm.attr('title')+'</option>';
			};
			html += '</optgroup>';

			selectGroupingSlug.html(html);

			form.find('.fieldset-grouping_slug').removeAttr('disabled');
		}
	}

	var onChange_AppearanceView = function() {
		var selectView = $(this);
		var selectedViewId = selectView.val();
		var form = selectView.closest('form');
		var saveButton = form.find('.widget-control-save');
		var widgetId = form.find('.widget-id').val();

		selectView.attr('readonly', true);
		form.find('.iasd-widget-appearance-width').attr('readonly', true);

		var view = iasdRules['views'][selectedViewId];
		if(view != undefined) {

			if(view['allow_grouping']) {
				form.find('.iasd-widget-appearance-grouping_taxonomy-container').removeAttr('disabled');
			} else {
				form.find(".iasd-widget-appearance-grouping_taxonomy-container option[value='']").attr('selected', true);
				form.find('.iasd-widget-appearance-grouping_taxonomy-container').attr('disabled', true);
			}

			if(view['allow_see_more']) {
				form.find('.iasd-widget-appearance-seemore-container').removeAttr('disabled');
			} else {
				form.find('.iasd-widget-appearance-seemore-container :checkbox').attr('checked', false);
				form.find('.iasd-widget-appearance-seemore-container').attr('disabled', true);
			}

			if(!form.find('.iasd-widget-appearance-posts_per_page-container input').val())
				form.find('.iasd-widget-appearance-posts_per_page-container input').val(view['posts_per_page']);

			if(view['posts_per_page_forced']) {
				form.find('.iasd-widget-appearance-posts_per_page-container').hide();
			} else {
				form.find('.iasd-widget-appearance-posts_per_page-container').show();
			}
		}
		onChange_AppearanceView_ReplaceWidth(form, widgetId);
	};

	var onChange_AppearanceView_ReplaceWidth = function(form, widgetId) {

		var selectedViewId = form.find('.iasd-widget-appearance-view').val();
		var selectWidth = form.find('.iasd-widget-appearance-width');

		var sidebar_name = form.closest('.widgets-sortables.ui-sortable').attr('id');
		var sidebar_class = iasdRules['sidebars'][sidebar_name];

		var previous_value = selectWidth.val();

		var html = '<option value="">'+iasdRules['translations']['select-one']+'</option>';
		if(selectedViewId) {
			var viewConfig = iasdRules['views'][selectedViewId];

			var is_selected = (previous_value == 'col-md-4') ? 'selected="selected"' : '';
			if(viewConfig['cols'].indexOf('col-md-4') >= 0)
				html += '<option value="col-md-4" '+is_selected+'>'+iasdRules['width']['col-md-4']+'</option>';

			var is_selected = (previous_value == 'col-md-8') ? 'selected="selected"' : '';
			if(viewConfig['cols'].indexOf('col-md-8') >= 0 && sidebar_class != 'col-md-4')
				html += '<option value="col-md-8" '+is_selected+'>'+iasdRules['width']['col-md-8']+'</option>';

			var is_selected = (previous_value == 'col-md-12') ? 'selected="selected"' : '';
			if(viewConfig['cols'].indexOf('col-md-12') >= 0 && sidebar_class == 'col-md-12')
				html += '<option value="col-md-12" '+is_selected+'>'+iasdRules['width']['col-md-12']+'</option>';
		}

		selectWidth.html(html);
	}

	var form_Validate = function(form) {
		form.find('.sidebar').val(form.closest('.widgets-sortables.ui-sortable').attr('id'));

		var mandatoryItems = form.find('.mandatory');
		var valid = true;

		for (var i = mandatoryItems.length - 1; i >= 0; i--) {
			var mandatoryItem = $(mandatoryItems[i]);
			if(mandatoryItem.hasClass('iasd-widget-title')) {
				var currentTitle = mandatoryItem.val();
				if($.trim(currentTitle).length < 3) {
					animateBorders(mandatoryItem);
					valid = false;
				} else {
					cleanBorders(mandatoryItem);
				}
			}
			if(mandatoryItem.hasClass('iasd-widget-appearance-view') || mandatoryItem.hasClass('iasd-widget-appearance-width')) {
				if(!mandatoryItem.val()) {
					animateBorders(mandatoryItem);
					valid = false;
				} else {
					cleanBorders(mandatoryItem);
				}
			}
			if((mandatoryItem.hasClass('iasd-widget-appearance-seemore-text') || mandatoryItem.hasClass('iasd-widget-appearance-seemore_title')) && !mandatoryItem.val()) {
				if(!mandatoryItem.attr("readonly") && !mandatoryItem.closest("fieldset").attr('disabled')) {
					animateBorders(mandatoryItem);
					valid = false;
				} else {
					cleanBorders(mandatoryItem);
				}
			}
			if(mandatoryItem.hasClass('iasd-widget-content-source_extra-field')) {
				if(form.find('.iasd-widget-content-source_id').val() == 'outra') {
					if(!mandatoryItem.val()) {
						valid = false;
						animateBorders(mandatoryItem);
					} else if(mandatoryItem.data('verified') != 1) {
						animateBorders(mandatoryItem);
						valid = false;
					} else {
						cleanBorders(mandatoryItem);
					}
				}
			}

			if(mandatoryItem.hasClass('iasd-widget-appearance-posts_per_page')) {
				if(!mandatoryItem.val() || isNaN(mandatoryItem.val()) || mandatoryItem.val() < 1) {
					valid = false;
					animateBorders(mandatoryItem);
				} else {
					cleanBorders(mandatoryItem);
				}
			}
		};

		if(!valid)
			alert(iasdRules['translations']['mandatory']);
		else
			onDocument_Ready_SaveDefaults();
		return valid;
	}

	var onClick_Save = function() {
		var saveButton = $(this);
		if(saveButton.attr('disabled'))
			return false;
		var form = saveButton.closest('form');

		return form_Validate(form);
	};

	var animateBorders = function (mandatoryItem) {
		mandatoryItem.closest( ".iasdlistadeposts-form-spacer" ).css('borderColor', '#a00');
	}
	var cleanBorders = function (mandatoryItem) {
		mandatoryItem.closest( ".iasdlistadeposts-form-spacer" ).css('borderColor', '#fff');
	}

	var onDocument_Ready_AjaxRules = function(response) {
		var response = response.substring(0, response.length - 1);
		var decoded_response = jQuery.parseJSON( response );

		if(decoded_response['taxonomies'] != undefined)
			iasdRules['taxonomies'] = decoded_response['taxonomies'];

		if(decoded_response['taxonomies_names'] != undefined)
			iasdRules['taxonomies_names'] = decoded_response['taxonomies_names'];

		if(decoded_response['sources'] != undefined)
			iasdRules['sources'] = decoded_response['sources'];

		if(decoded_response['views'] != undefined)
			iasdRules['views'] = decoded_response['views'];

		if(decoded_response['sidebars'] != undefined)
			iasdRules['sidebars'] = decoded_response['sidebars'];

		if(decoded_response['groups'] != undefined)
			iasdRules['groups'] = decoded_response['groups'];

		if(decoded_response['translations'] != undefined)
			iasdRules['translations'] = decoded_response['translations'];

		if(decoded_response['width'] != undefined)
			iasdRules['width'] = decoded_response['width'];
	}

	var onDocument_Ready_SaveDefaults = function() {
		var widgets = $('.iasd-listadeposts-widget-form-container');
		for (var i = widgets.length - 1; i >= 0; i--) {
			var formContainer = $(widgets[i]);

			var form = formContainer.closest('form');

			var widgetId = form.find('.widget-id').val();

			if(iasdRules['defaults'] == undefined)
				iasdRules['defaults'] = [];

			if(iasdRules['defaults'][widgetId] == undefined)
				iasdRules['defaults'][widgetId] = [];

			iasdRules['defaults'][widgetId]['source_id']    = form.find('.iasd-widget-content-source_id').val();
			iasdRules['defaults'][widgetId]['source_extra'] = form.find('.iasd-widget-content-source_extra').val();
			iasdRules['defaults'][widgetId]['post_type']    = form.find('.iasd-widget-content-post_type').val();
		};
	}

	var onClick_Preview = function() {
		var previewButton = $(this);
		var form = previewButton.closest('form');
		var data = form.serialize();
		data += '&preview=1&action=iasd-listadeposts-refresh';
		var myWindow = window.open(ajaxurl + '?' + data);
	}

	var onClick_Contents = function() {
		var buttonContents = $(this);
		var form = buttonContents.closest('form');
		if(!form_Validate(form))
			return false;
		checkContentForm = form;

		var t = this.title || this.name || null;
		var a = this.href || this.alt;
		var g = this.rel || false;
		tb_show(t,a,g);
		this.blur();

		onclick_Contents_Refresh();
		return false;
	}
	var onclick_Contents_Refresh = function() {
		$('.iasd-ldp-cc-container').hide();
		$('.iasd-ldp-cc-spinner').show();

		$.post(ajaxurl + '?action=iasd-listadeposts-check-contents', checkContentForm.serialize(), onClick_Contents_Ajax, 'html');
	}

	var onclick_WidgetSave = function () {

		var buttonContents = $(this);
		var form = buttonContents.closest('form');
		if(!form_Validate(form))
			return false;
		checkContentForm = form;

		var request = $.post(ajaxurl, checkContentForm.serialize() + '&action=save-widget', 

		function(){
			location.reload();
			$(".overlay_save_widget").html("<p>Widget salvo. Recarregando a página..</p>");
			$(".overlay_save_widget p").css({"width":"450px"});

		}
		, 'html');		

		};  

	var onClick_Contents_Ajax = function(response) {

		var response = response.substring(0, response.length - 1);
		$('#TB_ajaxContent').height($('#TB_window').height() - 45);

		var contentAnalizer = $('.iasd-ldp-cc-container-list');
		contentAnalizer.html(response);
		$('.iasd-ldp-cc-spinner').hide();
		$('.iasd-ldp-cc-container').fadeIn();
		restartSortable();
		checkFixeds();
	}

	var restartSortable = function() {
		var contentListOl = $('.iasd-ldp-cc-container-list ol');
		contentListOl.sortable(
			{
				items: 'li.iasd-ldp-fixed',
				stop: function( event, ui ) {
					trigger_SaveButton();
					checkFixeds();
				}
			});
		contentListOl.disableSelection();
		
	}

	var onClick_SetFixed = function() {
		if(!checkContentForm)
			return false;

		$(this).closest('li').toggleClass('iasd-ldp-fixed');

		trigger_SaveButton();
		checkFixeds();
		onclick_Contents_Refresh();
	}

	var onClick_RemoveCustom = function() {
		if(!checkContentForm)
			return false;

		var li = $(this).closest('li');
		li.toggleClass('iasd-ldp-fixed');
		var id = li.data('id');

		checkContentForm.find('.iasd-ldp-custom-post').val(JSON.stringify({action:'del', parameters:id }));

		onclick_Contents_Refresh();
		onclick_CancelPost();
		checkContentForm.find('.iasd-ldp-custom-post').val('');
	}

	var checkFixeds = function(forcedId) {
		var fixed_fields = $('.iasd-ldp-cc-container').find('.iasd-ldp-fixed');
		var fixed_ids = '';

		if(fixed_fields.length > 0) {
			for (var i = 0; i < fixed_fields.length; i++) {
				var fixed_field = $(fixed_fields[i]);
				if(fixed_ids != '')
					fixed_ids += ',';
				fixed_ids += fixed_field.data('id');
			};
		}

		if(forcedId != undefined && forcedId != "") {
			if(fixed_ids.indexOf(forcedId) == -1) {
				if(fixed_ids != '')
					fixed_ids += ',';
				fixed_ids += forcedId;
			}
		}

		checkContentForm.find('.iasd-ldp-fixed-ids').val(fixed_ids);
	}

	var onclick_AddPost = function() {
		// $('.iasd-ldp-cc').hide();
		// $('.iasd-ldp-ac').show();
		return false;
	}

	var onclick_EditPost = function() {
		var button = $(this);

		$('.iasd-ldp-ac form .form-id').val(button.data('id'));
		$('.iasd-ldp-ac form .form-title').val(button.data('title'));
		$('.iasd-ldp-ac form .form-excerpt').val(button.data('excerpt'));
		$('.iasd-ldp-ac form .form-thumbnail').val(button.data('thumb_url'));
		$('.iasd-ldp-ac form .form-link').val(button.data('link'));

		$('#tab-1').hide();
		$('#tab-2').hide();
		$('#tab-3').show();

		$('#li-tab-1').removeClass("current");
		$('#li-tab-2').removeClass("current");
		$('#li-tab-3').addClass("current");

		onclick_AddPost();
		return false;
	}


	var onclick_CancelPost = function() {
		// $('.iasd-ldp-ac').hide();
		// $('.iasd-ldp-cc').show();
		$('.iasd-ldp-ac form .form-id').val('');
		$('.iasd-ldp-ac form .form-title').val('');
		$('.iasd-ldp-ac form .form-excerpt').val('');
		$('.iasd-ldp-ac form .form-thumbnail').val('');
		$('.iasd-ldp-ac form .form-link').val('');
		return false;
	}

	var onclick_SavePost = function () {
		var form = $('.iasd-ldp-ac form');
		var rules = {
			'iasd-ldp-ac-form-title': { required: true },
			'iasd-ldp-ac-form-excerpt': { required: true },
			'iasd-ldp-ac-form-thumbnail': { required: true },
			'iasd-ldp-ac-form-link': { required: true }
		};
		var validator = form.validate();

		if(!form.valid()) {
			alert(iasdRules['translations']['mandatory']);
			return false;
		}

		var postData = {};
		postData.ID            = $('.iasd-ldp-ac form .form-id').val();
		postData.post_title    = $('.iasd-ldp-ac form .form-title').val();
		postData.post_excerpt  = $('.iasd-ldp-ac form .form-excerpt').val();
		postData.thumb_url     = $('.iasd-ldp-ac form .form-thumbnail').val();
		postData.guid          = $('.iasd-ldp-ac form .form-link').val();
		postData.isManual      = true;
		checkFixeds(postData.ID);

		checkContentForm.find('.iasd-ldp-custom-post').val(JSON.stringify({action:'add', parameters:postData }));

		trigger_SaveButton();
		onclick_Contents_Refresh();
		onclick_CancelPost();
		checkContentForm.find('.iasd-ldp-custom-post').val('');
		return false;
	}

	var onClick_TaxonomyShow = function() {
		var showListButton = $(this);
		showListButton.hide();

		var fieldset = showListButton.closest('fieldset');

		fieldset.find('.taxonomy-hide').show();
		fieldset.find('.taxonomy-checkbox-list').show();

		return false;
	}

	var onClick_TaxonomyHide = function() {
		var hideListButton = $(this);
		hideListButton.hide();

		var fieldset = hideListButton.closest('fieldset');

		fieldset.find('.taxonomy-show').show();
		fieldset.find('.taxonomy-checkbox-list').hide();

		return false;
	}

	var onClick_AuthorShow = function() {
		var showListButton = $(this);
		showListButton.hide();

		var fieldset = showListButton.closest('fieldset');

		fieldset.find('.author-hide').show();
		fieldset.find('.authors-checkbox-list').show();

		return false;
	}

	var onClick_AuthorHide = function() {
		var hideListButton = $(this);
		hideListButton.hide();

		var fieldset = hideListButton.closest('fieldset');

		fieldset.find('.author-show').show();
		fieldset.find('.authors-checkbox-list').hide();

		return false;
	}

	var trigger_SaveButton = function() {
		$('.widget-control-save').trigger('change');
	}

	var onDocument_Ready = function() {
		var body = $('body');
		body.on('change', '.iasd-widget-content-source_id',              onChange_ContentSourceId);
		body.on('change', '.iasd-widget-content-post_type',              onChange_ContentPostType);
		body.on('change', '.taxonomy-checkbox-list input',               onChange_ContentTaxonomy);

		body.on('change', '.iasd-widget-appearance-view',                onChange_AppearanceView);
		body.on('change', '.iasd-widget-appearance-seemore',             onChange_AppearanceSeeMore);
		body.on('change', '.iasd-widget-appearance-grouping_taxonomy',   onChange_AppearanceGroupingTaxonomy);
		body.on('change', '.iasd-widget-appearance-grouping_contextual', onChange_AppearanceGroupingContextual);
		body.on('change', '.iasd-widget-appearance-author_contextual',   onChange_AppearanceAuthorContextual);

		body.on('click', '.iasd-widget-content-source_extra-check',      onClick_ContentSourceExtraCheck);
		body.on('click', '.widget-control-save',                         onClick_Save);
		body.on('click', '.iasd-widget-preview',                         onClick_Preview);
		body.on('click', '.iasd-widget-contents',                        onClick_Contents);
		body.on('click', '.iasd-widget-contents-externo',                onClick_Contents);

		body.on('click', '.iasd-ldp-cc-refresh',                         onclick_Contents_Refresh);
		body.on('click', '.iasd-ldp-cc .add_post',                       onclick_AddPost);
		body.on('click', '.iasd-ldp-cc .edit_post',                      onclick_EditPost);
		body.on('click', '.iasd-ldp-cc .iasd-ldp-set-fixed',             onClick_SetFixed);
		body.on('click', '.iasd-ldp-cc .iasd-ldp-remove',                onClick_RemoveCustom);

		body.on('click', '.iasd-ldp-ac .cancel_post',                    onclick_CancelPost);
		body.on('click', '.iasd-ldp-ac .save_post',                      onclick_SavePost);
		body.on('click', '.save_widget_external',                        onclick_WidgetSave);

		body.on('click', '.taxonomy-hide',                               onClick_TaxonomyHide);
		body.on('click', '.taxonomy-show',                               onClick_TaxonomyShow);
		body.on('click', '.author-hide',                                 onClick_AuthorHide);
		body.on('click', '.author-show',                                 onClick_AuthorShow);

		$.post(ajaxurl, { action: iasd_rules_action }, onDocument_Ready_AjaxRules);

		$(document).on( 'widget-added', function(event, widget){
			setTimeout(function() {
				$('.widget-control-save', widget).trigger('change');
			}, 100);
		});

		onDocument_Ready_SaveDefaults();
	}

	$('document').ready(onDocument_Ready);

})(jQuery);


(function($){
    "use strict";

    var onAddWidget = function(widget) {
        //console.log($('.adaptablewidget', widget));
        if($('.adaptablewidget', widget).length)
            setTimeout(function() {
				$('.widget-control-save', widget).click();
			}, 500);
    };

    var onDocument_Ready = function() {
		$(document).on( 'widget-added', function(event, params) { 
			onAddWidget(params); 
		} );
    };

    $(document).ready(onDocument_Ready);

})(jQuery);


jQuery( document ).ready(function() {
	jQuery('.post_format').hide();
});