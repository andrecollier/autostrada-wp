(function($){

    let multifieldSelectors = '.multifield-row > .formfield > input[name], .multifield-row > .formfield > input[data-name], .multifield-row > .formfield > select, .multifield-row > .formfield > textarea, .multifield-row > .formfield .wp-sircon-colorpicker, .multifield-row > .field-radio input, .multifield-row > .formfield > label > input[data-name]',
        multifieldTemplateSelectors = '.multifield-row-template *';

	function editorGuid() {
		function s4() {
			return Math.floor((1 + Math.random()) * 0x10000)
			.toString(16)
			.substring(1);
		}
		return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
		s4() + '-' + s4() + s4() + s4();
	};

	function initDynamicEditor(i, wrapElem){
		let element = $(wrapElem),
		textareaId = editorGuid(),
		textArea = element.children('textarea').not('.editor-inited');
		if(textArea.length == 0){return;}
		textArea.attr('id', textareaId);
		textArea.addClass('editor-inited');
		tinyMCE.execCommand('mceAddEditor', false, textareaId);

		tinymce.editors[textareaId].on('change', function(){
			$('#'+textareaId).val(this.getContent()).trigger('change');
		});

		tinymce.editors[textareaId].on('keyup', function(){this.save();});
		tinymce.editors[textareaId].on('paste', function(){this.save();});
		tinymce.editors[textareaId].on('cut', function(){this.save();});
	};

    function getRowData(field){
        var rowdata = field.children('.multifield-json'),
            rowdata;

        if(rowdata.val() == ''){
            rowData = []; //Default to array
        }else{
            rowData = JSON.parse(rowdata.val());
        }
        return rowData;
    }

    function saveRowData(field, newData){
        var rowdata = field.children('.multifield-json');
        var customizerRowData = field.closest('.customize-control-multiple').find('.apply-multifield-json').first();
        if(customizerRowData.length){
            customizerRowData.val(JSON.stringify(newData));
            customizerRowData.trigger('change');
        }
        rowdata.val(JSON.stringify(newData));
    };

    function saveParentRowdata(childRows, childRowdata){
        var parentRow		= childRows.closest('.multifield-row'),
            parentRows		= parentRow.closest('.multifieldwrap'),
            parentSortables	= parentRows.children('.sortables'),
            parentRowIndex	= parentRow.index(),
            multifieldName	= childRows.children('.multifield-json').attr('data-name'),
            parentRowdata	= getRowData(parentRows);

        parentRowdata[parentRowIndex] = parentRowdata[parentRowIndex] || {};
        parentRowdata[parentRowIndex][multifieldName] = childRowdata;
        saveRowData(parentRows, parentRowdata);
    };

    $(document).on('click', function(event){
        var target 	= $(event.target),
            isConfirm	= target.is('.are-you-sure'),
            isDelete	= target.is('[data-action=remove]');
        if(isConfirm || isDelete){return;}
        target.closest('.multifield-row').removeClass('asks-if-sure');
        $('.are-you-sure').remove();
    });

    $(document).on('click', '[data-sircon=multifield-modifier]', function(event){
        event.stopPropagation();

        var target		= $(this).closest('[data-sircon=multifield-modifier]'),
            clicked	= $(event.target),
            row		= target.closest('.multifield-row'),
            rows		= target.closest('.multifieldwrap'),
            sortables	= rows.children('.sortables'),
            rowIndex	= row.index(),
            rowCount	= sortables.children('.multifield-row').length,
            doAdd		= (target.attr('data-action') == 'add'),
            doRemove	= (target.attr('data-action') == 'remove'),
            isOnlyRow	= (sortables.children('.multifield-row').length == 1),
            isSubMulti	= (rows.closest('.fieldtype-multiple.is-multifield').length > 0), //Multifield inside multifield?
            rowData;

        if(doRemove){
            if(!clicked.closest('.are-you-sure').length > 0){
                target.find('.are-you-sure').remove();
                target.append('<div class="are-you-sure"><div class="txt">Remove: Click to confirm</div></div>');
                target.closest('.multifield-row').addClass('asks-if-sure');
                return;
            }
            //remove from the form
            row.remove();
            rowCount--;

            //Remove from rowdata
            rowData = getRowData(rows);
            rowData.splice(rowIndex,1);
            saveRowData(rows, rowData);

            //If we removed the last row, reinsert the template so we don't sit empty!
            if(isOnlyRow){
                doAdd = true;
            }

        }

        if (doAdd){
            //Make a copy of the first row
            let clone = $(rows.children('.multifield-row-template').clone());
            clone.removeClass('multifield-row-template').addClass('multifield-row');

            //The template is the last element Insert just before last
            sortables.append(clone);
            $(document).trigger('multifield-row-added', clone);

			//Fix name for radiobuttons
			clone.find('.field-radio').each(function(i, e) {
				e = $(e);
				let identifier = Math.floor(Math.random() * (Number.MAX_SAFE_INTEGER - 1));
				e.find('input[type="radio"]').attr('name', 'sircon-multifield-radio-'+identifier);
			});

            //Editors must be created in a special way...
            clone.find('.fieldtype-editor').each(initDynamicEditor);

            //Also make room for this in the datavar
            rowData = getRowData(rows);
            if(isOnlyRow && rowData && (rowData.length <= 0)){
                rowData.splice(rowCount,0,{});
            }
            saveRowData(rows, rowData);
        }

        //multifield in multifield
        if(isSubMulti){
            saveParentRowdata(rows, rowData);
        }

    });


    //Save values
    $(document).on('change', multifieldSelectors, function(){
        if($(this).is(multifieldTemplateSelectors)){return;}

        var target		= $(this),
            row			= target.closest('.multifield-row'),
            rows		= target.closest('.multifieldwrap'),
            sortables	= rows.children('.sortables'),
            newVal		= target.val(),
            isCheckbox	= (target.attr('type') == 'checkbox'),
            rowIndex	= row.index(),
            valName		= target.attr('data-name'),
            rowData		= getRowData(rows),
            isSubMulti	= (rows.closest('.fieldtype-multiple.is-multifield').length > 0); //Multifield inside multifield?

        //checkbox values are dependant on checked status
        if(isCheckbox && !target.is(':checked')){
            newVal = false;
        }

        rowData[rowIndex] = rowData[rowIndex] || {};
        rowData[rowIndex][valName] = newVal;
        saveRowData(rows, rowData);

        if(isSubMulti){
            saveParentRowdata(rows, rowData);
        }
    });

    $(document).ready(function(){
        $(multifieldSelectors).not(multifieldTemplateSelectors).change();
        $('.multifield-row').each(function(){
            var target = $(this);
            target.trigger('multifieldrow-ready', target);
			target.find('.field-radio').each(function(i, e) {
				e = $(e);
				let identifier = Math.floor(Math.random() * (Number.MAX_SAFE_INTEGER - 1));
				e.find('input[type="radio"]').attr('name', 'sircon-multifield-radio-'+identifier);
			});
            setTimeout(function(){
                target.find('.fieldtype-editor').each(initDynamicEditor);
            },150);
        });

        $('.multifieldwrap[data-sortable=1]').each(function(){
            let wrap = $(this);
            let sortables = wrap.children('.sortables');
            Sortable.create(sortables[0], {
                draggable	: '.multifield-row',
                handle		: '.sort-row',
                onEnd		: function(){
                    wrap.find(multifieldSelectors).not(multifieldTemplateSelectors).change();
                }
            });
        });

    });
})(jQuery);