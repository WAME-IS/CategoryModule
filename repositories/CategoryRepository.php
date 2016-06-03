<?php

namespace Wame\CategoryModule\Repositories;

use Nette\Security\User;
use Nette\DI\Container;

use Kappa\DoctrineMPTT\Configurator;
use	Kappa\DoctrineMPTT\TraversableManager;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetParent;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetChildren;

use Wame\Utils\Tree\ComplexTreeSorter;
use Wame\UserModule\Entities\UserEntity;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryLangEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;

class CategoryRepository extends \Wame\Core\Repositories\BaseRepository
{
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
	
	/** @var CategoryItemRepository */
	private $categoryItemRepository;
	
	
	public function __construct(
		Container $container, 
		\Kdyby\Doctrine\EntityManager $entityManager, 
		\h4kuna\Gettext\GettextSetup $translator, 
		TraversableManager $traversableManager,
		CategoryItemRepository $categoryItemRepository,
		User $user
	) {
		parent::__construct($container, $entityManager, $translator, $user, CategoryEntity::class);
		
		$this->userEntity = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['id' => $user->id]);
		$this->categoryEntity = $this->entityManager->getRepository(CategoryEntity::class);
		
		$this->categoryItemRepository = $categoryItemRepository;
		
		$this->traversableManager = clone $traversableManager;
		$this->treeConfigurator = new Configurator($entityManager);
		$this->treeConfigurator->set(Configurator::ENTITY_CLASS, CategoryEntity::class);
		$this->traversableManager->setConfigurator($this->treeConfigurator);
	}
	
	/** CREATE ****************************************************************/
	
	/**
	 * Create category
	 * 
	 * @param CategoryLangEntity $categoryLangEntity		CategoryLangEntity
	 * @return CategoryEntity								CategoryEntity
	 * @throws \Wame\Core\Exception\RepositoryException		Exception
	 */
	public function create($categoryLangEntity)
	{
		$create = $this->entityManager->persist($categoryLangEntity->category);
		
		$this->entityManager->persist($categoryLangEntity);
		$this->entityManager->flush();
		
		if (!$create) {
			throw new \Wame\Core\Exception\RepositoryException(_('Could not create the category'));
		}
		
		return $categoryLangEntity->category;
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
		
		$category = $this->entity->find(['id' => $id, 'type' => $type]);
		
		return $category;
	}
	
	public function getTree($criteria)
	{
		$actual = $this->get($criteria);
		$query = new GetChildren($this->treeConfigurator, $actual);
		$categories = $query->fetch($this->entityManager->getRepository(CategoryEntity::class))->toArray();
		$categories[] = $actual;
		
		$sorter = new ComplexTreeSorter($categories);
		
		return $sorter->sortTree($actual->id);
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
	
	/**
	 * Get all and parse to key/value array
	 */
	public function getPairs($type)
	{	
		$qb = $this->entityManager->createQueryBuilder();
		
		$qb->select('c')
				->from(CategoryEntity::class, 'c')
				->leftJoin(\Wame\CategoryModule\Entities\CategoryLangEntity::class, 'l', \Doctrine\ORM\Query\Expr\Join::WITH, 'l.category_id = c.id')
				->where($qb->expr()->andX(
						$qb->expr()->orX(
							$qb->expr()->eq('c.depth', 0),
							$qb->expr()->eq('c.type', ':type')
						),
						$qb->expr()->eq('l.lang', ':lang')
				))
				->setParameter('lang', $this->lang)
				->setParameter('type', $type);
		
		$categories = $qb->getQuery()->getResult();
		
		$pairs = [];
		
		foreach($categories as $category) {
			$pairs[$category->id] = $category->langs[$this->lang]->title;
		}
		
		return $pairs;
	}
	
	
	/** UPDATE ****************************************************************/
	
	public function update($categoryLangEntity)
	{
		return $categoryLangEntity->category;
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
	
	public function detach($item, $type, $categoryId)
	{
		$this->categoryItemRepository->remove([
			'item_id' => $item->id, 
			'category_id' => $categoryId, 
			'type' => $type
		]);
	}
	
	public function detachAll($item, $type, $categories)
	{
		foreach($categories as $category) {
			$this->detach($item, $type, $category);
		}
	}
	
	public function sync($item, $type, $categories)
	{
		$attachedCategories = $this->categoryItemRepository->find(['item_id' => $item->id]);
		
		$attached = [];
		
		foreach($attachedCategories as $ai) {
			$attached[] = $ai->category_id;
		}
		
		$toAttach = array_diff($categories, $attached);
		$toDetach = array_diff($attached, $categories);
		
//		dump($toAttach, $toDetach); exit;
		
		$this->attachAll($item, $type, $toAttach);
		$this->detachAll($item, $type, $toDetach);
	}
	
	
	/** DELETE ****************************************************************/
	
	/**
	 * Remove category
	 * 
	 * @param type $id
	 */
	public function delete($id)
	{
		$category = $this->get(['id' => $id]);
		
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