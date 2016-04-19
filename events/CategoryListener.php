<?php

namespace Wame\CategoryModule\Events;

use Nette\Object;
use \Wame\CategoryModule\Repositories\CategoryRepository;

class CategoryListener extends Object 
{
	const TAG = 'CategoryListener';

	private $categoryRepository;
	
	public function __construct(CategoryRepository $categoryRepository)
	{
		$this->categoryRepository = $categoryRepository;
		
		$categoryRepository->onCreate[] = [$this, 'onCreate'];
//		$categoryRepository->onEdit[] = [$this, 'onEdit'];
//		$categoryRepository->onDelete[] = [$this, 'onDelete'];
	}

	public function onCreate($type, $entity, $values) 
	{
	}
	
	public function onEdit()
	{
	}
	
	public function onDelete()
	{
	}

}
