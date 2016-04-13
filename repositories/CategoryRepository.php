<?php

namespace Wame\CategoryModule\Repositories;

use Nette\Security\User;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\UserModule\Entities\UserEntity;
use Wame\CategoryModule\Entities\ItemCategoryEntity;
use Wame\CategoryModule\Entities\CategoryLangEntity;

class CategoryRepository extends \Wame\Core\Repositories\BaseRepository
{
	const TABLE_NAME = 'category';
	
	const STATUS_REMOVE = 0;
	const STATUS_ACTIVE = 1;
	
	/** @var UserEntity */
	private $userEntity;
	
	/** @var CategoryEntity */
	private $categoryEntity;
	
	public function __construct(
		\Nette\DI\Container $container, 
		\Kdyby\Doctrine\EntityManager $entityManager,
		User $user
	) {
		parent::__construct($container, $entityManager, self::TABLE_NAME); // zistit ci potrebujeme posuvat table_name
		
		$this->userEntity = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['id' => $user->id]);
		$this->categoryEntity = $this->entityManager->getRepository(CategoryEntity::class);
	}
	
	public function create($values)
	{
		$category = new CategoryEntity();
		
		$category->createDate = new \DateTime('now'); // TODO: je to nutne? neni lepsie to riesit na urovni DB default?
		$category->createUser = $this->userEntity;
		$category->status = self::STATUS_ACTIVE;
		
		$categoryLangEntity = new CategoryLangEntity();
		
		$categoryLangEntity->category = $category;
		$categoryLangEntity->lang = 'sk';
		$categoryLangEntity->title = $values['title'];
		$categoryLangEntity->slug = $values['slug'];
		$categoryLangEntity->editDate = new \DateTime('now');
		$categoryLangEntity->editUser = $this->userEntity;
		
		$this->entityManager->persist($categoryLangEntity);
		
		$c = $this->entityManager->persist($category);
//		$this->entityManager->persist($itemCategory);
		
		return $c;
	}
	
	/**
	 * Attach categories to item
	 * 
	 * @param type $itemId
	 * @param type $categoriesId
	 * @param type $type
	 */
	public function attach($itemId, $categoriesId, $type)
	{
		$itemCategory = new ItemCategoryEntity();
		$itemCategory->category_id = $itemId;
		$itemCategory->item_id = $categoriesId;
		$itemCategory->type = $type;
	}
	
	public function get($criteria)
	{
		$category = $this->categoryEntity->findOneBy($criteria);
		
//		dump($category->lang);
//		exit;
//		$return = new \stdClass();
//		$return->
		
		return $category;
	}
	
	public function getByItemId($id, $type, $parent = NULL)
	{
		// TODO: implementovat stromove vyhladavanie
		
		$category = $this->categoryEntity->findOneBy(['id' => $id, 'type' => $type]);
		
		return $category;
	}
	
	public function getAll()
	{
		$categories = $this->categoryEntity->findAll();
		
		return $categories;
	}
	
	public function update($id, $values)
	{
		$category = $this->categoryEntity->findOneBy(['id' => $id]);
		
		try {
			$category->title = $values->title;
			$category->parent = $values->parent;
		} catch(\Exception $e) {
			Log.d(TAG, $e->getMessage());
		}
	}
	
	public function delete($id)
	{
		$category = $this->categoryEntity->findOneBy(['id' => $id]);
		if($category) {
			$category->status = self::STATUS_REMOVE;
		}
	}
	
}

/**
 * entries/items
 * - id
 * - title
 * - ...
 * 
 * categories
 * - id
 * - title
 * - parent
 * - ...
 * 
 * items_categories
 * - item_id (integer)
 * - categories_id (integer)
 * - type (string) // <module_name>
 */