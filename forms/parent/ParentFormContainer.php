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
	
	public function __construct(CategoryRepository $categoryRepository, CategoryLangRepository $categoryLangRepository, \Kdyby\Doctrine\EntityManager $entityManager, TraversableManager $traversableManager) 
	{
		parent::__construct();
		
		$this->categoryRepository = $categoryRepository;
		$this->categoryLangRepository = $categoryLangRepository;
		
		// Traversable
		$this->traversableManager = clone $traversableManager;
		$this->treeConfigurator = new Configurator($entityManager);
		$this->treeConfigurator->set(Configurator::ENTITY_CLASS, CategoryEntity::class /*$this->getClass()*/);
		$this->traversableManager->setConfigurator($this->treeConfigurator);
	}
	
    public function render() 
	{
        $this->template->_form = $this->getForm();
        $this->template->render(__DIR__ . '/default.latte');
    }

    public function configure() 
	{
		$form = $this->getForm();

		$criteria = [
			'lang' => 'sk'
		];
		
		$categories = $this->categoryLangRepository->getPairs($criteria, 'title', [], 'category_id');
		
		$form->addSelect('parent', _('Parent'), $categories)
				->setPrompt(_('-Top rank-'));
		
//		$form->addSelect('parent', _('Parent'), []);
		
//		$form->addText('slug', _('URL'))
//				->setType('text');
    }
	
	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		
		$parent = $this->categoryRepository->getParent($object->categoryEntity);
		
		$form['parent']->setDefaultValue($parent->langs[$object->lang]->category_id );
	}
}