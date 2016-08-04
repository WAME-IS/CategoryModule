<?php

namespace App\CategoryModule\Presenters;

use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryLangRepository;
use Wame\CategoryModule\Repositories\CategoryItemRepository;
use Wame\CategoryModule\Components\ICategoryListControlFactory;

use Wame\ListByTypeControl\Components\IListByTypeControlFactory;


class CategoryPresenter extends \App\Core\Presenters\BasePresenter
{
	/** @var CategoryRepository @inject */
	public $categoryRepository;

	/** @var CategoryLangRepository @inject */
	public $categoryLangRepository;
    
    /** @var CategoryItemRepository @inject */
    public $categoryItemRepository;
	
	/** @var ICategoryListControlFactory @inject */
	public $ICategoryListControlFactory;
    
    /** @var IListByTypeControlFactory @inject */
    public $IListByTypeControlFactory;
    
	/** @var array */
	public $items = [];
    
    /** @var integer */
    public $selectedCategory;
    
    /** @var CategoryEntity */
    private $category;
    
    
    /** handles ***************************************************************/
    
    public function handleGen()
    {
        $categories = $this->categoryRepository->find();
        
        foreach($categories as $category) {
            $parent = $this->categoryRepository->getParent($category);
            
            if($parent) {
                $category->setParent($parent);
            }
        }
    }
    
    
    /** actions ***************************************************************/
    
    public function actionShow($id = null)
    {
        $this->category = $this->categoryRepository->get(['id' => $id]);
//        $categoryItems = $this->categoryItemRepository->getItems($category->type, $this->id);
    }
    
    
    public function renderShow()
    {
        $this->template->type = $this->category->type;
        $this->template->parent = $this->category;
    }
	
	/** components ************************************************************/
	
	protected function createComponentCategoryList()
	{
		$component = $this->ICategoryListControlFactory->create();
        $component->setCategoryParent($this->id);
		
		return $component;
	}
    
    protected function createComponentListByType()
    {
        $component = $this->IListByTypeControlFactory->create();
        
        return $component;
    }
	
}