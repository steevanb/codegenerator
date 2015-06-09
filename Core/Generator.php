<?php

namespace steevanb\CodeGenerator\Core;

/**
 * Class to extends to create a generator
 */
abstract class Generator
{
	const VISIBILITY_PUBLIC = 1;
	const VISIBILITY_PROTECTED = 2;
	const VISIBILITY_PRIVATE = 3;
	/**
	 * Tabulation character
	 *
	 * @var string
	 */
	protected $tabStr = '    ';

	/**
	 * Create directory
	 *
	 * @param string $dir
	 */
	protected function _createDir($dir)
	{
		$dirName = $dir;
		$reversedDirs = array($dir);
		while (($dirName = dirname($dirName)) != '/') {
			$reversedDirs[] = $dirName;
		}
		$dirs = array_reverse($reversedDirs);

		foreach ($dirs as $dir) {
			if (is_dir($dir) === false) {
				mkdir($dir, 0755);
				d($dir);
			}
		}
	}

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
	public function getCode4Line($line, $tabs = 0, $endOfLines = 1)
	{
		return $this->getTabs($tabs) . $line . $this->getEndOfLines($endOfLines);
	}
}