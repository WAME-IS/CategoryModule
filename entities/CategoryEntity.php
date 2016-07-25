<?php

namespace Wame\CategoryModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kappa\DoctrineMPTT\Entities\TraversableInterface;
use Wame\LanguageModule\Entities\TranslatableEntity;

/**
 * @ORM\Table(name="wame_category")
 * @ORM\Entity
 */
class CategoryEntity extends TranslatableEntity implements TraversableInterface
{
	use \Wame\Core\Entities\Columns\Identifier;
	use \Wame\Core\Entities\Columns\CreateDate;
	use \Wame\Core\Entities\Columns\CreateUser;
	use \Wame\Core\Entities\Columns\Status;
	
	use \Kappa\DoctrineMPTT\Entities\Traversable;

	/**
     * @ORM\OneToMany(targetEntity="CategoryLangEntity", mappedBy="category")
     */
    protected $langs;
	
	/**
	 * @ORM\Column(name="type", type="string", nullable=false)
	 */
	protected $type;
	
	
	/** getters ***************************************************************/
	
	public function getType()
	{
		return $this->type;
	}
	
	/** setters ***************************************************************/
	
	public function setType($type)
	{
		$this->type = $type;
	}
	
}