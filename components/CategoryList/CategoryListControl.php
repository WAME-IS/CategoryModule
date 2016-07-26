<?php

namespace Wame\CategoryModule\Components;

use Nette\DI\Container;
use Wame\CategoryModule\Repositories\CategoryItemRepository;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\Core\Components\BaseControl;
use Wame\Utils\Tree\ComplexTreeSorter;

interface ICategoryListControlFactory
{

    /** @return CategoryListControl */
    public function create();
}

class CategoryListControl extends BaseControl
{

    /** @var CategoryRepository */
    public $categoryRepository;

    /** @var CategoryItemRepository */
    public $categoryItemRepository;
    
    
    public function __construct(
        Container $container, 
        CategoryRepository $categoryRepository, 
        CategoryItemRepository $categoryItemRepository
    ) {
        parent::__construct($container);

        $this->categoryRepository = $categoryRepository;
        $this->categoryItemRepository = $categoryItemRepository;
    }

    
    public function render($parameters = [])
    {
        $depth = isset($parameters['depth']) ? $parameters['depth'] : 1;
        $type = isset($parameters['type']) ? $parameters['type'] : null;

        $criteria = [];

        if ($type) {
            $criteria['type'] = $type;
        }

        if ($depth) {
            $criteria['depth <='] = 1 + $depth;
            $criteria['depth >'] = 1;
        }

        $categories = $this->categoryRepository->find($criteria);

        if (!$depth) {
            $categories = (new ComplexTreeSorter($categories))->sortTree();
            $categories = $categories->child_nodes;
        }

        $this->template->categories = $categories;
    }
    
}
