<?php

namespace steevanb\CodeGenerator\PHP;

use steevanb\CodeGenerator\Core\Generator;
use steevanb\CodeGenerator\Exception\ClassNameExists;

/**
 * PHP class generator
 */
class ClassGenerator extends Generator
{

	use Code;
	/**
	 * @var string
	 */
	protected $className;

	/**
	 * @var string
	 */
	protected $namespace;

	/**
	 * @var string
	 */
	protected $extends;

	/**
	 * @var array
	 */
	protected $interfaces = array();

	/**
	 * @var array
	 */
	protected $traits = array();

	/**
	 * @var ClassProperty[]
	 */
	protected $properties = array();

	/**
	 * Current opened method
	 *
	 * @var pointer
	 */
	protected $currentMethod;

	/**
	 * @var Method[]
	 */
	protected $methods = array();

	/**
	 * @var boolean
	 */
	protected $extractUses = true;

	/**
	 * @var array
	 */
	protected $uses = array();

	/**
	 * Indicate if we need to close PHP tag
	 *
	 * @var type
	 */
	protected $endPHPTag = false;

	/**
	 * Indicate if uses will be concatened (use Foo, Bar;) or if we will have one line per uses (use Foo; use Bar;)
	 *
	 * @var boolean
	 */
	protected $concatUses = false;

	/**
	 * Indicate if traits will be concatened (use Foo, Bar;) or if we will have one line per uses (use Foo; use Bar;)
	 *
	 * @var boolean
	 */
	protected $concatTraits = false;

	/**
	 * Constructor
	 *
	 * @param string $name
	 */
	public function __construct($name = null)
	{
		$this->setClassName($name);
	}

	/**
	 * Define if we need to close PHP tag
	 *
	 * @param boolean $endPHPTag
	 * @return $this
	 */
	public function setEndPHPTag($endPHPTag)
	{
		$this->endPHPTag = $endPHPTag;
		return $this;
	}

	/**
	 * Indicate if we need to close PHP tag
	 *
	 * @return boolean
	 */
	public function getEndPHPTag()
	{
		return $this->endPHPTag;
	}

	/**
	 * Define if uses will be concatened (use Foo, Bar;) or if we will have one line per uses (use Foo; use Bar;)
	 *
	 * @param boolean $concat
	 * @return $this
	 */
	public function setConcatUses($concat)
	{
		$this->concatUses = $concat;
		return $this;
	}

	/**
	 * Indicate if uses will be concatened (use Foo, Bar;) or if we will have one line per uses (use Foo; use Bar;)
	 *
	 * @return boolean
	 */
	public function getConcatUses()
	{
		return $this->concatUses;
	}

	/**
	 * Define if traits will be concatened (use Foo, Bar;) or if we will have one line per uses (use Foo; use Bar;)
	 *
	 * @param boolean $concat
	 * @return $this
	 */
	public function setConcatTraits($concat)
	{
		$this->concatTraits = $concat;
		return $this;
	}

	/**
	 * Indicate if traits will be concatened (use Foo, Bar;) or if we will have one line per uses (use Foo; use Bar;)
	 *
	 * @return boolean
	 */
	public function getConcatTraits()
	{
		return $this->concatTraits;
	}

	/**
	 * Define if we need to extract all classes and add it as use
	 *
	 * @param boolean $extractUses
	 * @return $this
	 */
	public function setExtractUses($extractUses)
	{
		$this->extractUses = $extractUses;
		return $this;
	}

	/**
	 * Get if we need to extract all classes and add it as use
	 * @return boolean
	 */
	public function getExtractUses()
	{
		return $this->extractUses;
	}

	/**
	 * Define namespace
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setNamespace($name)
	{
		$this->namespace = $name;
		return $this;
	}

	/**
	 * Get namespace
	 *
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * Add a class to use
	 *
	 * @param string $use Fully qualified class name, with namespace and class name (ex : Foo\Bar\Class)
	 * @param string $className Final class name, null if you don't want to change it
	 * @return string
	 */
	public function addUse($use, $className = null)
	{
		// already added
		if (array_key_exists($use, $this->uses)) {
			return $this->uses[$use];
		}

		// new className
		if ($className === null) {
			$posBackSlash = strrpos($use, '\\');
			if ($posBackSlash !== false) {
				$class = substr($use, $posBackSlash + 1);
			} else {
				$class = $use;
			}
		} else {
			$class = $className;
		}
		if (in_array($class, $this->uses)) {
			throw new ClassNameExists('Class "' . $class . '" already added, change his name via keyword "as".');
		}
		$this->uses[$use] = $class;
		return $this->uses[$use];
	}

	/**
	 * Define all classes to use
	 *
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
	 * Clear all uses
	 *
	 * @return $this
	 */
	public function clearUses()
	{
		$this->uses = array();
		return $this;
	}

