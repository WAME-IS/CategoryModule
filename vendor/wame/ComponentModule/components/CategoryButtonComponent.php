<?php

namespace Wame\CategoryModule\Vendor\Wame\ComponentModule;

use Nette\Application\LinkGenerator;
use Wame\ComponentModule\Models\IComponent;
use Wame\MenuModule\Models\Item;
use Wame\CategoryModule\Components\ICategoryButtonControlFactory;

interface ICategoryButtonComponentFactory
{
    /** @return CategoryButtonComponent */
    public function create();   
}


class CategoryButtonComponent implements IComponent
{   
    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var ICategoryButtonControlFactory */
    private $ICategoryButtonControlFactory;


    public function __construct(
        LinkGenerator $linkGenerator,
        ICategoryButtonControlFactory $ICategoryButtonControlFactory
    ) {
        $this->linkGenerator = $linkGenerator;
        $this->ICategoryButtonControlFactory = $ICategoryButtonControlFactory;
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
        return 'categoryButton';
    }


    public function getTitle()
    {
        return _('Category button');
    }


    public function getDescription()
    {
        return _('Create category button');
    }


    public function getIcon()
    {
        return 'fa fa-bars';
    }


    public function getLinkCreate()
    {
        return $this->linkGenerator->link('Admin:CategoryButton:create');
    }


    public function getLinkDetail($componentEntity)
    {
        return $this->linkGenerator->link('Admin:CategoryButton:edit', ['id' => $componentEntity->id]);
    }


    public function createComponent($componentInPosition)
    {
        $control = $this->ICategoryButtonControlFactory->create();
        $control->setComponentInPosition($componentInPosition);

        return $control;
    }

}