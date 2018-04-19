<?php

namespace Wame\CategoryModule\Vendor\Wame\MenuModule\Components\MenuManager\Forms;

use Nette\Application\LinkGenerator;
use Wame\MenuModule\Models\Item;
use Wame\MenuModule\Models\DatabaseMenuProvider\IMenuItem;
use Wame\MenuModule\Repositories\MenuRepository;

interface ICategoryMenuItem
{
	/** @return CategoryMenuItem */
	public function create();
}


class CategoryMenuItem implements IMenuItem
{	
    /** @var LinkGenerator */
	private $linkGenerator;
	
    /** @var string */
	private $lang;
	
	
	public function __construct(
		LinkGenerator $linkGenerator,
		MenuRepository $menuRepository
	) {
		$this->linkGenerator = $linkGenerator;
		$this->lang = $menuRepository->lang;
	}

	
	public function addItem()
	{
		$item = new Item();
		$item->setName($this->getName());
		$item->setTitle($this->getTitle());
		$item->setDescription($this->getDescription());
		$item->setLink($this->getLinkCreate());
		$item->setIcon($this->getIcon());
		
		return $item->getItem();
	}

	
	public function getName()
	{
		return 'category';
	}
	
	
	public function getTitle()
	{
		return _('Category');
	}
	
	
	public function getDescription()
	{
		return _('Insert link to the category');
	}
	
	
	public function getIcon()
	{
		return 'fa fa-tags';
	}
	
	
	public function getLinkCreate($menuId = null)
	{
		return $this->linkGenerator->link('Admin:Category:menuItem', ['m' => $menuId]);
	}
	
	
	public function getLinkUpdate($menuEntity)
	{
		return $this->linkGenerator->link('Admin:Category:menuItem', ['id' => $menuEntity->id, 'm' => $menuEntity->component->id]);
	}
	
	
	public function getLink($menuEntity)
	{
		return $this->linkGenerator->link('Category:Category:show', ['id' => $menuEntity->langs[$this->lang]->slug, 'lang' => $this->lang]);
	}
	
}
