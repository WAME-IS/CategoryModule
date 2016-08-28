<?php

namespace Wame\CategoryModule\Entities;

use \Wame\Core\Entities\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="wame_category_item")
 * @ORM\Entity
 */
class CategoryItemEntity extends BaseEntity 
{
	use \Wame\Core\Entities\Columns\Identifier;
	
	/**
	 * @ORM\Column(name="item_id", type="integer", length=10, nullable=false)
	 */
	protected $item_id;
	
	/**
	 * @ORM\Column(name="category_id", type="integer", length=10, nullable=false)
	 * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
	 */
	protected $category;
	
	/**
	 * @ORM\Column(name="type", type="string", length=250, nullable=true)
	 */
	protected $type;
	
	
	public function getItem()
	{
//		switch($this->type) {
//			case 'article':
//				$entity = new \Wame\ArticleModule\Entities\{$type}Entity;
//				$this->entityManager($type)->find($this->item_id);
//				break;
//		}
	}

}

