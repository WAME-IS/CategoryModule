<?php

namespace Wame\CategoryModule\Components;

use Doctrine\Common\Collections\Criteria;
use Nette\DI\Container;
use Nette\InvalidArgumentException;
use Wame\CategoryModule\Components\CategoryListControl;
use Wame\CategoryModule\Components\ICategoryControlFactory;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponentsListControl\Components\ChameleonTreeListControl;
use Wame\Core\Registers\StatusTypeRegister;
use Wame\ListControl\Components\ISimpleEmptyListControlFactory;

interface ICategoryListControlFactory
{

    /** @return CategoryListControl */
    public function create();
}

class CategoryListControl extends ChameleonTreeListControl
{

    /** @persistent */
    public $category = null;

    /** @var CategoryRepository */
    private $categoryRepository;

    /** @var StatusTypeRegister */
    private $statusTypeRegister;

    public function __construct(Container $container, CategoryRepository $categoryRepository, StatusTypeRegister $statusTypeRegister, ICategoryControlFactory $ICategoryControlFactory, ISimpleEmptyListControlFactory $ISimpleEmptyListControlFactory)
    {
        parent::__construct($container);
        $this->categoryRepository = $categoryRepository;
        $this->statusTypeRegister = $statusTypeRegister;
        $this->setComponentFactory($ICategoryControlFactory);
        $this->setNoItemsFactory($ISimpleEmptyListControlFactory);
    }

    public function getListType()
    {
        return CategoryEntity::class;
    }

    public function getDataDefinition()
    {
        $categoryCriteria = Criteria::create();
        if ($this->depth) {
            $categoryCriteria->where(Criteria::expr()->lte('depth', $this->depth));
        }

        $relatedDefinition = new DataDefinition(new DataDefinitionTarget('*', true), $relatedCriteria);
        $relatedDefinition->onProcess[] = function($dataDefinition) use ($categoryCriteria) {
            $this->setTreeRoot($dataDefinition->getTarget()->getType(), $categoryCriteria);
        };
        if ($this->category) {
            $relatedDefinition->setHint('relation', $relatedDefinition)
            //Criteria::create()->where(Criteria::expr()->eq('category', $this->category));
        }

        $listDefinition = new DataDefinition(new DataDefinitionTarget($this->getListType(), true), $categoryCriteria);

        $controlDataDefinition = new ControlDataDefinition($this, [
            $relatedDefinition, $listDefinition
        ]);
        $controlDataDefinition->setTriggersProcessing(true);
        return $controlDataDefinition;
    }

    /**
     * @param string $type
     * @param Criteria $categoryCriteria
     * @throws InvalidArgumentException
     */
    private function setTreeRoot($type, $categoryCriteria)
    {
        $statusType = $this->statusTypeRegister->getByEntityClass($type);
        if (!$statusType) {
            throw new InvalidArgumentException("Unsupported category type");
        }

        $category = $this->categoryRepository->get(['depth' => 1, 'type' => $statusType->getAlias()]);
        if (!$category) {
            throw new InvalidArgumentException("Category not found");
        }

        $categoryCriteria->andWhere(Criteria::expr()->gte('lft', $category->getLeft()));
        $categoryCriteria->andWhere(Criteria::expr()->lte('rgt', $category->getRight()));

        $this->getTreeBuilder()->setFrom($category);
    }
}
