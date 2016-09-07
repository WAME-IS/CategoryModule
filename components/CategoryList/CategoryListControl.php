<?php

namespace Wame\CategoryModule\Components;

use Nette\DI\Container;
use Wame\CategoryModule\Components\CategoryListControl;
use Wame\CategoryModule\Components\ICategoryControlFactory;
use Wame\ChameleonComponentsListControl\Components\ChameleonTreeListControl;
use Wame\ListControl\Components\ISimpleEmptyListControlFactory;

interface ICategoryListControlFactory
{

    /** @return CategoryListControl */
    public function create();
}

class CategoryListControl extends ChameleonTreeListControl
{

    use CategoryListTrait;

    /** @persistent */
    public $category = null;

    public function __construct(Container $container, ICategoryControlFactory $ICategoryControlFactory, ISimpleEmptyListControlFactory $ISimpleEmptyListControlFactory)
    {
        parent::__construct($container);
        $this->setComponentFactory($ICategoryControlFactory);
        $this->setNoItemsFactory($ISimpleEmptyListControlFactory);
    }

    protected function getCategoriesIds()
    {
        if ($this->category) {
            $categories = $this->categoryRepository->getChildren($this->categoryRepository->get(['id' => $this->category]));
            $categoriesIds = array_map(function($e) {
                return $e->getId();
            }, $categories);
            $categoriesIds[] = $this->category;
            return $categoriesIds;
        }
    }
}
