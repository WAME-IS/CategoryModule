<?php

namespace Wame\CategoryModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;

interface ICategorySelect2ContainerFactory extends IBaseContainer
{
    /** @return CategorySelect2Container */
    public function create();
}

class CategorySelect2Container extends BaseContainer
{
    /** @var CategoryEntity[] */
    private $categories;
    
    
    public function configure()
    {
        $this->addSelect('categories', _('Categories'), $this->categories)->setAttribute('class', 'category-select2');
    }
    
    public function setItems($items)
    {
        \Tracy\Debugger::barDump("setItems");
        $this->categories = $items;
    }
    
}
