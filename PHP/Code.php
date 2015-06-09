<?php

namespace steevanb\CodeGenerator\PHP;

/**
 * PHP generator
 */
trait Code
{
	/**
	 * Return PHP code for a simple comment
	 *
	 * @param string $comment
	 * @param int $tabs Number of tabs, null to use internal counter
	 * @param int $endOfLines Number of end line character to add
	 * @return string
	 */
	public function getCode4Comment($comment, $tabs = 0, $endOfLines = 1)
	{
		return $this->getCode4Comments(array($comment), $tabs, $endOfLines);
	}

	/**
	 * Return PHP code for a simple comment
	 *
	 * @param string|array $comments Comment(s), could be a string or an array of comment
	 * @param int $tabs Number of tabs, null to use internal counter
	 * @param int $endOfLines Number of end line character to add
	 * @return array[]
	 */
	public function getCode4Comments($comments, $tabs = 0, $endOfLines = 1)
	{
		if (is_array($comments) == false) {
			$comments = array($comments);
		}
		$return = null;
		foreach ($comments as $index => $comment) {
			$eol = ($index < count($comments) - 1) ? 1 : 0;
			$return .= $this->getCode4Line('// ' . $comment, $tabs, $eol);
		}
		$return .= $this->getEndOfLines($endOfLines);

		return $return;
	}

	/**
	 * Get code for a PHP doc block
	 *
	 * @param string|array $comments Comment(s), could be a string or an array of comment
	 * @param int $tabs Number of tabs, null to use internal counter
	 * @param int $endOfLines Number of end line character to add
	 * @return string
	 */
	public function getCode4PHPDocComments($comments, $tabs = 0, $endOfLines = 1)
	{
		if (is_array($comments) == false) {
			$comments = array('comments' => $comments);
		}
		$return = null;
		$phpDocs = PHPDoc::generate($comments);
		foreach ($phpDocs as $phpDoc) {
			$return .= $this->getCode4Line($phpDoc, $tabs);
		}
		$return .= $this->getEndOfLines($endOfLines - 1);

		return $return;
	}

	/**
	 * Return code for namespace declaration
	 *
	 * @param string $namespace Namespace
	 * @param int $tabs Number of tabs, null to use internal counter
	 * @param int $endOfLines Number of end line character to add
	 * @return string
	 */
	public function getCode4Namespace($namespace, $tabs = 0, $endOfLines = 1)
	{
		return $this->getCode4Line('namespace ' . $namespace . ';', $tabs, $endOfLines);
	}

	/**
	 * Return code for uses
	 *
	 * @param array $uses Uses
	 * @param boolean $concat Indicate if you want to concat uses (use MyUse, MyUse2) or one line per use
	 * @param int $tabs Number of tabs, null to use internal counter
	 * @param int $endOfLines Number of end line character to add
	 * @return string
	 */
	public function getCode4Uses(array $uses, $concat = true, $tabs = 0, $endOfLines = 2)
	{
		$usesAs = array();
		foreach ($uses as $namespace => $class) {
			$posBackSlash = strrpos($namespace, '\\');
			$usesAs[] = ($posBackSlash !== false && substr($namespace, $posBackSlash + 1) != $class) ? $namespace . ' as ' . $class : $namespace;
		}

		$return = null;
		if ($concat) {
			$tabsStr = $this->getTabs($tabs);
			$return .= $tabsStr . 'use ' . implode(',' . $this->getEndOfLines() . $tabsStr . '    ', $usesAs) . ';' . $this->getEndOfLines();
		} else {
			foreach ($usesAs as $useAs) {
				$return .= $this->getCode4Line('use ' . $useAs . ';', $tabs, 1);
			}
		}
		if (count($uses) > 0 && $endOfLines > 1) {
			$return .= $this->getEndOfLines($endOfLines - 1);
		}

		return $return;
	}

	/**
	 * Return code for traits
	 *
	 * @param array $traits
	 * @param int $tabs Number of tabs, null to use internal counter
	 * @param int $endOfLines Number of end line character to add
	 * @return string
	 */
	public function getCode4Traits(array $traits, $tabs = 1, $endOfLines = 2)
	{
		$return = null;

		if ($this->getConcatTraits()) {
			$tabsStr = $this->getTabs($tabs);
			$return .= $tabsStr . 'use ' . implode(',' . $this->getEndOfLines() . $tabsStr . '    ', $traits) . ';' . $this->getEndOfLines();
		} else {
			foreach ($traits as $trait) {
				$return .= $this->getCode4Line('use ' . $trait . ';', $tabs);
			}
		}
		if (count($traits) > 0 && $endOfLines > 1) {
			$return .= $this->getEndOfLines($endOfLines - 1);
		}

		return $return;
	}

