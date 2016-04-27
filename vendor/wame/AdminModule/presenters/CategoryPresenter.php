<?php

namespace App\AdminModule\Presenters;

use Nette\Application\UI\Form;
use Wame\CategoryModule\Forms\CategoryForm;
use Wame\CategoryModule\Vendor\Wame\AdminModule\Forms\CreateCategoryForm;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryLangRepository;

class CategoryPresenter extends \App\AdminModule\Presenters\BasePresenter
{	
	/** @var CreateCategoryForm @inject */
	public $createCategoryForm;
	
	/** @var CategoryForm @inject */
	public $categoryForm;

	/** @var CategoryRepository @inject */
	public $categoryRepository;

	/** @var CategoryLangRepository @inject */
	public $categoryLangRepository;
	
	private $category;

	protected function createComponentCategoryForm()
	{
		$form = $this->categoryForm->create();
		$form->setRenderer(new \Tomaj\Form\Renderer\BootstrapVerticalRenderer);
		
		if ($this->id) {
			$category = $this->categoryRepository->find($this->id);

			$form['title']->setDefaultValue($category->lang->title);
			$form['slug']->setDefaultValue($category->lang->slug);
			
			$parent = $this->categoryRepository->getParent($category);
			
			if($parent) {
				$form['parent']->setDefaultValue($parent->id);
			}
		}
		
		$form->onSuccess[] = [$this, 'formSucceeded'];
		
		return $form;
	}
	
	protected function createComponentCreateCategoryForm() 
	{
		$form = $this->createCategoryForm->create();

		$form->onSuccess[] = [$this, 'formSucceeded'];
		
		return $form;
	}
	
	public function formSucceeded(Form $form, $values)
	{
		switch($this->action) {
			case 'edit':
				$this->categoryRepository->edit($this->id, $values);
				$this->flashMessage(_('The category was successfully updated'), 'success');
				break;
			case 'create':
				$category = $this->categoryRepository->add($values);
				// TODO: len pre testovanie
				$this->categoryRepository->onCreate($form, 'articles', $category, $values);
				$this->flashMessage(_('The category was successfully created'), 'success');
				break;
		}
		
		$this->redirect('this');
	}
	
	/**
	 * Render list
	 */
	public function renderDefault()
	{
		$this->template->siteTitle = _('Categories');
		$this->template->categories = $this->categoryRepository->getAll(['status NOT IN (?)' => [CategoryRepository::STATUS_REMOVE]]);
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
	
	/**
	 * Render edit form
	 * 
	 * @param integer $id
	 */
	public function renderEdit($id)
	{
		$this->template->siteTitle = _('Edit category');
	}
	
	public function actionShow()
	{
		$this->category = $this->categoryRepository->find($this->id);
		
		if($this->category->status == CategoryRepository::STATUS_REMOVE) {
			$this->flashMessage(_('Category is removed'), 'danger');
			$this->redirect(':Admin:Category:', ['id' => null]);
		}
	}
	
	/**
	 * Render show
	 */
	public function renderShow()
	{
		$this->template->category = $this->category;
		$this->template->siteTitle = _($this->category->langs[$this->lang]->title);
		
		$this->template->parent = $this->categoryRepository->getParent($this->category);
	}
	
	public function actionDelete()
	{
		$this->category = $this->categoryRepository->find($this->id);
	}
	
	/**
	 * Render delete
	 */
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