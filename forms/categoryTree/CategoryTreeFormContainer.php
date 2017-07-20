<?php

namespace Wame\CategoryModule\Forms;

use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\DynamicObject\Forms\BaseFormContainer;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryItemRepository;
use Wame\Utils\HttpRequest;


interface ICategoryTreeFormContainerFactory
{
	/** @return CategoryTreeFormContainer */
	public function create();
}


class CategoryTreeFormContainer extends BaseFormContainer
{
	/** CategoryRepository */
	protected $categoryRepository;
	
	/** @var CategoryItemRepository */
	protected $categoryItemRepository;
	
	/** @var string */
	protected $type;
	
	/** @var integer */
	private $id;


	public function __construct(CategoryRepository $categoryRepository, CategoryItemRepository $categoryItemRepository, HttpRequest $httpRequest)
	{
		parent::__construct();
		
		$this->type = $httpRequest->getParameter('type');
		$this->id = $httpRequest->getParameter('id');
		
		$this->categoryRepository = $categoryRepository;
		$this->categoryItemRepository = $categoryItemRepository;
	}
    
    
    public function configure() 
	{
		$form = $this->getForm();
		
		$form->addGroup(_('Category'));
		
		$form->addCategoryPicker('categories', _('Categories'))
				->setRepository($this->categoryRepository)
				->setType($this->type)
                ->setRequired();
    }

    
	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$itemCategories = $this->categoryItemRepository->findItems($this->type, $this->id);
		
		$pairs = [];
		
		foreach($itemCategories as $itemCategory) {
			$pairs[$itemCategory->id] = $itemCategory->title;
		}

        $pairs = $this->getCategoriesFromComponent($object, $pairs);

		$form["categories"]->setDefaultValue(implode(',', array_keys($pairs)));
	}


	private function getCategoriesFromComponent($object, $pairs)
    {
        if (isset($object->componentEntity) && $object->componentEntity instanceof ComponentEntity) {
            $categories = $object->componentEntity->getParameter('categories');

            if ($categories && is_array($categories)) {
                foreach ($categories as $category) {
                    $pairs[$category] = $category;
                }
            }
        }

        return $pairs;
    }

}
