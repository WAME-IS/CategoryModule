<?php

namespace Wame\CategoryModule\Forms;

use Wame\DynamicObject\Forms\BaseFormBuilder;
use Wame\DynamicObject\Forms\BaseForm;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Components\CategoryFilterControl;

class CategoryFilterFormBuilder extends BaseFormBuilder
{
    /** @var CategoryRepository */
    protected $categoryRepository;
    
    
    public function __construct(CategoryRepository $categoryRepository)
    {
        parent::__construct();
        
        $this->categoryRepository = $categoryRepository;
    }
    
    
    /** {@inheritDoc} */
    public function submit(BaseForm $form, array $values)
    {
        $categoryFilterControl = $form->lookup(CategoryFilterControl::class);
        $categories = $this->categoryRepository->find(['id IN' => $values['CategoryContainer']['category']]);
        $categoryIds = implode(',', array_keys(\Wame\Utils\Arrays::getPairs($categories, 'id', 'id')));
//        $categorySlugs = \Wame\Utils\Arrays::getPairs($categories, 'id', 'slug');
//        $categoryFilterControl->setCategories(implode(",", $categorySlugs));
        $categoryFilterControl->setCategories($categoryIds);
    }
    
}
