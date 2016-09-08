<?php

namespace Wame\CategoryModule\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryItemRepository;
use Wame\CategoryModule\FormCategory\Controls\ICategorySelect2Factory;

interface ICategorySelect2FormContainerFactory extends \Wame\DynamicObject\Registers\Types\IBaseFormContainerType
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


        $select = new \Wame\CategoryModule\FormCategory\Controls\CategorySelect2(_('Categories'));
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
