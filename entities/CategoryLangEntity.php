<?php

namespace Wame\CategoryModule\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="wame_category_lang")
 * @ORM\Entity
 */
class CategoryLangEntity extends \Wame\Core\Entities\BaseEntity 
{
	use \Wame\Core\Entities\Columns\Identifier;
	use \Wame\Core\Entities\Columns\EditDate;
	use \Wame\Core\Entities\Columns\EditUser;
	use \Wame\Core\Entities\Columns\Slug;
	use \Wame\Core\Entities\Columns\Title;
	use \Wame\Core\Entities\Columns\Lang;

//	/**
//	 * @ORM\Column(name="category_id", type="integer", nullable=false)
//	 */
//	protected $category_id;
	
	/**
     * @ORM\ManyToOne(targetEntity="CategoryEntity", inversedBy="langs")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
	protected $category;

	
	/** getters ***************************************************************/
	
	public function getCategory()
	{
		$this->category;
	}
	
	
	/** setters ***************************************************************/
	
	public function setCategory($category)
	{
		$this->category = $category;
	}
	
}

