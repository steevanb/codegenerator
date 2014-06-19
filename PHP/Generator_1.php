<?php

namespace steevanb\CodeGenerator\PHP;

use steevanb\CodeGenerator\Core\Generator as BaseGenerator;

/**
 * PHP generator
 */
abstract class Generator extends BaseGenerator
{

    /**
     * PHP generated content
     *
     * @var string
     */
    protected $content;

    /**
     * Internal tabs count
     *
     * @var int
     */
    protected $tabs = 0;

    /**
     * Indicate if we need to close PHP tag
     * @var type
     */
    protected $endPHPTag = false;

    /**
     * Define if we need to close PHP tag
     *
     * @param boolean $endPHPTag
     * @return PHP
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
     * Add a line
     *
     * @param string $line
     * @param int $tabs Number of tabs, null to use internal counter
     * @param int $endLine Number of end line character to add
     * @return PHP
     */
    public function addLine($line, $tabs = null, $endLine = 1)
    {
        $this->content .= $this->_getTabs($tabs) . $line . $this->_getEndOfLines($endLine);
        return $this;
    }

    /**
     * Add a PHP comment
     *
     * @param string|array $comment Comment(s), could be a string or an array of comment
     * @param int $tabs Number of tabs, null to use internal counter
     * @param int $endLine Number of end line character to add
     * @return PHP
     */
    public function addComment($comment, $tabs = null, $endLine = 1)
    {
        if (is_array($comment) == false) {
            $comment = array($comment);
        }
        foreach ($comment as $line) {
            $this->addLine('// ' . $line, $tabs, 1);
        }
        $this->addEmptyLine($endLine - 1);
        return $this;
    }

    /**
     * Add a PHP Doc comment block
     *
     * @param string|array $comment Comment(s), could be a string or an array of comment
     * @param int $tabs Number of tabs, null to use internal counter
     * @param int $endLine Number of end line character to add
     * @return PHP
     */
    public function addPHPDocComment($comment, $tabs = null, $endLine = 1)
    {
        if (is_array($comment) == false) {
            $comment = array($comment);
        }
        $this->addLine('/**', $tabs, 1);
        foreach ($comment as $line) {
            $this->addLine(' * ' . $line, $tabs, 1);
        }
        $this->addLine(' */', $tabs, $endLine);
        return $this;
    }

    /**
     * Increment internal tabulations counter
     *
     * @param int $count Number of tabulations to increment
     * @return PHP
     */
    public function incTab($count = 1)
    {
        $this->tabs += $count;
        return $this;
    }

    /**
     * Decrement internal tabulations counter
     *
     * @param int $count Number of tabulations to decrement
     * @return PHP
     */
    public function decTab($count = 1)
    {
        $this->tabs = max($this->tabs - $count, 0);
        return $this;
    }

    /**
     * Reset internal tabulations counter
     *
     * @return PHP
     */
    public function resetTab()
    {
        $this->tabs = 0;
        return $this;
    }

    /**
     * Add empty line
     *
     * @param int $count Number of empty lines to add
     * @return PHP
     */
    public function addEmptyLine($count = 1)
    {
        $this->content .= $this->_getEndLines($count);
        return $this;
    }

    public function writeInBundle($bundle, $fileName)
    {

    }
}