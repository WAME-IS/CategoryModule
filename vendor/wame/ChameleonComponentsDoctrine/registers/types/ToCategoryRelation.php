<?php

namespace Wame\CategoryModule\Vendor\Wame\ChameleonComponentsDoctrine\Registers\Types;

use Kdyby\Doctrine\QueryBuilder;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponentsDoctrine\Registers\Types\IRelation;
use Wame\Core\Entities\BaseEntity;

class ToCategoryRelation implements IRelation
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
        return new DataDefinitionTarget($this->className, true);
    }

    /**
     * @return DataDefinitionTarget
     */
    public function getTo()
    {
        return new DataDefinitionTarget(\Wame\CategoryModule\Entities\CategoryEntity::class, false);
    }

    /**
     * @param QueryBuilder $qb
     * @param DataSpace $from
     * @param DataSpace $to
     * @param string $relationAlias
     */
    public function process(QueryBuilder $qb, $from, $to, $relationAlias)
    {
        $category = $to ? $to->getControl()->getStatus()->get($this->className) : null;
        $mainAlias = $qb->getAllAliases()[0];

        $qb->innerJoin(\Wame\CategoryModule\Entities\CategoryItemEntity::class, $relationAlias);
        $qb->andWhere($relationAlias . '.item_id = ' . $mainAlias . '.id');
        $qb->andWhere($relationAlias . '.type = :type')->setParameter('type', $this->type);
        if ($category) {
            $qb->andWhere($relationAlias . '.category = :category')->setParameter('category', $category);
        }
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
