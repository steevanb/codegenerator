<?php

namespace steevanb\CodeGenerator\Php\ClassGenerator;

use steevanb\CodeGenerator\AbstractGenerator;
use steevanb\CodeGenerator\Exception\ClassNameExistsException;
use steevanb\CodeGenerator\Php\Behavior\CodeTrait;

class ClassGenerator extends AbstractGenerator
{
	use CodeTrait;

	/** @var string */
	protected $className;

	/** @var string */
	protected $namespace;

	/** @var string */
	protected $extends;

	/** @var array */
	protected $interfaces = array();

	/** @var array */
	protected $traits = array();

	/** @var Property[] */
	protected $properties = array();

	/** @var Method */
	protected $currentMethod;

	/** @var Method[] */
	protected $methods = array();

	/** @var boolean */
	protected $extractUses = true;

	/** @var array */
	protected $uses = array();

	/** @var bool */
	protected $endPhpTag = false;

	/** @var boolean */
	protected $concatUses = false;

	/** @var boolean */
	protected $concatTraits = false;

	/**
	 * @param string|null $name
	 */
	public function __construct($name = null)
	{
		$this->setClassName($name);
	}

	/**
	 * @param bool $endPhpTag
	 * @return $this
	 */
	public function setEndPhpTag($endPhpTag)
	{
		$this->endPhpTag = $endPhpTag;

		return $this;
	}

	/**
	 * Indicate if we need to close PHP tag
	 *
	 * @return boolean
	 */
	public function getEndPhpTag()
	{
		return $this->endPhpTag;
	}

