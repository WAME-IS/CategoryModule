<?php

namespace Wame\CategoryModule\Vendor\Core\Registers;

use Wame\RouterModule\Entities\RouterEntity;

/**
 * Adds /api route to site router.
 * 
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class CategoryRouterEntity {

	public static function create() {
		$entity = new RouterEntity();
		$entity->route = "[<lang>/]admin/category/<id>";
		$entity->module = "Admin";
		$entity->presenter = "Category";
		$entity->action = "default";
		$entity->defaults = [];
		$entity->sort = 0;
		$entity->sitemap = false;
		$entity->status = 1;
		return $entity;
	}

}
