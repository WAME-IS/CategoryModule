<?php

namespace Wame\CategoryModule\Components;

use Wame\Core\Components\BaseControl;

interface ICategoryButtonControlFactory
{
    /** @return CategoryButtonControl */
    public function create();
}

class CategoryButtonControl extends BaseControl
{
    public function render()
    {
        
    }

}
