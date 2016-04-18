<?php

namespace Wame\CategoryModule\FormCategory\Controls;

use Nette;
use Nette\Forms;
use Nette\Forms\Container;
use Nette\Utils;
use Nette\Utils\Html;

use Wame\CategoryModule\FormCategory\Controls\BaseControl;
use Wame\CategoryModule\Repositories\CategoryRepository;

class CategoryList extends BaseControl
{
	/**
	 * @var bool
	 */
	private static $registered = FALSE;
	
	public function __construct($label = NULL, $items = [], $depth = 1) {
		parent::__construct($label);
		
	}
	
	public function setItems($items)
	{
		$this->items = $items;
	}
	
	public function getControl()
	{
		return $this->generate($this->items);
	}
	
	public function generate($category)
	{
		$ul = Html::el('ul');
		
			$li = Html::el('li');

				$body = null;

				$body .= Html::el('input', ['value' => $category->item->id, 'type' => 'checkbox']);
				$body .= Html::el('span')->setText($category->item->title);

				if(sizeof($category->child_nodes) > 0) {
					foreach($category->child_nodes as $child) {
						$body .= Html::el('li')->setHtml($this->generate($child));
					}
				}

			$li->setHtml($body);
		
		$ul->setHtml($li);
		
		return $ul;
	}
	
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
				$component = new $class($label, $items, $depth);
				$form->addComponent($component, $name);
				return $component;
			}
		);
	}
	
}