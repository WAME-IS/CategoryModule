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
	private $type;
	
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
	
	
    public function render() 
	{
        $this->template->_form = $this->getForm();
        $this->template->render(__DIR__ . '/default.latte');
    }

    public function configure() 
	{
		$form = $this->getForm();
		
		$form->addGroup(_('Category'));
		
		$form->addCategoryPicker('categories', _('Categories'))
				->setRepository($this->categoryRepository);
    }
	
	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$itemCategories = $this->categoryItemRepository->getCategories($this->type, $this->id);//->find(['item_id' => $object->id]);
		
		$pairs = [];
		
		foreach($itemCategories as $itemCategory) {
			$pairs[$itemCategory->id] = $itemCategory->langs['sk']->title;
		}
		
		$form["categories"]->setDefaultValue($pairs);
	}
	
}