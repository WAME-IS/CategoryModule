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
     * @ORM\OneToMany(targetEntity="CategoryLangEntity", mappedBy="category", cascade={"persist"})
     */
    protected $langs;
	
	/**
	 * @ORM\Column(name="type", type="string", nullable=false)
	 */
	protected $type;
    
    /**
     * @ORM\ManyToOne(targetEntity="CategoryEntity", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
	protected $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="CategoryEntity", mappedBy="parent")
     */
	protected $children;
    
//    /**
//     * @ORM\Column(name="sort", type="integer", nullable=false)
//     */
//    protected $sort = 0;
	
	
	/** getters ***************************************************************/
	
	public function getType()
	{
		return $this->type;
	}
    
    public function getParent()
	{
		return $this->parent;
	}
    
    public function getChildren()
	{
		return $this->children;
	}
    
//    public function getSort()
//    {
//        return $this->sort;
//    }
	
    
	/** setters ***************************************************************/
	
	public function setType($type)
	{
		$this->type = $type;
	}
    
    
    /** others ****************************************************************/
	
    public function addChild(CategoryEntity $child)
    {
        $this->children[] = $child;
        $child->setParent($this);
    }
    
}