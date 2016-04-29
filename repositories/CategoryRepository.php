<?php

namespace Wame\CategoryModule\Repositories;

use Nette\Security\User;
use Nette\Utils\Strings;
use Nette\DI\Container;

use Kappa\DoctrineMPTT\Configurator;
use	Kappa\DoctrineMPTT\TraversableManager;
use	Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetAll;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetParent;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetChildren;

use Wame\Utils\Tree\ComplexTreeSorter;
use Wame\UserModule\Entities\UserEntity;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryLangEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;

class CategoryRepository extends \Wame\Core\Repositories\BaseRepository
{
//	const TABLE_NAME = 'category';
	
	const STATUS_REMOVE = 0;
	const STATUS_ACTIVE = 1;
	
	/** @var UserEntity */
	private $userEntity;
	
	/** @var CategoryEntity */
	private $categoryEntity;
	
	/** @var Configurator */
	private $treeConfigurator;
	
	/** @var TraversableManager */
	private $traversableManager;
	
	
	public function __construct(
		Container $container, 
		\Kdyby\Doctrine\EntityManager $entityManager, 
		\h4kuna\Gettext\GettextSetup $translator, 
		TraversableManager $traversableManager,
			
		User $user
		
	) {
		parent::__construct($container, $entityManager, $translator, $user, CategoryEntity::class);
		
		$this->userEntity = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['id' => $user->id]);
		$this->categoryEntity = $this->entityManager->getRepository(CategoryEntity::class);
		
//		$container->callInjects($this);
		
		$this->traversableManager = clone $traversableManager;
		$this->treeConfigurator = new Configurator($entityManager);
		$this->treeConfigurator->set(Configurator::ENTITY_CLASS, CategoryEntity::class /*$this->getClass()*/);
		$this->traversableManager->setConfigurator($this->treeConfigurator);
	}
	
