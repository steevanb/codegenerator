<?php

namespace steevanb\CodeGenerator\Behavior;

trait DefaultValueTrait
{
	/** @var mixed */
	protected $defaultValue = null;

	/**
	 * @param mixed $value
	 * @return $this
	 */
	public function setDefaultValue($value)
	{
		$this->defaultValue = $value;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}
}
