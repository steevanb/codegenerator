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
        $return = '/**' . $this->getEndOfLines();
        foreach ($comments as $comment) {
            $return .= $this->getLine4Code(' * ' . $comment, $tabs, 1);
        }
        $this->addLine(' */', $tabs, $endOfLines);
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
        return $this->getCode4Line('namespace ' . $namespace, $tabs, $endOfLines);
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

    public function getCode4Traits(array $traits, $tabs = 0, $endOfLines = 2)
    {
        $return = null;
        if ($this->getConcatTraits()) {
            $tabsStr = $this->getTabs($tabs);
            $return .= $tabsStr . 'use ' . implode(',' . $this->getEndOfLines() . $tabsStr . '    ', $traits) . ';' . $this->getEndOfLines();
        } else {
            foreach ($traits as $trait) {
                $return .= $this->getCode4Line('use ' . $trait . ';', $tabs, 1);
            }
        }
        if (count($traits) > 0 && $endOfLines > 1) {
            $return .= $this->getEndOfLines($endOfLines - 1);
        }

        return $return;
    }

    public function getStartCode4Class($className, $extends = null, array $interfaces = array(), array $traits = array())
    {
        // className
        $return = 'class ' . $className;

        // extends
        if ($extends != null) {
            $return .= ' extends ' . $extends;
        }

        // interfaces
        if (count($interfaces) > 0) {
            $return .= ' implements ' . implode(', ', $interfaces);
        }

        // end of declaration
        $return .= $this->getEndOfLines();
        $return .= $this->getCode4Line('{', 0, 1);

        // traits
        if (count($traits) > 0) {
            $return .= $this->getCode4Traits($traits, 1, 2);
        }

        return $return;
    }

    public function getEndCode4Class()
    {
        return $this->getCode4Line('}', 0, 0);
    }
}