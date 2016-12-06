<?php

namespace Wame\CategoryModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\Utils\Arrays;

interface ICategorySelect2ContainerFactory extends IBaseContainer
{
    /** @return CategorySelect2Container */
    public function create();
}

class CategorySelect2Container extends BaseContainer
{
    /** @var CategoryEntity[] */
    private $categories = [];
    
    
    public function configure()
    {
        $this->addSelect('categories', _('Categories'), $this->categories)->setAttribute('class', 'category-select2');
    }
    
    public function setItems($items)
    {
        $this->categories = $items;
        
        $this['categories']->setItems(Arrays::getPairs($items, 'id', 'title'));
    }
    
}
