(function($){
	initSirconDatepickers = function(){
		let datepickers = $('.sircon-datepicker').not('.multifield-row-template .sircon-datepicker').not('.flatpickr-input');
		if(datepickers.length == 0){return;}
		datepickers.each(function(i, e) {
			e = $(e);
			let config = e.attr('data-config');
			try { config = JSON.parse(config); } catch(e) { config = {} }
			e.flatpickr(config);
			e.trigger('change');
		});
	}
	$(document).on('widget-updated', initSirconDatepickers);
	$(document).ready(initSirconDatepickers);
	$(document).on('multifield-row-added', initSirconDatepickers);
	
	/* flatpickr v4.5.0, @license MIT */
	(function (global, factory) {
		typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
		typeof define === 'function' && define.amd ? define(['exports'], factory) :
		(factory((global.no = {})));
	}(this, (function (exports) { 'use strict';
		
		var fp = typeof window !== "undefined" && window.flatpickr !== undefined ? window.flatpickr : {
			l10ns: {}
		};
		var Norwegian = {
			weekdays: {
				shorthand: ["Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør"],
				longhand: ["Søndag", "Mandag", "Tirsdag", "Onsdag", "Torsdag", "Fredag", "Lørdag"]
			},
			months: {
				shorthand: ["Jan", "Feb", "Mar", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des"],
				longhand: ["Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember"]
			},
			firstDayOfWeek: 1,
			rangeSeparator: " til ",
			weekAbbreviation: "Uke",
			scrollTitle: "Scroll for å endre",
			toggleTitle: "Klikk for å veksle",
			ordinal: function ordinal() {
				return ".";
			}
		};
		fp.l10ns.no = Norwegian;
		var no = fp.l10ns;
		
		exports.Norwegian = Norwegian;
		exports.default = no;
		
		Object.defineProperty(exports, '__esModule', { value: true });
	})));
})(jQuery);
