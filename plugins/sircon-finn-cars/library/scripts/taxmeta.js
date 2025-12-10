(function($){
	$(document).ajaxSuccess(function(e, request, settings){
		var object = deparam(settings.data);
		if(object.action === 'add-tag'){
			$('.sircon-taxmeta-add').find('.custom-img-preview').html('').removeAttr('style');
			$('.sircon-taxmeta-add').find('.fieldtype-image input[type="hidden"]').val('');
		}
	});
})(jQuery);