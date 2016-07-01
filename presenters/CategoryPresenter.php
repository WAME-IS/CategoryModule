<?php

namespace App\CategoryModule\Presenters;

use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryLangRepository;
use Wame\CategoryModule\Components\ICategoryListControlFactory;


class CategoryPresenter extends \App\Core\Presenters\BasePresenter
{
	/** @var CategoryRepository @inject */
	public $categoryRepository;

	/** @var CategoryLangRepository @inject */
	public $categoryLangRepository;
	
	/** @var ICategoryListControlFactory @inject */
	public $ICategoryListControlFactory;
	
	/** @var array */
	public $items = [];
	
	
	/** components ************************************************************/
	
	public function createComponentCategoryList()
	{
		$component = $this->ICategoryListControlFactory->create();
		
		return $component;
	}
	
}