<?php

namespace Wame\CategoryModule\Components;

use Doctrine\Common\Collections\Criteria;
use Nette\DI\Container;
use Nette\InvalidArgumentException;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\Core\Registers\StatusTypeRegister;

trait CategoryListTrait
{

    /** @var CategoryRepository */
    protected $categoryRepository;

    /** @var StatusTypeRegister */
    protected $statusTypeRegister;

    /** @var string */
    protected $categoryType;

    
    public function injectCategories(CategoryRepository $categoryRepository, StatusTypeRegister $statusTypeRegister)
    {
        $this->categoryRepository = $categoryRepository;
        $this->statusTypeRegister = $statusTypeRegister;
    }

    public function getListType()
    {
        return CategoryEntity::class;
    }

    public function getDataDefinition()
    {
        
        $categoryCriteria = $this->loadParametersCriteria();
        
        $componentStatusType = $this->getComponentParameter('statusType');
        
        $target = '*';
        
        if($componentStatusType) {
            $target = $this->statusTypeRegister->getByName($componentStatusType)->getEntityName();
        }
        
        $relatedDefinition = new DataDefinition(new DataDefinitionTarget($target, true));
        $relatedDefinition->onProcess[] = function($dataDefinition) use ($categoryCriteria) {
            $statusType = $this->statusTypeRegister->getByEntityClass($dataDefinition->getTarget()->getType());
            if (!$statusType) {
                throw new InvalidArgumentException("Unsupported category type");
            }
            $statusAlias = $statusType->getAlias();
            $this->categoryType = $statusAlias;

            $this->setTreeRoot($statusAlias, $categoryCriteria);
        };

        $categoriesIds = $this->getCategoriesIds();
        if ($categoriesIds) {
            $query = $relatedDefinition->getHint('query');
            $query[] = function($qb) {
                $mainAlias = $qb->getAllAliases()[0];
                $qb->innerJoin(CategoryItemEntity::class, 'ci');
                $qb->andWhere($qb->expr()->in('ci.category', $categoriesIds));
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

    protected abstract function getCategoriesIds();

    /**
     * @param string $statusAlias
     * @param Criteria $categoryCriteria
     * @throws InvalidArgumentException
     */
    protected function setTreeRoot($statusAlias, $categoryCriteria)
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
