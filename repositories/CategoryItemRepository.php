<?php

namespace Wame\CategoryModule\Repositories;

use Nette\Security\User;
use Nette\DI\Container;

use Doctrine\ORM\Query\Expr\Join;

use Wame\UserModule\Entities\UserEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;

class CategoryItemRepository extends \Wame\Core\Repositories\BaseRepository
{
	const FROM_CATEGORY = 0;
	const FROM_ITEM = 1;
	
	const TABLE_ARTICLES = 'articles';
	const TABLE_UNITS = 'units';
	
	/** @var UserEntity */
	private $userEntity;
	
	/** @var CategoryItemEntity */
	private $categoryItemEntity;
	
	public function __construct(
		Container $container, 
		\Kdyby\Doctrine\EntityManager $entityManager, 
		\h4kuna\Gettext\GettextSetup $translator,
		User $user
	) {
		parent::__construct($container, $entityManager, $translator, $user, CategoryItemEntity::class);
		
		$this->userEntity = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['id' => $user->id]);
		$this->categoryItemEntity = $this->entityManager->getRepository(CategoryItemEntity::class);
	}
	
	public function getItems($type, $categoryId = null)
	{
		$qb = $this->entityManager->createQueryBuilder();
		
		$qb->select('a')
		   ->from(CategoryItemEntity::class, 'ci')
		   ->leftJoin($this->getEntityNameByAlias(self::FROM_ITEM, $type), 'a', Join::WITH, 'ci.item_id = a.id');
		
		if($categoryId) {
			$qb->where('ci.category_id = ' . $categoryId);
		}
		
		$query = $qb->getQuery();
		$results = $query->getResult();

		return $results;
	}
	
	public function getCategories($type, $itemId = null, $depth = 1)
	{
		$qb = $this->entityManager->createQueryBuilder();
		
		$qb->select('c')
		   ->from(CategoryItemEntity::class, 'ci')
		   ->leftJoin($this->getEntityNameByAlias(self::FROM_CATEGORY, $type), 'c', Join::WITH, 'ci.category_id = c.id');
		
		if($itemId) {
			$qb->where('ci.item_id = ' . $itemId);
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
		   ->leftJoin($this->getEntityNameByAlias(self::FROM_CATEGORY, $type), 'a', Join::WITH, 'ci.category_id = a.id');
		
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
			$arr[$categories[$ci->category_id]][$ci->item_id] = $items[$ci->item_id];
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
	
	// TODO: neskor nacitavat niekde z DB
	public function getEntityNameByAlias($from, $alias)
	{
		if($from == self::FROM_CATEGORY) {
			// TODO: ak sa rozbije do viac tabuliek tak zas aliasy riesit
			return \Wame\CategoryModule\Entities\CategoryEntity::class;
		} else {
			switch($alias) {
				case self::TABLE_ARTICLES:
					return '\Wame\ArticleModule\Entities\ArticleEntity';
				case self::TABLE_UNITS:
					return '\Wame\UnitModule\Entities\UnitEntity';
				default:
					throw new \Exception("Invalid table alias '$alias'");
			}
		}
	}
	
}