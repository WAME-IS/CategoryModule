<?php

namespace Wame\CategoryModule\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryItemRepository;


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


	public function __construct(CategoryRepository $categoryRepository, CategoryItemRepository $categoryItemRepository, \Wame\Utils\HttpRequest $httpRequest) 
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
		
		$form["categories"]->setDefaultValue(implode(',', array_keys($pairs)));
	}

}