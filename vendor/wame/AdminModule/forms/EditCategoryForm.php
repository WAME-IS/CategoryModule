<?php

namespace Wame\CategoryModule\Vendor\Wame\AdminModule\Forms;

use Nette\Security\User;
use Nette\Application\UI\Form;

use Kdyby\Doctrine\EntityManager;
use Kappa\DoctrineMPTT\Configurator;
use	Kappa\DoctrineMPTT\TraversableManager;

use Wame\Core\Forms\FormFactory;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\UserModule\Repositories\UserRepository;
use Wame\CategoryModule\Repositories\CategoryRepository;

class EditCategoryForm extends FormFactory
{	
	/** @val CategoryEntity */
	public $categoryEntity;
	
	/** @val string */
	public $lang;
	
	/** @var EntityManager */
	private $entityManager;
	
	/** @var CategoryRepository */
	private $categoryRepository;
	
	/** @val UserEntity */
	private $userEntity;
	
	/** @var Configurator */
	private $treeConfigurator;
	
	/** @var TraversableManager */
	private $traversableManager;
	
	
	public function __construct(
		CategoryRepository $categoryRepository, 
		UserRepository $userRepository, 
		User $user, 
		\Kdyby\Doctrine\EntityManager $entityManager, 
		TraversableManager $traversableManager
	) {
		$this->categoryRepository = $categoryRepository;
		$this->userEntity = $userRepository->get(['id' => $user->id]);
		$this->lang = $categoryRepository->lang;
		
		$this->entityManager = $entityManager;
		
		$this->traversableManager = clone $traversableManager;
		$this->treeConfigurator = new Configurator($entityManager);
		$this->treeConfigurator->set(Configurator::ENTITY_CLASS, CategoryEntity::class);
		$this->traversableManager->setConfigurator($this->treeConfigurator);
	}
	
	
	/**
	 * Build form
	 * 
	 * @return Form	form
	 */
	public function build()
	{
		$form = $this->createForm();
		
		$form->addSubmit('submit', _('Edit category'));
		
		if ($this->id) {
			$this->categoryEntity = $this->categoryRepository->get(['id' => $this->id]);
			$this->setDefaultValues();
		}
		
		$form->onSuccess[] = [$this, 'formSucceeded'];

		return $form;
	}

	/**
	 * Form succeeded event
	 * 
	 * @param Form $form	form
	 * @param type $values	values
	 * @throws \Exception	exception
	 */
	public function formSucceeded(Form $form, $values)
	{
		$presenter = $form->getPresenter();
		
		try {
			$categoryEntity = $this->update($presenter->id, $values);
		
			$this->categoryRepository->onUpdate($form, $values, $categoryEntity);

			$presenter->flashMessage(_('The category was successfully updated.'), 'success');
			
			$presenter->redirect('this');
		} catch (\Exception $e) {
			if ($e instanceof \Nette\Application\AbortException) {
				throw $e;
			}
			
			$form->addError($e->getMessage());
			$this->entityManager->clear();
		}
	}
	
	/**
	 * Update
	 * 
	 * @param int $categoryId	category id
	 * @param array $values		values
	 * @return CategoryEntity
	 */
	public function update($categoryId, $values)
	{
		// category
		$categoryEntity = $this->categoryRepository->get(['id' => $categoryId]);
		
		// lang
		$categoryLangEntity = $categoryEntity->langs[$this->lang];
		$categoryLangEntity->setTitle($values['title']);
		$categoryLangEntity->setSlug($values['slug']);
		
		// parent
		$parent = $this->categoryRepository->get(['id' => $values->parent]);
		
		if($categoryEntity && $parent) {
			// TODO: exception -> The EntityManager is closed.
			$this->traversableManager->moveItem($categoryEntity, $parent, TraversableManager::DESCENDANT, FALSE);
		}
		
		return $this->categoryRepository->update($categoryEntity);
	}
	
}
