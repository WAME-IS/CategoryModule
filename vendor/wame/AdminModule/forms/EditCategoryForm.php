<?php

namespace Wame\CategoryModule\Vendor\Wame\AdminModule\Forms;

use Nette\Security\User;
use Nette\Application\UI\Form;

use Kdyby\Doctrine\EntityManager;

use Kappa\DoctrineMPTT\Configurator;
use	Kappa\DoctrineMPTT\TraversableManager;
use	Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetAll;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetParent;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetChildren;

use Wame\Core\Forms\FormFactory;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryLangEntity;
use Wame\UserModule\Repositories\UserRepository;
use Wame\CategoryModule\Repositories\CategoryRepository;



class EditCategoryForm extends FormFactory
{	
	/** @var EntityManager */
	private $entityManager;
	
	/** @var CategoryRepository */
	private $categoryRepository;
	
	/** @val UserEntity */
	private $userEntity;
	
	/** @val CategoryEntity */
	public $categoryEntity;
	
	/** @var Configurator */
	private $treeConfigurator;
	
	/** @var TraversableManager */
	private $traversableManager;
	
	/** @val string */
	public $lang;
	
	public function __construct(CategoryRepository $categoryRepository, UserRepository $userRepository, User $user, \Kdyby\Doctrine\EntityManager $entityManager, TraversableManager $traversableManager) {
		$this->categoryRepository = $categoryRepository;
		$this->userEntity = $userRepository->get(['id' => $user->id]);
		$this->lang = $categoryRepository->lang;
		
		$this->entityManager = $entityManager;
		
		$this->traversableManager = clone $traversableManager;
		$this->treeConfigurator = new Configurator($entityManager);
		$this->treeConfigurator->set(Configurator::ENTITY_CLASS, CategoryEntity::class /*$this->getClass()*/);
		$this->traversableManager->setConfigurator($this->treeConfigurator);
	}
	
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
	
	public function update($categoryId, $values)
	{
		$category = $this->categoryRepository->get(['id' => $categoryId]);
		
		$categoryLangEntity = $this->entityManager->getRepository(CategoryLangEntity::class)->findOneBy(['category' => $category, 'lang' => $this->lang]);
		$categoryLangEntity->title = $values['title'];
		$categoryLangEntity->slug = $values['slug'];
		
		$parent = $this->categoryRepository->get(['id' => $values->parent]);
//		$parent = $this->categoryRepository->getParent($category);// get($values->parent);
		
		if($category && $parent) {
			// TODO: exception -> The EntityManager is closed.
			$this->traversableManager->moveItem($category, $parent, TraversableManager::DESCENDANT, FALSE);
		}
		
		return $this->categoryRepository->update($categoryLangEntity);
	}
}
