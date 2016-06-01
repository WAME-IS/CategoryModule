<?php

namespace Wame\CategoryModule\Forms;

use Wame\ComponentModule\Forms\ComponentForm;

class CategoryListForm extends \Wame\Core\Forms\FormFactory
{
	/** @var ComponentForm */
	private $componentForm;
	
	/** @var string */
	private $type;
	
	
	public function __construct(
			ComponentForm $componentForm
	) {
		$this->componentForm = $componentForm;
	}
	
	public function build()
	{
		$form = $this->componentForm
					->setType($this->type)
					->setId($this->id)
					->build();

		return $form;
	}
	
	/**
	 * Set component type
	 * 
	 * @param string $type
	 * @return \Wame\ComponentModule\Forms\ComponentForm
	 */
	public function setType($type)
	{
		$this->type = $type;
		
		return $this;
	}
}