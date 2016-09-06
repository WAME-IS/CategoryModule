<?php

namespace Wame\CategoryModule\Repositories;

use Doctrine\ORM\Query\Expr\Join;
use h4kuna\Gettext\GettextSetup;
use Kdyby\Doctrine\EntityManager;
use Nette\DI\Container;
use Nette\Security\User;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;
use Wame\CategoryModule\Registers\CategoryRegister;
use Wame\Core\Repositories\BaseRepository;

class CategoryItemRepository extends BaseRepository
{
	const FROM_CATEGORY = 0;
	const FROM_ITEM = 1;
	
	const TABLE_ARTICLES = 'article';
	const TABLE_UNITS = 'units';
	const TABLE_SHOP_PRODUCT = 'shopProduct';
	
	/** @var CategoryItemEntity */
	private $categoryItemEntity;
	
	/** @var CategoryRegister */
	private $categoryRegister;
	
    
	public function __construct(
		Container $container, 
		EntityManager $entityManager, 
		GettextSetup $translator,
		User $user,
		CategoryRegister $categoryRegister
	) {
		parent::__construct($container, $entityManager, $translator, $user, CategoryItemEntity::class);
		
		$this->categoryItemEntity = $this->entityManager->getRepository(CategoryItemEntity::class);
		
		$this->categoryRegister = $categoryRegister;
	}
	
    
	public function getItems($type = null, $categoryId = null, $order = null)
	{
		$qb = $this->entityManager->createQueryBuilder();
        
		$qb->select('i')
			->from(CategoryItemEntity::class, 'ci')
			->innerJoin($this->categoryRegister->getByName($type)->getClassName(), 'i', Join::WITH, 'ci.item_id = i.id')
			->innerJoin(CategoryEntity::class, 'c', Join::WITH, 'ci.category = c')
//			->andWhere('i.status != :status')->setParameter('status', 0)
			->andWhere('c.type = :type')->setParameter('type', $type);
		
		if($categoryId) {
			$qb->andWhere('ci.category = ' . $categoryId);
		}
        
        if($order) {
            $qb->orderBy('i.id', $order);
        }
        
		return $qb->getQuery()->getResult();
	}
	
	public function getCategories($type, $itemId = null, $depth = 1)
	{
		$qb = $this->entityManager->createQueryBuilder();
		
		$qb->select('c')
			->from(CategoryItemEntity::class, 'ci')
			->leftJoin(CategoryEntity::class, 'c', Join::WITH, 'ci.category = c')
            ->andWhere('c.type = :type')->setParameter('type', $type);
//			->andWhere('c.status != :status')->setParameter('status', 0);
		
		if($itemId) {
			$qb->andWhere('ci.item_id = :itemId');
			$qb->setParameter('itemId', $itemId);
		}
		
//		if($depth) {
//			$qb->andWhere('c.depth = ' . $depth);
//		}
		
		$query = $qb->getQuery();
		$results = $query->getResult();

		return $results;
	}
	
	public function getCategoryItem($type, $itemId = null)
	{
		$qb = $this->entityManager->createQueryBuilder();
		
		$qb->select('a')
		   ->from(CategoryItemEntity::class, 'ci')
		   ->leftJoin(CategoryEntity::class, 'a', Join::WITH, 'ci.category = a');
		
		if($itemId) {
			$qb->where('ci.item_id = ' . $itemId);
		}
		
		$query = $qb->getQuery();
		$results = $query->getResult();

		return $results;
	}
	
	public function getAssoc($type)
	{
		// TODO: spojit do 1 query, ako? treba dbat aj na relacie lang
		$categoryItem = $this->find(['type' => $type]);
		$categories = $this->generatePairs($this->getCategories($type));
		$items = $this->generatePairs($this->getItems($type));
		
		$arr = [];
		
		foreach($categoryItem as $ci) {
			$arr[$categories[$ci->category->getId()]][$ci->item_id] = $items[$ci->item_id];
		}
		
		return $arr;
	}

    
	private function generatePairs($array)
	{
		$arr = [];
		
		foreach($array as $a) {
			$arr[$a->id] = $a->langs[$this->lang]->title;
		}
		
		return $arr;
	}
	
}