<?php

namespace Wame\CategoryModule\Repositories;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Kappa\DoctrineMPTT\Configurator;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetChildren;
use Kappa\DoctrineMPTT\Queries\Objects\Selectors\GetParent;
use Kappa\DoctrineMPTT\TraversableManager;
use Kdyby\Doctrine\EntityManager;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\CategoryModule\Entities\CategoryItemEntity;
use Wame\CategoryModule\Entities\CategoryLangEntity;
use Wame\Core\Entities\BaseEntity;
use Wame\Core\Exception\RepositoryException;
use Wame\LanguageModule\Repositories\TranslatableRepository;
use Wame\Utils\Strings;
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
     * @param int $id item ID
     * @param string $type type
     * @param int $parent parent
     * @return CategoryEntity
     */
    public function getByItemId($id, $type, $parent = NULL)
    {
        // TODO: tiez tam vyuzit GetAll na stromove vyhladavanie

        /** @var CategoryEntity $category */
        $category = $this->entity->find(['id' => $id, 'type' => $type]);

        return $category;
    }

    public function getTree($criteria = null)
    {
        /** @var CategoryEntity $actual */
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
     *
     * @param string $type type
     * @return array
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
     * @param BaseEntity $item          entity
     * @param CategoryEntity $category  category
     */
    public function attach($item, $category, $isMain = false)
    {
        $itemCategory = new CategoryItemEntity();

        if($category instanceof CategoryEntity) {
            $itemCategory->category = $category;
        } else {
            $categoryEntity = $this->get(['id' => $category]);
            $itemCategory->category = $categoryEntity;
        }

        $itemCategory->item_id = $item->id;
        $itemCategory->main = $isMain;
//		$itemCategory->type = $type;

        $this->entityManager->persist($itemCategory);
    }

    /**
     * Attach all
     *
     * @param BaseEntity $item                      entity
     * @param CategoryEntity[]|int[] $categories    categories
     */
    public function attachAll($item, $categories, $main = null)
    {
        // if categories contains ids
        if($categories === array_filter($categories, 'is_int')) {
            $categories = $this->find(['id IN' => $categories]);
        }
        
        foreach ($categories as $category) {
            $isMain = ($main !== null && $category->getId() == $main);
            $this->attach($item, $category, $isMain);
        }
    }

    /**
     * Detach
     *
     * @param BaseEntity $item          entity
     * @param CategoryEntity $category  category
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
     * @param BaseEntity $item                      entity
     * @param CategoryEntity[]|int[] $categories    categories
     */
    public function detachAll($item, $categories)
    {
        // if categories contains ids
        if($categories === array_filter($categories, 'is_int')) {
            $categories = $this->find(['id IN' => $categories]);
        }
        
        foreach ($categories as $category) {
            $this->detach($item, $category);
        }
    }

    /**
     * Sync
     *
     * @param BaseEntity $item              entity
     * @param CategoryEntity[] $categories  categories
     * @param boolean $main                 is main
     */
    public function sync($item, $categories, $main = null)
    {
        $attachedCategories = $this->categoryItemRepository->find(['item_id' => $item->id]);

        $attached = [];
        foreach ($attachedCategories as $ai) {
            $attached[] = $ai->category->getId();
        }

        $this->attachAll($item, $this->find(['id IN' => array_diff($categories, $attached)]));
        $this->detachAll($item, $this->find(['id IN' => array_diff($attached, $categories)]));
    }


    /** API *******************************************************************/

    /**
     * Get categories
     *
     * @api {get} /categories/:ids Get categories
     * @param string $ids
     * @return array
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
     * @return array
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
     * @param string $phrase
     *
     * @return CategoryEntity[]
     */
    public function apiGetCategories($phrase = null)
    {
        $separator = ' ';

        $phrases = explode($separator, $phrase);

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

//    /**
//     * Api get count of categories items
//     *
//     * @api {get} /categories-count/ Get count of items in given category ids
//     * @param int|array $categories
//     *
//     * @return array
//     */
//    public function apiGetCount($categories)
//    {
//        if(!is_array($categories)) {
//            $categories = [$categories];
//        }
//
//        $qb = $this->createQueryBuilder('c');
//
//        $qb2 = $qb;
//
////        $qb2 = $this->createQueryBuilder('x');
//
//        $qb2->select('c.id')
//            ->where($qb2->expr()->lt('c.rgt', ':koren'))
//            ->setParameter('koren', 'c.id');
//
//        $qb->select(['c.id', "({$qb->expr()->count('ci.id')}) AS itemCount"])
//            ->leftJoin(CategoryItemEntity::class, 'ci', Join::WITH, "c.id = ci.category")
//            ->andWhere($qb->expr()->in('c.id', $qb2->getDQL()))
//            ->groupBy('c.id');
//
////        return $qb->getQuery()->getDQL();
//
//        return $qb->getQuery()->getResult();
//    }

    /**
     * Api get categories item count
     *
     * @api {get} /categories-item-count/ Get categories item count
     * @param int|array $categories     categories ids
     *
     * @return array
     */
    public function apiGetCategoriesItemCount($categories)
    {
        if(!is_array($categories)) {
            $categories = [$categories];
        }

        $arr = [];

        foreach($categories as $category) {
            $cid = $this->getChildrenIds($category);

            if(is_array($cid)) {
                $arr[$category] = $this->categoryItemRepository->countBy(['item_id IN' => [$category] + $cid]);
            }
        }

        return $arr;
    }

    /**
     * Api get categories child count
     *
     * @api {get} /categories-child-count/ Get categories child count
     * @param int|array $categories     categories ids
     * @param bool $direct              is direct descendant
     *
     * @return array
     */
    public function apiGetCategoriesChildCount($categories, $direct = true)
    {
        if(!is_array($categories)) {
            $categories = [$categories];
        }

        $arr = [];

        foreach($categories as $category) {
            $cid;

            if(filter_var($direct, FILTER_VALIDATE_BOOLEAN)) {
                $cid = $this->findPairs(['parent' => $category], 'id');
            } else {
                $cid = $this->getChildrenIds($category);
            }

            if(is_array($cid)) {
                $arr[$category] = $this->countBy(['id IN' => $cid]);
            }
        }

        return $arr;
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


    private function getChildrenIds($category)
    {
        $c = $this->get(['id' => $category]);
        $categories = $this->getChildren($c);

        $children = array_map(function($e) {
            return $e->getId();
        }, $categories);

        return $children;
    }


    /**
     * Get type by entity
     *
     * @param $namespace
     *
     * @return string
     */
    public static function getTypeByEntity($namespace)
    {
        $className = Strings::getClassName($namespace);

        return strtolower(str_replace('Entity', '', $className));
    }


    /** api *********************************************************** */

    /**
     * @api {get} /category-search/ Search categories
     * @param array $columns
     * @param string $phrase
     * @param string $select
     * @return array
     */
    public function findLike($columns = [], $phrase = null, $select = '*')
    {
        $search = $this->entityManager->createQueryBuilder()
            ->select($select)
            ->from(CategoryEntity::class, 'a')
            ->leftJoin(CategoryLangEntity::class, 'langs', Join::WITH, 'a.id = langs.category')
            ->andWhere('a.status = :status')
            ->setParameter('status', self::STATUS_ACTIVE)
            ->andWhere('langs.lang = :lang')
            ->setParameter('lang', $this->lang);

        foreach ($columns as $column) {
            $search->andWhere($column . ' LIKE :phrase');
        }

        $search->setParameter('phrase', '%' . $phrase . '%');

        return $search->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

}
