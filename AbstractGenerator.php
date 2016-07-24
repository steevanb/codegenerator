<?php

namespace steevanb\CodeGenerator;

abstract class AbstractGenerator
{
	const VISIBILITY_PUBLIC = 1;
	const VISIBILITY_PROTECTED = 2;
	const VISIBILITY_PRIVATE = 3;

	/** @var string */
	protected $tabStr = '    ';

    /**
	 * @param bool $tabsAsSpaces
	 * @return $this
	 */
	public function setTabsAsSpaces($tabsAsSpaces)
	{
		$this->tabStr = ($tabsAsSpaces) ? '    ' : "\t";

		return $this;
	}

    /**
	 * @return boolean
	 */
	public function getTabsAsSpaces()
	{
		return ($this->tabStr == "\t");
	}

    /**
	 * @param int $count
	 * @return string
	 */
	public function getCodeForTabs($count = 1)
	{
		return str_repeat($this->tabStr, $count);
	}

    /**
	 * @param int $count
	 * @return string
	 */
	public function getCodeForEndOfLines($count = 1)
	{
		return str_repeat(PHP_EOL, $count);
	}

    /**
	 * @param string $line
	 * @param int $tabs
	 * @param int $endOfLines
	 * @return string
	 */
	public function getCodeForLine($line, $tabs = 0, $endOfLines = 1)
	{
		return $this->getCodeForTabs($tabs) . $line . $this->getCodeForEndOfLines($endOfLines);
	}

    /**
     * @param string $dir
     * @return $this
     */
    protected function createDir($dir)
    {
        $dirName = $dir;
        $reversedDirs = array($dir);
        while (($dirName = dirname($dirName)) !== DIRECTORY_SEPARATOR) {
            $reversedDirs[] = $dirName;
        }
        $dirs = array_reverse($reversedDirs);

        foreach ($dirs as $dir) {
            if (is_dir($dir) === false) {
                mkdir($dir, 0755);
            }
        }

        return $this;
    }
}
