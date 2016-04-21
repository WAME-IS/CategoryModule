<?php

namespace Wame\CategoryModule\Controls;

use Nette\Application\UI\Control;

use Wame\CategoryModule\Repositories\CategoryRepository;

class BaseControl extends Control
{
	/** @var CategoryRepository @inject */
	public $categoryRepository;
	
	protected $items;
	
	protected $lang;
	
	public function injectRepository(CategoryRepository $categoryRepository)
	{
		$this->categoryRepository = $categoryRepository;
		$this->lang = $this->categoryRepository->lang;
	}
	
}