<?php

namespace Wame\CategoryModule\Repositories;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Kappa\DoctrineMPTT\Configurator;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetChildren;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetParent;
use Kappa\DoctrineMPTT\TraversableManager;
use Kdyby\Doctrine\EntityManager;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;
use Wame\CategoryModule\Entities\CategoryLangEntity;
use Wame\CategoryModule\Queries\GetChildrenWithLang;
use Wame\Core\Entities\BaseEntity;
use Wame\Core\Exception\RepositoryException;
use Wame\LanguageModule\Repositories\TranslatableRepository;
use Wame\Utils\Tree\NestedSetTreeBuilder;

class CategoryRepository extends TranslatableRepository
{
    const STATUS_REMOVE = 0;
    const STATUS_ACTIVE = 1;

    /** @var Configurator */
    private $treeConfigurator;

    /** @var TraversableManager */
    private $traversableManager;

    /** @var CategoryItemRepository */
    private $categoryItemRepository;

    
    public function __construct(
        EntityManager $entityManager,
        TraversableManager $traversableManager, 
        CategoryItemRepository $categoryItemRepository
    ) {
        parent::__construct(CategoryEntity::class, CategoryLangEntity::class);

        $this->categoryItemRepository = $categoryItemRepository;

        $this->traversableManager = clone $traversableManager;
        $this->treeConfigurator = new Configurator($entityManager);
        $this->treeConfigurator->set(Configurator::ENTITY_CLASS, CategoryEntity::class);
        $this->traversableManager->setConfigurator($this->treeConfigurator);
    }
    
    
    /** CREATE ****************************************************************/

    /**
     * Create category
     * 
     * @param CategoryEntity $categoryEntity	CategoryEntity
     * @return CategoryEntity					CategoryEntity
     * @throws RepositoryException				Exception
     */
    public function create($categoryEntity)
    {
        $this->entityManager->persist($categoryEntity);
        $this->entityManager->flush();

        return $categoryEntity;
    }
    
    
    /** READ ******************************************************************/

    /**
     * Get category by item ID
     * 
     * @param type $id		item ID
     * @param type $type	type
     * @param type $parent	parent
     * @return type			category
     */
    public function getByItemId($id, $type, $parent = NULL)
    {
        // TODO: tiez tam vyuzit GetAll na stromove vyhladavanie

        $category = $this->entity->find(['id' => $id, 'type' => $type]);

        return $category;
    }

    public function getTree($criteria = null)
    {
        $actual = $this->get($criteria);

        if ($actual) {
            $query = new GetChildren($this->treeConfigurator, $actual);
            $categories = $query->fetch($this->entityManager->getRepository(CategoryEntity::class))->toArray();
            $categories[] = $actual;

            $builder = new NestedSetTreeBuilder();
            $builder->setFrom($actual);

            return $builder->buildTree($categories);
        } else {
            return [];
        }
    }

    public function getParent($actual)
    {
        $query = new GetParent($this->treeConfigurator, $actual);

        try {
            return $query->fetchOne($this->entityManager->getRepository(CategoryEntity::class));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getChildren($actual)
    {
        $query = new GetChildren($this->treeConfigurator, $actual);
        return $query->fetch($this->entityManager->getRepository(CategoryEntity::class))->toArray();
    }

    /**
     * Get all and parse to key/value array
     */
    public function getPairs($type)
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('c')
            ->from(CategoryEntity::class, 'c')
            ->leftJoin(CategoryLangEntity::class, 'l', Join::WITH, 'l.category = c')
            ->where($qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->eq('c.depth', 0), $qb->expr()->eq('c.type', ':type')
                    ), $qb->expr()->eq('l.lang', ':lang')
            ))
            ->setParameter('lang', $this->lang)
            ->setParameter('type', $type);

        $categories = $qb->getQuery()->getResult();

        $pairs = [];

        foreach ($categories as $category) {
            $pairs[$category->id] = $category->langs[$this->lang]->title;
        }

