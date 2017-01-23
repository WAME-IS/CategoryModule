<?php

namespace Wame\CategoryModule\Components;

use Doctrine\Common\Collections\Criteria;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Wame\Core\Components\BaseControl;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\ChameleonComponents\IO\DataLoaderControl;
use Wame\Utils\Doctrine;
use Wame\Utils\Strings;
use Wame\Utils\Tree\NestedSetTreeBuilder;

interface ICategoryFilterControlFactory
{
    /** @return CategoryFilterControl */
    public function create();
}

class CategoryFilterControl extends BaseControl implements DataLoaderControl
{
    use CategoryListTrait;

    
    /** @persistent */
    public $categories;

    /** @var CategoryEntity[] */
    public $allowedCategories = [];

//    /** @var CategoryFilterFormBuilder */
//    private $categoryFilterFormBuilder;
    
    
    public function __construct(
        Container $container
//        CategoryFilterFormBuilder $categoryFilterFormBuilder
    ) {
        parent::__construct($container);
        
//        $this->categoryFilterFormBuilder = $categoryFilterFormBuilder;

        $this->getStatus()->get(Strings::plural(CategoryEntity::class), function ($value) {
            $this['categoryFilterForm']['CategorySelect2Container']['categories']->setItems($value);
        });
    }
    
    
    /** rendering *************************************************************/
    
    /** {@inheritDoc} **/
    public function beforeRender()
    {
        parent::beforeRender();
        
        $statusName = Strings::plural(\Wame\ShopProductModule\Entities\ShopProductEntity::class); // TODO: odstranit zavyslost!!
        $statusNameQb = $statusName . '-qb';
        
        /* @var $qb QueryBuilder */
        $qb = clone $this->getStatus()->get($statusNameQb);
        
        if($qb && in_array('cat', $qb->getAllAliases())) {
            $qb->select('cat')
                ->setFirstResult(null)
                ->setMaxResults(null);
            
            Doctrine::removeWherePart($qb, 'c.category', ['c_category']);
            Doctrine::removeWherePart($qb, 'c.item_id');
            
            $foundCategories = $qb->getQuery()->getResult();
            
            foreach($foundCategories as $category) {
                $this->allowedCategories[] = $category->id;
            }
        }
    }
    
    /** {@inheritDoc} **/
    public function render()
    {
        
    }
    
    
    /** methods ***************************************************************/
    
    public function getTreeBuilder()
    {
        return new NestedSetTreeBuilder();
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories($categories)
    {
        $this->categories = $categories;
    }
    
    
    protected function loadParametersCriteria()
    {
        return Criteria::create();
    }

    protected function getCategoriesIds()
    {
        if ($this->categories) {
            $categoriesIds = explode(",", $this->categories);
            $rootCategories = $this->categoryRepository->find(['id IN' => $categoriesIds]);
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

    
    /** components ************************************************************/
    
    protected function createComponentForm()
    {
        $presenter = $this->lookup(Presenter::class);
        $form = $presenter->context->getService("CategoryFilterFormBuilder")->build();
        
        if($this->categories) {
            $categories = explode(",", $this->categories);
            $form["CategoryContainer"]["category"]->setValue($categories);
        }
        
        return $form;
    }
    
}
