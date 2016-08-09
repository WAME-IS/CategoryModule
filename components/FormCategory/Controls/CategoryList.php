<?php

namespace Wame\CategoryModule\FormCategory\Controls;

use Nette;
use Nette\Forms;
use Nette\Utils\Html;

use Wame\CategoryModule\FormCategory\Controls\BaseControl;
use Wame\CategoryModule\Repositories\CategoryRepository;

class CategoryList extends BaseControl
{
	/** @var CategoryRepository */
	private $categoryRepository;
	
	/** @var bool */
	private static $registered = FALSE;
	
//	protected $value = [];
	
	
	public function __construct($label = NULL, $items = [], $type = null, $depth = 1) {
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
        
        $control
            ->addAttributes([
                'type' => 'hidden',
                'name' => $this->getHtmlName()
            ])
            ->setValue($this->getValue());
        
		$tree = Html::el('div', ['id' => "tree1", 'data-url' => "/api/v1/category/?type=" . $this->type]);
		return $tree . $control;
		
//		$items = $this->categoryRepository->getTree(['status' => 1, 'type' => $this->type]);
//		return $this->generate($items, true);
	}
	
//	public function generate($category, $isRoot = false)
//	{
//		if($category) {
//			$ul = Html::el('ul');
//			
//			if($isRoot) {
//				$ul->addAttributes(['class' => 'tree']);
//			}
//			
//			$li = Html::el('li');
//				$body = null;
//				$body .= Html::el('input', ['name' => 'categories[]', 'value' => $category->item->id])
//							->type('checkbox')->addAttributes(['checked' => isset($this->value[$category->item->id])]);
//				$body .= Html::el('span')->setText($category->item->langs[$this->categoryRepository->lang]->title);
//
//				if(sizeof($category->child_nodes) > 0) {
//					foreach($category->child_nodes as $child) {
//						if($child->status == 1) $body .= $this->generate($child);
//					}
//				}
//			$li->setHtml($body);
//			$ul->setHtml($li);
//			
//			return $ul;
//		} else {
//			return Html::el('div')->setText(_('Category doesnt exists'));
//		}
//	}
	
	public static function register($items = [], $type = null, $depth = 1, $method = 'addCategoryPicker')
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