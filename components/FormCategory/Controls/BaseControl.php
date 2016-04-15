<?php

namespace Wame\CategoryModule\FormCategory\Controls;

use Nette;
use Nette\Application\UI;
use Nette\Bridges;
use Nette\Forms;
use Nette\Localization;
use Nette\Utils;

use Latte;

use Wame\CategoryModule\Repositories\CategoryRepository;

abstract class BaseControl extends Forms\Controls\BaseControl
{
	/**
	 * @var string
	 */
	protected $items;
	
	/**
	 *
	 * @var integer
	 */
	protected $depth;
	
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