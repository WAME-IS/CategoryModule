<?php

namespace Wame\CategoryModule\Grids;

use Wame\DataGridControl\BaseGridColumn;

class TreeGridView extends BaseGridColumn
{
	public function addColumn($grid) {
		$grid->setTreeView([$this, []]);
		
		return $grid;
	}
	
	private function getChildren()
	{
		
	}
}