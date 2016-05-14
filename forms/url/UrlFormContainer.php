<?php

namespace Wame\CategoryModule\Forms;

use Nette\Application\UI\Form;
use Wame\DynamicObject\Forms\BaseFormContainer;

interface IUrlFormContainerFactory
{
	/** @return UrlFormContainer */
	public function create();
}

class UrlFormContainer extends BaseFormContainer
{
    public function render() 
	{
        $this->template->_form = $this->getForm();
        $this->template->render(__DIR__ . '/default.latte');
    }

    public function configure() 
	{
		$form = $this->getForm();

		$form->addText('slug', _('URL'))
				->setType('text');
    }
	
	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$form['slug']->setDefaultValue($object->categoryEntity->langs[$object->lang]->slug);
	}
}