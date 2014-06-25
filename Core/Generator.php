<?php

namespace steevanb\CodeGenerator\Core;

/**
 * Class to extends to create a generator
 */
abstract class Generator
{

    /**
     * Tabulation character
     *
     * @var string
     */
    protected $tabStr = true;

    /**
     * Write content in $fileName
     *
     * @param string $fileName
     */
    abstract public function write($fileName);

    /**
     * Define if tabulations are replaced by 4 spaces
     *
     * @param boolean $tabsAsSpaces
     * @return PHP
     */
    public function setTabsAsSpaces($tabsAsSpaces)
    {
        $this->tabStr = ($tabsAsSpaces) ? '    ' : "\t";
        return $this;
    }

    /**
     * Indicate if tabulations are replaced by 4 spaces
     *
     * @return boolean
     */
    public function getTabsAsSpaces()
    {
        return ($this->tabStr == "\t");
    }

    /**
     * Return tag string $count times
     *
     * @param int $count Number of time to repeat tab string
     * @return string
     */
    public function getTabs($count = 1)
    {
        return str_repeat($this->tabStr, $count);
    }

    /**
     * Return end of line string $count times
     *
     * @param int $count Number of times to repeat end of line string
     * @return type
     */
    public function getEndOfLines($count = 1)
    {
        return str_repeat(PHP_EOL, $count);
    }

    /**
     * Returne line of code for $line
     *
     * @param string $line Line of code
     * @param int $tabs Number of time to repeat tab string
     * @param int $endOfLines Number of times to repeat end of line character
     * @return type
     */
    public function getCode4Line($line, $tabs = null, $endOfLines = 1)
    {
        return $this->getTabs($tabs) . $line . $this->getEndOfLines($endOfLines);
    }
}