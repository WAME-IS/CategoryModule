<?php

namespace Wame\CategoryModule\Vendor\Wame\AdminModule\Forms;

use Nette\Security\User;
use Nette\Application\UI\Form;
use Nette\Utils\Strings;

use Wame\Core\Forms\FormFactory;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryLangEntity;
use Wame\UserModule\Repositories\UserRepository;
use Wame\CategoryModule\Repositories\CategoryRepository;


use Kdyby\Doctrine\EntityManager;


use Kappa\DoctrineMPTT\Configurator;
use	Kappa\DoctrineMPTT\TraversableManager;
use	Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetAll;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetParent;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetChildren;

class CreateCategoryForm extends FormFactory
{	
	/** @var EntityManager */
	private $entityManager;
	
	/** @var CategoryRepository */
	private $categoryRepository;
	
	/** @val UserEntity */
	private $userEntity;
	
//	/** @val CategoryEntity */
//	private $categoryEntity;
	
	/** @var Configurator */
	private $treeConfigurator;
	
	/** @var TraversableManager */
	private $traversableManager;
	
	/** @val string */
	private $lang;
	
	public function __construct(CategoryRepository $categoryRepository, UserRepository $userRepository, User $user, \Kdyby\Doctrine\EntityManager $entityManager, TraversableManager $traversableManager) {
		$this->entityManager = $entityManager;
		
		$this->categoryRepository = $categoryRepository;
		$this->userEntity = $userRepository->get(['id' => $user->id]);
		$this->lang = $categoryRepository->lang;
		
		$this->traversableManager = clone $traversableManager;
		$this->treeConfigurator = new Configurator($entityManager);
		$this->treeConfigurator->set(Configurator::ENTITY_CLASS, \Wame\CategoryModule\Entities\CategoryEntity::class /*$this->getClass()*/);
		$this->traversableManager->setConfigurator($this->treeConfigurator);
//		dump($this->treeConfigurator); exit;
	}
	
	public function build()
	{
		$form = $this->createForm();
		
		$form->addSubmit('submit', _('Create category'));
		
		$form->onSuccess[] = [$this, 'formSucceeded'];

		return $form;
	}
	
	public function formSucceeded(Form $form, $values)
	{
		$presenter = $form->getPresenter();
		
		try {
			$category = $this->create($values);
			// TODO: len pre testovanie
			$this->categoryRepository->onCreate($form, 'articles', $category, $values);

			$presenter->flashMessage(_('The category was successfully created'), 'success');
			
			$presenter->redirect('this');
		} catch (Exception $ex) {
			throw $ex;
		}
		
		$this->redirect('this');
	}

	/**
	 * Create category
	 * 
	 * @param array $values		values
	 * @return CategoryEntity	category
	 */
	public function create($values)
	{
		// category
		$category = new CategoryEntity();
		$category->createDate = new \DateTime('now');
		$category->createUser = $this->userEntity;
		$category->status = CategoryRepository::STATUS_ACTIVE;
		
//		$this->entityManager->persist($category);
		
		
		// categoryLang
		$categoryLangEntity = new CategoryLangEntity();
		$categoryLangEntity->category = $category;
		$categoryLangEntity->lang = $this->lang;
		$categoryLangEntity->title = $values['title'];
		$categoryLangEntity->slug = $values['slug']?:(Strings::webalize($values['title']));
		$categoryLangEntity->editDate = new \DateTime('now');
		$categoryLangEntity->editUser = $this->userEntity;
		
//		dump($categoryLangEntity); exit;
		
		// category tree
		$parent = $this->categoryRepository->get(['id' => $values->parent]);
		$this->traversableManager->insertItem($category, $parent);
		
//		
//		$this->entityManager->persist($categoryLangEntity);
//		$this->entityManager->persist($category);
		
//		exit;
		
		
//		dump($this->traversableManager); exit;
//		dump(get_class($category)); exit;
		
//		dump($this->treeConfigurator); exit;
		
		
		
//		$this->entityManager->persist($category);
//		$this->entityManager->persist($categoryLangEntity);
//		$this->entityManager->flush();
		
		
		
		
		
		return $this->categoryRepository->create($categoryLangEntity);
		
//		return $category;
	}
}
