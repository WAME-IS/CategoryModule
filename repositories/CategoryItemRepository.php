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
		parent::__construct($container, $entityManager, $translator, $user);
		
		$this->userEntity = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['id' => $user->id]);
		$this->categoryItemEntity = $this->entityManager->getRepository(CategoryItemEntity::class);
	}
	
	public function getItems($type, $categoryId = null)
	{
		$qb = $this->entityManager->createQueryBuilder();
		
		$qb->select('a')
		   ->from(CategoryItemEntity::class, 'ci')
		   ->leftJoin($this->getEntityNameByAlias(self::FROM_ITEM, $type), 'a', Join::WITH, 'ci.item_id = a.id')
		   ->where('ci.category_id = ' . $categoryId);
		
		$query = $qb->getQuery();
		$results = $query->getResult();

		return $results;
	}
	
	public function getCategories($type, $itemId = null)
	{
		$qb = $this->entityManager->createQueryBuilder();
		
		$qb->select('a')
		   ->from(CategoryItemEntity::class, 'ci')
		   ->leftJoin($this->getEntityNameByAlias(self::FROM_CATEGORY, $type), 'a', Join::WITH, 'ci.category_id = a.id')
		   ->where('ci.item_id = ' . $itemId);
		
		$query = $qb->getQuery();
		$results = $query->getResult();

		return $results;
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
			}
		}
	}
	
}