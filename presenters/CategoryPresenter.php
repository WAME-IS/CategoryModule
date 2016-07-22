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
    
    
    public function actionShow($id)
    {
        $category = $this->categoryRepository->get(['id' => $id]);
        $categoryItems = $this->categoryItemRepository->getItems($category->type, $this->id);
    }
    
	
	/** components ************************************************************/
	
	protected function createComponentCategoryList()
	{
		$component = $this->ICategoryListControlFactory->create();
		
		return $component;
	}
    
    protected function createComponentListByType()
    {
        $component = $this->IListByTypeControlFactory->create();
        
        return $component;
    }
	
}