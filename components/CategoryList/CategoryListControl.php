<?php

namespace Wame\CategoryModule\Components;

use Nette\DI\Container;
use Wame\CategoryModule\Components\CategoryListControl;
use Wame\CategoryModule\Components\ICategoryControlFactory;
use Wame\ChameleonComponentsListControl\Components\ChameleonTreeListControl;
use Wame\ListControl\Components\ISimpleEmptyListControlFactory;
use Wame\TitleControl\Components\TitleControl;
use Doctrine\Common\Collections\Criteria;

interface ICategoryListControlFactory
{
    /** @return CategoryListControl */
    public function create();
}

class CategoryListControl extends ChameleonTreeListControl
{
    use CategoryListTrait;
    
    
    const PARAM_CATEGORY = 'category';

    
    /** @persistent */
    public $category = null;
    
    /** @var bool */
    public $main = false;
    
    /** @var CategoryEntity */
    protected $selectedCategory;
    

    public function __construct(Container $container, ICategoryControlFactory $ICategoryControlFactory, ISimpleEmptyListControlFactory $ISimpleEmptyListControlFactory)
    {
        parent::__construct($container);
        
        $this->setComponentFactory($ICategoryControlFactory);
        $this->setNoItemsFactory($ISimpleEmptyListControlFactory);
    }
    
    
    /** rendering *************************************************************/
    
    public function render()
    {
        $depthFrom = $this->getDepth();
        
        $this->template->depthFrom = $depthFrom;
        
        parent::render();
    }
    
    /** {@inheritDoc} */
    public function beforeRender()
    {
        parent::beforeRender();
        
        $this->main = $this->getComponentParameter('main');
    }

    
    /** {@inheritDoc} */
    protected function getCategoriesIds()
    {
        $selectedCategory = $this->getSelectedCategory();
        
        if ($selectedCategory) {
            TitleControl::add($selectedCategory->getTitle());
            
            $categories = $this->categoryRepository->getChildren($selectedCategory);
            
            $categoriesIds = array_map(function($e) {
                return $e->getId();
            }, $categories);
            
            $categoriesIds[] = $selectedCategory->id;
            
            return $categoriesIds;
        }
    }

    /** {@inheritDoc} */
    protected function getSelectedCategory()
    {
        if($this->selectedCategory) {
            return $this->selectedCategory;
        }
        
        $category = $this->category ?: ($this->getComponentParameter(self::PARAM_CATEGORY) ?: $this->presenter->getParameter('category'));
        
        if($category) {
            return $this->categoryRepository->get(['id' => $category]);
        }
    }
    
    /** {@inheritDoc} */
    protected function loadParametersCriteria()
    {
        $depth = $this->getComponentParameter(self::PARAM_DEPTH);
        if ($depth) {
            $this->setDepth($this->getDepth() + $depth);
        }

        return $this->getCriteria();
    }
    
    
    /**
     * Get depth
     * 
     * @return type
     */
    private function getDepth()
    {
        return $this->getSelectedCategory() ? $this->getSelectedCategory()->getDepth() : 1;
    }

}
