(function($) {
    $.fn.categoryMenu = function(s) {
        var defaults = {  
            iconClosed: 'fa fa-bars',
            iconOpened: 'fa fa-times'
           // defaults
        };  

        var settings = $.extend(defaults, s);

        return this.each(function() {  
            var $obj = $(this);
            
            var options = $.extend(settings, $obj.data());
            
            var $button = $obj.children('.category-menu-button');
            var $list = $obj.children('.category-menu-list');
            
            $button.on('click', function(e) {
                e.preventDefault();
                
                $(this).children('.fa').attr('class', function(i, oldClass) {
                    return oldClass === options.iconClosed ? options.iconOpened : options.iconClosed;
                });
                
                $list.toggle();
            });
            
            $list.hide();
        });  
    };
})(jQuery);  