	/**
	 * @param bool $concat
	 * @return $this
	 */
	public function setConcatUses($concat)
	{
		$this->concatUses = $concat;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getConcatUses()
	{
		return $this->concatUses;
	}

	/**
	 * @param bool $concat
	 * @return $this
	 */
	public function setConcatTraits($concat)
	{
		$this->concatTraits = $concat;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getConcatTraits()
	{
		return $this->concatTraits;
	}

	/**
	 * @param bool $extractUses
	 * @return $this
	 */
	public function setExtractUses($extractUses)
	{
		$this->extractUses = $extractUses;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getExtractUses()
	{
		return $this->extractUses;
	}

	/**
	 * @param string $namespace
	 * @return $this
	 */
	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @param string $fullyQualifiedClassName
	 * @param string $className
	 * @return string
     * @throws ClassNameExistsException
	 */
	public function addUse($fullyQualifiedClassName, $className = null)
	{
		// already added
		if (array_key_exists($fullyQualifiedClassName, $this->uses)) {
			return $this->uses[$fullyQualifiedClassName];
		}

		// new className
		if ($className === null) {
			$posBackSlash = strrpos($fullyQualifiedClassName, '\\');
			if ($posBackSlash !== false) {
				$class = substr($fullyQualifiedClassName, $posBackSlash + 1);
			} else {
				$class = $fullyQualifiedClassName;
			}
		} else {
			$class = $className;
		}
		if (in_array($class, $this->uses)) {
			throw new ClassNameExistsException('Class "' . $class . '" already added, change his name via keyword "as".');
		}
		$this->uses[$fullyQualifiedClassName] = $class;

		return $this->uses[$fullyQualifiedClassName];
	}

	/**
	 * @param array $uses
	 * @return $this
	 */
	public function setUses(array $uses)
	{
		$this->clearUses();
		foreach ($uses as $use) {
			$this->addUse($use);
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearUses()
	{
		$this->uses = array();

		return $this;
	}

	/**
	 * @return array
	 */
	public function getUses()
	{
		return $this->uses;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setClassName($name)
	{
		$this->className = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getClassName()
	{
		return $this->className;
	}

	/**
	 * @return string
	 */
	public function getFullyQualifiedClassName()
	{
		$return = null;
		if ($this->getNamespace() != null) {
			$return .= $this->getNamespace() . '\\';
		}
		$return .= $this->getClassName();

		return $return;
	}

	/**
	 * @param string $extends
	 * @return $this
	 */
	public function setExtends($extends)
	{
		$this->extends = $extends;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getExtends()
	{
		return $this->extends;
	}

	/**
	 * @param array $interfaces
	 * @return $this
	 */
	public function setInterfaces(array $interfaces)
	{
		$this->interfaces = $interfaces;

		return $this;
	}

	/**
	 * @param string $interface
	 * @return $this
	 */
	public function addInterface($interface)
	{
		if (in_array($interface, $this->interfaces) === false) {
			$this->interfaces[] = $interface;
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearInterfaces()
	{
		$this->interfaces = array();

		return $this;
	}

	/**
	 * @return array
	 */
	public function getInterfaces()
	{
		return $this->interfaces;
	}

	/**
	 * @param array $traits
	 * @return $this
	 */
	public function setTraits(array $traits)
	{
		$this->traits = $traits;

		return $this;
	}

	/**
	 * @param string $trait
	 * @return $this
	 */
	public function addTrait($trait)
	{
		if (in_array($trait, $this->traits) === false) {
			$this->traits[] = $trait;
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearTraits()
	{
		$this->traits = array();

		return $this;
	}

	/**
	 * @return array
	 */
	public function getTraits()
	{
		return $this->traits;
	}

	/**
	 * @param Property $property
	 * @return $this
	 */
	public function addProperty(Property $property)
	{
		$this->properties[$property->getName()] = $property;

		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearProperties()
	{
		$this->properties = array();

		return $this;
	}

	/**
	 * @return Property[]
	 */
	public function getProperties()
	{
		return $this->properties;
	}

	/**
	 * @param Method $method
	 * @return $this
	 */
	public function addMethod(Method $method)
	{
		$this->methods[] = $method;

		return $this;
	}

	/**
	 * @return Method[]
	 */
	public function getMethods()
	{
		return $this->methods;
	}

	/**
	 * @return string
	 */
	public function getCode()
	{
		$countProperties = count($this->getProperties());
		$countMethods = count($this->getMethods());

		$return = $this->getCodeForLine('<?php', 0, 2);

		// namespace
		if ($this->getNamespace() != null) {
			$return .= $this->getCodeForNamespace($this->getNamespace(), 0, 2);
		}

		// extract uses
		if ($this->getExtractUses()) {
			// extends
			if ($this->getExtends() !== null && strpos($this->getExtends(), '\\') !== false) {
				$extends = $this->addUse($this->getExtends());
				$this->setExtends($extends);
			}

			// interfaces
			foreach ($this->interfaces as $interface) {
				if (strpos($interface, '\\') !== false) {
					$this->addUse($interface);
				}
			}

			// traits
			foreach ($this->traits as $trait) {
				if (strpos($trait, '\\') !== false) {
					$this->addUse($trait);
				}
			}

			// properties
			foreach ($this->getProperties() as $property) {
				if (strpos($property->getType(), '\\') !== false) {
					$property->setType($this->addUse($property->getType()));
				}
			}

			// methods
			foreach ($this->getMethods() as $method) {
				// parameters
				foreach ($method->getParameters() as $parameter) {
					if (strpos($parameter->getType(), '\\') !== false) {
						$parameter->setType($this->addUse($parameter->getType()));
					}
				}

				// return
				if (strpos($method->getReturn(), '\\') !== false) {
					$method->setReturn($this->addUse($method->getReturn()));
				}
			}
		}

		// uses
		$return .= $this->getCodeForUses($this->getUses(), $this->getConcatUses(), 0, 2);

		// class declaration
		$return .= $this->getCodeForStartClass(
            $this->getClassName(),
            $this->getExtends(),
            $this->getInterfaces(),
            $this->getTraits()
        );

		// properties
		$indexProperties = 0;
		foreach ($this->getProperties() as $property) {
			$return .= $this->getCodeForProperty($property);
			if ($indexProperties < $countProperties - 1) {
				$return .= $this->getCodeForEndOfLines();
			}

			$indexProperties++;
		}
		if ($countProperties > 0 && $countMethods > 0) {
			$return .= $this->getCodeForEndOfLines();
		}

		// methods
		$indexMethods = 0;
		foreach ($this->getMethods() as $method) {
			$return .= $this->getCodeForMethod($method);
			if ($indexMethods < $countMethods - 1) {
				$return .= $this->getCodeForEndOfLines();
			}
			$indexMethods++;
		}

		$return .= $this->getCodeForEndClass();

		return $return;
	}

	/**
	 * @param string $fileName
     * @return $this
	 */
	public function write($fileName)
	{
		$code = $this->getCode();
		$this->createDir(dirname($fileName));
		file_put_contents($fileName, $code);

        return $this;
	}
}
