<?php

namespace Wame\CategoryModule\Grids;

class EditGridAction extends \Wame\DataGridControl\BaseGridColumn
{
	public function addColumn($grid)
	{
		$grid->addAction('edit', '', ":{$grid->parent->presenterName}:edit")
			->setIcon('edit')
			->setTitle('Edit')
			->setClass('btn btn-xs btn-info ajax');
		
		return $grid;
	}
}