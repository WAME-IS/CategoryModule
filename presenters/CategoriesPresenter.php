<?php

namespace App\CategoryModule\Presenters;

class CategoriesPresenter extends \App\Presenters\BasePresenter
{
	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}

}
