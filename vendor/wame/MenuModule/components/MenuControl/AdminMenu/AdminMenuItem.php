<?php

namespace Wame\CategoryModule\Vendor\Wame\MenuModule\Components\MenuControl\AdminMenu;

use Wame\MenuModule\Models\Item;

class AdminMenuItem
{	
	public $name = 'category';

    /** @var \Nette\Application\LinkGenerator */
	private $linkGenerator;
	
	public function __construct($linkGenerator)
	{
		$this->linkGenerator = $linkGenerator;
	}
    
	public function addItem()
	{
		$item = new Item();
		$item->setName('articles');
		
		$item->addNode($this->categoriesDefault(), 'categories');
		$item->addNode($this->categoryCreate(), 'addCategory');
		
		return $item->getItem();
	}
	
	private function categoriesDefault()
	{
		$item = new Item();
		$item->setTitle(_('Categories'));
		$item->setLink($this->linkGenerator->link('Admin:Category:', ['id' => null]));
		
		return $item->getItem();
	}
	
	private function categoryCreate()
	{
		$item = new Item();
		$item->setTitle(_('Add category'));
		$item->setLink($this->linkGenerator->link('Admin:Category:create', ['type' => 'article']));
		
		return $item->getItem();
	}

}

/**
 * TODO:
 * 
 * * odkazy musia byt pre article
 */