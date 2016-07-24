<?php

namespace steevanb\CodeGenerator\Php\Behavior;

use steevanb\CodeGenerator\AbstractGenerator;
use steevanb\CodeGenerator\Php\ClassGenerator\Method;
use steevanb\CodeGenerator\Php\ClassGenerator\Property;
use steevanb\CodeGenerator\Php\PhpDoc;

trait CodeTrait
{
    /**
     * @param string $line
     * @param int $tabs
     * @param int $endOfLines
     * @return string
     */
    abstract public function getCodeForLine($line, $tabs = 0, $endOfLines = 1);

    /**
     * @param int $count
     * @return string
     */
    abstract public function getCodeForEndOfLines($count = 1);

    /**
     * @param int $count
     * @return string
     */
    abstract public function getCodeForTabs($count = 1);

    /**
     * @return bool
     */
    abstract public function getConcatTraits();

	/**
	 * @param string $comment
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCodeForComment($comment, $tabs = 0, $endOfLines = 1)
	{
		return $this->getCodeForComments(array($comment), $tabs, $endOfLines);
	}

	/**
	 * @param string|array $comments
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCodeForComments($comments, $tabs = 0, $endOfLines = 1)
	{
		if (is_array($comments) === false) {
			$comments = [$comments];
		}
		$return = null;
		foreach ($comments as $index => $comment) {
			$eol = ($index < count($comments) - 1) ? 1 : 0;
			$return .= $this->getCodeForLine('// ' . $comment, $tabs, $eol);
		}
		$return .= $this->getCodeForEndOfLines($endOfLines);

		return $return;
	}

	/**
	 * @param string|array $comments
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCodeForPhpDocComments($comments, $tabs = 0, $endOfLines = 1)
	{
		if (is_array($comments) === false) {
			$comments = ['comments' => $comments];
		}
		$return = null;
		$phpDocs = PhpDoc::asString($comments);
		foreach ($phpDocs as $phpDoc) {
			$return .= $this->getCodeForLine($phpDoc, $tabs);
		}
		$return .= $this->getCodeForEndOfLines($endOfLines - 1);

		return $return;
	}

	/**
	 * @param string $namespace
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCodeForNamespace($namespace, $tabs = 0, $endOfLines = 1)
	{
		return $this->getCodeForLine('namespace ' . $namespace . ';', $tabs, $endOfLines);
	}

	/**
	 * @param array $uses
	 * @param boolean $concat
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCodeForUses(array $uses, $concat = true, $tabs = 0, $endOfLines = 2)
	{
		$usesAs = array();
		foreach ($uses as $namespace => $class) {
			$posBackSlash = strrpos($namespace, '\\');
            if ($posBackSlash !== false && substr($namespace, $posBackSlash + 1) != $class) {
                $usesAs[] = $namespace . ' as ' . $class;
            } else {
                $usesAs[] = $namespace;
            }
		}

		$return = null;
		if ($concat) {
			$tabsStr = $this->getCodeForTabs($tabs);
			$return .= $tabsStr . 'use ' . implode(',' . $this->getCodeForEndOfLines() . $tabsStr . '    ', $usesAs) . ';';
            $return .= $this->getCodeForEndOfLines();
		} else {
			foreach ($usesAs as $useAs) {
				$return .= $this->getCodeForLine('use ' . $useAs . ';', $tabs, 1);
			}
		}
		if (count($uses) > 0 && $endOfLines > 1) {
			$return .= $this->getCodeForEndOfLines($endOfLines - 1);
		}

		return $return;
	}

	/**
	 * @param array $traits
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCodeForTraits(array $traits, $tabs = 1, $endOfLines = 2)
	{
		$return = null;

		if ($this->getConcatTraits()) {
			$tabsStr = $this->getCodeForTabs($tabs);
            $glue = ',' . $this->getCodeForEndOfLines() . $tabsStr . '    ';
			$return .= $tabsStr . 'use ' . implode($glue, $traits) . ';' . $this->getCodeForEndOfLines();
		} else {
			foreach ($traits as $trait) {
				$return .= $this->getCodeForLine('use ' . $trait . ';', $tabs);
			}
		}
		if (count($traits) > 0 && $endOfLines > 1) {
			$return .= $this->getCodeForEndOfLines($endOfLines - 1);
		}

		return $return;
	}

	/**
	 * @param string $className
	 * @param string $extends
	 * @param array $interfaces
	 * @param array $traits
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCodeForStartClass(
        $className,
        $extends = null,
        array $interfaces = array(),
        array $traits = array(),
        $tabs = 0,
        $endOfLines = 1
    ) {
		$declaration = 'class ' . $className;

		if ($extends != null) {
			$declaration .= ' extends ' . $extends;
		}

		if (count($interfaces) > 0) {
			$declaration .= ' implements ' . implode(', ', $interfaces);
		}

		$return = $this->getCodeForLine($declaration, $tabs, 1);
		$return .= $this->getCodeForLine('{', $tabs, 1);

		if (count($traits) > 0) {
			$return .= $this->getCodeForTraits($traits, $tabs + 1, 2);
		}

		$return .= $this->getCodeForEndOfLines($endOfLines - 1);

		return $return;
	}

	/**
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCodeForEndClass($tabs = 0, $endOfLines = 0)
	{
		return $this->getCodeForLine('}', $tabs, $endOfLines);
	}

    /**
     * @param int $visibility
     * @return string
     * @throws \Exception
     */
	public function getCodeForVisibility($visibility)
	{
		switch ($visibility) {
			case AbstractGenerator::VISIBILITY_PUBLIC:
				return 'public ';
			case AbstractGenerator::VISIBILITY_PROTECTED:
				return 'protected ';
			case AbstractGenerator::VISIBILITY_PRIVATE:
				return 'private ';
            default:
                throw new \Exception('Visibility "' . $visibility . '" does not exists.');
		}
	}

