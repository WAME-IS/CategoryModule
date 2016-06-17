<?php

namespace App\CategoryModule\Presenters;

use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryLangRepository;
//use Wame\CategoryModule\Repositories\CategoryItemRepository;
use Wame\CategoryModule\Components\ICategoryListControlFactory;
use Wame\ArticleCategoryPlugin\Controls\ArticlesOfCategory;

use Wame\ArticleCategoryPlugin\Repositories\ArticleCategoryRepository;


class CategoryPresenter extends \App\Core\Presenters\BasePresenter
{
	/** @var CategoryRepository @inject */
	public $categoryRepository;

	/** @var CategoryLangRepository @inject */
	public $categoryLangRepository;
	
//	/** @var CategoryItemRepository @inject */
//	public $categoryItemRepository;
	
	/** @var ArticlesOfCategory @inject */
	public $articlesOfCategory;
	
	/** @var ICategoryListControlFactory @inject */
	public $ICategoryListControlFactory;
	
	/** @var ArticleCategoryRepository @inject */
	public $articleCategoryRepository;
	
	/** @var array */
	public $items = [];
	
	
//	public function renderArticleCategories()
//	{
//		$articleCategories = $this->articleCategoryRepository->find();
//	}
	
//	public function renderArticles()
//	{
//		$this->template->items = $this->items;
//	}
	
//	public function createComponentArticlesOfCategory()
//	{
//		return $this->articlesOfCategory;
//	}
	
	/** components ************************************************************/
	
	public function createComponentCategoryList()
	{
		$component = $this->ICategoryListControlFactory->create();
		
		return $component;
	}
	
}