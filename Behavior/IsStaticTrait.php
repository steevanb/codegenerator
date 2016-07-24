<?php

namespace steevanb\CodeGenerator\Behavior;

trait IsStaticTrait
{
	/** @var boolean */
	protected $isStatic = false;

	/**
	 * @param boolean $isStatic
	 * @return $this
	 */
	public function setStatic($isStatic)
	{
		$this->isStatic = $isStatic;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isStatic()
	{
		return $this->isStatic;
	}
}
