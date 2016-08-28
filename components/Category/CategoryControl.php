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

    public function render()
    {
        parent::render();

        $categoryList = $this->lookup(CategoryListControl::class);
        if ($categoryList instanceof CategoryListControl) {
            $this->template->categoryLink = $categoryList->link('this', ['category' => $this->getEntity()->id]);
        } else {
            $this->template->categoryLink = $categoryList->getPresenter()->link('Category:Category:default', ['category' => $this->getEntity()->id]);
        }
    }
}
