<?php

namespace Wame\CategoryModule\Repositories;

use Nette\Security\User;
use Nette\Utils\Strings;
use Nette\DI\Container;

use Kappa\DoctrineMPTT\Configurator;
use	Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetAll;
use	Kappa\DoctrineMPTT\TraversableManager;

use Wame\Tree\ComplexTreeSorter;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\UserModule\Entities\UserEntity;
use Wame\CategoryModule\Entities\ItemCategoryEntity;
use Wame\CategoryModule\Entities\CategoryLangEntity;

class CategoryRepository extends \Wame\Core\Repositories\BaseRepository
{
	const TABLE_NAME = 'category';
	
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
		parent::__construct($container, $entityManager, $translator, $user, self::TABLE_NAME);
		
		$this->userEntity = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['id' => $user->id]);
		$this->categoryEntity = $this->entityManager->getRepository(CategoryEntity::class);
		
//		$container->callInjects($this);
		
//		dump(CategoryEntity::class);
//		exit;
		
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
		$category = new CategoryEntity();
		
		$category->createDate = new \DateTime('now');
		$category->createUser = $this->userEntity;
		$category->status = self::STATUS_ACTIVE;
		
//		dump($values->parent);
		
		$parent = $this->categoryEntity->findOneBy(['id' => $values->parent]);
//		$parent = parent::find($values->parent);
		
//		dump($parent);
		
//		$parent = $values->category;
		
//		dump($values);
//		exit;
		
		$this->traversableManager->insertItem($category, $parent);
		
		$categoryLangEntity = new CategoryLangEntity();
		
		$categoryLangEntity->category = $category;
		$categoryLangEntity->lang = 'sk';
		$categoryLangEntity->title = $values['title'];
		$categoryLangEntity->slug = $values['slug']?:(Strings::webalize($categoryLangEntity->title));
		$categoryLangEntity->editDate = new \DateTime('now');
		$categoryLangEntity->editUser = $this->userEntity;
		
		$this->entityManager->persist($categoryLangEntity);
		
		$c = $this->entityManager->persist($category);
//		$this->entityManager->persist($itemCategory);
		
		return $c;
	}
	
	
	/** READ ******************************************************************/
	
	/**
	 * Get category
	 * 
	 * @param type $criteria	criteria
	 * @return type				category
	 */
	public function get($criteria)
	{
//		return $this->categoryEntity->findOneBy($criteria);
		
		return parent::find(1);
	}
	
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
		
//		dump($this->treeConfigurator);
//		exit;
		
//		$fetch = $query->fetch($this->entityManager->getRepository('Wame\CategoryModule\Entities\CategoryEntity'));
		
		return $query->fetch($this->entityManager->getRepository('Wame\CategoryModule\Entities\CategoryEntity'))->toArray();
		
//		return $this->categoryEntity->findAll($criteria);
		
//		return parent::findBy($criteria);
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
		$category->parent = $values->parent;
	}
	
	/**
	 * Attach categories to item
	 * 
	 * @param Integer $itemId			item ID
	 * @param Integer $categoriesId		category ID
	 * @param String $type				type
	 */
	public function attach($itemId, $categoriesId, $type)
	{
		$itemCategory = new ItemCategoryEntity();
		$itemCategory->category_id = $itemId;
		$itemCategory->item_id = $categoriesId;
		$itemCategory->type = $type;
	}
	
	
	/** DELETE ****************************************************************/
	
	/**
	 * Remove category
	 * 
	 * @param type $id
	 */
	public function remove($id)
	{
		$category = $this->categoryEntity->findOneBy(['id' => $id]);
		
		if($category) {
			$category->status = self::STATUS_REMOVE;
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