	/**
	 * @param boolean $static
	 * @return string
	 */
	public function getCodeForStatic($static)
	{
		return ($static) ? 'static ' : null;
	}

	/**
	 * @param Method $method
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCodeForMethod(Method $method, $tabs = 1, $endOfLines = 1)
	{
		$return = $this->getCodeForPhpDocComments($method->getPhpDocParts(), $tabs, 1);

		$declaration = $this->getCodeForVisibility($method->getVisibility());
		$declaration .= $this->getCodeForStatic($method->isStatic());
		$declaration .= 'function ' . $method->getName() . '(';
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

		$return .= $this->getCodeForLine($declaration, $tabs, 1);
		$return .= $this->getCodeForLine('{', $tabs, 1);

		foreach ($method->getLines() as $line) {
			$return .= $this->getCodeForLine($line, 2);
		}

		$return .= $this->getCodeForEndMethod($tabs, $endOfLines);

		return $return;
	}

	/**
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCodeForEndMethod($tabs = 1, $endOfLines = 1)
	{
		return $this->getCodeForLine('}', $tabs, $endOfLines);
	}

	/**
	 * @param Property $property
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCodeForProperty(Property $property, $tabs = 1, $endOfLines = 1)
	{
		$return = null;

		$comments = array('comments' => $property->getComments());
		if ($property->getType() !== null) {
			$comments['@var'] = array('type' => $property->getType());
		}
		$return .= $this->getCodeForPhpDocComments($comments, $tabs);

		$declaration = $this->getCodeForVisibility($property->getVisibility());
		$declaration .= $this->getCodeForStatic($property->isStatic());
		$declaration .= (substr($property->getName(), 0, 1) == '$') ? $property->getName() : '$' . $property->getName();
		if ($property->getDefaultValue() !== null) {
			$declaration .= ' = ' . $property->getDefaultValue();
		}
		$declaration .= ';';
		$return .= $this->getCodeForLine($declaration, $tabs, $endOfLines);

		return $return;
	}
}
