<?php

namespace Wame\CategoryModule\Repositories;

use Nette\Security\User;
use Wame\CategoryModule\Entities\CategoryLangEntity;
use Wame\UserModule\Entities\UserEntity;

class CategoryLangRepository extends \Wame\Core\Repositories\BaseRepository
{
	const TABLE_NAME = 'category_lang';
	
	/** @var UserEntity */
	private $userEntity;
	
	/** @var CategoryLangEntity */
	private $categoryLangEntity;
	
	public function __construct(
		\Nette\DI\Container $container, 
		\Kdyby\Doctrine\EntityManager $entityManager,
		User $user
	) {
		parent::__construct($container, $entityManager, self::TABLE_NAME);

		$this->userEntity = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['id' => $user->id]);
		$this->categoryLangEntity = $this->entityManager->getRepository(CategoryLangEntity::class);
	}
	
	public function create($category, $values)
	{		
		$categoryLangEntity = new CategoryLangEntity();
		
		$categoryLangEntity->category = $category;
		$categoryLangEntity->lang = 'sk';
		$categoryLangEntity->title = $values['title'];
		$categoryLangEntity->slug = $values['slug'];
		$categoryLangEntity->editDate = new \DateTime('now');
		$categoryLangEntity->editUser = $this->userEntity;
		
		$this->entityManager->persist($categoryLangEntity);
	}
	
	/**
	 * Get all and parse to key/value array
	 * 
	 * @param type $criteria	criteria
	 * @param type $value		value column
	 * @param type $orderBy		order by array
	 * @param type $key			key column
	 */
	public function getPairs($criteria = [], $value = null, $orderBy = [], $key = 'id')
	{
		return $this->categoryLangEntity->findPairs($criteria, $value, $orderBy, $key);
	}
	
	public function update($id, $values)
	{
		
	}
	
	public function delete($id)
	{
		
	}
	
}

/**
 * TODO:
 * 
 * - prekontrolovat ci vsetky stlpce koresponduju
 */