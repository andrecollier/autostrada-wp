(function($){
	function sirconfixtabheight(){
		var fieldset = $('.sircon-option-tabs .current-fieldset'),
		contentHeight = fieldset.find('.fieldset-content').outerHeight() + 60; //Add 60 px bottom air
		$('.sircon-option-tabs fieldset').height('auto');
		fieldset.height(contentHeight);
	}
	
	//Make tabs clickable
	$('.sircon-option-tabs legend').click(function(){
		let fieldset = $(this).parent();
		$('.sircon-option-tabs fieldset').not(fieldset).removeClass('current-fieldset');
		fieldset.addClass('current-fieldset');
		sirconfixtabheight();
	});
	
	//Those pesky enablers..
	$('.sircon-option-tabs .sircon-enabler').click(sirconfixtabheight);
	
	//Fix height on tabs - absolute problem
	$(document).ready(function(){
		//Position absolute height-fix
		sirconfixtabheight();
		setTimeout(sirconfixtabheight, 100); //In case wordpress gets in the way
	});
	$(document).on('change', '.sircon-option-tabs input, .sircon-option-tabs select, .sircon-option-tabs textarea', function() {
		sirconfixtabheight();
		setTimeout(sirconfixtabheight, 100); //In case wordpress gets in the way
	});
	$(document).on('multifield-row-added', function() {
		sirconfixtabheight();
		setTimeout(sirconfixtabheight, 100); //In case wordpress gets in the way
	});
})(jQuery);
