<?php

namespace Wame\CategoryModule\FormCategory\Controls;

use Nette;
use Nette\Forms;

abstract class BaseControl extends Forms\Controls\BaseControl
{
	/** @var string */
	protected $items;
	
	/** @var integer */
	protected $depth;
	
	/** @var integer */
	protected $type;
	
	/**
	 * This method will be called when the component becomes attached to Form
	 *
	 * @param  Nette\ComponentModel\IComponent
	 */
	public function attached($form)
	{
		parent::attached($form);
	}
	
}