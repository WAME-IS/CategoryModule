<?php

namespace App\AdminModule\Presenters;

use Nette\Application\UI\Form;
//use Wame\CategoryModule\Forms\CategoryForm;
use Wame\CategoryModule\Vendor\Wame\AdminModule\Forms\CreateCategoryForm;
use Wame\CategoryModule\Vendor\Wame\AdminModule\Forms\EditCategoryForm;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryLangRepository;
use Wame\DataGridControl\DataGridControl;
use Wame\CategoryModule\Vendor\Wame\AdminModule\Grids\CategoryGrid;

class CategoryPresenter extends \App\AdminModule\Presenters\BasePresenter
{	
	/** @var CreateCategoryForm @inject */
	public $createCategoryForm;
	
	/** @var EditCategoryForm @inject */
	public $editCategoryForm;
	
//	/** @var CategoryForm @inject */
//	public $categoryForm;

	/** @var CategoryRepository @inject */
	public $categoryRepository;

	/** @var CategoryLangRepository @inject */
	public $categoryLangRepository;
	
	/** @var DataGridControl @inject */
	public $gridControl;
	
	/** @var CategoryGrid @inject */
	public $categoryGrid;
	
	private $category;

//	protected function createComponentCategoryForm()
//	{
//		$form = $this->categoryForm->create();
//		$form->setRenderer(new \Tomaj\Form\Renderer\BootstrapVerticalRenderer);
//		
//		if ($this->id) {
//			$category = $this->categoryRepository->find($this->id);
//
//			$form['title']->setDefaultValue($category->lang->title);
//			$form['slug']->setDefaultValue($category->lang->slug);
//			
//			$parent = $this->categoryRepository->getParent($category);
//			
//			if($parent) {
//				$form['parent']->setDefaultValue($parent->id);
//			}
//		}
//		
//		$form->onSuccess[] = [$this, 'formSucceeded'];
//		
//		return $form;
//	}
	
	protected function createComponentCreateCategoryForm() 
	{
		$form = $this->createCategoryForm->build();
		
		return $form;
	}
	
	protected function createComponentEditCategoryForm() 
	{
		$form = $this->editCategoryForm->setId($this->id)->build();
		
		return $form;
	}
	
	public function createComponentCategoryGrid()
	{
		$grid = $this->gridControl;

		$categories = $this->categoryRepository->find(['status NOT IN (?)' => [CategoryRepository::STATUS_REMOVE]]);
		
//		$dummy = [
//			[
//				'id' => 1,
//				'children' => ['id' => 2]
//			],
//			[
//				'id' => 1,
//				'children' => ['id' => 2]
//			]
//		];
		
//		$grid->setDataSource($dummy);
		$grid->setDataSource($categories);
		
		$grid->setProvider($this->categoryGrid);
		
		return $grid;
	}
	
	/**
	 * Render list
	 */
	public function renderDefault()
	{
		$this->template->siteTitle = _('Categories');
		$this->template->categories = $this->categoryRepository->find(['status NOT IN (?)' => [CategoryRepository::STATUS_REMOVE]]);
	}
	
	/**
	 * Create
	 * 
	 * Render page for create
	 */
	public function renderCreate()
	{
		$categories = $this->categoryRepository->find();
		
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
		$this->category = $this->categoryRepository->find(['id' => $this->id]);
		
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
		$this->category = $this->categoryRepository->get(['id' => $this->id]);
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
		$this->categoryRepository->delete($this->category->id);
		
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