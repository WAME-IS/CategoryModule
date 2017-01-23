<?php

namespace App\AdminModule\Presenters;

use Kdyby\GeneratedProxy\__CG__\Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Vendor\Wame\AdminModule\Grids\CategoryGrid;
use Wame\MenuModule\Forms\MenuItemForm;
use Wame\DynamicObject\Vendor\Wame\AdminModule\Presenters\AdminFormPresenter;

class CategoryPresenter extends AdminFormPresenter
{
	/** @var CategoryRepository @inject */
	public $repository;

	/** @var CategoryGrid @inject */
	public $categoryGrid;

	/** @var MenuItemForm @inject */
	public $menuItemForm;

	/** @var string */
	private $type;

    /** @var CategoryEntity */
    private $parent;


	/** actions ***************************************************************/

	public function actionDefault()
	{
		$this->type = $this->id;

        if($this->type) {
            $this->count = $this->repository->countBy(['type' => $this->type, 'depth >' => 1]);
        } else {
            $this->redirect(':Admin:Dashboard:');
        }
	}

	public function actionShow()
	{
		$this->entity = $this->repository->get(['id' => $this->id]);

		if($this->entity->status == CategoryRepository::STATUS_REMOVE) {
			$this->flashMessage(_('Category is removed'), 'danger');
			$this->redirect(':Admin:Category:', ['id' => null]);
		}

        $this->parent = $this->repository->getParent($this->entity);
	}

    public function actionEdit()
	{
		$this->entity = $this->repository->get(['id' => $this->id]);
	}

	public function actionDelete()
	{
		$this->entity = $this->repository->get(['id' => $this->id]);
	}


	/** handles ***************************************************************/

	public function handleDelete()
	{
		$this->repository->delete($this->entity->id);

		$this->redirectToDefault();
	}

	public function handleBack()
	{
		// TODO: lepsie by bolo redirect back, backurl musi mat BasePresenter
		$this->redirectToDefault();
	}

    public function handleSort($item_id, $prev_id, $next_id)
    {
        if ($this->isAjax()) {
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }


	/** renders ***************************************************************/

	/**
	 * Render list
	 */
	public function renderDefault()
	{
		$this->template->type = $this->type;
		$this->template->siteTitle = _('Categories');
		$this->template->count = $this->count;
	}

	/**
	 * Create
	 *
	 * Render page for create
	 */
	public function renderCreate()
	{
		$this->template->siteTitle = _('Create category');
	}

	/**
	 * Render edit form
	 */
	public function renderEdit()
	{
		$this->template->siteTitle = _('Edit category');
		$this->template->subTitle = $this->entity->title;
	}

	/**
	 * Render show
	 */
	public function renderShow()
	{
		$this->template->category = $this->entity;
		$this->template->siteTitle = $this->entity->title;

		$this->template->parent = $this->parent;
	}

	/**
	 * Render delete
	 */
	public function renderDelete()
	{
		$this->template->siteTitle = _('Delete category');
		$this->template->subTitle = $this->entity->title;
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
     * @param integer $parentId    parent id
     * @return QueryBuilder
     */
	public function getChildren($parentId)
    {
        $qb = $this->repository->createQueryBuilder('a');
        $qb->andWhere($qb->expr()->eq('a.parent', ':parent'))->setParameter('parent', $parentId);

        return $qb;
    }


	/** components ************************************************************/

    /**
     * Category grid component
     *
     * @return CategoryGrid
     */
	protected function createComponentGrid()
	{
        $qb = $this->repository->createQueryBuilder('a');
        $qb->andWhere($qb->expr()->eq('a.type', ':type'))->setParameter('type', $this->type);
        $qb->andWhere($qb->expr()->eq('a.depth', ':depth'))->setParameter('depth', 2);

		$this->categoryGrid->setDataSource($qb);
		$this->categoryGrid->setTreeView([$this, 'getChildren'], 'children');

		return $this->categoryGrid;
	}

	/**
	 * Menu item form component
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


    /** implements ************************************************************/

    /** {@inheritdoc} */
    protected function getFormBuilderServiceAlias()
    {
        return "Admin.CategoryFormBuilder";
    }

//    /** {@inheritdoc} */
//    protected function getGridServiceAlias()
//    {
//        return "Admin.CategoryGrid";
//    }

}