<?php

namespace Wame\CategoryModule\FormCategory\Controls;

use Nette;
use Nette\Forms;
use Nette\Forms\Container;
use Nette\Utils;
use Nette\Utils\Html;

use Wame\CategoryModule\FormCategory\Controls\BaseControl;

class CategoryList extends BaseControl
{
	/**
	 * @var bool
	 */
	private static $registered = FALSE;
	
	public function __construct($items = [], $depth = 1, $label = NULL) {
		parent::__construct($label);
		
//		$this->items = $items;
		$this->items = [12 => 'kava'];// $items;
		$this->depth = $depth;
		
		// TODO: remove dump
//		dump($this);
//		exit();
	}
	
	public function getControl()
	{
		$rows = null;
		
		foreach($this->items as $id => $name) {
			$row = null;
			
			$row .= Html::el('input', ['value' => $id, 'type' => 'checkbox']);
			$row .= Html::el('span')->setText($name);
			
			$rows .= Html::el('li')->setHtml($row);
		}
		
		return Html::el('ul', ['class' => 'list-unstyled'])->setHtml($rows);
	}
	
//	public function setValue($value)
//	{
//		dump($value);
//		exit();
//	}
	
	public static function register($items = [], $depth = 1, $method = 'addCategoryPicker')
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
	

//	/**
//	 * Adds addRangeSlider() method to \Nette\Forms\Form
//	 */
//	public static function register()
//	{
//		Container::extensionMethod('addCategoryPicker', callback(__CLASS__, 'addCategoryPicker'));
//	}
//	/**
//	 * @param Container $container
//	 * @param string $name
//	 * @param null|string $label
//	 * @param Range $range
//	 * @return RangeSlider provides fluent interface
//	 */
//	public static function addCategoryPicker(Container $container, $name, $label = NULL, $items, $depth)
//	{
//		$container[$name] = new self($label, $items, $depth);
//		return $container[$name];
//	}
}