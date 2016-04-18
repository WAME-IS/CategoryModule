<?php

namespace Wame\CategoryModule\FormCategory\Controls;

use Nette;
use Nette\Forms;
use Nette\Forms\Container;
use Nette\Utils;
use Nette\Utils\Html;

use Wame\CategoryModule\FormCategory\Controls\BaseControl;

class CategorySelect extends BaseControl
{
	/**
	 * @var bool
	 */
	private static $registered = FALSE;
	
	public function __construct($items = [], $depth = 1, $label = NULL) {
		parent::__construct($label);
	}
	
	public function setItems($items)
	{
		$this->items = $items;
	}
	
	public function getControl()
	{
		return Html::el('select')->setHtml($this->generate($this->items));
	}
	
	public function generate($category)
	{
		$body = null;

		$body .= Html::el('option', ['value' => $category->item->id])->setHtml(str_repeat('-', $category->item->depth) . $category->item->title);

		if(sizeof($category->child_nodes) > 0) {
			foreach($category->child_nodes as $child) {
				$body .= $this->generate($child);
			}
		}

		return $body;
	}
	
	public static function register($items = [], $depth = 1, $method = 'addCategorySelect')
	{
		// Check for multiple registration
		if (static::$registered) {
			throw new Nette\InvalidStateException('Category picker control already registered.');
		}
		
		static::$registered = TRUE;
		
		$class = function_exists('get_called_class')?get_called_class():__CLASS__;
		Forms\Container::extensionMethod(
			$method, function (Forms\Container $form, $name, $label = NULL) use ($class, $items, $depth) {
				$component = new $class($items, $depth, $label);
				$form->addComponent($component, $name);
				return $component;
			}
		);
	}
	
}