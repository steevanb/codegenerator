<?php

namespace steevanb\CodeGenerator\Model;

/**
 * Add defaultValue property and accessors
 */
trait DefaultValue
{
	/**
	 * Default value
	 *
	 * @var string
	 */
	protected $defaultValue = null;

	/**
	 * Set default value
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setDefaultValue($value)
	{
		$this->defaultValue = $value;

		return $this;
	}

	/**
	 * Get default value
	 * 
	 * @return string
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}

}
