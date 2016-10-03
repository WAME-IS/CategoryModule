<?php

namespace Wame\CategoryModule\Vendor\Wame\ChameleonComponentsDoctrine\Registers\Types;

use Kdyby\Doctrine\QueryBuilder;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponentsDoctrine\Registers\Types\IRelation;
use Wame\Core\Entities\BaseEntity;

class FromCategoryRelation implements IRelation
{

    /** @var string */
    private $type;

    /** @var string */
    private $className;

    public function __construct($type, $className)
    {
        $this->type = $type;
        $this->className = $className;
    }

    /**
     * @return DataDefinitionTarget
     */
    public function getFrom()
    {
        return new DataDefinitionTarget(CategoryEntity::class, true);
    }

    /**
     * @return DataDefinitionTarget
     */
    public function getTo()
    {
        return new DataDefinitionTarget($this->className, false);
    }

    /**
     * @param QueryBuilder $qb
     * @param DataSpace $from
     * @param DataSpace $to
     * @param string $relationAlias
     */
    public function process(QueryBuilder $qb, $from, $to, $relationAlias)
    {
        $item = $to->getControl()->getStatus()->get($this->className);
        $mainAlias = $qb->getAllAliases()[0];

        $qb->innerJoin(CategoryItemEntity::class, $relationAlias);
        $qb->andWhere($mainAlias . ' = ' . $relationAlias . '.category');
//        $qb->andWhere($relationAlias . '.type = :type')->setParameter('type', $this->type);
        $qb->andWhere($relationAlias . '.item_id = :item')->setParameter('item', $item->getId());
    }

    /**
     * @param BaseEntity[] $result
     * @param DataSpace $from
     * @param DataSpace $to
     */
    public function postProcess(&$result, $from, $to)
    {
        
    }

    /**
     * @param mixed $hint
     * @return boolean
     */
    public function matchHint($hint)
    {
        return false;
    }
}
