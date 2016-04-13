<?php

namespace Wame\CategoryModule\Forms;

use Nette\Object;
use Wame\Core\Forms\FormFactory;

class CategoryForm extends Object
{	
	/** @var FormFactory */
	private $formFactory;
	
	public function __construct(
		FormFactory $formFactory
	) {
		$this->formFactory = $formFactory;
	}

	public function create()
	{
		$form = $this->formFactory->createForm();
		
		$form->addGroup(_('Basic info'));
		
		$form->addText('title', _('Title'))
				->setRequired(_('Please enter title'));

		$form->addText('slug', _('URL'))
				->setRequired(_('Please enter url'));
		
		$form->addSelect('parent', _('Parent'));

		$form->addSubmit('submit', _('Submit'));
		
		return $form;
	}

}
