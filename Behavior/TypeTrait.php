<?php

namespace steevanb\CodeGenerator\Behavior;

/**
 * Add type property and accessors
 */
trait TypeTrait
{
	/** @var string */
	protected $type = null;

	/**
	 * @param string $type
	 * @return $this
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
}
