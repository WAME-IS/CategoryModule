<?php

namespace Wame\CategoryModule\Vendor\Wame\ChameleonComponentsDoctrine\Registers\Types;

use Kdyby\Doctrine\QueryBuilder;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponentsDoctrine\Registers\Types\IRelation;
use Wame\Core\Entities\BaseEntity;
use Wame\Utils\Strings;
use Doctrine\ORM\Query\Expr\Join;

class ToCategoryListRelation implements IRelation
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
        return new DataDefinitionTarget(CategoryEntity::class, true);
    }

    /**
     * @param QueryBuilder $qb
     * @param DataSpace $from
     * @param DataSpace $to
     * @param string $relationAlias
     */
    public function process(QueryBuilder $qb, $from, $to, $relationAlias)
    {
        $categories = $to ? $to->getControl()->getStatus()->get(Strings::plural($this->className)) : null;
        $mainAlias = $qb->getAllAliases()[0];

        $qb->innerJoin(CategoryItemEntity::class, $relationAlias, Join::WITH, "$relationAlias.item_id = $mainAlias.id");
        $qb->andWhere($relationAlias . '.item_id = ' . $mainAlias . '.id');
//        $qb->andWhere($relationAlias . '.type = :type')->setParameter('type', $this->type);
        if ($categories) {
            $qb->andWhere($qb->expr()->in($relationAlias . '.category', $categories));
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
