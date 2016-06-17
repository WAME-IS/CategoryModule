<?php

namespace Wame\CategoryModule\Queries;

use Kdyby\Doctrine\QueryObject;
use Kappa\DoctrineMPTT\Entities\TraversableInterface;
use Kappa\DoctrineMPTT\Configurator;
use Doctrine\ORM\Query\Expr\Join;
use Kappa\DoctrineMPTT\Utils\StringComposer;

use Wame\CategoryModule\Entities\CategoryLangEntity;

/**
 * Class GetChildrenWithLang
 *
 * @package Kappa\DoctrineMPTT\Queries\Objects\Selectors
 * @author Rene Gmitter
 */
class GetChildrenWithLang extends QueryObject
{
	/** @var Configurator */
	private $configurator;
	
	/** @var TraversableInterface */
	private $actual;
	
	/** @var string */
	private $type;
	
	/** @var string */
	private $lang;
	
	/**
	 * @param Configurator $configurator
	 * @param TraversableInterface $actual
	 */
	public function __construct(Configurator $configurator, TraversableInterface $actual, $type, $lang = null)
	{
		$this->configurator = $configurator;
		$this->actual = $actual;
		
		$this->type = $type;
		$this->lang = $lang;
	}
	/**
	 * @param \Kdyby\Persistence\Queryable $repository
	 * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
	 */
	protected function doCreateQuery(\Kdyby\Persistence\Queryable $repository)
	{
		$stringComposer = new StringComposer([
			':leftName:' => $this->configurator->get(Configurator::LEFT_NAME),
			':rightName:' => $this->configurator->get(Configurator::RIGHT_NAME)
		]);
		
//		dump(Wame\CategoryModule\Entities\CategoryEntity::class); exit;
		
//		$qb = $repository->createQueryBuilder('e')
//			->select('l')
//			->select('l, (COUNT(parent.id) - (sub_tree.depth + 1)) AS depth')
//			->from('\Wame\CategoryModule\Entities\CategoryEntity', 'node')
//			->from('\Wame\CategoryModule\Entities\CategoryEntity', 'parent')
//			->from('\Wame\CategoryModule\Entities\CategoryEntity', 'sub_parent')
//			->addSelect('(SELECT node.id, (COUNT(parent.id) - 1) AS depth FROM \Wame\CategoryModule\Entities\CategoryEntity AS node, \Wame\CategoryModule\Entities\CategoryEntity AS parent WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.id = 2) AS sub_tree')
//			->leftJoin(CategoryLangEntity::class, 'l', Join::WITH, 'e.id = l.category')
//			->where($stringComposer->compose('e.:leftName: > ?0'))
//			->andWhere($stringComposer->compose('e.:rightName: < ?1'))
//			->setParameters([
//				$this->actual->getLeft(),
//				$this->actual->getRight()
//			])
//			->andWhere('e.type = :type')
//			->setParameter('type', $this->type)
//			->andWhere('e.depth = :depth')
//			->setParameter('depth', $this->actual->depth + 1);
		
		
		
		$qb = $repository->createQueryBuilder('e')
			->select('l')
//			->addSelect("(SELECT COUNT(id) FROM e WHERE lft BETWEEN ?0 AND ?1) as count")
//			->addSelect(sprintf('(%s) AS my_count', $subQ->getDql()))
			->leftJoin(CategoryLangEntity::class, 'l', Join::WITH, 'e.id = l.category')
			->where($stringComposer->compose('e.:leftName: > ?0'))
			->andWhere($stringComposer->compose('e.:rightName: < ?1'))
			->setParameters([
				$this->actual->getLeft(),
				$this->actual->getRight()
			])
			->andWhere('e.type = :type')
			->setParameter('type', $this->type)
			->andWhere('e.depth = :depth')
			->setParameter('depth', $this->actual->depth + 1);
		
//		$subQb = $qb-> $repository->createQueryBuilder('e')
//				->select('COUNT(id) as count')
////				->from(\Wame\CategoryModule\Entities\CategoryEntity::class, 'c')
//				->where($qb->expr()->between('e.lft', '?0', '?1'));
		
//		$qb->addSelect(sprintf('(%s) AS my_count', $subQb->getDQL()));
		
		
		if($this->lang) {
			$qb->andWhere('l.lang = :lang')
				->setParameter('lang', $this->lang);
		}
		
//		var_dump($qb->getQuery()->getResult()); exit;
		
		return $qb;
	}
}