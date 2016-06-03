<?php

namespace App\AdminModule\Presenters;

use Wame\ComponentModule\Forms\ComponentForm;
use Wame\PositionModule\Repositories\PositionRepository;
//use Wame\ArticleCategoryPlugin\Wame\CategoryModule\Wame\AdminModule\Forms\ICategoryTreeFormContainerFactory;
use Wame\CategoryModule\Forms\CategoryListForm;
use Wame\CategoryModule\Forms\IDepthFormContainerFactory;
use Wame\CategoryModule\Forms\ICategoryFormContainerFactory;
use Wame\CategoryModule\Forms\ITypeFormContainerFactory;

class CategoryListPresenter extends ComponentPresenter
{		
	/** @var ComponentForm @inject */
	public $componentForm;

	/** @var PositionRepository @inject */
	public $positionRepository;
	
	/** @var CategoryListForm @inject */
	public $categoryListForm;

	/** @var IDepthFormContainerFactory @inject */
	public $IDepthFormContainer;

	/** @var ICategoryFormContainerFactory @inject */
	public $ICategoryFormContainer;
	
	/** @var ITypeFormContainerFactory @inject */
	public $ITypeFormContainer;
	
	
	public function actionCreate()
	{
		if (!$this->user->isAllowed('categoryList', 'create')) {
			$this->flashMessage(_('To enter this section you do not have have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:');
		}
		
		if ($this->getParameter('p')) {
			$position = $this->positionRepository->get(['id' => $this->getParameter('p')]);
			
			if (!$position) {
				$this->flashMessage(_('This position does not exist.'), 'danger');
				$this->redirect(':Admin:Component:', ['id' => null]);
			}
			
			if ($position->status == PositionRepository::STATUS_REMOVE) {
				$this->flashMessage(_('This position is removed.'), 'danger');
				$this->redirect(':Admin:Component:', ['id' => null]);
			}
			
			if ($position->status == PositionRepository::STATUS_DISABLED) {
				$this->flashMessage(_('This position is disabled.'), 'warning');
			}
		}
	}
	
	public function actionEdit()
	{
		if (!$this->user->isAllowed('categoryList', 'edit')) {
			$this->flashMessage(_('To enter this section you do not have have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:');
		}
	}

	public function renderCreate()
	{
		$this->template->siteTitle = _('Create category list');
	}

	/**
	 * Menu component form
	 * 
	 * @return ComponentForm
	 */
	protected function createComponentCategoryListForm()
	{
		$form = $this->componentForm
						->setType('CategoryListComponent')
						->setId($this->id)
						->build();
		
		return $form;
	}
	
	public function renderEdit()
	{
		$this->template->siteTitle = _('Edit category list');
		
//						->addFormContainer($this->IDepthFormContainer->create(), 'DepthFormContainer')
//						->addFormContainer($this->ICategoryFormContainer->create(), 'CategoryFormContainer')
//						->addFormContainer($this->ITypeFormContainer->create(), 'TypeFormContainer')
//						->build();

//		return $form;
	}
	
}