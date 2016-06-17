<?php

namespace App\AdminModule\Presenters;

use Nette\Application\UI\Form;
use Nette\Http\Request;

use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryItemRepository;
use Wame\CategoryModule\Repositories\CategoryLangRepository;
use Wame\CategoryModule\Vendor\Wame\AdminModule\Forms\CreateCategoryForm;
use Wame\CategoryModule\Vendor\Wame\AdminModule\Forms\EditCategoryForm;
use Wame\CategoryModule\Vendor\Wame\AdminModule\Grids\CategoryGrid;
use Wame\DataGridControl\DataGridControl;
use Wame\MenuModule\Forms\MenuItemForm;

class CategoryPresenter extends \App\AdminModule\Presenters\BasePresenter
{	
	/** @var Request @inject */
	public $request;
	
	/** @var CreateCategoryForm @inject */
	public $createCategoryForm;
	
	/** @var EditCategoryForm @inject */
	public $editCategoryForm;

	/** @var CategoryRepository @inject */
	public $categoryRepository;
	
	/** @var CategoryItemRepository @inject */
	public $categoryItemRepository;

	/** @var CategoryLangRepository @inject */
	public $categoryLangRepository;
	
	/** @var DataGridControl @inject */
	public $gridControl;
	
	/** @var CategoryGrid @inject */
	public $categoryGrid;
	
	/** @var MenuItemForm @inject */
	public $menuItemForm;
	
	/** @var CategoryEntity */
	private $category;
	
	/** @var array */
	private $categories;
	
	/** @var string */
	private $type;

	
	/** actions ***************************************************************/
	
	public function actionDefault()
	{
		$this->type = $this->id;
		$this->categories = $this->categoryRepository->find(['type' => $this->type, 'status NOT IN (?)' => [CategoryRepository::STATUS_REMOVE]]);
	}
	
	public function actionShow()
	{
		$this->category = $this->categoryRepository->get(['id' => $this->id]);
		
		if($this->category->status == CategoryRepository::STATUS_REMOVE) {
			$this->flashMessage(_('Category is removed'), 'danger');
			$this->redirect(':Admin:Category:', ['id' => null]);
		}
	}
	
	public function actionDelete()
	{
		$this->category = $this->categoryRepository->get(['id' => $this->id]);
	}
	
	
	/** handles ***************************************************************/
	
	public function handleDelete()
	{
		$this->categoryRepository->delete($this->category->id);
		
		$this->redirectToDefault();
	}
	
	public function handleBack()
	{
		// TODO: lepsie by bolo redirect back, backurl musi mat BasePresenter
		$this->redirectToDefault();
	}
	
	
	/** renders ***************************************************************/
	
	/**
	 * Render list
	 */
	public function renderDefault()
	{
		$this->template->type = $this->type;
		$this->template->siteTitle = _('Categories');
		$this->template->categories = $this->categories;
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
	 */
	public function renderEdit()
	{
		$this->template->siteTitle = _('Edit category');
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
	
	/**
	 * Render delete
	 */
	public function renderDelete()
	{
		$this->template->siteTitle = _('Delete category');
		$this->template->category = $this->category;
	}
	
	/**
	 * Render menu item
	 */
	public function renderMenuItem()
	{
		if ($this->id) {
			$this->template->siteTitle = _('Edit category item in menu');
		} else {
			$this->template->siteTitle = _('Add category item to menu');
		}
	}
	
	
	/** callbacks *************************************************************/
	
	/**
	 * Get children
	 * 
	 * @return type
	 */
	public function getChildren()
	{
		return [];
	}
	
	
	/** components ************************************************************/
	
	protected function createComponentCreateCategoryForm() 
	{
		$form = $this->createCategoryForm
				->setActionForm($this->request->getUrl()->setQueryParameter('do', 'createCategoryForm-submit'))
				->build();
		
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
		$grid->setDataSource($this->categories);
		$grid->setProvider($this->categoryGrid);
//		$grid->setTreeView([$this, 'getChildren'], 'id'); // TODO: pridat ked sa poriesi @noApi
		
		return $grid;
	}
	
	/**
	 * Menu item form
	 * 
	 * @return MenuItemForm
	 */
	protected function createComponentCategoryMenuItemForm()
	{
		$form = $this->menuItemForm
						->setActionForm('categoryMenuItemForm')
						->setType('category')
						->setId($this->id)
						->addFormContainer(new \Wame\CategoryModule\Vendor\Wame\MenuModule\Components\MenuManager\Forms\CategoryFormContainer(), 'CategoryFormContainer', 50)
						->build();

		return $form;
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