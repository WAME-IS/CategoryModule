<?php

namespace Wame\CategoryModule\Repositories;

use Doctrine\ORM\Query\Expr\Join;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;
use Wame\CategoryModule\Registers\CategoryRegister;
use Wame\Core\Repositories\BaseItemRepository;


class CategoryItemRepository extends BaseItemRepository
{
	public function __construct(CategoryRegister $categoryRegister)
    {
		parent::__construct(CategoryItemEntity::class);

		$this->register = $categoryRegister;
	}


    /**
     * Create category item
     *
     * @param CategoryItemEntity $categoryItemEntity
     * @return CategoryItemEntity
     */
    public function create($categoryItemEntity)
    {
        $this->entityManager->persist($categoryItemEntity);

        return $categoryItemEntity;
    }


    /**
     * Get assoc
     *
     * @param string $type  type
     * @return array
     */
    public function getAssoc($type)
	{
		// TODO: spojit do 1 query, ako? treba dbat aj na relacie lang
		$categoryItem = $this->find(/*['type' => $type]*/);
		$categories = $this->generatePairs($this->getCategories($type));
		$items = $this->generatePairs($this->getItems($type));

		$arr = [];

		foreach($categoryItem as $ci) {
			$arr[$categories[$ci->category->getId()]][$ci->item_id] = $items[$ci->item_id];
		}

		return $arr;
	}


    /**
     * Find categories by type
     *
     * @param string $type
     * @param int $itemId
     * @param boolean $main
     * @return array
     */
    public function findByType($type, $itemId, $main = null)
    {
        $qb = $this->createQueryBuilder('ci');
        $qb->leftJoin(CategoryEntity::class, 'c', Join::WITH, 'c.id = ci.category');
        $qb->where($qb->expr()->eq('ci.item_id', ':itemId'))->setParameter('itemId', $itemId);
        $qb->andWhere($qb->expr()->eq('c.type', ':type'))->setParameter('type', $type);

        if ($main != null) {
            $qb->andWhere($qb->expr()->eq('ci.main', ':main'))->setParameter('main', (int)$main);
        }

        return $qb->getQuery()->getResult();
    }


    /**
     * Set item to categories
     *
     * @param string $type
     * @param int $itemId
     * @param CategoryEntity[] $categories
     */
    public function setItemToCategory($type, $itemId, $categories)
    {
        if (!is_array($categories)) { $categories = [$categories]; }

        // Find item in categories
        $oldCategories = [];

        foreach ($this->findItems($type, $itemId) as $item) {
            $categoryId = $item->getId();

            if (isset($categories[$categoryId])) {
                unset($categories[$categoryId]);
            } else {
                $oldCategories[$categoryId] = $item;
            }
        }

        // Add to new categories
        foreach ($categories as $category) {
            $categoryItem = new CategoryItemEntity();
            $categoryItem->setCategory($category);
            $categoryItem->setItemId($itemId);

            $this->create($categoryItem);
        }

        // Remove from old categories
        if (count($oldCategories) > 0) {
            $this->remove(['item_id' => $itemId, 'category IN' => $oldCategories]);
        }

        $this->entityManager->flush();
    }


    /** {@inheritDoc} */
    protected function getAlias()
    {
        return 'category';
    }


    /** {@inheritDoc} */
    protected function getClassName()
    {
        return CategoryEntity::class;
    }


    /** {@inheritDoc} */
    protected function getItemClassName()
    {
        return CategoryItemEntity::class;
    }

}
