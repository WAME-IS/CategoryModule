<?php

namespace Wame\CategoryModule\Managers;


class CategoryManager
{
	private $types = [];
	
	
	/**
	 * Add item to list
	 * 
	 * @param string $item	item
	 */
	public function addType($type)
	{
		$types[] = $type;
	}
	
	/**
	 * Get list
	 * 
	 * @return array	list
	 */
	public function getTypes()
	{
		return $types;
	}
}