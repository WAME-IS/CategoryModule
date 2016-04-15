<?php

namespace Wame\CategoryModule\Entities;

use \Wame\Core\Entities\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Kappa\DoctrineMPTT\Entities\TraversableInterface;

/**
 * @ORM\Table(name="wame_category")
 * @ORM\Entity
 */
class CategoryEntity extends BaseEntity implements TraversableInterface 
{
	use \Wame\Core\Entities\Columns\Identifier;
	use \Wame\Core\Entities\Columns\CreateDate;
	
	use \Kappa\DoctrineMPTT\Entities\Traversable;
	
	/**
	 * @ORM\Column(name="status", type="integer", length=1, nullable=true)
	 */
	protected $status = 1;
	
//	/**
//	 * @ORM\OneToOne(targetEntity="CategoryEntity")
//	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
//	 */
//	protected $parent;

	/**
     * @ORM\OneToMany(targetEntity="CategoryLangEntity", mappedBy="id")
     */
    protected $langs;
	
	public function getLang()
	{
		$langs = parent::sortLangs($this->langs);
		return $langs['sk'];
	}
	
	public function getTitle()
	{
		return $this->lang->title;
	}
	
	public function getSlug()
	{
		return $this->lang->slug;
	}
	
	public function setTitle($title)
	{
		$this->lang->title = $title;
	}
	
	public function setSlug($slug)
	{
		$this->lang->slug = $slug;
	}
	
}