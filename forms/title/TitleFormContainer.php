<?php

namespace Wame\CategoryModule\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;


interface ITitleFormContainerFactory
{
	/** @return TitleFormContainer */
	public function create();
}


class TitleFormContainer extends BaseFormContainer
{
    public function configure() 
	{
		$form = $this->getForm();

		$form->addGroup(_('Basic info'));
		
		$form->addText('title', _('Title'))
				->setRequired(_('Please enter title'));
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$form['title']->setDefaultValue($object->categoryEntity->langs[$object->lang]->title);
	}

}