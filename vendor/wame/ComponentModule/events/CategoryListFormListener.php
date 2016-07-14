<?php

namespace Wame\FavoriteModule\Vendor\Wame\ComponentModule;

use Nette\Object;
use Wame\ComponentModule\Repositories\ComponentRepository;

class CategoryListFormListener extends Object 
{
	const COMPONENT = 'CategoryListComponent';
	
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
		$array = [
			'depth' => $values->depth
		];
		
		return array_replace($parameters, $array);
	}

}
