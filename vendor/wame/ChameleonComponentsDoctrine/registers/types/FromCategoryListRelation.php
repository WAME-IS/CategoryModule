<?php

namespace Wame\CategoryModule\Vendor\Wame\ChameleonComponentsDoctrine\Registers\Types;

use Kdyby\Doctrine\QueryBuilder;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponentsDoctrine\Registers\Types\IRelation;
use Wame\Core\Entities\BaseEntity;

class FromCategoryListRelation implements IRelation
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
        return new DataDefinitionTarget($this->className, true);
    }

    /**
     * @param QueryBuilder $qb
     * @param DataSpace $from
     * @param DataSpace $to
     * @param string $relationAlias
     */
    public function process(QueryBuilder $qb, $from, $to, $relationAlias)
    {
        $items = $to->getControl()->getStatus()->get(\Wame\Utils\Strings::plural($this->className));
        $mainAlias = $qb->getAllAliases()[0];

        $items = array_map(function($item) {
            return $item->getId();
        }, $items);

        $qb->innerJoin(CategoryItemEntity::class, $relationAlias);
        $qb->andWhere($relationAlias . '.category = ' . $mainAlias);
//        $qb->andWhere($relationAlias . '.type = :type')->setParameter('type', $this->type);
        if ($items) {
            $qb->andWhere($qb->expr()->in($relationAlias . '.item_id', $items));
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
