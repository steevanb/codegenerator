<?php

namespace steevanb\CodeGenerator\PHP;

/**
 * PHP generator
 */
trait Generator
{
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
            $return .= $this->getLine4Code('// ' . $comment, $tabs, $eol);
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
            $comments = array($comments);
        }
        $return = $this->getCode4Line('/**', $tabs, 1);
        foreach ($comments as $comment) {
            $return .= $this->getCode4Line(' * ' . $comment, $tabs, 1);
        }
        $return .= $this->getCode4Line(' */', $tabs, $endOfLines);
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
     * @param int $tabs Number of tabs, null to use internal counter
     * @param int $endOfLines Number of end line character to add
     * @return string
     */
    public function getCode4Uses(array $uses, $tabs = 0, $endOfLines = 2)
    {
        $usesAs = array();
        foreach ($uses as $namespace => $class) {
            $usesAs[] = ($class != null && basename($namespace) != $class) ? $namespace . ' as ' . $class : $namespace;
        }

        $return = null;
        if ($this->getConcatUses()) {
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
     * Return code to start a method
     *
     * @param string $name
     * @param array $parameters
     * @param string $return
     * @param int $visibility
     * @param boolean $static
     * @param array $throws
     * @param array $comments
     * @param int $tabs Number of tabs, null to use internal counter
     * @param int $endOfLines Number of end line character to add
     * @return string
     */
    public function getStartCode4Method($name, array $parameters = array(), $return = null, $visibility = self::VISIBILITY_PUBLIC, $static = false, array $throws = array(), array $comments = array(), $tabs = 1, $endOfLines = 1)
    {
        $returnStr = null;
        // if a phpdoc is required
        if (count($parameters) > 0 || $return !== null || count($throws) > 0 || count($comments) > 0) {
            $phpDoc = array();

            // comments
            if (count($comments) > 0) {
                $phpDoc = array_merge($phpDoc, $comments);
                $phpDoc[] = null;
            }

            // parameters
            foreach ($parameters as $paramName => $infos) {
                $paramStr = '@param ' . $infos['type'] . ' ' . $paramName;
                if ($infos['comment'] != null) {
                    $paramStr .= ' ' . $infos['comment'];
                }
                $phpDoc[] = $paramStr;
            }

            // exceptions
            foreach ($throws as $throw) {
                $phpDoc[] = '@throws ' . $throw;
            }

            // return
            if ($return !== null) {
                $phpDoc[] = '@return ' . $return;
            }

            $returnStr .= $this->getCode4PHPDocComments($phpDoc, $tabs, 1);
        }

        $declaration = null;

        // visibility
        $declaration .= $this->getCode4Visibility($visibility);

        // static
        $declaration .= $this->getCode4Static($static);

        $declaration .= 'function ' . $name . '(';
        // parameters
        $paramsDeclaration = array();
        foreach ($parameters as $paramName => $infos) {
            $paramDeclaration = null;
            if ($infos['forceType']) {
                $paramDeclaration .= $infos['type'] . ' ';
            }
            $paramDeclaration .= $paramName;
            if ($infos['defaultValue'] !== null) {
                $paramDeclaration .= ' = ' . $infos['defaultValue'];
            }
            $paramsDeclaration[] = $paramDeclaration;
        }
        $declaration .= implode(', ', $paramsDeclaration);
        $declaration .= ')';

        $returnStr .= $this->getCode4Line($declaration, $tabs, 1);
        $returnStr .= $this->getCode4Line('{', $tabs, 1, $endOfLines);

        return $returnStr;
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
     * @param array $property
     * @param int $tabs Number of tabs, null to use internal counter
     * @param int $endOfLines Number of end line character to add
     * @return string
     */
    public function getCode4Property($property, $tabs = 1, $endOfLines = 1)
    {
        $return = null;

        // phpdoc
        $comments = array();
        if (count($property['comments']) > 0) {
            $comments = array_merge($comments, $property['comments']);
        }
        if ($property['type'] !== null) {
            if (count($property['comments']) > 0) {
                $comments[] = null;
            }
            $comments[] = '@var ' . $property['type'];
        }
        $return .= $this->getCode4PHPDocComments($comments, $tabs);

        // php declaration
        $declaration = $this->getCode4Visibility($property['visibility']);
        $declaration .= $this->getCode4Static($property['static']);
        $declaration .= '$' . $property['name'];
        if ($property['defaultValue'] !== null) {
            $declaration .= ' = ' . $property['defaultValue'];
        }
        $declaration .= ';';
        $return .= $this->getCode4Line($declaration, $tabs, $endOfLines);

        return $return;
    }
}