//	public function injectTree(\Kdyby\Doctrine\EntityManager $entityManager, TraversableManager $traversableManager) {
//		$this->traversableManager = clone $traversableManager;
//		$this->treeConfigurator = new Configurator($entityManager);
//		$this->treeConfigurator->set(Configurator::ENTITY_CLASS, $this->getClass());
//		$this->traversableManager->setConfigurator($this->treeConfigurator);
//	}
	
	/** CREATE ****************************************************************/
	
	/**
	 * Add category
	 * 
	 * @param Array $values		values
	 * @return CategoryEntity	category
	 */
	public function add($values)
	{
		// category
		$category = new CategoryEntity();
		$category->createDate = new \DateTime('now');
		$category->createUser = $this->userEntity;
		$category->status = self::STATUS_ACTIVE;
		
		// categoryLang
		$categoryLangEntity = new CategoryLangEntity();
		$categoryLangEntity->category = $category;
		$categoryLangEntity->lang = $this->lang;
		$categoryLangEntity->title = $values['title'];
		$categoryLangEntity->slug = $values['slug']?:(Strings::webalize($categoryLangEntity->title));
		$categoryLangEntity->editDate = new \DateTime('now');
		$categoryLangEntity->editUser = $this->userEntity;
		
		// category tree
		$parent = $this->categoryEntity->findOneBy(['id' => $values->parent]);
		$this->traversableManager->insertItem($category, $parent);
		
		$this->entityManager->persist($categoryLangEntity);
		$this->entityManager->persist($category);
		
		return $category;
	}
	
	
	/** READ ******************************************************************/
	
	/**
	 * Get category by item ID
	 * 
	 * @param type $id		item ID
	 * @param type $type	type
	 * @param type $parent	parent
	 * @return type			category
	 */
	public function getByItemId($id, $type, $parent = NULL)
	{
		// TODO: tiez tam vyuzit GetAll na stromove vyhladavanie
		
		$category = $this->categoryEntity->findOneBy(['id' => $id, 'type' => $type]);
		
		return $category;
	}
	
	/**
	 * Get all categories
	 * 
	 * @return Array	categories
	 */
	public function getAll($criteria = [])
	{
		$query = new GetAll($this->treeConfigurator);
		return $query->fetch($this->entityManager->getRepository('Wame\CategoryModule\Entities\CategoryEntity'))->toArray();
	}
	
	/**
	 * Get all categories in pairs
	 * 
	 * @param Array $criteria	criteria
	 * @param String $value		value
	 * @param Array $orderBy	order by
	 * @param String $key		key
	 * @return Array			categories
	 */
	public function getPairs($criteria = [], $value = null, $orderBy = [], $key = 'id')
	{
		return $this->categoryEntity->findPairs($criteria, $value);
	}
	
	/**
	 * Get categories tree structure
	 * 
	 * @return type
	 */
	public function getTree($criteria = [])
	{
		$items = $this->getAll($criteria);
		$sorter = new ComplexTreeSorter($items);
		return $sorter->sortTree();
	}
	
	public function getParent($actual)
	{
		$query = new GetParent($this->treeConfigurator, $actual);
		
		try {
			return $query->fetchOne($this->entityManager->getRepository('Wame\CategoryModule\Entities\CategoryEntity'));
		} catch(\Exception $e) {
			return null;
		}
	}
	
	public function getChildren($actual)
	{
		$query = new GetChildren($this->treeConfigurator, $actual);
		return $query->fetch($this->entityManager->getRepository('Wame\CategoryModule\Entities\CategoryEntity'))->toArray();
	}
	
	
	/** UPDATE ****************************************************************/
	
	/**
	 * Edit category
	 * 
	 * @param Integer $id		id
	 * @param Array $values		values
	 */
	public function edit($id, $values)
	{
		$category = $this->categoryEntity->findOneBy(['id' => $id]);
		
		$category->title = $values['title'];
		$category->slug = $values['slug']?:(Strings::webalize($category->title));
		
		$parent = $this->find($values->parent);
		
		if($parent) {
			$this->traversableManager->moveItem($category, $parent, TraversableManager::DESCENDANT);
		}
	}
	
	/**
	 * Attach categories to item
	 * 
	 * @param type $item		entity
	 * @param type $type		type
	 * @param type $categoryId	category ID	
	 */
	public function attach($item, $type, $categoryId)
	{
		$itemCategory = new CategoryItemEntity();
		$itemCategory->category_id = (int)$categoryId;
		$itemCategory->item_id = $item->id;
		$itemCategory->type = $type;
		
		$this->entityManager->persist($itemCategory);
	}
	
	/**
	 * Attach all 
	 * @param type $item
	 * @param type $type
	 * @param type $categories
	 */
	public function attachAll($item, $type, $categories)
	{
		foreach($categories as $category) {
			$this->attach($item, $type, $category);
		}
	}
	
	
	/** DELETE ****************************************************************/
	
	/**
	 * Remove category
	 * 
	 * @param type $id
	 */
	public function remove($id)
	{
		$category = $this->find($id);
		
		if($category) {
			$category->status = self::STATUS_REMOVE;
			
			$children = $this->getChildren($category);
			
			foreach($children as $child)
			{
				$child->status = self::STATUS_REMOVE;
			}
		}
	}

	/** implements **/
	
//	public function select($alias = NULL) {
//		if($alias) {
//			return $alias;
//		}
//		
////		$em = $this->container->get('doctrine')->getEntityManager(); 
//		$className = $this->entityManager->getClassMetadata(get_class($this->categoryEntity))->getName();
//		
//		return $className;
//		
////		dump(CategoryEntity::class);
////		exit(); 
////		return CategoryEntity::class;
////		parent::select($alias);
//	}
	
	/**
	 * Flush
	 * 
	 * @param type $entity
	 */
	public function flush($entity = null) {
		if (!$entity->getLeft()) {
			$this->traversableManager->insertItem($entity);
		} else {
			parent::flush($entity);
		}
	}

}