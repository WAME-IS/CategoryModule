<?php

namespace App\AdminModule\Presenters;

use Wame\DynamicObject\Vendor\Wame\AdminModule\Presenters\AdminFormPresenter;
use Wame\CategoryModule\Entities\CategoryItemEntity;
use Wame\CategoryModule\Repositories\CategoryItemRepository;


class CategoryItemPresenter extends AdminFormPresenter
{
    /** @var CategoryItemRepository @inject */
    public $repository;

    /** @var CategoryItemEntity */
    protected $entity;

    /** @var string */
    private $type;


    /** actions ***************************************************************/

    public function actionCreate()
    {
        $this->type = $this->getParameter('t');
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
        $this->template->type = $this->type;
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
