<?php

namespace Wame\CategoryModule\Repositories;

use Nette\Security\User;
use Nette\DI\Container;

use Kappa\DoctrineMPTT\Configurator;
use	Kappa\DoctrineMPTT\TraversableManager;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetParent;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetChildren;

use Wame\Utils\Tree\ComplexTreeSorter;
use Wame\Core\Exception\RepositoryException;
use Wame\UserModule\Entities\UserEntity;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;
use Wame\CategoryModule\Queries\GetChildrenWithLang;

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
	 * @param CategoryEntity $categoryEntity	CategoryEntity
	 * @return CategoryEntity					CategoryEntity
	 * @throws RepositoryException				Exception
	 */
	public function create($categoryEntity)
	{
		$create = $this->entityManager->persist($categoryEntity);
		
		$this->entityManager->persist($categoryEntity->langs);
		$this->entityManager->flush();
		
		if (!$create) {
			throw new RepositoryException(_('Could not create the category'));
		}
		
		return $categoryEntity;
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
		
		if($actual) {
			$query = new GetChildren($this->treeConfigurator, $actual);
			$categories = $query->fetch($this->entityManager->getRepository(CategoryEntity::class))->toArray();
			$categories[] = $actual;

			$sorter = new ComplexTreeSorter($categories);
			
			return $sorter->sortTree($actual->getLeft());
		} else {
			return [];
		}
	}
	
	public function getParent($actual)
	{
		$query = new GetParent($this->treeConfigurator, $actual);
		
		try {
			return $query->fetchOne($this->entityManager->getRepository(CategoryEntity::class));
		} catch(\Exception $e) {
			return null;
		}
	}
	
	public function getChildren($actual)
	{
		$query = new GetChildren($this->treeConfigurator, $actual);
		return $query->fetch($this->entityManager->getRepository(CategoryEntity::class))->toArray();
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
	
	public function update($categoryEntity)
	{
		return $categoryEntity;
	}
	
	
	/** DELETE ****************************************************************/
	
	/**
	 * Remove category
	 * 
	 * @param integer $id
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
	
	
	/** RELATION **************************************************************/
	
	/**
	 * Attach categories to item
	 * 
	 * @param type $item		entity
	 * @param type $categoryId	category ID	
	 */
	public function attach($item, $categoryId)
	{
		$itemCategory = new CategoryItemEntity();
		$itemCategory->category_id = (int)$categoryId;
		$itemCategory->item_id = $item->id;
//		$itemCategory->type = $type;
		
		$this->entityManager->persist($itemCategory);
	}
	
	/**
	 * Attach all 
	 * @param type $item
	 * @param type $type
	 * @param type $categories
	 */
	public function attachAll($item, $categories)
	{
		foreach($categories as $category) {
			$this->attach($item, $category);
		}
	}
	
	public function detach($item, $categoryId)
	{
		$this->categoryItemRepository->remove([
			'item_id' => $item->id, 
			'category_id' => $categoryId
		]);
	}
	
	public function detachAll($item, $categories)
	{
		foreach($categories as $category) {
			$this->detach($item, $category);
		}
	}
	
	public function sync($item, $categories)
	{
		$attachedCategories = $this->categoryItemRepository->find(['item_id' => $item->id]);
		
		$attached = [];
		
		foreach($attachedCategories as $ai) {
			$attached[] = $ai->category_id;
		}
		
//		$simpleCategories = [];
//		foreach($categories as $c) {
//			$simpleCategories[$c['id']] = $c;
//		}
//		
//		$toAttach = array_diff_key($simpleCategories, $attached);
//		$toDetach = array_diff_key($attached, $simpleCategories);
		
		$toAttach = array_diff($categories, $attached);
		$toDetach = array_diff($attached, $categories);
		
		$this->attachAll($item, $toAttach);
		$this->detachAll($item, $toDetach);
	}
	
	
	/** API *******************************************************************/
	
	/**
	 * Get category descendants
	 * 
	 * @api {get} /category/:type/:node Get category by id
	 * @param string $type
	 * @param int $node
	 */
	public function categoryDescendant($type, $node = 1)
	{
		$actual = $this->get(['id' => $node]);
		
		$query = new GetChildrenWithLang($this->treeConfigurator, $actual, $type, $this->lang);
		
		$categories = $query->fetch($this->entityManager->getRepository(CategoryEntity::class))->toArray(\Doctrine\ORM\Query::HYDRATE_ARRAY);
		
		$nodes = [];
		
		foreach($categories as $category) {
			$nodes[] = [
				'label' => $category['title'],
				'id' => $category['category_id'],
				'load_on_demand' => true,
//				'has_children' => $category->hasChildren
			];
		}
		
		return $nodes;
	}
	
	
	/** implements **/

	/**
	 * Flush
	 * 
	 * @param Entity $entity
	 */
	public function flush($entity = null) {
		if (!$entity->getLeft()) {
			$this->traversableManager->insertItem($entity);
		} else {
			parent::flush($entity);
		}
	}
	
}