<?php

namespace Wame\CategoryModule\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;


interface ITypeFormContainerFactory
{
	/** @return TypeFormContainer */
	public function create();
}


class TypeFormContainer extends BaseFormContainer
{
    public function render() 
	{
        $this->template->_form = $this->getForm();
        $this->template->render(__DIR__ . '/default.latte');
    }

    public function configure() 
	{
		$form = $this->getForm();

		$form->addGroup(_('Basic info'));

		$types = [
			'article' => 'article',
			'shopProduct' => 'shopProduct'
		];
		
		$form->addSelect('type', _('Type'), $types);
    }
	
	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$form['type']->setDefaultValue($object->categoryEntity->type);
	}
	
}