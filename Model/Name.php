<?php

namespace steevanb\CodeGenerator\Model;

/**
 * Add name property and accessors
 */
trait Name
{
	/**
	 * Name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Set name
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}
