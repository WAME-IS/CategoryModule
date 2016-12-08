<?php

namespace Wame\CategoryModule\Vendor\Wame\AdminModule\Grids\ToolbarButtons;

use Wame\AdminModule\Vendor\Wame\DataGridControl\ToolbarButtons\Add as AdminAdd;


class AddCategory extends AdminAdd
{
    public function __construct()
    {
        $this->setTitle(_('Create category'));
        $this->isAjaxModal(AdminAdd::MEDIUM_MODAL, AdminAdd::FIXED_MODAL);
    }

}