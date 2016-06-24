<?php

namespace Wame\CategoryModule\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;


interface IUrlFormContainerFactory
{
	/** @return UrlFormContainer */
	public function create();
}


class UrlFormContainer extends BaseFormContainer
{
    public function configure() 
	{
		$form = $this->getForm();

		$form->addText('slug', _('URL'));
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$form['slug']->setDefaultValue($object->categoryEntity->langs[$object->lang]->slug);
	}

}