	/**
	 * Return start code to start a class
	 *
	 * @param string $className
	 * @param string $extends
	 * @param array $interfaces
	 * @param array $traits
	 * @param int $tabs Number of tabs, null to use internal counter
	 * @param int $endOfLines Number of end line character to add
	 * @return string
	 */
	public function getStartCode4Class($className, $extends = null, array $interfaces = array(), array $traits = array(), $tabs = 0, $endOfLines = 1)
	{
		// className
		$declaration = 'class ' . $className;

		// extends
		if ($extends != null) {
			$declaration .= ' extends ' . $extends;
		}

		// interfaces
		if (count($interfaces) > 0) {
			$declaration .= ' implements ' . implode(', ', $interfaces);
		}

		// end of declaration
		$return = $this->getCode4Line($declaration, $tabs, 1);
		$return .= $this->getCode4Line('{', $tabs, 1);

		// traits
		if (count($traits) > 0) {
			$return .= $this->getCode4Traits($traits, $tabs + 1, 2);
		}

		$return .= $this->getEndOfLines($endOfLines - 1);

		return $return;
	}

	/**
	 * Return code for the end of a class
	 *
	 * @param int $tabs Number of tabs, null to use internal counter
	 * @param int $endOfLines Number of end line character to add
	 * @return string
	 */
	public function getEndCode4Class($tabs = 0, $endOfLines = 0)
	{
		return $this->getCode4Line('}', $tabs, $endOfLines);
	}

	/**
	 * Return code for visibility
	 *
	 * @param int $visibility Use self::VISIBLITY_XXX
	 * @return string
	 */
	public function getCode4Visibility($visibility)
	{
		switch ($visibility) {
			case self::VISIBILITY_PUBLIC:
				return 'public ';
			case self::VISIBILITY_PROTECTED:
				return 'protected ';
			case self::VISIBILITY_PRIVATE:
				return 'private ';
		}
	}

	/**
	 * Get code for static
	 *
	 * @param boolean $static
	 * @return string
	 */
	public function getCode4Static($static)
	{
		return ($static) ? 'static ' : null;
	}

	/**
	 * Return method code
	 *
	 * @param Method $method
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCode4Method(Method $method, $tabs = 1, $endOfLines = 1)
	{
		$return = $this->getCode4PHPDocComments($method->getPHPDocParts(), $tabs, 1);

		$declaration = null;

		// visibility
		$declaration .= $this->getCode4Visibility($method->getVisibility());

		// static
		$declaration .= $this->getCode4Static($method->isStatic());

		$declaration .= 'function ' . $method->getName() . '(';
		// parameters
		$paramsDeclaration = array();
		foreach ($method->getParameters() as $parameter) {
			$paramDeclaration = null;
			if ($parameter->getForceType()) {
				$paramDeclaration .= $parameter->getType() . ' ';
			}
			$paramDeclaration .= '$' . $parameter->getName();
			if ($parameter->getDefaultValue() !== null) {
				$paramDeclaration .= ' = ' . $parameter->getDefaultValue();
			}
			$paramsDeclaration[] = $paramDeclaration;
		}
		$declaration .= implode(', ', $paramsDeclaration);
		$declaration .= ')';

		$return .= $this->getCode4Line($declaration, $tabs, 1);
		$return .= $this->getCode4Line('{', $tabs, 1, $endOfLines);

		foreach ($method->getLines() as $line) {
			$return .= $this->getCode4Line($line, 2);
		}

		$return .= $this->getcode4Line('}', 1);

		return $return;
	}

	/**
	 * Return code for the end of a method
	 *
	 * @param int $tabs Number of tabs, null to use internal counter
	 * @param int $endOfLines Number of end line character to add
	 * @return string
	 */
	public function getEndCode4Method($tabs = 1, $endOfLines = 1)
	{
		return $this->getCode4Line('}', $tabs, $endOfLines);
	}

	/**
	 * Return code for a property declaration
	 *
	 * @param ClassProperty $property
	 * @param int $tabs Number of tabs, null to use internal counter
	 * @param int $endOfLines Number of end line character to add
	 * @return string
	 */
	public function getCode4Property(ClassProperty $property, $tabs = 1, $endOfLines = 1)
	{
		$return = null;

		// phpdoc
		$comments = array('comments' => $property->getComments());
		if ($property->getType() !== null) {
			$comments['@var'] = array('type' => $property->getType());
		}
		$return .= $this->getCode4PHPDocComments($comments, $tabs);

		// php declaration
		$declaration = $this->getCode4Visibility($property->getVisibility());
		$declaration .= $this->getCode4Static($property->isStatic());
		$declaration .= (substr($property->getName(), 0, 1) == '$') ? $property->getName() : '$' . $property->getName();
		if ($property->getDefaultValue() !== null) {
			$declaration .= ' = ' . $property->getDefaultValue();
		}
		$declaration .= ';';
		$return .= $this->getCode4Line($declaration, $tabs, $endOfLines);

		return $return;
	}
}
