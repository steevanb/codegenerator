<?php

namespace steevanb\CodeGenerator\PHP;

use steevanb\CodeGenerator\Model\Comments;
use steevanb\CodeGenerator\Model\IsStatic;
use steevanb\CodeGenerator\Model\Name;
use steevanb\CodeGenerator\Model\Visibility;
use steevanb\CodeGenerator\Model\Type;
use steevanb\CodeGenerator\Model\DefaultValue;

class ClassProperty
{

	use Name,
	 Visibility,
	 IsStatic,
	 Comments,
	 Type,
	 DefaultValue;

	public function __construct($name = null)
	{
		$this->setName($name);
	}

}