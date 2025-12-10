(function($){
    /* TABS */
    $(document).on('click', '.sircon-tabs-wrapper .sircon-tab-title', function(event){
        event.preventDefault();

        var $this		= $(this),
            $wrapper	= $this.closest('.sircon-tabs-wrapper'),
            isReadied	= $wrapper.hasClass('readied'),
            targetTab	= $this.attr('data-tabtarget'),
            $targetTab	= $wrapper.find('#'+targetTab),
            $tabtitle	= $wrapper.find('.sircon-tab-title[data-tabtarget='+targetTab+']'),
            isCurrentTitle		= $this.hasClass('current'),
            isTitleInTabContent	= $this.closest('.them-tabs').length > 0;

        //disable current title and content
        $wrapper.children('.them-tabs, .tab-titles').children('.current').removeClass('current');

        //allow toggle off
        if(isReadied && isCurrentTitle && isTitleInTabContent){return;}

        if(!isReadied){
            $wrapper.addClass('readied');
        }


        //activate current title
        $tabtitle.addClass('current');

        //activate current tab contents
        $targetTab.addClass('current');
    })
})(jQuery);