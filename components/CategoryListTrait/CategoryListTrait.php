<?php

namespace Wame\CategoryModule\Components;

use Doctrine\Common\Collections\Criteria;
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

    /** @var string */
    protected $categoryType;

    
    public function injectCategories(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
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
                throw new InvalidArgumentException("Unsupported category type $statusType");
            }
            $statusAlias = $statusType->getAlias();
            $this->categoryType = $statusAlias;

            $this->setTreeRoot($statusAlias, $categoryCriteria);
        };
        
        $categoriesIds = $this->getCategoriesIds();
        if ($categoriesIds) {
            $relationCriteria = Criteria::create()->where(Criteria::expr()->in('category', $categoriesIds));
            $relatedDefinition->addRelation(new DataDefinitionTarget(CategoryEntity::class, true), $relationCriteria);
        }
        
        $listDefinition = new DataDefinition(new DataDefinitionTarget($this->getListType(), true), $categoryCriteria);

        $controlDataDefinition = new ControlDataDefinition($this, [
            $relatedDefinition, $listDefinition
        ]);
        $controlDataDefinition->setTriggersProcessing(true);
        
        return $controlDataDefinition;
    }

    protected abstract function getCategoriesIds();

    protected function getSelectedCategory() {}
    
    /**
     * @param string $statusAlias
     * @param Criteria $categoryCriteria
     * @throws InvalidArgumentException
     */
    protected function setTreeRoot($statusAlias, $categoryCriteria)
    {
//        $depthFrom = $this->getComponentParameter('depthFrom');
//        $depthTo = $this->getComponentParameter('depthTo');
        
        $category = $this->getSelectedCategory() ?: $this->categoryRepository->get(['depth' => 1, 'type' => $statusAlias]);
        
//        \Tracy\Debugger::barDump($category);
        
        if (!$category) {
            throw new InvalidArgumentException("Category not found");
        }

        $categoryCriteria->andWhere(Criteria::expr()->gte('lft', $category->getLeft()));
        $categoryCriteria->andWhere(Criteria::expr()->lte('rgt', $category->getRight()));
//        $categoryCriteria->andWhere(Criteria::expr()->lte('depth', $depthTo));
//        $categoryCriteria->andWhere(Criteria::expr()->gte('depth', $depthFrom));
        
//        $categoryCriteria->andWhere(Criteria::expr()->eq('parent', null));

        $this->getTreeBuilder()->setFrom($category);
    }
    
    protected function loadParametersCriteria()
    {
        return Criteria::create();
    }
}
