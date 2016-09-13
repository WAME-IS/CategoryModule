<?php

namespace Wame\CategoryModule\Registers;

use Wame\Core\Registers\BaseRegister;

class CategoryRegister extends BaseRegister
{
    public function __construct()
    {
        parent::__construct(Types\CategoryType::class);
    }
    
}