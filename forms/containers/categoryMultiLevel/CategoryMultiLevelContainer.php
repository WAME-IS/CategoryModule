<?php

namespace Wame\CategoryModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;

interface ICategoryMultiLevelContainerFactory extends IBaseContainer
{
	/** @return CategoryMultiLevelContainer */
	public function create();
}

class CategoryMultiLevelContainer extends BaseContainer
{
    /** @var string */
    protected $ype;


    /** {@inheritDoc} */
    public function configure() 
	{
		$this->addHidden('category', _('Category'))
				->setRequired(_('Please select category'));
    }

    /** {@inheritDoc} */
	public function setDefaultValues($entity, $langEntity = null)
	{
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