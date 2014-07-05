<?php

namespace steevanb\CodeGenerator\PHP;

use steevanb\CodeGenerator\Core\Generator;
use steevanb\CodeGenerator\PHP\Generator as PHPGenerator;

/**
 * PHP class generator
 */
class ClassGenerator extends Generator
{

    use PHPGenerator;
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
     * @var array
     */
    protected $properties = array();

    /**
     * Current opened method
     *
     * @var pointer
     */
    protected $currentMethod;

    /**
     * @var array
     */
    protected $methods = array();

    /**
     * @TODO
     *
     * @var boolean
     */
    protected $extractUses = true;

    /**
     * @var array
     */
    protected $uses = array();

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
     * @param string $use
     * @return string
     */
    public function addUse($use)
    {
        // className already added
        if (array_key_exists($use, $this->uses)) {
            return $this->uses[$use];
        }

        // new className
        $class = basename($use);
        if (in_array($class, $this->uses)) {
            $class = uniqd($class);
        }
        $this->uses[$use] = $class;
        return $class;
    }

    /**
     * Define all classes to use
     *
     * @param array $uses
     * @return $this
     */
    public function setUses(array $uses)
    {
        $this->uses = $uses;
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
     * @param string $name
     * @param string $type
     * @param string  $defaultValue
     * @param int $visibility Use ClassGenerator::VISIBILITY_XXX
     * @param boolean $static
     * @param array $comments
     * @return $this
     */
    public function addProperty($name, $type = null, $defaultValue = null, $visibility = self::VISIBILITY_PRIVATE, $static = false, array $comments = array())
    {
        $this->properties[$name] = array(
            'name' => $name,
            'type' => $type,
            'defaultValue' => $defaultValue,
            'visibility' => $visibility,
            'static' => $static,
            'comments' => $comments
        );
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
     * Start a method
     * Use addMethodParameter, addMethodThrows, addMethodLine and finishMethod when it's over
     *
     * @param string $name
     * @param int $visibility Use ClassGenerator::VISIBILITY_XXX
     * @param boolean $static
     * @param array $comments
     * @param string $return
     * @return $this
     */
    public function startMethod($name, $visibility = self::VISIBILITY_PUBLIC, $static = false, array $comments = array(), $return = null)
    {
        $this->methods[$name] = array(
            'visibility' => $visibility,
            'static' => $static,
            'content' => array(),
            'parameters' => array(),
            'throws' => array(),
            'comments' => $comments,
            'return' => $return
        );
        $this->currentMethod = &$this->methods[$name];
        return $this;
    }

    /**
     * Add a parameter to the current open method
     *
     * @param string $name
     * @param string $type
     * @param string  $defaultValue
     * @param boolean $forceType Indicate if type will be added in method declaration, like MyType $myParam
     * @param string $comment
     * @return $this
     */
    public function addMethodParameter($name, $type, $defaultValue = null, $forceType = false, $comment = null)
    {
        $this->currentMethod['parameters'][$name] = array(
            'type' => $type,
            'comment' => $comment,
            'defaultValue' => $defaultValue,
            'forceType' => $forceType
        );
        return $this;
    }

    /**
     * Add a line to the current open method
     *
     * @param string $line
     * @param int $endOfLines
     * @throws MethodNotStarted
     * @return $this
     */
    public function addMethodLine($line, $endOfLines = 1)
    {
        if (is_array($this->currentMethod) === false) {
            throw new MethodNotStarted('Method not started, call startMethod() first, and finish it with finishMethod().');
        }
        $this->currentMethod['content'][] = $line;
        for ($x = 0; $x < $endOfLines - 1; $x++) {
            $this->currentMethod['content'][] = null;
        }
        return $this;
    }

    /**
     * Add an exception throwned by the current open method
     *
     * @param string $throws
     * @return $this
     */
    public function addMethodThrows($throws)
    {
        if (in_array($throws, $this->currentMethod['throws']) === false) {
            $this->currentMethod['throws'][] = $throws;
        }
        return $this;
    }

    /**
     * Finish current open method
     *
     * @return $this
     */
    public function finishMethod()
    {
        $this->currentMethodContent = false;
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
     * Write PHP code to file
     *
     * @param string $fileName
     */
    public function write($fileName)
    {
        $countProperties = count($this->getProperties());
        $countMethods = count($this->getMethods());

        $content = $this->getCode4Line('<?php', 0, 2);

        // namespace
        if ($this->getNamespace() != null) {
            $content .= $this->getCode4Namespace($this->getNamespace(), 0, 2);
        }

        // uses
        if ($this->getExtractUses()) {
            $this->setExtends($this->addUse($this->getExtends()));
            foreach ($this->interfaces as &$interface) {
                $interface = $this->addUse($interface);
            }
            foreach ($this->traits as &$trait) {
                $trait = $this->addUse($trait);
            }
        }
        $content .= $this->getCode4Uses($this->getUses(), 0, 2);

        // class declaration
        $content .= $this->getStartCode4Class($this->getClassName(), $this->getExtends(), $this->getInterfaces(), $this->getTraits());

        // properties
        $indexProperties = 0;

        foreach ($this->getProperties() as $property) {
            $content .= $this->getCode4Property($property);

            if ($indexProperties < $countProperties - 1) {
                $content .= $this->getEndOfLines();
            }

            $indexProperties++;
        }
        if ($countProperties > 0 && $countMethods > 0) {
            $content .= $this->getEndOfLines();
        }

        // methods
        $indexMethods = 0;
        foreach ($this->getMethods() as $name => $infos) {
            $content .= $this->getStartCode4Method($name, $infos['parameters'], $infos['return'], $infos['visibility'], $infos['static'], $infos['throws'], $infos['comments']);
            foreach ($infos['content'] as $line) {
                $content .= $this->getCode4Line($line, 2, 1);
            }
            $content .= $this->getEndCode4Method();
            if ($indexMethods < $countMethods - 1) {
                $content .= $this->getEndOfLines();
            }
            $indexMethods++;
        }


        $content .= $this->getEndCode4Class();

        file_put_contents($fileName, $content);
    }
}