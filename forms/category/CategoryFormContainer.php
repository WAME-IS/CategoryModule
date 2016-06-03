<?php

namespace Wame\CategoryModule\Forms;

use Nette\Application\UI\Form;
use Wame\DynamicObject\Forms\BaseFormContainer;

interface ICategoryFormContainerFactory
{
	/** @return CategoryFormContainer */
	public function create();
}

class CategoryFormContainer extends BaseFormContainer
{
    public function render() 
	{
        $this->template->_form = $this->getForm();
        $this->template->render(__DIR__ . '/default.latte');
    }

    public function configure() 
	{
		$form = $this->getForm();

		$form->addText('category', _('Category'))
				->setType('number');
    }
	
	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$form['category']->setDefaultValue($object->componentEntity->getParameter('category'));
	}
}