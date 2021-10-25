(function($){

	var WidgetController = {

		// For this widget work well, depends on setting the proper width on
		// .single_gallery_widget_item (we are using .span5)
		initialize : function(){
			var jqWidget = $('.single_gallery_widget');
			var widgetWidth = (jqWidget.closest('[class*=span]').outerWidth()+3) * jqWidget.children().size();
			jqWidget.width(widgetWidth);
			jqWidget.closest('.single_gallery_widget_container').scrollLeft(0);
		},

		nextPrevClick : function(event) {
			var jqThis = $(this);
			var width = jqThis.closest('.single_gallery_widget_item').outerWidth();
			var scrollChange = ((jqThis.is('.next')) ? '+=' : '-=') + width;
			jqThis.closest('.single_gallery_widget_container').animate({
				scrollLeft: scrollChange
			}, 400);
		
			return false;
		}
	};

	$(document).ready(function () {

		WidgetController.initialize();
		$(window).resize(WidgetController.initialize);
		$('.single_gallery_widget_item .slider-nav').click(WidgetController.nextPrevClick);

	});

	$('#single-galeria .social-widgets ul.thumbnails a').click(function() {
		var $this = $(this);
		var $img = $this.find('img').first();
		$('#single-galeria #main_image_thumb img').attr('src', $img.attr('src'));
		return false;
	});
})(jQuery);





