
(function($){
	var iasdListaDePostsMap = [];

	var onAjax_IasdWidgetRefresh = function(raw_html, textStatus, jqXHR) {
			if(raw_html.substring(raw_html.length - 1) == '0')
				raw_html = raw_html.substring(0, raw_html.length - 1);

			var remote_html = $('<div>').html(raw_html);

			var widget_id = '#' + remote_html.find('.iasd-widget').attr('id');

			var remote_container = remote_html.find(widget_id + ' > .iasd-widget-list');
			var local_container = $(widget_id + ' > .iasd-widget-list');
			local_container.html(remote_container.html());

			var remote_title = remote_html.find(widget_id + ' > :first-child');
			var local_title = $(widget_id + ' > :first-child');
			local_title.html(remote_title.html());


			var remote_link = remote_html.find(widget_id + ' .more-link');
			var local_link = $(widget_id + ' .more-link');
			local_link.attr('href', remote_link.attr('href'));

			$(widget_id + ' .config.open').removeClass('open');

			return false;
		}

	var onClick_iasdConfigSubmit = function() {
			var buttonSubmit = $(this);
			var form = buttonSubmit.closest('form');

			$.get(ajaxurl, form.serialize(), onAjax_IasdWidgetRefresh, 'html');
			return false;
		}

	var onDocument_Ready = function() {
			$('body').on(
				'click',
				'.iasd-widget-config .config.open .btn.btn-default',
				onClick_iasdConfigSubmit);
		}

	$('document').ready(onDocument_Ready);

})(jQuery);