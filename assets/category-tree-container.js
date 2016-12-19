$(function() {
    var initCategoryTree = function () {
        var $categories;
        var $tree = $('.categoryTree').tree();

        $tree
            .bind('tree.init', function() {
                var name = 'CategoryTreeContainer[category]';
        
                if ($(this).data('input')) {
                    name = $(this).data('input');
                }
                
                $categories = $('input[name="' + name + '"]');
            })
            .bind('tree.select', function() {
                
            })
            .bind('tree.selectNode', function(e) {
                console.log('som tutaj');
            })
            .bind('tree.open', function(e) {
                console.log(e.node);
            })
            .bind('tree.click', function(e) {
                // Disable single selection
                e.preventDefault();

                var selected_node = e.node;

                if (selected_node.id == undefined) {
                    console.log('The multiple selection functions require that nodes have an id');
                }

                if ($tree.tree('isNodeSelected', selected_node)) {
                    $tree.tree('removeFromSelection', selected_node);
                }
                else {
                    $tree.tree('addToSelection', selected_node);
                }

                var state = $tree.tree('getState');
                $categories.val((state.selected_node).join());
                
                initSelectCategory(selected_node.id, selected_node.name);
            });
            
                            

    };
    
    initCategoryTree();

    $.nette.ext('category.tree', {
        success: function(payload, status, xhr) {
            initCategoryTree();
        }
    });
    
    var initSearchCategory = function () {
        $('.modal#search-category input[name="search-category"]').keyup(function() {
            if ($(this).val() === '') {
                $('.modal#search-category #category-tree').show();
            } else {
                $('.modal#search-category #category-tree').hide();
            }
        });

        $('.modal#search-category').delegate('.autocomplete-remove', 'click', function() {
            $('.modal#search-category #category-tree').show();
        });
    };
    
    initSearchCategory();
    
    var initSelectCategory = function(categoryId, categoryName) {
        if ($('.modal#search-category .stack div[data-category="' + categoryId + '"]').length) {
            $('.modal#search-category .stack div[data-category="' + categoryId + '"]').remove();
        } else {
            var close = $('<i/>', {
                'data-category' : categoryId,
                'class' : 'close material-icons',
                'html' : 'close'
            });

            var chip = $('<div/>', {
                'data-category' : categoryId,
                'class' : 'chip',
                'html' : categoryName
            }).append(close);

            $('.modal#search-category .stack').append(chip);
        }
    };
    
    var initChipRemove = function() {
        $('.modal#search-category .stack').delegate('.chip .close', 'click', function() {
            
            var categoryId = $(this).data('category');
            var input = $('[name="CategorySelectedContainer"]');
            var categories = input.val().split(',');
            var newCategories = [];
            
            $.each(categories, function(i, val) {
                if (val != categoryId) {
                    newCategories.push(val);
                }
            });
            
            input.val(newCategories.join());
        });
    };
    
    initChipRemove();
    
//    $('.modal#search-category .ui-autocomplete li').on('click', function() {
//        console.log('som tu');
//        initSelectCategory($(this));
//    });
    
    var initSubmitCategory = function() {
        $('.modal#search-category .modal-check').on('click', function(e) {
            e.preventDefault();
            
            var url = $(this).attr('href');
            var categories = $('[name="CategorySelectedContainer"]').val();
            
            $.nette.ajax({
                url: url,
                data: { 'categories' : categories },
                unique: false,
                success: function(data) {
                    var element = $('.overlay-wrapper[data-overlay-wrapper="CategorySelectedContainer"]');
                    
                    element.find('ul.category-list').remove();
                    element.find('.overlay-block').remove();
                    
                    element.append(data);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
    };
    
    initSubmitCategory();
   
});
