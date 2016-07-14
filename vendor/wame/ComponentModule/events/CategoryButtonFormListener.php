<?php

namespace Wame\CategoryModule\Vendor\Wame\ComponentModule\Events;

use Nette\Object;
use Wame\ComponentModule\Repositories\ComponentRepository;

class CategoryButtonFormListener extends Object 
{
	const COMPONENT = 'CategoryButtonComponent';
	
	/** @var ComponentRepository */
	private $componentRepository;
	

	public function __construct(
		ComponentRepository $componentRepository
	) {
		$this->componentRepository = $componentRepository;
		
		$componentRepository->onCreate[] = [$this, 'onCreate'];
		$componentRepository->onUpdate[] = [$this, 'onUpdate'];
		$componentRepository->onDelete[] = [$this, 'onDelete'];
	}

	
	public function onCreate($form, $values, $componentEntity) 
	{
		if ($componentEntity->type == self::COMPONENT) {				
			$componentEntity->setParameters($this->getParams($values, $componentEntity->getParameters()));
		}
	}
	
	
	public function onUpdate($form, $values, $componentEntity)
	{
		if ($componentEntity->type == self::COMPONENT) {
			$componentEntity->setParameters($this->getParams($values, $componentEntity->getParameters()));
		}
	}
	
	
	public function onDelete()
	{
		
	}
	
	
	/**
	 * Get parameters
	 * 
	 * @param array $values
	 * @param array $parameters
	 * @return array
	 */
	private function getParams($values, $parameters = [])
	{
		$array = [];
		
		return array_replace($parameters, $array);
	}
    
}
