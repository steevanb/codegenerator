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
    protected $className;
    protected $namespace;
    protected $extends;
    protected $interfaces = array();
    protected $traits = array();
    protected $properties = array();
    protected $currentMethod;
    protected $methods = array();
    protected $extractUses = true;
    protected $uses = array();

    public function setExtractUses($extractUses)
    {
        $this->extractUses = $extractUses;
        return $this;
    }

    public function getExtractUses()
    {
        return $this->extractUses;
    }

    public function setNamespace($name)
    {
        $this->namespace = $name;
        return $this;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

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

    public function setUses(array $uses)
    {
        $this->uses = $uses;
    }

    public function clearUses()
    {
        $this->uses = array();
    }

    public function getUses()
    {
        return $this->uses;
    }

    public function setClassName($name)
    {
        $this->className = $name;
        return $this;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function setExtends($extends)
    {
        $this->extends = $extends;
        return $this;
    }

    public function getExtends()
    {
        return $this->extends;
    }

    public function setInterfaces($interfaces)
    {
        $this->interfaces = $interfaces;
    }

    public function addInterface($interface)
    {
        if (in_array($interface, $this->interfaces) == false) {
            $this->interfaces[] = $interface;
        }
        return $this;
    }

    public function clearInterfaces()
    {
        $this->interfaces = array();
        return $this;
    }

    public function getInterfaces()
    {
        return $this->interfaces;
    }

    public function setTraits(array $traits)
    {
        $this->traits = $traits;
    }

    public function addTrait($trait)
    {
        if (in_array($trait, $this->traits) == false) {
            $this->traits[] = $trait;
        }
        return $this;
    }

    public function clearTraits()
    {
        $this->traits = array();
        return $this;
    }

    public function getTraits()
    {
        return $this->traits;
    }

    public function addProperty($name, $defaultValue = null, $visibility = self::VISIBILITY_PRIVATE, $static = false)
    {
        $this->properties[$name] = array(
            'defaultValue' => $defaultValue,
            'visibility' => $visibility,
            'static' => $static
        );
        return $this;
    }

    public function cleanProperties()
    {
        $this->properties = array();
        return $this;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function startMethod($name, $visibility = self::VISIBILITY_PUBLIC, $static = false, array $comments = array())
    {
        $this->methods[$name] = array(
            'visibility' => $visibility,
            'static' => $static,
            'content' => array(),
            'parameters' => array(),
            'return' => null,
            'throws' => array(),
            'comments' => $comments
        );
        $this->currentMethod = &$this->methods[$name];
        return $this;
    }

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

    public function setMethodReturn($type)
    {
        $this->currentMethod['return'] = $type;
        return $this;
    }

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

    public function addMethodThrows($throws)
    {
        if (in_array($throws, $this->currentMethod['throws']) === false) {
            $this->currentMethod['throws'][] = $throws;
        }
        return $this;
    }

    public function finishMethod()
    {
        $this->currentMethodContent = null;
        return $this;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function write($fileName)
    {
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
        // methods
        $countMethods = count($this->getMethods());
        $index = 0;
        foreach ($this->getMethods() as $name => $infos) {
            $content .= $this->getStartCode4Method($name, $infos['parameters'], $infos['return'], $infos['visibility'], $infos['static'], $infos['throws'], $infos['comments']);
            foreach ($infos['content'] as $line) {
                $content .= $this->getCode4Line($line, 2, 1);
            }
            $content .= $this->getEndCode4Method();
            if ($index < $countMethods - 1) {
                $content .= $this->getEndOfLines();
            }
            $index++;
        }


        $content .= $this->getEndCode4Class();

        file_put_contents($fileName, $content);
    }
}