	/**
	 * Get uses
	 *
	 * @return array
	 */
	public function getUses()
	{
		return $this->uses;
	}

	/**
	 * Define class name
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setClassName($name)
	{
		$this->className = $name;
		return $this;
	}

	/**
	 * Get class name
	 *
	 * @return string
	 */
	public function getClassName()
	{
		return $this->className;
	}

	/**
	 * Return fully qualified class name
	 *
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
	 * Define extended class
	 *
	 * @param string $extends
	 * @return $this
	 */
	public function setExtends($extends)
	{
		$this->extends = $extends;
		return $this;
	}

	/**
	 * Get extended class
	 *
	 * @return string
	 */
	public function getExtends()
	{
		return $this->extends;
	}

	/**
	 * Define interfaces
	 *
	 * @param array $interfaces
	 * @return $this
	 */
	public function setInterfaces($interfaces)
	{
		$this->interfaces = $interfaces;
		return $this;
	}

	/**
	 * Add interface used by the class
	 *
	 * @param string $interface
	 * @return $this
	 */
	public function addInterface($interface)
	{
		if (in_array($interface, $this->interfaces) == false) {
			$this->interfaces[] = $interface;
		}
		return $this;
	}

	/**
	 * Clear all interfaces
	 *
	 * @return $this
	 */
	public function clearInterfaces()
	{
		$this->interfaces = array();
		return $this;
	}

	/**
	 * Get interfaces
	 *
	 * @return array
	 */
	public function getInterfaces()
	{
		return $this->interfaces;
	}

	/**
	 * Define traits
	 *
	 * @param array $traits
	 * @return $this
	 */
	public function setTraits(array $traits)
	{
		$this->traits = $traits;
		return $this;
	}

	/**
	 * Add a trait
	 *
	 * @param string $trait
	 * @return $this
	 */
	public function addTrait($trait)
	{
		if (in_array($trait, $this->traits) == false) {
			$this->traits[] = $trait;
		}
		return $this;
	}

	/**
	 * Clear all traits
	 *
	 * @return $this
	 */
	public function clearTraits()
	{
		$this->traits = array();
		return $this;
	}

	/**
	 * Get traits
	 *
	 * @return array
	 */
	public function getTraits()
	{
		return $this->traits;
	}

	/**
	 * Add a property
	 *
	 * @param ClassProperty $property
	 * @return $this
	 */
	public function addProperty(ClassProperty $property)
	{
		$this->properties[$property->getName()] = $property;
		return $this;
	}

	/**
	 * Clear all properties
	 *
	 * @return $this
	 */
	public function clearProperties()
	{
		$this->properties = array();
		return $this;
	}

	/**
	 * Get properties
	 *
	 * @return array
	 */
	public function getProperties()
	{
		return $this->properties;
	}

	/**
	 * Add a method
	 *
	 * @param Method $method
	 * @return $this
	 */
	public function addMethod(Method $method)
	{
		$this->methods[] = $method;
		return $this;
	}

	/**
	 * Get methods
	 *
	 * @return array
	 */
	public function getMethods()
	{
		return $this->methods;
	}

	/**
	 * Return PHP generated code
	 *
	 * @return string
	 */
	public function getCode()
	{
		$countProperties = count($this->getProperties());
		$countMethods = count($this->getMethods());

		$return = $this->getCode4Line('<?php', 0, 2);

		// namespace
		if ($this->getNamespace() != null) {
			$return .= $this->getCode4Namespace($this->getNamespace(), 0, 2);
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
					$interface = $this->addUse($interface);
				}
			}

			// traits
			foreach ($this->traits as $trait) {
				if (strpos($trait, '\\') !== false) {
					$trait = $this->addUse($trait);
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
		$return .= $this->getCode4Uses($this->getUses(), $this->getConcatUses(), 0, 2);

		// class declaration
		$return .= $this->getStartCode4Class($this->getClassName(), $this->getExtends(), $this->getInterfaces(), $this->getTraits());

		// properties
		$indexProperties = 0;
		foreach ($this->getProperties() as $property) {
			$return .= $this->getCode4Property($property);
			if ($indexProperties < $countProperties - 1) {
				$return .= $this->getEndOfLines();
			}

			$indexProperties++;
		}
		if ($countProperties > 0 && $countMethods > 0) {
			$return .= $this->getEndOfLines();
		}

		// methods
		$indexMethods = 0;
		foreach ($this->getMethods() as $method) {
			$return .= $this->getCode4Method($method);
			if ($indexMethods < $countMethods - 1) {
				$return .= $this->getEndOfLines();
			}
			$indexMethods++;
		}

		$return .= $this->getEndCode4Class();

		return $return;
	}

	/**
	 * Write PHP code to file
	 *
	 * @param string $fileName
	 */
	public function write($fileName)
	{
		$code = $this->getCode();
		$this->_createDir(dirname($fileName));
		file_put_contents($fileName, $code);
	}

}