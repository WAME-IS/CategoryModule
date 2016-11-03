<?php

namespace Wame\CategoryModule\Forms\Containers;

use Kdyby\Doctrine\EntityManager;
use Kappa\DoctrineMPTT\Configurator;
use	Kappa\DoctrineMPTT\TraversableManager;
use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\Core\Registers\StatusTypeRegister;

interface IParentContainerFactory extends IBaseContainer
{
	/** @return ParentContainer */
	public function create();
}

class ParentContainer extends BaseContainer
{
    /** @var CategoryRepository */
    protected $repository;
    
    /** @var string */
	protected $type;
    
    /** @var Configurator */
	private $treeConfigurator;
	
	/** @var TraversableManager */
	private $traversableManager;
    
    /** @var StatusTypeRegister */
    protected $statusTypeRegister;
    
    
    public function __construct(
        CategoryRepository $categoryRepository,
        EntityManager $entityManager, 
		TraversableManager $traversableManager,
        StatusTypeRegister $statusTypeRegister
    ) {
        parent::__construct();
        
        $this->repository = $categoryRepository;
        
        $this->traversableManager = clone $traversableManager;
		$this->treeConfigurator = new Configurator($entityManager);
		$this->treeConfigurator->set(Configurator::ENTITY_CLASS, CategoryEntity::class);
		$this->traversableManager->setConfigurator($this->treeConfigurator);
        
        $this->statusTypeRegister = $statusTypeRegister;
        
        $this->monitor(\Nette\Application\UI\Presenter::class);
    }
    
    
    /** {@inheritDoc} */
    public function configure() 
	{
		$this->addSelect('parent', _('Parent'))
				->setPrompt(_('-Top rank-'));
    }
    
    /** {@inheritDoc} */
	public function setDefaultValues($entity, $langEntity = null)
	{
        
	}

    /** {@inheritDoc} */
    public function create($form, $values)
    {
        $type = $this->getForm()->getPresenter()->getParameter('id');
        
        $entity = method_exists($form, 'getLangEntity') && property_exists($form->getLangEntity(), 'parent') ? $form->getLangEntity() : $form->getEntity();
        
        $entity->setType($type);
        
        $parent = $this->repository->get(['id' => $values['parent']]);
        
        $this->traversableManager->insertItem($entity, $parent);
        $entity->setParent($parent);
    }

    /** {@inheritDoc} */
    public function update($form, $values)
    {
        $entity = method_exists($form, 'getLangEntity') && property_exists($form->getLangEntity(), 'parent') ? $form->getLangEntity() : $form->getEntity();
        
        $parent = $this->repository->get(['id' => $values['parent']]);
        $this->traversableManager->moveItem($entity, $parent, TraversableManager::DESCENDANT, FALSE);
        $entity->setParent($values['parent']);
    }
    
    
    /** {@inheritDoc} */
    protected function attached($object)
    {
        parent::attached($object);
        
        if($object instanceof \Nette\Application\UI\Presenter)
        {
            $entity = $this->getForm()->getEntity();
            
            $this->type = $entity->getType() ?: $object->getParameter('id');
            
            if($this->type) {
                $this['parent']->setItems($this->repository->getPairs($this->type));
                
                $parent = $this->repository->getParent($entity);
                
                if($parent) {
                    $this['parent']->setDefaultValue($parent->getId());
                }
            }
        }
    }
    
    
    /**
     * Get type
     * 
     * @return type
     */
    protected function getType($form)
    {
        return $this->getEntityAlias($this->statusTypeRegister, $form->getEntity());
    }

}