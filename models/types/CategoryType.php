<?php

namespace Wame\CategoryModule\Models\Type;

abstract class CategoryType
{
	abstract function getAlias();
	
	abstract function getName();
	
	abstract function getClassName();
	
}