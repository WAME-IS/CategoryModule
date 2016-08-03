<?php

namespace Wame\CategoryModule\Grids;

class EditGridAction extends \Wame\DataGridControl\BaseGridColumn
{
	public function addColumn($grid)
	{
		$grid->addAction('edit', '', ":{$grid->presenter->getName()}:edit")
			->setIcon('edit')
			->setTitle('Edit')
			->setClass('btn btn-xs btn-info');
		
		return $grid;
	}
}