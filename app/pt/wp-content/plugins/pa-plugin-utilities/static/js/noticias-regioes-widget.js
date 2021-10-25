
function runRegionAction(region_container) {
	var selected_option = jQuery('#'+region_container+' .region-picker option:selected');
	console.log(selected_option);
	var region_slug = selected_option.attr('region_slug');
	var ajax_url = selected_option.attr('region_url') + '&region_slug=' + region_slug;
	console.log(ajax_url);
	jQuery.ajax({
		url: ajax_url
	}).done(function(html, textStatus, jqXHR){
		jQuery('#'+region_container+' UL').html(html);
	}).fail(function(error){
		console.log('erro');
	});
	return false;
}