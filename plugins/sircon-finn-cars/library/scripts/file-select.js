function sirconFileSelect(button){
	let mediaFrame;
	let formField = $(button).closest('.formfield');
	let inputField = formField.find('input[type=hidden]');
	let filenameWrapper = formField.find('.file-preview-name');

	mediaFrame = wp.media.frames.mediaFrame = wp.media({
		title: $(button).html(),
		button: {
			text: sirconlib.fileSelectButtonText,
		},
		multiple: false
	});

	mediaFrame.on('select', function() {
		let attachment = mediaFrame.state().get('selection').first().toJSON();
		inputField.val(attachment.id).trigger('change');
		filenameWrapper.html('<a href="'+attachment.url+'" target="_blank">'+attachment.name+'.'+attachment.subtype+'</a>');
	});

	mediaFrame.open();
}
function sirconFileDeselect(button){
	let formField = $(button).closest('.formfield');
	formField.find('input[type=hidden]').val('').trigger('change');
	formField.find('.file-preview-name').html('');
}