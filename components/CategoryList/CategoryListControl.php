<?php

namespace Wame\CategoryModule\Components;

use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryItemRepository;

use Wame\Utils\Tree\ComplexTreeSorter;


interface ICategoryListControlFactory
{
	/** @return CategoryListControl */
	public function create();	
}


class CategoryListControl extends \Wame\Core\Components\BaseControl
{
	/** @var CategoryRepository */
	public $categoryRepository;
	
	/** @var CategoryItemRepository */
	public $categoryItemRepository;
	
	/** @var string */
	private $lang;
	
	
	public function __construct(CategoryRepository $categoryRepository, CategoryItemRepository $categoryItemRepository) 
	{
		parent::__construct();
		
		$this->categoryRepository = $categoryRepository;
		$this->categoryItemRepository = $categoryItemRepository;
		$this->lang = $categoryRepository->lang;
	}
	
	
	public function render($parameters = [])
	{
		$depth = isset($parameters['depth']) ? $parameters['depth'] : 1;
		$type = isset($parameters['type']) ? $parameters['type'] : null;
		
		dump($type);
		
		if($type) {
			$categories = $this->categoryRepository->find(['type' => $type, 'depth' => $depth]);
//			$categories = $this->categoryItemRepository->getCategories($type, null);
		} else {
			if($depth) {
				$categories = $this->categoryRepository->find(['depth' => $depth]);
			} else {
				$categories = $this->categoryRepository->find();
				$categories = (new ComplexTreeSorter($categories))->sortTree();
				$categories = $categories->child_nodes;
			}
		}
		
		$this->template->lang = $this->lang;
		$this->template->categories = $categories;
		
		$this->getTemplateFile();
		$this->template->render();
	}
}