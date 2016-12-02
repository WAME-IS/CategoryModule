<?php

namespace Wame\CategoryModule\Components;

use Nette\DI\Container;
use Wame\CategoryModule\Components\CategoryListControl;
use Wame\CategoryModule\Components\ICategoryControlFactory;
use Wame\ChameleonComponentsListControl\Components\ChameleonTreeListControl;
use Wame\ListControl\Components\ISimpleEmptyListControlFactory;
use Doctrine\Common\Collections\Criteria;

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
    
    /** @var bool */
    public $main = false;
    
    protected $selectedCategory;
    

    public function __construct(Container $container, ICategoryControlFactory $ICategoryControlFactory, ISimpleEmptyListControlFactory $ISimpleEmptyListControlFactory)
    {
        parent::__construct($container);
        $this->setComponentFactory($ICategoryControlFactory);
        $this->setNoItemsFactory($ISimpleEmptyListControlFactory);
    }
    
    public function render() {
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

    
    protected function getCategoriesIds()
    {
        $selectedCategory = $this->getSelectedCategory();
        
        if ($selectedCategory) {
            $categories = $this->categoryRepository->getChildren($selectedCategory);
            
            
            \Tracy\Debugger::barDump($categories);
            $categoriesIds = array_map(function($e) {
                return $e->getId();
            }, $categories);
            $categoriesIds[] = $selectedCategory->id;
            
            \Tracy\Debugger::barDump($categoriesIds);
            
            return $categoriesIds;
        }
    }

    protected function getSelectedCategory()
    {
        if($this->selectedCategory) {
            return $this->selectedCategory;
        }
        
        $category = $this->category ?: $this->presenter->getParameter('category');
        
        if($category) {
            return $this->categoryRepository->get(['id' => $category]);
        }
    }
    
//    protected function loadParametersCriteria()
//    {
//        $categoryCriteria = Criteria::create();
//        
//        $sub = $this->getComponentParameter('sub') ?: 0;
//        
//        $depthTo = $this->getSelectedCategory()->depth + $sub;
//        
//        
//        
//        \Tracy\Debugger::barDump($this->getSelectedCategory(), $depthTo);
//        
//        $categoryCriteria->andWhere(Criteria::expr()->lte('depth', $depthTo));
//        
//        return $categoryCriteria;
//    }
    
    protected function loadParametersCriteria()
    {
//        $listCriteria = $this->getComponentParameter(ChameleonListControl::PARAM_LIST_CRITERIA);
//        if ($listCriteria) {
//            $this->addCriteria(Utils::readCriteria($listCriteria));
//        }

        $depth = $this->getComponentParameter(self::PARAM_DEPTH);
        if ($depth) {
            \Tracy\Debugger::barDump($this->getDepth());
            $this->setDepth($this->getDepth() + 1);
        }
        
//        $criteria = $this->getCriteria();
//        
//        $criteria->andWhere(Criteria::expr()->eq(''))

        return $this->getCriteria();
    }
    
    private function getDepth()
    {
        return $this->getSelectedCategory() ? $this->getSelectedCategory()->getDepth() : 1;
    }

}
