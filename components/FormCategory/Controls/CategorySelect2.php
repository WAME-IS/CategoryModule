<?php

namespace Wame\CategoryModule\FormCategory\Controls;

use Nette;
use Nette\Forms;
use Nette\Forms\Container;
use Nette\Utils;
use Nette\Utils\Html;

use Nette\Forms\Controls\BaseControl;

interface ICategorySelect2Factory extends \Wame\DynamicObject\Registers\Types\IBaseFormContainerType
{
	/** @return CategorySelect2 */
	public function create();
}

class CategorySelect2 extends BaseControl
{
//	/** @var bool */
//	private static $registered = FALSE;
    
    /** @var string */
    private $type;
    
    /** CategoryEntity[] */
    private $items;
	
    
//	public function __construct($items = [], $depth = 1, $label = NULL)
//    {
//		parent::__construct($label);
//	}
    
//    public function __construct($label) 
//    {
//		parent::__construct($label);
//
//        $this->labelName = $label;
//	}
    
    
//    /**
//     * Set repository
//     * 
//     * @param type $categoryRepository
//     * @return \Wame\CategoryModule\FormCategory\Controls\CategorySelect2
//     */
//    public function setRepository($categoryRepository)
//    {
//        $this->categoryRepository = $categoryRepository;
//        
//        return $this;
//    }
    
    public function setItems($items)
    {
        $this->items = $items;
        
        return $this;
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
    
    
	
	public function getControl()
	{
		return Html::el('select')
                ->setHtml($this->generate())
                ->addClass('category-select2');
	}
    
	public function generate()
	{
        if(!$this->items) {
            return;
        }
        
		$body = null;
        foreach($this->items as $category) {
            $body .= Html::el('option')
                    ->setValue($category->id)
                    ->setText($category->title);
        }
		return $body;
	}
	
//	public static function register($items = [], $depth = 1, $method = 'addCategorySelect2')
//	{
//		// Check for multiple registration
//		if (static::$registered) {
//			throw new Nette\InvalidStateException('Category select2 control already registered.');
//		}
//		
//		static::$registered = TRUE;
//		
//		$class = function_exists('get_called_class') ? get_called_class() : __CLASS__;
//        
//		Forms\Container::extensionMethod(
//			$method, function (Forms\Container $form, $name, $label = NULL) use ($class, $items, $depth) {
//				$component = new $class($items, $depth, $label);
//				$form->addComponent($component, $name);
//				return $component;
//			}
//		);
//	}
	
}