        return $pairs;
    }

    
    /** UPDATE ****************************************************************/
    
    /**
     * Update
     * 
     * @param CategoryEntity $categoryEntity    category
     * @return CategoryEntity
     */
    public function update($categoryEntity)
    {
        return $categoryEntity;
    }
    
    
    /** DELETE ****************************************************************/

    /**
     * Remove category
     * 
     * @param integer $id
     */
    public function delete($id)
    {
        $category = $this->get(['id' => $id]);

        if ($category) {
            $category->status = self::STATUS_REMOVE;

            $children = $this->getChildren($category);

            foreach ($children as $child) {
                $child->status = self::STATUS_REMOVE;
            }
        }
    }
    
    
    /** RELATION **************************************************************/

    /**
     * Attach categories to item
     * 
     * @param BaseEntity $item Entity
     * @param CategoryEntity $category Category ID	
     */
    public function attach($item, $category)
    {
        $itemCategory = new CategoryItemEntity();
        
        if($category instanceof CategoryEntity) {
            $itemCategory->category = $category->id;
        } else {
            $itemCategory->category = $category;
        }
        
        $itemCategory->item_id = $item->id;
//		$itemCategory->type = $type;

        $this->entityManager->persist($itemCategory);
    }

    /**
     * Attach all
     * 
     * @param BaseEntity $item Entity
     * @param CategoryEntity[] $categories
     */
    public function attachAll($item, $categories)
    {
        foreach ($categories as $category) {
            $this->attach($item, $category);
        }
    }

    /**
     * Detach
     * 
     * @param BaseEntity $item Entity
     * @param CategoryEntity $category Category ID
     */
    public function detach($item, $category)
    {
        $this->categoryItemRepository->remove([
            'item_id' => $item->id,
            'category' => $category
        ]);
    }

    /**
     * Detach all
     * 
     * @param BaseEntity $item Entity
     * @param CategoryEntity[] $categories Categories
     */
    public function detachAll($item, $categories)
    {
        foreach ($categories as $category) {
            $this->detach($item, $category);
        }
    }

    /**
     * Sync
     * 
     * @param BaseEntity $item Entity
     * @param CategoryEntity[] $categories Categories
     */
    public function sync($item, $categories)
    {
        $attachedCategories = $this->categoryItemRepository->find(['item_id' => $item->id]);

        $attached = [];

        foreach ($attachedCategories as $ai) {
            $attached[] = $ai->category;
        }

        $toAttach = array_diff($categories, $attached);
        $toDetach = array_diff($attached, $categories);

        $this->attachAll($item, $toAttach);
        $this->detachAll($item, $toDetach);
    }
    
    
    /** API *******************************************************************/

    /**
     * Get categories
     * 
     * @api {get} /categories/:ids Get categories
     * @param string $ids
     */
    public function categories($ids)
    {
        $categories = $this->find(['id' => explode(',', $ids)]);

        $array = [];

        foreach ($categories as $category) {
            $array[] = [
                'id' => $category->id,
                'title' => $category->title
            ];
        }

        return $array;
    }

    /**
     * Get category descendants
     * 
     * @api {get} /category/:type/:node Get category by id
     * @param string $type
     * @param int $node
     */
    public function categoryDescendant($type, $node = null)
    {
        $criteria = [
            'type' => $type
        ];
        
        if($node) {
            $actual = $this->get(['id' => $node]);
            $criteria['parent'] = $actual;
        } else {
            $criteria['depth'] = 1;
        }
        
        $categories = $this->find($criteria);
        
        $nodes = [];
        
        
        foreach ($categories as $category) {
            $nodes[] = [
                'label' => $category->title,
                'id' => $category->id,
                'load_on_demand' => true,
				'has_children' => count($category->children) > 0
            ];
        }

        return $nodes;
    }
    
    
    /**
     * Api get categories
     * 
     * @api {get} /categories/ Search categories
     * @param string $query
     * 
     * @return CategoryEntity[]
     */
    public function apiGetCategories($query = null)
    {
        $separator = ' ';
        
        $phrases = explode($separator, $query);
        
        /* @var $qb QueryBuilder */
        $qb = $this->createQueryBuilder('a');
        
        $qb->select('a.id, l0.title');

        $likeTitle = $qb->expr()->andx();
        foreach($phrases as $phrase) {
            $likeTitle->add($qb->expr()->like("l0.title", $qb->expr()->literal("%$phrase%")));
        }
                
        $qb->andWhere($likeTitle);
        
        return $qb->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }
    
    
    /** implements ************************************************************/
    
    /**
     * Flush
     * 
     * @param Entity $entity
     */
    public function flush($entity = null)
    {
        if ($entity && !$entity->getLeft()) {
            $this->traversableManager->insertItem($entity);
        } else {
            $this->entityManager->flush($entity);
        }
    }
    
}
