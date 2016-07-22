<?php

namespace Wame\CategoryModule\Vendor\Wame\ComponentModule;

use Nette\Application\LinkGenerator;
use Wame\ComponentModule\Registers\IComponent;
use Wame\MenuModule\Models\Item;
use Wame\CategoryModule\Components\ICategoryListControlFactory;

interface ICategoryListComponentFactory
{
    /** @return CategoryListComponent */
    public function create();   
}


class CategoryListComponent implements IComponent
{   
    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var ICategoryListControlFactory */
    private $ICategoryListControlFactory;


    public function __construct(
        LinkGenerator $linkGenerator,
        ICategoryListControlFactory $ICategoryListControlFactory
    ) {
        $this->linkGenerator = $linkGenerator;
        $this->ICategoryListControlFactory = $ICategoryListControlFactory;
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
        return 'categoryList';
    }


    public function getTitle()
    {
        return _('Category list');
    }


    public function getDescription()
    {
        return _('Create category list');
    }


    public function getIcon()
    {
        return 'fa fa-list-alt';
    }


    public function getLinkCreate()
    {
        return $this->linkGenerator->link('Admin:CategoryList:create');
    }


    public function getLinkDetail($componentEntity)
    {
        return $this->linkGenerator->link('Admin:CategoryList:edit', ['id' => $componentEntity->id]);
    }


    public function createComponent()
    {
        $control = $this->ICategoryListControlFactory->create();
        return $control;
    }

}