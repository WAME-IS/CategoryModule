<?php

namespace Wame\CategoryModule\Components;

use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryItemRepository;
use Wame\Utils\Tree\ComplexTreeSorter;

interface ICategoryListControlFactory {

    /** @return CategoryListControl */
    public function create();
}

class CategoryListControl extends \Wame\Core\Components\BaseControl {

    /** @var CategoryRepository */
    public $categoryRepository;

    /** @var CategoryItemRepository */
    public $categoryItemRepository;

    /** @var string */
    private $lang;

    public function __construct(CategoryRepository $categoryRepository, CategoryItemRepository $categoryItemRepository) {
        parent::__construct();

        $this->categoryRepository = $categoryRepository;
        $this->categoryItemRepository = $categoryItemRepository;
        $this->lang = $categoryRepository->lang;
    }

    public function render($parameters = []) {
        $depth = isset($parameters['depth']) ? $parameters['depth'] : 1;
        $type = isset($parameters['type']) ? $parameters['type'] : null;

        $criteria = [];

        if ($type) {
            $criteria['type'] = $type;
        }

        if ($depth) {
            $criteria['depth'] = $depth;
        }

        $categories = $this->categoryRepository->find($criteria);

        if (!$depth) {
            $categories = (new ComplexTreeSorter($categories))->sortTree();
            $categories = $categories->child_nodes;
        }

        $this->template->lang = $this->lang;
        $this->template->categories = $categories;

        $this->getTemplateFile();
        $this->template->render();
    }

}
