<?php
/**
 * This file is part of the Kappa\DoctrineMPTT package.
 *
 * (c) Ondřej Záruba <zarubaondra@gmail.com>
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */

namespace Wame\CategoryModule\Selectors;

use Kappa\DoctrineMPTT\Configurator;
use Kappa\DoctrineMPTT\Utils\StringComposer;
use Kdyby;
use Kdyby\Doctrine\QueryObject;

/**
 * Class GetAllToDepth
 *
 * @package Wame\CategoryModule\Selectors
 * @author Rene Gmitter
 * @deprecated 
 */
class GetAllToDepth extends QueryObject
{
	/** @var Configurator */
	private $configurator;
	
	/** @var integer */
	private $depth;

	/**
	 * @param Configurator $configurator
	 */
	public function __construct(Configurator $configurator, $depth)
	{
		$this->configurator = $configurator;
		
		$this->depth = $depth;
	}

	/**
	 * @param \Kdyby\Persistence\Queryable $repository
	 * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
	 */
	protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
	{
		$stringComposer = new StringComposer([
			':leftName:' => $this->configurator->get(Configurator::LEFT_NAME)
		]);
		return $repository->createQueryBuilder('e')
			->select('e')
			->where('e.depth <= :depth')->setParameter('depth', $this->depth)
			->orderBy($stringComposer->compose('e.:leftName:'), 'ASC');
	}
}
