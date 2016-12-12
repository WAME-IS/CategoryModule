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
    use \Wame\Core\Entities\Columns\Main;

	/**
	 * @ORM\Column(name="item_id", type="integer", length=10, nullable=false)
	 */
	protected $item_id;

    /**
     * @ORM\ManyToOne(targetEntity="CategoryEntity")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
	protected $category;

    
    public function getItemId()
    {
        return $this->item_id;
    }

}
