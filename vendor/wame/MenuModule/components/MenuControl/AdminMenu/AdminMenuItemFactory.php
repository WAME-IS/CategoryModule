<?php

namespace Wame\CategoryModule\Vendor\Wame\MenuModule\Components\MenuControl\AdminMenu;

class AdminMenuItemFactory
{	
	/** @var \Nette\Application\LinkGenerator */
	private $linkGenerator;

	public function __construct(
		\Nette\Application\LinkGenerator $linkGenerator
	) {
		$this->linkGenerator = $linkGenerator;
	}

	/** @return AdminMenuItem */
	public function create() 
	{
		return new AdminMenuItem(
			$this->linkGenerator
		);
	}
}
