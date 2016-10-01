<?php

namespace Wame\CategoryModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;

interface ICategoryTreeContainerFactory extends IBaseContainer
{
	/** @return CategoryTreeContainer */
	public function create();
}

class CategoryTreeContainer extends BaseContainer
{
    private $type;
    
    
    /** {@inheritDoc} */
    public function configure() 
	{
		$this->addHidden('category', _('Category'))
				->setRequired(_('Please select category'));
    }

    /** {@inheritDoc} */
	public function setDefaultValues($entity, $langEntity = null)
	{
        // TODO:
        $this->type = '';
//        $this['category']->setDefaultValue($entity->getTitle());
	}

    /** {@inheritDoc} */
    public function create($form, $values)
    {
        // TODO:
//        $entity = method_exists($form, 'getLangEntity') ? $form->getLangEntity(): $form->getEntity();
//        $entity->setCategory($values['category']);
    }

    /** {@inheritDoc} */
    public function update($form, $values)
    {
        // TODO:
//        $entity = method_exists($form, 'getLangEntity') ? $form->getLangEntity(): $form->getEntity();
//        $entity->setCategory($values['category']);
    }

}