<?php

namespace Wame\CategoryModule\Vendor\Wame\MenuModule\Components\MenuManager\Events;

use Nette\Object;
use Wame\MenuModule\Repositories\MenuRepository;
use Wame\CategoryModule\Repositories\CategoryRepository;

class MenuItemListener extends Object 
{
	const TYPE = 'category';
	
	/** @var MenuRepository */
	private $menuRepository;
	
	/** @var CategoryRepository */
	private $categoryRepository;
	
	/** @var string */
	private $lang;
	
	
	public function __construct(
		MenuRepository $menuRepository,
		CategoryRepository $categoryRepository
	) {
		$this->menuRepository = $menuRepository;
		$this->categoryRepository = $categoryRepository;
		$this->lang = $menuRepository->lang;
		
		$menuRepository->onCreate[] = [$this, 'onCreate'];
		$menuRepository->onUpdate[] = [$this, 'onUpdate'];
		$menuRepository->onDelete[] = [$this, 'onDelete'];
	}

	
	public function onCreate($form, $values, $menuEntity) 
	{
		if ($menuEntity->type == self::TYPE) {
			$category = $this->categoryRepository->find(['id' => $values['value']]);

			$menuEntity->setValue($category->id);

			$menuEntity->langs[$this->lang]->setTitle($category->langs[$this->lang]->title);
			$menuEntity->langs[$this->lang]->setAlternativeTitle($values['alternative_title']);
			$menuEntity->langs[$this->lang]->setSlug($category->langs[$this->lang]->slug);
		}
	}
	
	
	public function onUpdate($form, $values, $menuEntity)
	{
		if ($menuEntity->type == self::TYPE) {
			$category = $this->categoryRepository->find(['id' => $values['value']]);

			$menuEntity->setValue($category->id);

			$menuEntity->langs[$this->lang]->setTitle($category->langs[$this->lang]->title);
			$menuEntity->langs[$this->lang]->setAlternativeTitle($values['alternative_title']);
			$menuEntity->langs[$this->lang]->setSlug($category->langs[$this->lang]->slug);
		}
	}
	
	
	public function onDelete()
	{
		
	}

}
