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
    public function compose($template)
    {
        $template->type = "test";
    }

    /** {@inheritDoc} */
    public function configure() 
	{
        // TODO: zapracovat chameleona
        $categories = $this->categoryRepository->find(['type' => 'shopProduct', 'depth' => 2]);
        
		$this->addCheckboxList('category', _('Category'), \Wame\Utils\Arrays::getPairs($categories, 'id', 'title'));
    }
    
    private function getPairs($categories)
    {
        $pairs = [];
        
        foreach($categories as $category) {
            $pairs[$category->id] = $category->title;
        }
        
        return $pairs;
    }

}