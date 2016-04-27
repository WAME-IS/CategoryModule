<?php

namespace Wame\CategoryModule\Forms;

use Nette\Application\UI\Form;
use Wame\DynamicObject\Forms\BaseFormContainer;

interface ITitleFormContainerFactory
{
	/** @return TitleFormContainer */
	public function create();
}

class TitleFormContainer extends BaseFormContainer
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
		
		$form->addText('title', _('Title'))
				->setType('text')
				->setRequired(_('Please enter title'))
				->addRule(Form::FILLED, _('Title can not be empty'));
    }
	
}