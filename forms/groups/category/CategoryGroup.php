<?php

namespace Wame\CategoryModule\Forms\Groups;

use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\DynamicObject\Forms\Groups\BaseGroup;


interface ICategoryGroupFactory extends IBaseContainer
{
	/** @return CategoryGroup */
	function create();
}


class CategoryGroup extends BaseGroup
{
    public function __construct()
    {
        parent::__construct();
        
        $this->setWidth('col-sm-6');
    }
    
    
    /** {@inheritDoc} */
    public function getText()
    {
        return _('Category');
    }

}