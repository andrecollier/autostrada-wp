function sirconImageSelect(button){
	let mediaFrame;
	let formField = $(button).closest('.formfield');
	let inputField = formField.find('input[type=hidden]');
	let imagePreview = formField.find('.custom-img-preview');

	mediaFrame = wp.media.frames.mediaFrame = wp.media({
		title: $(button).html(),
		button: {
			text: sirconlib.imageSelectButtonText,
		},
		multiple: false
	});

	mediaFrame.on('select', function() {
		let attachment = mediaFrame.state().get('selection').first().toJSON();
		inputField.val(attachment.id).trigger('change');

		if (typeof attachment.sizes !== 'undefined') {
			let url;
			if(attachment.sizes && attachment.sizes.hd){
				url = attachment.sizes.hd.url;
			} else if(attachment.sizes && attachment.sizes.medium){
				url = attachment.sizes.medium.url;
			} else if(attachment.sizes.thumbnail){
				url = attachment.sizes.thumbnail.url;
			} else if(attachment.sizes.full){
				url = attachment.sizes.full.url;
			} else{
				url = attachment.url;
			}
			imagePreview.css('background-image', 'url('+url+')').html('<img src="'+url+'" alt="preview" />');
		} else if (attachment.mime === 'image/svg+xml'){
			let xhr = new XMLHttpRequest();
			xhr.open('GET', attachment.url);
			xhr.onload = function() {if (xhr.readyState === 4) imagePreview.html(xhr.responseXML.documentElement);}
			xhr.overrideMimeType('image/svg+xml');
			xhr.send();
		} else {
			imagePreview.html('<a href="'+attachment.url+'">'+attachment.id+'</a>');
		}
	});

	mediaFrame.open();
}
function sirconImageDeselect(button){
	let formField = $(button).closest('.formfield');
	formField.find('input[type=hidden]').val('').trigger('change');
	formField.find('.custom-img-preview').html('').css('background-image', '');
}