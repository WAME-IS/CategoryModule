<?php

namespace App\AdminModule\Presenters;

use Wame\ComponentModule\Forms\ComponentForm;
use Wame\PositionModule\Repositories\PositionRepository;
//use Wame\ArticleCategoryPlugin\Wame\CategoryModule\Wame\AdminModule\Forms\ICategoryTreeFormContainerFactory;
//use Wame\CategoryModule\Forms\CategoryListForm;
use Wame\CategoryModule\Forms\IDepthFormContainerFactory;
//use Wame\CategoryModule\Forms\ICategoryFormContainerFactory;
use Wame\CategoryModule\Forms\ITypeFormContainerFactory;

class CategoryMenuPresenter extends ComponentPresenter
{		
	/** @var ComponentForm @inject */
	public $componentForm;

	/** @var PositionRepository @inject */
	public $positionRepository;
	
//	/** @var CategoryListForm @inject */
//	public $categoryListForm;

	/** @var IDepthFormContainerFactory @inject */
	public $IDepthFormContainer;

//	/** @var ICategoryFormContainerFactory @inject */
//	public $ICategoryFormContainer;
	
	/** @var ITypeFormContainerFactory @inject */
	public $ITypeFormContainer;
	
	
	public function actionCreate()
	{
		if (!$this->user->isAllowed('categoryMenu', 'create')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
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
		if (!$this->user->isAllowed('categoryMenu', 'edit')) {
			$this->flashMessage(_('To enter this section you do not have enough privileges.'), 'danger');
			$this->redirect(':Admin:Dashboard:');
		}
	}

	public function renderCreate()
	{
		$this->template->siteTitle = _('Create category menu');
	}
    
    public function renderEdit()
	{
		$this->template->siteTitle = _('Edit category menu');
	}

    
	/**
	 * Menu component form
	 * 
	 * @return ComponentForm
	 */
	protected function createComponentCategoryMenuForm()
	{
		$form = $this->componentForm
						->setType('CategoryMenuComponent')
						->setId($this->id)
                        ->addFormContainer($this->ITypeFormContainer->create(), 'TypeFormContainer')
                        ->addFormContainer($this->IDepthFormContainer->create(), 'DepthFormContainer')
						->build();
		
		return $form;
	}
    
}