<?php

namespace App\AdminModule\Presenters;

use Wame\Core\Presenters\Traits\UseParentTemplates;


class CategoryFilterControlPresenter extends AbastractComponentPresenter
{	
    use UseParentTemplates;
    
    
    protected function getComponentIdentifier()
    {
        return 'CategoryFilterComponent';
    }
    
    
    protected function getComponentName()
    {
        return _('Category filter component');
    }
 
}
