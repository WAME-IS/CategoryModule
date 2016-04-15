<?php

namespace Wame\CategoryModule\Repositories;

use Nette\Security\User;
use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\UserModule\Entities\UserEntity;
use Wame\CategoryModule\Entities\ItemCategoryEntity;
use Wame\CategoryModule\Entities\CategoryLangEntity;
use Nette\Utils\Strings;

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
	
	
	/** CREATE ****************************************************************/
	
	/**
	 * Add category
	 * 
	 * @param Array $values		values
	 * @return CategoryEntity	category
	 */
	public function add($values)
	{
		$category = new CategoryEntity();
		
		$category->createDate = new \DateTime('now');
		$category->createUser = $this->userEntity;
		$category->status = self::STATUS_ACTIVE;
		
		$categoryLangEntity = new CategoryLangEntity();
		
		$categoryLangEntity->category = $category;
		$categoryLangEntity->lang = 'sk';
		$categoryLangEntity->title = $values['title'];
		$categoryLangEntity->slug = $values['slug']?:(Strings::webalize($categoryLangEntity->title));
		$categoryLangEntity->editDate = new \DateTime('now');
		$categoryLangEntity->editUser = $this->userEntity;
		
		$this->entityManager->persist($categoryLangEntity);
		
		$c = $this->entityManager->persist($category);
//		$this->entityManager->persist($itemCategory);
		
		return $c;
	}
	
	
	/** READ ******************************************************************/
	
	public function get($criteria)
	{
		return $category = $this->categoryEntity->findOneBy($criteria);
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
	
	public function getPairs($criteria = [], $value = null, $orderBy = [], $key = 'id')
	{
		return $this->categoryEntity->findPairs($criteria, $value);
	}
	
	
	/** UPDATE ****************************************************************/
	
	/**
	 * Edit category
	 * 
	 * @param Integer $id		id
	 * @param Array $values		values
	 */
	public function edit($id, $values)
	{
		$category = $this->categoryEntity->findOneBy(['id' => $id]);
		
		$category->title = $values['title'];
		$category->slug = $values['slug']?:(Strings::webalize($category->title));
		$category->parent = $values->parent;
	}
	
	/**
	 * Attach categories to item
	 * 
	 * @param Integer $itemId			item ID
	 * @param Integer $categoriesId		category ID
	 * @param String $type				type
	 */
	public function attach($itemId, $categoriesId, $type)
	{
		$itemCategory = new ItemCategoryEntity();
		$itemCategory->category_id = $itemId;
		$itemCategory->item_id = $categoriesId;
		$itemCategory->type = $type;
	}
	
	
	/** DELETE ****************************************************************/
	
	/**
	 * Remove category
	 * 
	 * @param type $id
	 */
	public function remove($id)
	{
		$category = $this->categoryEntity->findOneBy(['id' => $id]);
		
		if($category) {
			$category->status = self::STATUS_REMOVE;
		}
	}
	
}