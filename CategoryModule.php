<?php

namespace Wame;

use Wame\Core\Models\Plugin;
use Wame\PermissionModule\Models\PermissionObject;

class CategoryModule extends Plugin 
{
	/** @var PermissionObject */
	private $permission;

	public function __construct(PermissionObject $permission) 
	{
		$this->permission = $permission;
	}
	
	public function onEnable() 
	{
		$this->permission->addResource('category');
		$this->permission->addResourceAction('category', 'view');
		$this->permission->allow('guest', 'category', 'view');
		$this->permission->addResourceAction('category', 'add');
		$this->permission->allow('moderator', 'category', 'add');
		$this->permission->addResourceAction('category', 'edit');
		$this->permission->allow('moderator', 'category', 'edit');
		$this->permission->addResourceAction('category', 'delete');
		$this->permission->allow('moderator', 'category', 'delete');
	}
	
}
