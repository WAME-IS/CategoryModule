<?php

namespace Wame\CategoryModule\Vendor\Wame\MenuModule\Components\MenuManager\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;

interface ICategoryFormContainerFactory
{
	/** @return CategoryFormContainer */
	public function create();
}


class CategoryFormContainer extends BaseFormContainer
{
    public function render() 
	{
        $this->template->_form = $this->getForm();
        $this->template->render(__DIR__ . '/default.latte');
    }

	
    public function configure() 
	{
		$form = $this->getForm();

		$form->addAutocomplete('value', _('Category'), '/api/v1/category-search', [
			'columns' => ['langs.title'],
			'select' => 'a.id, langs.title'
		]);
		
		$form->addText('alternative_title', _('Alternative title'));
    }
	
	
	public function setDefaultValues($object)
	{
		$form = $this->getForm();

		$form['value']->setDefaultValue($object->menuEntity->value);
		$form['alternative_title']->setDefaultValue($object->menuEntity->langs[$object->lang]->alternativeTitle);
	}

}