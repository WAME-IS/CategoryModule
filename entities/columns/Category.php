<?php

namespace Wame\CategoryModule\Entities\Columns;

trait Category
{
	/**
     * @ORM\ManyToOne(targetEntity="CategoryEntity", inversedBy="langs")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
	protected $category;

	
	/** get ************************************************************/

	public function getCategory()
	{
		return $this->category;
	}

	/** set ************************************************************/

	public function setCategory($category)
	{
		$this->category = $category;
		
		return $this;
	}
}