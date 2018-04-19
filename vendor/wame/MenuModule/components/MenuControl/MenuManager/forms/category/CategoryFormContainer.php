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
    public function configure() 
	{
		$form = $this->getForm();
//
//		$form->addAutocomplete('value', _('Category'), '/api/v1/category-search', [
//			'columns' => ['langs.title'],
//			'select' => 'a.id, langs.title'
//		]);

        $form->addAutocomplete('value', _('Category'))
                ->setAttribute('placeholder', _('Begin typing the article title'))
                ->setSource('/api/v1/category-search')
                ->setColumns(['langs.title'])
                ->setSelect('a.id, langs.title')
                ->setRequired(_('You must select category'));
		
		$form->addText('alternative_title', _('Alternative title'));
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();

		$form['value']->setDefaultValue($object->menuEntity->value);
		$form['alternative_title']->setDefaultValue($object->menuEntity->langs[$object->lang]->alternativeTitle);
	}

}
