/** jquery-tree ***************************************************************/

$('.tree').tree({
	/* specify here your options */
});


/** categoryMenu **************************************************************/

$('.cnt-CategoryButtonControl').categoryMenu();


/** jqTree ********************************************************************/

var $categories;
var $tree = $('#tree1').tree();

$tree
	.bind('tree.init', function() {
		$categories = $('input[name="categories"]');
	})
	.bind('tree.select', function() {
		
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
	});


/** Autocomplete for add category group action ********************************/

$('[data-wame-autocomplete="category"]').wameAutocomplete({
    source: '/api/v1/categories',
    columns: ['langs.title'],
    select: 'a.id, langs.title'
});