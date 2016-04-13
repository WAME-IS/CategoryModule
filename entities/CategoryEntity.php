<?php

namespace Wame\CategoryModule\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="wame_category")
 * @ORM\Entity
 */
class CategoryEntity extends \Wame\Core\Entities\BaseEntity 
{
	use \Wame\Core\Entities\Columns\Identifier;
	use \Wame\Core\Entities\Columns\CreateDate;
	
	/**
	 * @ORM\Column(name="status", type="integer", length=1, nullable=true)
	 */
	protected $status = 1;

	/**
     * @ORM\OneToMany(targetEntity="CategoryLangEntity", mappedBy="category")
     */
    protected $langs;
	
	protected $lang;
	
	public function getLang()
	{
		$langs = parent::sortLangs($this->langs);
		return $langs['sk'];
	}
	
}