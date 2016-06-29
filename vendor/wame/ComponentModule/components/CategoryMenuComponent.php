<?php

namespace Wame\CategoryModule\Vendor\Wame\ComponentModule;

use Nette\Application\LinkGenerator;
use Wame\ComponentModule\Models\IComponent;
use Wame\MenuModule\Models\Item;
use Wame\CategoryModule\Components\ICategoryMenuControlFactory;

interface ICategoryMenuComponentFactory
{
    /** @return CategoryMenuComponent */
    public function create();   
}


class CategoryMenuComponent implements IComponent
{   
    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var ICategoryMenuControlFactory */
    private $ICategoryMenuControlFactory;


    public function __construct(
        LinkGenerator $linkGenerator,
        ICategoryMenuControlFactory $ICategoryMenuControlFactory
    ) {
        $this->linkGenerator = $linkGenerator;
        $this->ICategoryMenuControlFactory = $ICategoryMenuControlFactory;
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
        return 'categoryMenu';
    }


    public function getTitle()
    {
        return _('Category menu');
    }


    public function getDescription()
    {
        return _('Create category menu');
    }


    public function getIcon()
    {
        return 'fa fa-bars';
    }


    public function getLinkCreate()
    {
        return $this->linkGenerator->link('Admin:CategoryMenu:create');
    }


    public function getLinkDetail($componentEntity)
    {
        return $this->linkGenerator->link('Admin:CategoryMenu:edit', ['id' => $componentEntity->id]);
    }


    public function createComponent($componentInPosition)
    {
        $control = $this->ICategoryMenuControlFactory->create();
        $control->setComponentInPosition($componentInPosition);

        return $control;
    }

}