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
	
	public function __construct(
		\Nette\DI\Container $container, 
		\Kdyby\Doctrine\EntityManager $entityManager,
		User $user
	) {
		parent::__construct($container, $entityManager, self::TABLE_NAME);

		$this->userEntity = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['id' => $user->id]);
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
	
	public function read($id)
	{
		
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