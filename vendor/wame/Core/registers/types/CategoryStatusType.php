<?php

namespace Wame\CategoryModule\Vendor\Wame\Core\Registers\Types;

use Wame\CategoryModule\Entities\CategoryEntity;
use Wame\Core\Registers\Types\StatusType;

class CategoryStatusType extends StatusType
{
    /** @var {@inheritDoc} */
    public function getTitle()
    {
        return _('Category');
    }
    
    /** @var {@inheritDoc} */
    public function getEntityName()
    {
        return CategoryEntity::class;
    }
    
}
