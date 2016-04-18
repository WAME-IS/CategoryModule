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

	/**
	 * @ORM\Column(name="category_id", type="integer", length=10, nullable=false)
	 */
	protected $category_id;
	
	/**
     * @ORM\ManyToOne(targetEntity="CategoryEntity", inversedBy="lang")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
	protected $category;
	
	/**
	 * @ORM\Column(name="lang", type="string", length=2, nullable=true)
	 */
	protected $lang;
	
	/**
	 * @ORM\Column(name="title", type="string", length=250, nullable=true)
	 */
	protected $title;

	/**
	 * @ORM\Column(name="slug", type="string", length=250, nullable=true)
	 */
	protected $slug;

}

