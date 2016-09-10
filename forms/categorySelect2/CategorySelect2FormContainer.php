<?php

namespace Wame\CategoryModule\Forms;

use Wame\CategoryModule\FormCategory\Controls\CategorySelect2;
use Wame\DynamicObject\Forms\BaseFormContainer;
use Wame\DynamicObject\Registers\Types\IBaseFormContainerType;

interface ICategorySelect2FormContainerFactory extends IBaseFormContainerType
{

    /** @return CategorySelect2FormContainer */
    public function create();
}

class CategorySelect2FormContainer extends BaseFormContainer
{

    public function __construct()
    {
        parent::__construct();
    }

    public function configure()
    {
        $form = $this->getForm();


        $select = new CategorySelect2(_('Categories'));
        $select->setAttribute('class', 'category-select2');
        $form->addComponent($select, 'categories');
    }
//	public function setDefaultValues($object)
//	{
//		$form = $this->getForm();
//		
//		$itemCategories = $this->categoryItemRepository->getCategories($this->type, $this->id);//->find(['item_id' => $object->id]);
//		
//		$pairs = [];
//		
//		foreach($itemCategories as $itemCategory) {
//			$pairs[$itemCategory->id] = $itemCategory->title;
//		}
//		
//		$form["categories"]->setDefaultValue(implode(',', array_keys($pairs)));
//	}
}
