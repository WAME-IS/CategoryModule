<?php

namespace Wame\CategoryModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\CategoryModule\Repositories\CategoryRepository;

interface ICategoryContainerFactory extends IBaseContainer
{
	/** @return CategoryContainer */
	public function create();
}

class CategoryContainer extends BaseContainer
{
    /** @var CategoryRepository */
    private $categoryRepository;
    
    
    public function __construct(CategoryRepository $categoryRepository)
    {
        parent::__construct();
        $this->categoryRepository = $categoryRepository;
    }
    
    
    /** {@inheritDoc} */
    public function configure() 
	{
        $categories = $this->categoryRepository->find();
        
		$this->addCheckboxList('category', _('Category'), $this->getPairs($categories));
    }

//    /** {@inheritDoc} */
//	public function setDefaultValues($entity, $langEntity = null)
//	{
//        $this['category']->setDefaultValue($langEntity ? $langEntity->getTitle() : $entity->getTitle());
//	}
//
//    /** {@inheritDoc} */
//    public function create($form, $values)
//    {
//        $entity = method_exists($form, 'getLangEntity') ? $form->getLangEntity(): $form->getEntity();
//        $entity->setTitle($values['category']);
//    }
//
//    /** {@inheritDoc} */
//    public function update($form, $values)
//    {
//        $entity = method_exists($form, 'getLangEntity') ? $form->getLangEntity(): $form->getEntity();
//        $entity->setTitle($values['category']);
//    }
    
    private function getPairs($categories)
    {
        $pairs = [];
        
        foreach($categories as $category) {
            $pairs[$category->id] = $category->title;
        }
        
        return $pairs;
    }

}