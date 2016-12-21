<?php

namespace App\AdminModule\Presenters;

use Nette\Utils\Strings;
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
        $this->id = $this->getParameter('id');
        $entity = $this->repository->get(['id' => $this->id]);

        $items = $this->repository->find(['category.type' => $entity->getCategory()->getType(), 'item_id' => $entity->getItemId()]);

        foreach ($items as $item) {
            $item->setMain(false);
        }

        $entity->setMain(true);
        $this->entityManager->flush();

        $this->itemId = $entity->getItemId();
        $this->type = $entity->getCategory()->getType();
        $this->entities = $this->repository->findByType($this->type, $this->itemId);
    }


    /** handles ***************************************************************/

    public function handleDelete()
    {
        $this->repository->remove(['id' => $this->id]);

        $this->flashMessage(sprintf(_('Category %s has been successfully removed'), $this->entity->getCategory()->getTitle()), 'success');

        $this->redirect(':Admin:' . Strings::firstUpper($this->entity->getCategory()->getType()) . ':edit', ['id' => $this->entity->getItemId()]);
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
        $this->template->subTitle = $this->entity->getCategory()->getTitle();
    }


    public function renderSetMain()
    {
        $this->template->setFile(__DIR__ . '/templates/CategoryItem/create.latte');
        $this->template->categories = $this->entities;
        $this->template->type = $this->type;
        $this->template->itemId = $this->itemId;
    }


    /** abstract methods ******************************************************/

    /** {@inheritdoc} */
    protected function getFormBuilderServiceAlias()
    {
        return 'Admin.Form.CategoryItem';
    }

}
