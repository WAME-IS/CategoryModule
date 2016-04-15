<?php

namespace App\AdminModule\Presenters;

use Nette\Application\UI\Form;
use Wame\CategoryModule\Forms\CategoryForm;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryLangRepository;
use Wame\CategoryModule\Entities\CategoryEntity;

class CategoryPresenter extends \App\AdminModule\Presenters\BasePresenter
{	
	/** @var CategoryForm @inject */
	public $categoryForm;

	/** @var CategoryRepository @inject */
	public $categoryRepository;

	/** @var CategoryLangRepository @inject */
	public $categoryLangRepository;

	/** @var CategoryEntity */
	private $categoryEntity;
	
	private $category;

	public function startup() 
	{
		parent::startup();
		
		$this->categoryEntity = $this->entityManager->getRepository(CategoryEntity::class);
		
		$this->categoryRepository->get(['id' => 2]);
	}
	
	protected function createComponentCategoryForm()
	{
		$form = $this->categoryForm->create();
		$form->setRenderer(new \Tomaj\Form\Renderer\BootstrapVerticalRenderer);
		
		if ($this->id) {
			$defaults = $this->categoryEntity->findOneBy(['id' => $this->id]);

			$form['title']->setDefaultValue($defaults->lang->title);
			$form['slug']->setDefaultValue($defaults->lang->slug);
			
			if($defaults->parent) {
				$form['parent']->setDefaultValue($defaults->parent->id);
			}
		}
		
		$form->onSuccess[] = [$this, 'categoryFormSucceeded'];
		
		return $form;
	}
	
	public function categoryFormSucceeded(Form $form, $values)
	{
		if ($this->id) {
			$this->categoryRepository->edit($this->id, $values);

			$this->flashMessage(_('The category was successfully update'), 'success');
		} else {
			$category = $this->categoryRepository->add($values);

			$this->flashMessage(_('The category was created successfully'), 'success');
		}
		
		$this->redirect('this');
	}
	
	public function renderDefault()
	{
		$this->template->siteTitle = _('Categories');
		
		$categories = $this->categoryRepository->getAll();
		
		$this->template->categories = $categories;
	}
	
	/**
	 * Create
	 * 
	 * Render page for create
	 */
	public function renderCreate()
	{
		$categories = $this->categoryRepository->getAll();
		
		$this->template->setFile(__DIR__ . '/templates/Category/edit.latte');
		$this->template->siteTitle = _('Create category');
		$this->template->categories = $categories;
	}
	
	public function renderEdit($id)
	{
		$this->template->siteTitle = _('Edit category');
	}
	
	public function actionShow()
	{
		$this->category = $this->categoryRepository->get(['id' => $this->id]);
		
		if($this->category->status == CategoryRepository::STATUS_REMOVE) {
			$this->flashMessage(_('Category is removed'), 'danger');
			$this->redirect(':Admin:Category:', ['id' => null]);
		}
	}
	
	public function renderShow()
	{
		$this->template->category = $this->category;
		
		
		$this->template->siteTitle = _($this->category->lang->title);
	}
	
	public function actionDelete()
	{
		$this->category = $this->categoryRepository->get(['id' => $this->id]);
	}
	
	public function renderDelete()
	{
		$this->template->siteTitle = _('Delete category');
		
		$this->template->category = $this->category;
	}
	
	public function handleDelete()
	{
		$this->categoryRepository->remove($this->category->id);
		
		$this->redirectToDefault();
	}
	
	public function handleBack()
	{
		// TODO: lepsie by bolo redirect back, backurl musi mat BasePrezenter
		$this->redirectToDefault();
	}
	
	/**
	 * Redirect to list
	 */
	private function redirectToDefault()
	{
		$this->redirect(':Admin:Category:default', ['id' => null]);
	}
}

/**
 * TODO:
 * 
 * forward pre presmerovanie spat
 * - https://doc.nette.org/en/2.3/presenters#toc-redirection
 */