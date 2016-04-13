<?php

namespace Wame\CategoryModule\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="wame_item_category")
 * @ORM\Entity
 */
class CategoryItemEntity extends \Wame\Core\Entities\BaseEntity 
{
	
	/**
	 * @ORM\Column(name="item_id", type="integer", length=10, nullable=false)
	 */
	protected $item_id;
	
	/**
	 * @ORM\Column(name="lang", type="integer", length=10, nullable=false)
	 */
	protected $category_id;
	
	/**
	 * @ORM\Column(name="type", type="integer", length=10, nullable=false)
	 */
	protected $type;

}

