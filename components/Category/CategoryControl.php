<?php

namespace Wame\CategoryModule\Components;

use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\ChameleonComponents\Components\SingleEntityControl;
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
            /** NEW ***********************************************************/
            if($categoryList->main) {
                $this->template->categoryLink = $categoryList->getPresenter()->link(':ShopProduct:ShopProduct:default', ['category' => $this->getEntity()->slug ?: $this->getEntity()->id]);
            } else {
                $this->template->categoryLink = $categoryList->link('this', ['category' => $this->getEntity()->slug ?: $this->getEntity()->id]);
            }
            /** /NEW **********************************************************/
            
            // povodne
//            $this->template->categoryLink = $categoryList->link('this', ['category' => $this->getEntity()->id]);
        } else {
            $this->template->categoryLink = $categoryList->getPresenter()->link('Category:Category:default', ['category' => $this->getEntity()->slug ?: $this->getEntity()->id]);
        }
    }
}
