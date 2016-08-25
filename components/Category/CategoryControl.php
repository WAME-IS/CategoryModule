<?php

namespace Wame\CategoryModule\Components;

use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\Core\Components\SingleEntityControl;
use Wame\ListControl\Components\IEntityControlFactory;

interface ICategoryControlFactory extends IEntityControlFactory
{

    /** @return CategoryControl */
    public function create($entity = null);
}

class CategoryControl extends SingleEntityControl
{

    protected function getEntityType()
    {
        return CategoryEntity::class;
    }
    
    public function handleSelect()
    {
        $list = $this->lookup(CategoryListControl::class);
        $list->setCategory($this->getEntity());
    }
}
