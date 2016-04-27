<?php

namespace Wame\CategoryModule\Forms;

use Nette\Application\UI\Form;
use Wame\DynamicObject\Forms\BaseFormContainer;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryLangRepository;

interface IParentFormContainerFactory
{
	/** @return ParentFormContainer */
	public function create();
}

class ParentFormContainer extends BaseFormContainer
{
	/** @var CategoryRepository */
	protected $categoryRepository;
	
	/** @var CategoryLangRepository */
	protected $categoryLangRepository;
	
	public function __construct(CategoryRepository $categoryRepository, CategoryLangRepository $categoryLangRepository) 
	{
		parent::__construct();
		
		$this->categoryRepository = $categoryRepository;
		$this->categoryLangRepository = $categoryLangRepository;
	}
	
    public function render() 
	{
        $this->template->_form = $this->getForm();
        $this->template->render(__DIR__ . '/default.latte');
    }

    public function configure() 
	{
		$form = $this->getForm();

		$criteria = [
			'lang' => 'sk'
		];
		
		$categories = $this->categoryLangRepository->getPairs($criteria, 'title', [], 'category_id');
		
		$form->addSelect('parent', _('Parent'), $categories)
				->setPrompt(_('-Top rank-'));
		
//		$form->addSelect('parent', _('Parent'), []);
		
//		$form->addText('slug', _('URL'))
//				->setType('text');
    }
	
}