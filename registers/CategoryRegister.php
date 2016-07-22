<?php

namespace Wame\CategoryModule\Registers;


class CategoryRegister extends \Wame\Core\Registers\BaseRegister
{
    public function __construct() {
        parent::__construct(Types\CategoryType::class);
    }
    
}