<?php

namespace Wame\CategoryModule\Forms\Groups;

use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\DynamicObject\Forms\Groups\BaseGroup;
use Nette\Utils\Html;

interface ICategoryGroupFactory extends IBaseContainer
{
	/** @return CategoryGroup */
	function create();
}

class CategoryGroup extends BaseGroup
{
    /** {@inheritDoc} */
    public function getText()
    {
        return _("Category");
    }

}