<?php

namespace Wame\CategoryModule\Vendor\Wame\ComponentModule;

use Nette\Application\LinkGenerator;
use Wame\CategoryModule\Components\ICategoryFilterControlFactory;
use Wame\ComponentModule\Registers\IComponent;
use Wame\MenuModule\Models\Item;

class CategoryFilterComponent implements IComponent
{
    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var ICategoryFilterControlFactory */
    private $ICategoryFilterControlFactory;

    
    public function __construct(
        LinkGenerator $linkGenerator, ICategoryFilterControlFactory $ICategoryFilterControlFactory
    ) {
        $this->linkGenerator = $linkGenerator;
        $this->ICategoryFilterControlFactory = $ICategoryFilterControlFactory;
    }
    

    /** {@inheritDoc} */
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

    /** {@inheritDoc} */
    public function getName()
    {
        return 'categoryFilter';
    }

    /** {@inheritDoc} */
    public function getTitle()
    {
        return _('Category filter');
    }

    /** {@inheritDoc} */
    public function getDescription()
    {
        return _('Create category filter component');
    }

    /** {@inheritDoc} */
    public function getIcon()
    {
        return 'fa fa-ban';
    }

    /** {@inheritDoc} */
    public function getLinkCreate()
    {
        return $this->linkGenerator->link('Admin:CategoryFilterControl:create');
    }

    /** {@inheritDoc} */
    public function getLinkDetail($componentEntity)
    {
        return $this->linkGenerator->link('Admin:CategoryFilterControl:edit', ['id' => $componentEntity->id]);
    }

    /** {@inheritDoc} */
    public function createComponent()
    {
        $control = $this->ICategoryFilterControlFactory->create();
        return $control;
    }
    
}
