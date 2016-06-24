<?php

namespace Wame\CategoryModule\Forms;

use Nette\Application\UI\Form;
use Wame\DynamicObject\Forms\BaseFormContainer;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryLangRepository;

use Kappa\DoctrineMPTT\Configurator;
use	Kappa\DoctrineMPTT\TraversableManager;
use	Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetAll;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetParent;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetChildren;


interface IParentFormContainerFactory
{
	/** @return ParentFormContainer */
	public function create();
}


class ParentFormContainer extends BaseFormContainer
{
	/** @var CategoryRepository */
	protected $categoryRepository;
	
	/** @var CategoryLangRepository */
	protected $categoryLangRepository;
	
	/** @var Configurator */
	private $treeConfigurator;
	
	/** @var TraversableManager */
	private $traversableManager;
	
	/** @var string */
	private $type;


	public function __construct(
		\Wame\Utils\HttpRequest $httpRequest, 
		CategoryRepository $categoryRepository, 
		CategoryLangRepository $categoryLangRepository, 
		\Kdyby\Doctrine\EntityManager $entityManager, 
		TraversableManager $traversableManager
	) {
		parent::__construct();
		
		$this->type = $httpRequest->getParameter('id');
		
		$this->categoryRepository = $categoryRepository;
		$this->categoryLangRepository = $categoryLangRepository;
		
		// Traversable
		$this->traversableManager = clone $traversableManager;
		$this->treeConfigurator = new Configurator($entityManager);
		$this->treeConfigurator->set(Configurator::ENTITY_CLASS, CategoryEntity::class);
		$this->traversableManager->setConfigurator($this->treeConfigurator);
	}


    public function configure() 
	{
		$form = $this->getForm();
		
		$form->addSelect('parent', _('Parent'))
				->setPrompt(_('-Top rank-'));
		
		if($this->type) {
			$form['parent']->setItems($this->categoryRepository->getPairs($this->type));
		}
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$parent = $this->categoryRepository->getParent($object->categoryEntity);
		
		if($parent) {
			$form['parent']->setItems($this->categoryRepository->getPairs($object->categoryEntity->type))->setDefaultValue($parent->langs[$object->lang]->category_id );
		}
	}

}