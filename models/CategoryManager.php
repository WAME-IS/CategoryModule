<?php

namespace Wame\CategoryModule\Managers;


class CategoryManager
{
	/** @var array */
	private $types = [];
	
	
	/**
	 * Add item to list
	 * 
	 * @param string $type	type
	 */
	public function addType($type)
	{
		if($type instanceof \Wame\CategoryModule\Models\Type\CategoryType) {
			$this->types[$type->getAlias()] = $type;
		} else {
			throw new \Exception("Wrong category type");
		}
	}
	
	/**
	 * Get list
	 * 
	 * @return array	list
	 */
	public function getTypes()
	{
		return $this->types;
	}
	
	/**
	 * Get type
	 * 
	 * @param string $type		type
	 * @return CategoryType		category type
	 */
	public function getType($type)
	{
		if(isset($this->types[$type])) {
			return $this->types[$type];
		} else {
			throw new \Exception('Type doesnt exists');
		}
	}
	
}