<?php

namespace Wame\CategoryModule\Forms\Groups;

use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\DynamicObject\Forms\Groups\BaseGroup;
use Nette\Utils\Html;

interface ICategoryGroupFactory extends IBaseContainer
{
	/** @return CategoryContainer */
	function create();
}

class CategoryContainer extends BaseGroup
{
    /** @var Html */
    private $tag;
    
    
    /** {@inheritDoc} */
	protected function getGroupTitle()
    {
        return _('Category');
    }

    protected function getGroupAttributes() {
        return [];
    }

    protected function getGroupTag()
    {
        if(!$this->tag) {
            $this->tag = Html::el('div')->setAttibutes($this->getGroupAttributes());
        }
        
        return $this->tag;
    }

}