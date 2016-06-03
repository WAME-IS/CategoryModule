<?php

namespace Wame\CategoryModule\Forms;

use Nette\Application\UI\Form;
use Wame\DynamicObject\Forms\BaseFormContainer;

interface IDepthFormContainerFactory
{
	/** @return DepthFormContainer */
	public function create();
}

class DepthFormContainer extends BaseFormContainer
{
    public function render() 
	{
        $this->template->_form = $this->getForm();
        $this->template->render(__DIR__ . '/default.latte');
    }

    public function configure() 
	{
		$form = $this->getForm();

		$form->addText('depth', _('Depth'))
				->setType('number');
    }
	
	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$form['depth']->setDefaultValue($object->componentEntity->getParameter('depth'));
	}
}