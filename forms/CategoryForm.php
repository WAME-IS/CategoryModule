<?php

namespace Wame\CategoryModule\Forms;

use Nette\Application\UI\Form;
use Nette\Object;
use Wame\Core\Forms\FormFactory;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryLangRepository;

class CategoryForm extends Object
{	
	/** @var FormFactory */
	private $formFactory;
	
	/** @var CategoryRepository */
	public $categoryRepository;
	
	/** @var CategoryLangRepository */
	public $categoryLangRepository;
	
	public function __construct(
		FormFactory $formFactory,
		CategoryRepository $categoryRepository,
		CategoryLangRepository $categoryLangRepository
	) {
		$this->formFactory = $formFactory;
		$this->categoryRepository = $categoryRepository;
		$this->categoryLangRepository = $categoryLangRepository;
	}

	public function create()
	{
		$form = $this->formFactory->createForm();
		
		$form->addGroup(_('Basic info'));
		
		$form->addText('title', _('Title'))
				->addCondition(Form::MIN_LENGTH, 3)
				->addCondition(Form::MAX_LENGTH, 250)
				->setRequired(_('Please enter title'));

		$form->addText('slug', _('URL'))
				->addCondition(Form::MIN_LENGTH, 3)
				->addCondition(Form::MAX_LENGTH, 250)
				->setRequired(_('Please enter url'));
		
		$criteria = [
			'lang' => 'sk'
		];
		
		$cat = $this->categoryRepository->getTree();
		
//		dump(gettype ($cat));
//		exit;
		
		$categories = $this->categoryLangRepository->getPairs($criteria, 'title', [], 'category_id');
		
		$form->addSelect('parent', _('Parent'), $categories)
				->setPrompt(_('-Top rank-'));
		
		$form->addCategoryPicker('categories', _('Categories'))
				->setRepository($this->categoryRepository);
		
//				->setItems($cat);
		
//		$form->addCategorySelect('categories2', _('Categories2'))->setItems($cat);

		$form->addSubmit('submit', _('Submit'));
		
		return $form;
	}

}
