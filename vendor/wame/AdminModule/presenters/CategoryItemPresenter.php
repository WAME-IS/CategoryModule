<?php

namespace App\AdminModule\Presenters;

use Wame\DynamicObject\Vendor\Wame\AdminModule\Presenters\AdminFormPresenter;
use Wame\CategoryModule\Entities\CategoryItemEntity;
use Wame\CategoryModule\Repositories\CategoryItemRepository;
use Wame\CategoryModule\Repositories\CategoryRepository;


class CategoryItemPresenter extends AdminFormPresenter
{
    /** @var CategoryItemRepository @inject */
    public $repository;

    /** @var CategoryRepository @inject */
    public $categoryRepository;

    /** @var CategoryItemEntity */
    protected $entity;

    /** @var CategoryItemEntity[] */
    protected $entities;

    /** @var string */
    private $type;

    /** @var int */
    private $itemId;


    /** actions ***************************************************************/

    public function actionCreate()
    {
        $this->itemId = $this->getParameter('id');
        $this->type = $this->getParameter('t');
        $categoryIds = explode(',', $this->getParameter('categories'));

        $categories = $this->categoryRepository->find(['id IN' => $categoryIds]);

        $this->repository->setItemToCategory($this->type, $this->itemId, $categories);

        $this->entities = $this->repository->findByType($this->type, $this->itemId);
    }


    public function actionSetMain()
    {
        $entity = $this->repository->get(['id' => $this->id]);

        $items = $this->repository->find(['category.type' => $entity->getCategory()->getType(), 'item_id' => $entity->getItemId()]);

        foreach ($items as $item) {
            $item->setMain(false);
        }

        $entity->setMain(true);

        if ($this->isAjax()) {
            $this->redrawControl();
            $this->sendPayload();
        } else {
            $this->redirect('this');
        }
    }


    /** handles ***************************************************************/

    public function handleDelete()
    {
        $this->repository->delete(['id' => $this->id]);

        $this->flashMessage(_('Report type has been successfully deleted'), 'success');
        $this->redirect(':Admin:ReportType:', ['id' => null]);
    }


    /** renders ***************************************************************/

    public function renderCreate()
    {
        $this->template->siteTitle = _('Add to category');
        $this->template->categories = $this->entities;
        $this->template->type = $this->type;
        $this->template->itemId = $this->itemId;
    }


    public function renderDelete()
    {
        $this->template->siteTitle = _('Remove category');
    }


    /** abstract methods ******************************************************/

    /** {@inheritdoc} */
    protected function getFormBuilderServiceAlias()
    {
        return 'Admin.Form.CategoryItem';
    }

}
