<?php

namespace Wame\CategoryModule\Vendor\Wame\AdminModule\Forms;

use Wame\Core\Forms\FormFactory;

class CreateCategoryForm extends FormFactory
{	
	public function create()
	{
		$form = $this->createForm();
		
		$form->addSubmit('submit', _('Create category'));

		return $form;
	}

}
