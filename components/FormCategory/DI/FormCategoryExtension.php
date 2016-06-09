<?php

namespace Wame\CategoryModule\FormCategory\DI;

use Nette;
use Nette\DI;
use Nette\PhpGenerator as Code;

class FormCategoryExtension extends DI\CompilerExtension
{
	/**
	 * @param Code\ClassType $class
	 */
	public function afterCompile(Code\ClassType $class)
	{
		parent::afterCompile($class);
		$initialize = $class->methods['initialize'];
		$initialize->addBody('Wame\CategoryModule\FormCategory\Controls\CategoryList::register();');
		$initialize->addBody('Wame\CategoryModule\FormCategory\Controls\CategorySelect::register();');
		$initialize->addBody('Wame\CategoryModule\FormCategory\Controls\CategoryMultiLevel::register();');
	}
}