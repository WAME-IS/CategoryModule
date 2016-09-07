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
	/** CategoryRepository */
	protected $categoryRepository;
	
	/** @var CategoryItemRepository */
	protected $categoryItemRepository;
	
	/** @var string */
	protected $type;
	
	/** @var integer */
	private $id;
    
    /** @var ICategorySelect2Factory */
    private $ICategorySelect2Factory;


	public function __construct(ICategorySelect2Factory $ICategorySelect2Factory, CategoryRepository $categoryRepository, CategoryItemRepository $categoryItemRepository, \Wame\Utils\HttpRequest $httpRequest) 
	{
		parent::__construct();
        
        $this->ICategorySelect2Factory = $ICategorySelect2Factory;
		
		$this->type = $httpRequest->getParameter('type');
		$this->id = $httpRequest->getParameter('id');
		
	}
    
    
    public function configure() 
	{
		$form = $this->getForm();
        
        
        
        $form->addMultiSelect('categories', _('Categories'), $this->getPairs())
            ->setAttribute('class', 'category-select2');
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
    
    
    /**
     * Get pairs
     * 
     * @return string
     */
    private function getPairs()
    {
        $categories = $this->categoryItemRepository->getCategories('shopProduct');
        
        $categoryPairs = [];
        
        foreach($categories as $category) {
            $categoryPairs[$category->id] = $category->title;
        }
        
        return $categoryPairs;
    }

}