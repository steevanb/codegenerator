<?php

namespace steevanb\CodeGenerator\Model;

/**
 * Add type property and accessors
 */
trait Type
{
	/**
	 * Type
	 *
	 * @var string
	 */
	protected $type = null;

	/**
	 * Set type
	 *
	 * @param string $type
	 * @return $this
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
}
