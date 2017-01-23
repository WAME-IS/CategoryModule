<?php

namespace Wame\CategoryModule\Events;

use Nette\Object;
use \Wame\CategoryModule\Repositories\CategoryRepository;

class CategoryListener extends Object 
{
	const TAG = 'CategoryListener';


	/** @var CategoryRepository */
	private $categoryRepository;


	public function __construct(CategoryRepository $categoryRepository)
	{
		$this->categoryRepository = $categoryRepository;
		
		$categoryRepository->onCreate[] = [$this, 'onCreate'];
		$categoryRepository->onUpdate[] = [$this, 'onUpdate'];
//		$categoryRepository->onDelete[] = [$this, 'onDelete'];
	}

	public function onCreate($form, $values, $categoryEntity) 
	{
		$values['categories'] = $form->getHttpData($form::DATA_TEXT, 'categories[]');
		
		$this->categoryRepository->attachAll($categoryEntity, $values->categories);
	}
	
	public function onUpdate()
	{
		
	}
	
	public function onDelete()
	{
		
	}

}
