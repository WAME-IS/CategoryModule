<?php

namespace Wame\CategoryModule\Registers\Types;

abstract class CategoryType
{
    /**
     * Get alias
     */
	abstract function getAlias();
	
    /**
     * Get name
     */
	abstract function getName();
	
    /**
     * Get class name
     */
	abstract function getClassName();
	
}