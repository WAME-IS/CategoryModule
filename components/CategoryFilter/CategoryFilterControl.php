<?php

namespace Wame\CategoryModule\Components;

use Doctrine\Common\Collections\Criteria;
use Nette\DI\Container;
use Wame\Core\Components\BaseControl;
use Wame\CategoryModule\Forms\CategoryFilterFormBuilder;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\ChameleonComponents\IO\DataLoaderControl;
use Wame\Utils\Strings;
use Wame\Utils\Tree\NestedSetTreeBuilder;

interface ICategoryFilterControlFactory
{
    /** @return CategoryFilterControl */
    public function create();
}

class CategoryFilterControl extends BaseControl implements DataLoaderControl
{
    use \Wame\CategoryModule\Components\CategoryListTrait;

    
    /** @persistent */
    public $categories;

    /** @var CategoryFilterFormBuilder */
    private $categoryFilterFormBuilder;
    
    
    public function __construct(
        Container $container, 
        CategoryFilterFormBuilder $categoryFilterFormBuilder
    ) {
        parent::__construct($container);
        
        $this->categoryFilterFormBuilder = $categoryFilterFormBuilder;

        $this->getStatus()->get(Strings::plural(CategoryEntity::class), function ($value) {
            $this['categoryFilterForm']['CategorySelect2Container']['categories']->setItems($value);
        });
    }

    protected function getCategoriesIds()
    {
        if ($this->categories) {
            $categoriesIds = [];
            $rootCategories = $this->categoryRepository->find(['id IN' => $this->categories]);
            foreach ($rootCategories as $rootCategory) {
                $categories = $this->categoryRepository->getChildren($rootCategory);
                $localCategoriesIds = array_map(function($e) {
                    return $e->getId();
                }, $categories);
                $categoriesIds = array_merge($categoriesIds, $localCategoriesIds);
            }
            return $categoriesIds;
        }
    }

    public function createComponentForm()
    {
        return $this->categoryFilterFormBuilder->build();
    }

    public function getTreeBuilder()
    {
        return new NestedSetTreeBuilder();
    }

    protected function loadParametersCriteria()
    {
        return Criteria::create();
    }

    public function render()
    {
        
    }

    function getCategories()
    {
        return $this->categories;
    }

    function setCategories($categories)
    {
        $this->categories = $categories;
    }
    
}
