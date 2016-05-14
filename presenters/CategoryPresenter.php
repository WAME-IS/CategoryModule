<?php

namespace App\CategoryModule\Presenters;

use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryLangRepository;
//use Wame\CategoryModule\Repositories\CategoryItemRepository;

use Wame\ArticleCategoryPlugin\Controls\ArticlesOfCategory;

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
	
	public $items = [];
	
	
	public function renderArticles()
	{
		$this->template->items = $this->items;
	}
	
	public function createComponentArticlesOfCategory()
	{
		return $this->articlesOfCategory;
	}
	
}