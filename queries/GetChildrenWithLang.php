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
 * @deprecated
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
        
//        var_dump($qb->getQuery()->getDQL());
//        dump($this->actual->depth + 1);
		
		
		if($this->lang) {
			$qb->andWhere('l.lang = :lang')
				->setParameter('lang', $this->lang);
		}
		
		return $qb;
	}
}