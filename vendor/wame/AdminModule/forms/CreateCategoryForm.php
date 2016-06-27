<?php

namespace Wame\CategoryModule\Vendor\Wame\AdminModule\Forms;

use Nette\Security\User;
use Nette\Application\UI\Form;
use Nette\Utils\Strings;

use Kdyby\Doctrine\EntityManager;
use Kappa\DoctrineMPTT\Configurator;
use	Kappa\DoctrineMPTT\TraversableManager;

use Wame\Core\Forms\FormFactory;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryLangEntity;
use Wame\UserModule\Repositories\UserRepository;
use Wame\CategoryModule\Repositories\CategoryRepository;

class CreateCategoryForm extends FormFactory
{	
	/** @var EntityManager */
	private $entityManager;
	
	/** @var CategoryRepository */
	private $categoryRepository;
	
	/** @var UserEntity */
	private $userEntity;
	
//	/** @var CategoryEntity */
//	private $categoryEntity;
	
	/** @var Configurator */
	private $treeConfigurator;
	
	/** @var TraversableManager */
	private $traversableManager;
	
	/** @var string */
	private $lang;
	
	/** @var string */
	private $type;
	
	
	public function __construct(
			CategoryRepository $categoryRepository, 
			UserRepository $userRepository, 
			User $user, 
			\Kdyby\Doctrine\EntityManager $entityManager, 
			TraversableManager $traversableManager, 
			\Wame\Utils\HttpRequest $httpRequest
	) {
		$this->entityManager = $entityManager;
		
		$this->categoryRepository = $categoryRepository;
		$this->userEntity = $userRepository->get(['id' => $user->id]);
		$this->lang = $categoryRepository->lang;
		
		$this->traversableManager = clone $traversableManager;
		$this->treeConfigurator = new Configurator($entityManager);
		$this->treeConfigurator->set(Configurator::ENTITY_CLASS, CategoryEntity::class);
		$this->traversableManager->setConfigurator($this->treeConfigurator);
		
		$this->type = $httpRequest->getRequest()->getParameter('id');
	}
	
	
	public function build()
	{
		$form = $this->createForm();
		
		$form->addHidden('type', $this->type);
		
		$form->addSubmit('submit', _('Create category'));
		
		$form->onSuccess[] = [$this, 'formSucceeded'];

		return $form;
	}
	
	public function formSucceeded(Form $form, $values)
	{
		$presenter = $form->getPresenter();
		
		try {
			$categoryEntity = $this->create($values, $presenter);
			$this->categoryRepository->onCreate($form, $values, $categoryEntity);

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
		$categoryEntity = new CategoryEntity();
		$categoryEntity->setType($values->type);
		$categoryEntity->setCreateDate(new \DateTime('now'));
		$categoryEntity->setCreateUser($this->userEntity);
		$categoryEntity->setStatus(CategoryRepository::STATUS_ACTIVE);
		
		// category lang
		$categoryLangEntity = new CategoryLangEntity();
		$categoryLangEntity->setCategory($categoryEntity);
		$categoryLangEntity->setLang($this->lang);
		$categoryLangEntity->setTitle($values['title']);
		$categoryLangEntity->setSlug($values['slug']?:(Strings::webalize($values['title'])));
		$categoryLangEntity->setEditDate(new \DateTime('now'));
		$categoryLangEntity->setEditUser($this->userEntity);
		$categoryEntity->addLang($this->lang, $categoryLangEntity);
		
		// category tree
		$parent = $this->categoryRepository->get(['id' => $values->parent]);
		$this->traversableManager->insertItem($categoryEntity, $parent);
		
		return $this->categoryRepository->create($categoryEntity);
	}
	
}
