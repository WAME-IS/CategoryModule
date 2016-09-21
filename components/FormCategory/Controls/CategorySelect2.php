<?php

namespace Wame\CategoryModule\FormCategory\Controls;

use Nette\Utils\Html;
use Nette\Forms\Controls\BaseControl;

interface ICategorySelect2Factory
{
	/** @return CategorySelect2 */
	public function create();
}

class CategorySelect2 extends BaseControl
{
    /** @var string */
    private $type;
    
    /** CategoryEntity[] */
    private $items;
    
    
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
	
}