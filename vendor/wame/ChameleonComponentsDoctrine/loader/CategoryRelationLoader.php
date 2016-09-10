<?php

namespace Wame\CategoryModule\Vendor\Wame\ChameleonComponentsDoctrine\Loader;

use Nette\DI\Container;
use Wame\CategoryModule\Vendor\Wame\ChameleonComponentsDoctrine\Registers\Types\FromCategoryListRelation;
use Wame\CategoryModule\Vendor\Wame\ChameleonComponentsDoctrine\Registers\Types\FromCategoryRelation;
use Wame\CategoryModule\Vendor\Wame\ChameleonComponentsDoctrine\Registers\Types\ToCategoryListRelation;
use Wame\CategoryModule\Vendor\Wame\ChameleonComponentsDoctrine\Registers\Types\ToCategoryRelation;
use Wame\ChameleonComponentsDoctrine\Registers\RelationsRegister;
use Wame\Core\Registers\StatusTypeRegister;

class CategoryRelationLoader
{

    /** @var Container */
    private $container;

    /** @var StatusTypeRegister */
    private $statusTypeRegister;

    public function __construct(Container $container, StatusTypeRegister $statusTypeRegister)
    {
        $this->container = $container;
        $this->statusTypeRegister = $statusTypeRegister;
    }

    public function initialize(RelationsRegister $relationsRegister)
    {
        foreach ($this->statusTypeRegister as $statusType) {
            $relationsRegister->add(new FromCategoryRelation($statusType->getAlias(), $statusType->getEntityName()));
            $relationsRegister->add(new ToCategoryRelation($statusType->getAlias(), $statusType->getEntityName()));
//            $relationsRegister->add(new FromCategoryListRelation($statusType->getAlias(), $statusType->getEntityName()));
            $relationsRegister->add(new ToCategoryListRelation($statusType->getAlias(), $statusType->getEntityName()));
        }
    }
}
