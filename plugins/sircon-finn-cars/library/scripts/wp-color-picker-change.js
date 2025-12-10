(function($){
	initSirconColorpickers = function(){
	let colorpickers = $('.wp-sircon-colorpicker').not('.multifield-row-template .wp-sircon-colorpicker');
	if(colorpickers.length == 0){return;}
	colorpickers.wpColorPicker({
		change: _.throttle(function(event) {
				if(!event){return;}
				$(this).closest('.wp-sircon-colorpicker').trigger('change');
			}, 250)
		});
	};
	$(document).on('widget-updated', initSirconColorpickers);
	$(document).ready(initSirconColorpickers);
	$(document).on('multifield-row-added', initSirconColorpickers);
})(jQuery);