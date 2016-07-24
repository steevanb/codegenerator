<?php

namespace steevanb\CodeGenerator\Php\ClassGenerator;

use steevanb\CodeGenerator\Behavior;
use steevanb\CodeGenerator\Php\PhpDoc;

class PropertyFactory
{
    /**
	 * @param \ReflectionProperty $property
	 * @return Property
	 */
	public static function createFromReflection(\ReflectionProperty $property)
	{
		$return = new Property();
		$return->setName($property->getName());
		$phpDoc = PhpDoc::parse($property->getDocComment());
        if (array_key_exists('comments', $phpDoc)) {
            $return->setComments($phpDoc['comments']);
        }
		if (array_key_exists('@var', $phpDoc)) {
			$return->setType($phpDoc['@var'][0]['type']);
		}

		return $return;
	}
}
