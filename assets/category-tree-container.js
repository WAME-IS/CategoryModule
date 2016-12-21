$(function() {
    var initCategoryTree = function() {
        var $categories;
        var $tree = $('.categoryTree').tree({
            selectable: true
        });
        
        var selectTreeCheckbox = function() {
            $tree.tree('setState', {open_nodes: [], selected_node: $categories.attr('value').split(',')});
        };

        $tree
            .bind('tree.init', function() {
                $categories = getCategoryInput($(this));
                
                selectTreeCheckbox();
            })
            .bind('tree.open', function() {
                selectTreeCheckbox();
            })
            .bind('tree.click', function(e) {
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
            
        return $tree;
    };
    
    initCategoryTree();
 
    /**
     * Init in modal
     */
    $.nette.ext('category.tree', {
        success: function(payload, status, xhr) {
            initCategoryTree();
        }
    });
    
    /**
     * Return category input
     */
    var getCategoryInput = function(object = null) {
        var name = 'CategoryTreeContainer[category]';
        
        if (object.data('input')) {
            name = object.data('input');
        }
                
        return $('input[name="' + name + '"]');
    };
    
    /**
     * Select category
     * create chip
     * 
     * @param int categoryId
     * @param string categoryName
     */
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
                'class' : 'chip chip-light',
                'html' : categoryName
            }).append(close);

            $('.modal#search-category .stack').append(chip);
        }
    };
    
    /**
     * Chip remove
     */
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
            
            var node = $('.categoryTree').tree('getNodeById', categoryId);
            $('.categoryTree').tree('removeFromSelection', node);
        });
    };
    
    initChipRemove();

    
    /**
     * Autocomplete search keyup
     * Hidden category-tree
     */
    var initSearchCategory = function () {
        $('.modal#search-category input[name="search-category"]').keyup(function() {
            if ($(this).val().length < 3) {
                $('.modal#search-category #category-tree').show();
            } else {
                $('.modal#search-category #category-tree').hide();
            }
        });

        $('.modal#search-category').delegate('.autocomplete-remove', 'click', function() {
            $('.modal#search-category #category-tree').show();
        });
    };
    
//    initSearchCategory();
    
    /**
     * Select category from autocomplete list
     */
    var initSearchSelect = function() {
        $('.modal#search-category').delegate('.ui-autocomplete li', 'click', function() {
            var modal = $('.modal#search-category');
            var input = modal.find('[name="search-category"]');
            var div = modal.find('.autocomplete-value');
            var categoryId = input.val();
            var categoryName = div.text();

            initSelectCategory(categoryId, categoryName);

            input.val('').show();
            div.remove();
            $('.modal#search-category #category-tree').show();

            var inputContainer = $('[name="CategorySelectedContainer"]');
            var categories = inputContainer.val().split(',');
            
            categories.push(categoryId);
            
            inputContainer.val(categories.join());
            
            var node = $('.categoryTree').tree('getNodeById', categoryId);
            $('.categoryTree').tree('addToSelection', node);
        });
    };
    
    initSearchSelect();
    
    /**
     * Submit modal
     */
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
