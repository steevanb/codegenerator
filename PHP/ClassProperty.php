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

	/**
	 * Get a property from a \RelfectionProperty object
	 *
	 * @param \ReflectionProperty $property
	 * @return $this
	 */
	public static function getFromReflection(\ReflectionProperty $property)
	{
		$return = new ClassProperty();
		$return->setName($property->getName());
		$phpDoc = PHPDoc::parse($property->getDocComment());
		$return->setComments($phpDoc['comments']);
		if (array_key_exists('@var', $phpDoc)) {
			$return->setType($phpDoc['@var'][0]['type']);
		}
		return $return;
	}

	/**
	 * Constructor
	 *
	 * @param string $name
	 */
	public function __construct($name = null)
	{
		$this->setName($name);
	}

}