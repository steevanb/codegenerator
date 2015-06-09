<?php

namespace steevanb\CodeGenerator\Model;

/**
 * Add isStatic property and accessors
 */
trait IsStatic
{
	/**
	 * Indicate if it's static
	 *
	 * @var boolean
	 */
	protected $isStatic = false;

	/**
	 * Set if it's static
	 *
	 * @param boolean $isStatic
	 * @return $this
	 */
	public function setStatic($isStatic)
	{
		$this->isStatic = $isStatic;

		return $this;
	}

	/**
	 * Get if it's static
	 *
	 * @return boolean
	 */
	public function isStatic()
	{
		return $this->isStatic;
	}
}
