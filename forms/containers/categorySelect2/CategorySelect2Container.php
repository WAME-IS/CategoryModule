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
    public function configure()
    {
        $categories = ['test'];
        
        $this->addSelect('categories', _('Categories'), $categories)->setAttribute('class', 'category-select2');
    }
    
}
