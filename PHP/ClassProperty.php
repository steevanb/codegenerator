<?php

namespace steevanb\CodeGenerator\PHP;

use steevanb\CodeGenerator\Model;

class ClassProperty
{

	use Model\Name;
    use Model\Visibility;
    use Model\IsStatic;
    use Model\Comments;
    use Model\Type;
    use Model\DefaultValue;

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