<?php

namespace Wame\CategoryModule\Components;

use Doctrine\Common\Collections\Criteria;
use Nette\DI\Container;
use Nette\InvalidArgumentException;
use Wame\CategoryModule\Components\CategoryListControl;
use Wame\CategoryModule\Components\ICategoryControlFactory;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;
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

    /** @var string */
    private $categoryType;

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

        $relatedDefinition = new DataDefinition(new DataDefinitionTarget('*', true));
        $relatedDefinition->onProcess[] = function($dataDefinition) use ($categoryCriteria) {
            $statusType = $this->statusTypeRegister->getByEntityClass($dataDefinition->getTarget()->getType());
            if (!$statusType) {
                throw new InvalidArgumentException("Unsupported category type");
            }
            $statusAlias = $statusType->getAlias();
            $this->categoryType = $statusAlias;

            $this->setTreeRoot($statusAlias, $categoryCriteria);
        };

        if ($this->category) {
            $query = $relatedDefinition->getHint('query');
            $query[] = function($qb) {
                $mainAlias = $qb->getAllAliases()[0];
                $qb->innerJoin(CategoryItemEntity::class, 'ci');
                $qb->andWhere($qb->expr()->in('ci.category', $this->getCategoriesIds()));
                $qb->andWhere('ci.type = :type')->setParameter('type', $this->categoryType);
                $qb->andWhere('ci.item_id = ' . $mainAlias . '.id');
            };
            $relatedDefinition->setHint('query', $query);
        }

        $listDefinition = new DataDefinition(new DataDefinitionTarget($this->getListType(), true), $categoryCriteria);

        $controlDataDefinition = new ControlDataDefinition($this, [
            $relatedDefinition, $listDefinition
        ]);
        $controlDataDefinition->setTriggersProcessing(true);
        return $controlDataDefinition;
    }

    private function getCategoriesIds()
    {
        $categories = $this->categoryRepository->getChildren($this->categoryRepository->get(['id' => $this->category]));
        $categoriesIds = array_map(function($e) {
            return $e->getId();
        }, $categories);
        $categoriesIds[] = $this->category;
        return $categoriesIds;
    }

    /**
     * @param string $statusAlias
     * @param Criteria $categoryCriteria
     * @throws InvalidArgumentException
     */
    private function setTreeRoot($statusAlias, $categoryCriteria)
    {
        $category = $this->categoryRepository->get(['depth' => 1, 'type' => $statusAlias]);
        if (!$category) {
            throw new InvalidArgumentException("Category not found");
        }

        $categoryCriteria->andWhere(Criteria::expr()->gte('lft', $category->getLeft()));
        $categoryCriteria->andWhere(Criteria::expr()->lte('rgt', $category->getRight()));

        $this->getTreeBuilder()->setFrom($category);
    }
}
