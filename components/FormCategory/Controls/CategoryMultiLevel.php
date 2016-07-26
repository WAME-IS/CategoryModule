<?php

namespace Wame\CategoryModule\FormCategory\Controls;

use Nette;
use Nette\Forms;
use Nette\Utils\Html;

use Wame\CategoryModule\FormCategory\Controls\BaseControl;
use Wame\CategoryModule\Repositories\CategoryRepository;

class CategoryMultiLevel extends Forms\Controls\HiddenField
{	
	/** @var CategoryRepository */
	private $categoryRepository;
	
	/** @var bool */
	private static $registered = FALSE;
	
	
	public function __construct($label = NULL, $items = [], $type = null, $depth = 1)
    {
		parent::__construct($label);
		
		$this->type = $type;
	}
	
    
	/**
	 * Set type
	 * 
	 * @param string $type	type
	 */
	public function setType($type)
	{
		$this->type = $type;
		
		return $this;
	}
	
	public function setDepth($depth)
	{
		$this->depth = $depth;
		
		return $this;
	}
	
	public function setRepository($categoryRepository)
	{
		$this->categoryRepository = $categoryRepository;
		
		return $this;
	}
	
	public function setItems($items)
	{
		$this->items = $items;
		
		return $this;
	}
	
	public function getControl()
	{
        $control = parent::getControl();
        
        $control->addAttributes([
            'type' => 'hidden',
            'name' => $this->getHtmlName()
        ]);
        
		$tree = Html::el('div', ['id' => "menu", 'data-url' => "/api/v1/category/?type=" . $this->type]);
        
        return $tree . $control;
	}
	
	public static function register($items = [], $type = null, $depth = 1, $method = 'addCategoryMultiLevel')
	{
		// Check for multiple registration
		if (static::$registered) {
			throw new Nette\InvalidStateException('Category picker control already registered.');
		}
		
		static::$registered = TRUE;
		
		$class = function_exists('get_called_class')?get_called_class():__CLASS__;
		Forms\Container::extensionMethod(
			$method, function (Forms\Container $form, $name, $label = NULL) use ($class, $items, $type, $depth) {
				$component = new $class($label, $items, $type, $depth);
				$form->addComponent($component, $name);
				return $component;
			}
		);
	}
	
}